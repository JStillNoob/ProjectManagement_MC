<?php

namespace App\Http\Controllers;

use App\Models\IssuanceRecord;
use App\Models\IssuanceRecordItem;
use App\Models\InventoryRequest;
use App\Models\InventoryItem;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class IssuanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = IssuanceRecord::with(['project', 'milestone', 'items']);

        // Foreman can only see issuances for their assigned projects
        if ($user->UserTypeID == 4) { // Foreman
            $assignedProjectIds = \App\Models\ProjectEmployee::where('EmployeeID', $user->EmployeeID)
                ->pluck('ProjectID')
                ->toArray();
            $query->whereIn('ProjectID', $assignedProjectIds);
        }

        if ($request->has('project_id') && $request->project_id != '') {
            $query->where('ProjectID', $request->project_id);
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('IssuanceDate', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('IssuanceDate', '<=', $request->date_to);
        }

        if ($request->has('search') && $request->search != '') {
            $query->where('IssuanceNumber', 'like', '%' . $request->search . '%');
        }

        $issuances = $query->orderBy('IssuanceDate', 'desc')->paginate(15);
        $projects = Project::where('StatusID', 3)->orderBy('ProjectName')->get(); // StatusID 3 = On Going

        return view('issuance.index', compact('issuances', 'projects'));
    }

    public function create(Request $request)
    {
        $requestId = $request->get('request_id');
        $inventoryRequest = null;
        
        if ($requestId) {
            $inventoryRequest = InventoryRequest::with(['items.item.resourceCatalog', 'project', 'milestone', 'employee'])
                ->findOrFail($requestId);
        }

        $projects = Project::where('StatusID', 3)->orderBy('ProjectName')->get(); // StatusID 3 = On Going
        $inventoryItems = InventoryItem::with('resourceCatalog')
            ->where('Status', 'Active')
            ->where('AvailableQuantity', '>', 0)
            ->join('resource_catalog', 'inventory_items.ResourceCatalogID', '=', 'resource_catalog.ResourceCatalogID')
            ->orderBy('resource_catalog.ItemName')
            ->select('inventory_items.*')
            ->get();
        
        // Get approved inventory requests for dropdown
        $requests = InventoryRequest::with('project')
            ->where('Status', 'Approved')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get employees for received by dropdown
        $employees = Employee::orderBy('first_name')->get();

        return view('issuance.create', compact('projects', 'inventoryItems', 'inventoryRequest', 'requests', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ProjectID' => 'required|exists:projects,ProjectID',
            'MilestoneID' => 'nullable|exists:project_milestones,milestone_id',
            'IssuanceDate' => 'required|date',
            'ReceivedBy' => 'required|exists:employees,id',
            'Notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.ItemID' => 'required|exists:inventory_items,ItemID',
            'items.*.Quantity' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $employeeId = $user->getAttributeValue('EmployeeID');
            
            if (!$employeeId) {
                return back()->with('error', 'Employee record not found for current user.');
            }

            $issuanceRecord = IssuanceRecord::create([
                'IssuanceNumber' => IssuanceRecord::generateIssuanceNumber(),
                'RequestID' => $request->RequestID ?? null,
                'ProjectID' => $validated['ProjectID'],
                'MilestoneID' => $validated['MilestoneID'],
                'IssuanceDate' => $validated['IssuanceDate'],
                'IssuedBy' => $employeeId,
                'ReceivedBy' => $validated['ReceivedBy'],
                'Remarks' => $validated['Notes'] ?? null,
            ]);

            foreach ($validated['items'] as $itemData) {
                $inventoryItem = InventoryItem::with('resourceCatalog')->find($itemData['ItemID']);
                
                // Check availability
                if ($inventoryItem->AvailableQuantity < $itemData['Quantity']) {
                    DB::rollBack();
                    $itemName = $inventoryItem->resourceCatalog->ItemName ?? 'Unknown Item';
                    return back()->withInput()->with('error', 
                        'Insufficient stock for ' . $itemName . 
                        '. Available: ' . $inventoryItem->AvailableQuantity);
                }

                $itemType = ($inventoryItem->resourceCatalog->Type ?? 'Materials') === 'Equipment' ? 'Equipment' : 'Material';
                
                IssuanceRecordItem::create([
                    'IssuanceID' => $issuanceRecord->IssuanceID,
                    'ItemID' => $itemData['ItemID'],
                    'QuantityIssued' => $itemData['Quantity'],
                    'Unit' => $inventoryItem->resourceCatalog->Unit ?? 'units',
                    'ItemType' => $itemType,
                ]);
            }

            // Update inventory
            $issuanceRecord->processIssuance();

            // Update request status if linked
            if ($request->RequestID) {
                $inventoryRequest = InventoryRequest::find($request->RequestID);
                if ($inventoryRequest) {
                    $inventoryRequest->update(['Status' => 'Fulfilled']);
                }
            }

            DB::commit();

            return redirect()->route('issuance.show', $issuanceRecord->IssuanceID)
                ->with('success', 'Items issued successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating issuance: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $issuance = IssuanceRecord::with([
            'project',
            'milestone',
            'issuer',
            'receiver',
            'items.inventoryItem.resourceCatalog',
            'inventoryRequest'
        ])->findOrFail($id);

        return view('issuance.show', compact('issuance'));
    }

    public function generatePDF($id)
    {
        $issuance = IssuanceRecord::with([
            'project',
            'milestone',
            'issuedBy',
            'receivedBy',
            'items.inventoryItem.resourceCatalog'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('issuance.pdf', compact('issuance'));
        
        $filename = $issuance->IssuanceNumber . '.pdf';
        
        return $pdf->download($filename);
    }

    public function destroy($id)
    {
        $issuanceRecord = IssuanceRecord::findOrFail($id);

        DB::beginTransaction();
        try {
            // Reverse inventory updates
            $issuanceRecord->reverseIssuance();
            
            // Delete issuance record
            $issuanceRecord->delete();

            DB::commit();

            return redirect()->route('issuance.index')
                ->with('success', 'Issuance record deleted and inventory updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting issuance: ' . $e->getMessage());
        }
    }
}
