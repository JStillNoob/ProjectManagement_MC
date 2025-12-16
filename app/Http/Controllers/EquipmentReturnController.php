<?php

namespace App\Http\Controllers;

use App\Models\EquipmentIncident;
use App\Models\InventoryItem;
use App\Models\Project;
use App\Models\ProjectMilestoneEquipment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EquipmentReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = ProjectMilestoneEquipment::with(['inventoryItem.resourceCatalog', 'milestone.project', 'milestone']);

        if ($request->has('project_id') && $request->project_id != '') {
            $query->whereHas('milestone', function($q) use ($request) {
                $q->where('project_id', $request->project_id);
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('Status', $request->status);
        }

        $equipmentAssignments = $query->whereNotNull('DateAssigned')
            ->orderBy('DateAssigned', 'desc')
            ->paginate(15);
        
        $assignments = $equipmentAssignments; // Alias for view compatibility

        $projects = Project::where('StatusID', 3)->orderBy('ProjectName')->get(); // StatusID 3 = On Going
        
        // Get equipment items for filter - using resourceCatalog relationship
        $equipmentItems = InventoryItem::with('resourceCatalog')
            ->whereHas('resourceCatalog', function($q) {
                $q->where('Type', 'Equipment');
            })
            ->get();
        
        // Calculate summary statistics
        $totalAssigned = ProjectMilestoneEquipment::whereNotNull('DateAssigned')->count();
        $returnedGood = ProjectMilestoneEquipment::whereNotNull('DateReturned')
            ->where('Status', 'Returned')
            ->count();
        $returnedDamaged = ProjectMilestoneEquipment::whereNotNull('DateReturned')
            ->where('Status', 'Damaged')
            ->count();
        $inUse = ProjectMilestoneEquipment::whereNotNull('DateAssigned')
            ->whereNull('DateReturned')
            ->count();

        return view('equipment.returns.index', compact(
            'equipmentAssignments',
            'assignments',
            'projects', 
            'equipmentItems',
            'totalAssigned',
            'returnedGood',
            'returnedDamaged',
            'inUse'
        ));
    }

    public function create($id)
    {
        $assignment = ProjectMilestoneEquipment::with([
            'inventoryItem.resourceCatalog',
            'milestone.project'
        ])->findOrFail($id);

        if ($assignment->DateReturned) {
            return back()->with('error', 'This equipment has already been returned.');
        }

        $employees = Employee::active()->orderBy('first_name')->get();

        return view('equipment.returns.create', compact('assignment', 'employees'));
    }

    public function store(Request $request, $id)
    {
        // Normalize empty strings to null for incident fields when status is Returned
        $requestData = $request->all();
        if ($request->Status === 'Returned') {
            if (empty($requestData['incident_type']) || $requestData['incident_type'] === '') {
                $requestData['incident_type'] = null;
            }
            if (empty($requestData['incident_description']) || $requestData['incident_description'] === '') {
                $requestData['incident_description'] = null;
            }
            $request->merge($requestData);
        }

        $rules = [
            'DateReturned' => 'required|date',
            'Status' => 'required|in:Returned,Damaged,Missing',
            'ReturnRemarks' => 'nullable|string',
            'damage_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'estimated_cost' => 'nullable|numeric|min:0',
        ];

        // Only require incident fields if status is Damaged or Missing
        if (in_array($request->Status, ['Damaged', 'Missing'])) {
            $rules['incident_type'] = 'required|in:Damage,Loss,Theft,Malfunction';
            $rules['incident_description'] = 'required|string';
        } else {
            // When status is Returned, these fields are not required and can be null
            $rules['incident_type'] = 'nullable';
            $rules['incident_description'] = 'nullable';
        }

        $validated = $request->validate($rules);

        $assignment = ProjectMilestoneEquipment::findOrFail($id);

        DB::beginTransaction();
        try {
            $photoPath = null;
            if ($request->hasFile('damage_photo')) {
                $photoPath = $request->file('damage_photo')->store('equipment-incidents', 'public');
            }

            // Update assignment
            $assignment->update([
                'DateReturned' => $validated['DateReturned'],
                'Status' => $validated['Status'],
                'ReturnRemarks' => $validated['ReturnRemarks'],
                'ReturnedBy' => Auth::user()->EmployeeID,
            ]);

            // Update inventory based on condition
            $inventoryItem = $assignment->inventoryItem;
            
            if ($validated['Status'] == 'Returned') {
                // Return to available stock
                $inventoryItem->CommittedQuantity -= $assignment->QuantityAssigned;
                $inventoryItem->AvailableQuantity += $assignment->QuantityAssigned;
            } elseif ($validated['Status'] == 'Damaged') {
                // Remove from committed, mark as damaged (don't add to available)
                $inventoryItem->CommittedQuantity -= $assignment->QuantityAssigned;
                $inventoryItem->TotalQuantity -= $assignment->QuantityAssigned;
                
                // Create incident report
                EquipmentIncident::create([
                    'ItemID' => $assignment->ItemID,
                    'ProjectID' => $assignment->milestone->project_id ?? null,
                    'EquipmentAssignmentID' => $assignment->EquipmentAssignmentID,
                    'IncidentType' => $validated['incident_type'],
                    'IncidentDate' => $validated['DateReturned'],
                    'ResponsibleEmployeeID' => null, // Can be set later
                    'Description' => $validated['incident_description'],
                    'EstimatedCost' => $validated['estimated_cost'] ?? 0,
                    'PhotoPath' => $photoPath,
                ]);
            } elseif ($validated['Status'] == 'Missing') {
                // Remove completely from inventory
                $inventoryItem->CommittedQuantity -= $assignment->QuantityAssigned;
                $inventoryItem->TotalQuantity -= $assignment->QuantityAssigned;
                
                // Create incident report
                EquipmentIncident::create([
                    'ItemID' => $assignment->ItemID,
                    'ProjectID' => $assignment->milestone->project_id ?? null,
                    'EquipmentAssignmentID' => $assignment->EquipmentAssignmentID,
                    'IncidentType' => $validated['incident_type'],
                    'IncidentDate' => $validated['DateReturned'],
                    'Description' => $validated['incident_description'],
                    'EstimatedCost' => $validated['estimated_cost'] ?? 0,
                    'PhotoPath' => $photoPath,
                ]);
            }

            $inventoryItem->save();

            DB::commit();

            return redirect()->route('equipment.returns.index')
                ->with('success', 'Equipment return processed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            
            return back()->withInput()->with('error', 'Error processing return: ' . $e->getMessage());
        }
    }

    public function incidents(Request $request)
    {
        $query = EquipmentIncident::with(['inventoryItem', 'project', 'responsibleEmployee']);

        if ($request->has('type') && $request->type != '') {
            $query->where('IncidentType', $request->type);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('Status', $request->status);
        }

        $incidents = $query->orderBy('IncidentDate', 'desc')->paginate(15);

        return view('equipment.incidents.index', compact('incidents'));
    }

    public function showIncident($id)
    {
        $incident = EquipmentIncident::with([
            'inventoryItem',
            'project',
            'equipmentAssignment',
            'responsibleEmployee'
        ])->findOrFail($id);

        return view('equipment.incidents.show', compact('incident'));
    }

    public function updateIncident(Request $request, $id)
    {
        $validated = $request->validate([
            'Status' => 'required|in:Reported,Under Investigation,Resolved,Closed',
            'ResponsibleEmployeeID' => 'nullable|exists:employees,EmployeeID',
            'ActionTaken' => 'nullable|string',
        ]);

        $incident = EquipmentIncident::findOrFail($id);
        $incident->update($validated);

        return back()->with('success', 'Incident updated successfully.');
    }
}
