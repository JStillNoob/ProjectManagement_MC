<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryRequest;
use App\Models\InventoryRequestItem;
use App\Models\Project;
use App\Models\InventoryItem;
use App\Models\ProjectMilestone;
use App\Models\ProjectEmployee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->get('status');
        $projectId = $request->get('project_id');

        $query = InventoryRequest::with(['project', 'employee', 'items.item.resourceCatalog', 'milestone', 'approver']);

        // Admin (UserTypeID = 2) can see all requests, others see only their own
        if ($user->UserTypeID != 2 && $user->EmployeeID) {
            $query->where('EmployeeID', $user->EmployeeID);
        }

        // Filter by status
        if ($status) {
            $query->where('Status', $status);
        }

        // Filter by project
        if ($projectId) {
            $query->where('ProjectID', $projectId);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get user's assigned projects for filter
        $userProjects = [];
        if ($user->EmployeeID) {
            $employee = \App\Models\Employee::find($user->EmployeeID);
            if ($employee) {
                $userProjects = $employee->projects()->where('project_employees.status', 'Active')->get();
            }
        } else {
            // Admin/Production Head can see all projects
            $userProjects = Project::all();
        }

        // Preload active inventory items for selectors
        $materials = $this->availableItemsCollection('Materials');
        $equipment = $this->availableItemsCollection('Equipment');

        // For foreman: get assigned project with milestones and required items
        $foremanProject = null;
        if ($user->UserTypeID == 3 && $user->EmployeeID) {
            $employee = \App\Models\Employee::find($user->EmployeeID);
            if ($employee) {
                $foremanProject = $employee->projects()
                    ->wherePivot('status', 'Active')
                    ->with(['milestones.requiredItems.item.resourceCatalog'])
                    ->first();
            }
        }

        return view('inventory.requests.index', compact(
            'requests',
            'userProjects',
            'status',
            'projectId',
            'user',
            'materials',
            'equipment',
            'foremanProject'
        ));
    }

    /**
     * Show request history for foreman
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        
        // Only foremen (UserTypeID == 3) can access this page
        if ($user->UserTypeID != 3 || !$user->EmployeeID) {
            return redirect()->route('inventory.requests.index')
                ->with('error', 'You do not have permission to access this page.');
        }

        $employee = \App\Models\Employee::find($user->EmployeeID);
        if (!$employee) {
            return redirect()->route('inventory.requests.index')
                ->with('error', 'Employee record not found.');
        }

        // Get foreman's assigned project
        $foremanProject = $employee->projects()
            ->wherePivot('status', 'Active')
            ->first();

        if (!$foremanProject) {
            return redirect()->route('inventory.requests.index')
                ->with('error', 'You are not assigned to any project.');
        }

        // Get all requests for this project made by the foreman
        $query = InventoryRequest::with(['project', 'employee', 'items.item.resourceCatalog', 'milestone', 'approver'])
            ->where('ProjectID', $foremanProject->ProjectID)
            ->where('EmployeeID', $user->EmployeeID);

        // Filter by status if provided
        $status = $request->get('status');
        if ($status) {
            $query->where('Status', $status);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('inventory.requests.history', compact('requests', 'foremanProject', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('inventory.requests.index')
            ->with('warning', 'Use the Bulk Material Requisition button to submit new requests.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->EmployeeID) {
            return redirect()->back()
                ->with('error', 'Only employees assigned to projects can create requests.');
        }

        $payload = $request->all();

        if (isset($payload['items']) && is_string($payload['items'])) {
            $decoded = json_decode($payload['items'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $payload['items'] = $decoded;
            }
        }

        $validated = validator($payload, [
            'ProjectID' => 'required|exists:projects,ProjectID',
            'MilestoneID' => 'required|exists:project_milestones,milestone_id',
            'Reason' => 'nullable|string|max:500',
            'RequestType' => 'nullable|in:Material,Equipment,Mixed',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'nullable|exists:inventory_items,ItemID',
            'items.*.resource_catalog_id' => 'nullable|exists:resource_catalog,ResourceCatalogID',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.needs_purchase' => 'nullable|boolean',
        ])->validate();
        
        // Auto-create inventory items for resource catalog items that don't exist in inventory yet
        foreach ($validated['items'] as $index => $item) {
            if (!$item['inventory_item_id'] && !empty($item['resource_catalog_id'])) {
                $resourceCatalog = \App\Models\ResourceCatalog::find($item['resource_catalog_id']);
                
                if ($resourceCatalog) {
                    // Create inventory item
                    $inventoryItem = InventoryItem::create([
                        'ResourceCatalogID' => $resourceCatalog->ResourceCatalogID,
                        'ItemName' => $resourceCatalog->ItemName,
                        'Unit' => $resourceCatalog->Unit,
                        'QuantityInStock' => 0,  // New item, no stock
                        'ReorderLevel' => 10,
                        'Status' => 'Active',
                    ]);
                    
                    // Update the validated data with the new inventory_item_id
                    $validated['items'][$index]['inventory_item_id'] = $inventoryItem->ItemID;
                    $validated['items'][$index]['needs_purchase'] = true;  // Mark as needs purchase
                }
            }
        }

        // Verify employee is assigned to the project
        $projectEmployee = ProjectEmployee::where('ProjectID', $validated['ProjectID'])
            ->where('EmployeeID', $user->EmployeeID)
            ->where('status', 'Active')
            ->first();

        if (!$projectEmployee) {
            return redirect()->back()
                ->with('error', 'You are not assigned to this project.');
        }

        // Verify milestone belongs to project
        $milestone = ProjectMilestone::where('milestone_id', $validated['MilestoneID'])
            ->where('project_id', $validated['ProjectID'])
            ->first();
        
        if (!$milestone) {
            return redirect()->back()
                ->with('error', 'Invalid milestone for this project.');
        }

        // Check if user is foreman (UserTypeID == 3) and milestone status is Pending
        if ($user->UserTypeID == 3 && strtolower($milestone->status ?? 'Pending') === 'pending') {
            return redirect()->back()
                ->with('error', 'You cannot request items for a milestone with "Pending" status. The milestone must be "In Progress" or "Completed" to request items.');
        }

        // Check if milestone is waiting for approval - disable requests
        if ($milestone->SubmissionStatus === 'Pending Approval') {
            return redirect()->back()
                ->with('error', 'You cannot request items for this milestone. It is currently waiting for approval. Please wait until it is approved before making new requests.');
        }

        // Check if there's an existing request for this milestone
        // If yes, mark the new request as "Additional Request"
        $existingRequest = InventoryRequest::where('MilestoneID', $validated['MilestoneID'])
            ->where('ProjectID', $validated['ProjectID'])
            ->whereIn('Status', ['Pending', 'Pending - To Order', 'Ordered', 'Approved'])
            ->first();
        
        $isAdditionalRequest = $existingRequest ? true : false;

        $normalizedItems = collect($validated['items'])
            ->map(function ($item) {
                return [
                    'inventory_item_id' => (int) $item['inventory_item_id'],
                    'quantity' => (float) $item['quantity'],
                ];
            })
            ->groupBy('inventory_item_id')
            ->map(function ($rows) {
                return [
                    'inventory_item_id' => $rows->first()['inventory_item_id'],
                    'quantity' => $rows->sum('quantity'),
                ];
            })
            ->values();

        $shortages = [];

        try {
            DB::transaction(function () use (&$shortages, $normalizedItems, $validated, $user, $isAdditionalRequest) {
                // Auto-determine RequestType based on items
                $requestType = $validated['RequestType'] ?? null;
                if (!$requestType) {
                    // Load items to check their types
                    $itemIds = $normalizedItems->pluck('inventory_item_id')->toArray();
                    $items = InventoryItem::with('resourceCatalog')->whereIn('ItemID', $itemIds)->get();
                    
                    $hasMaterials = false;
                    $hasEquipment = false;
                    
                    foreach ($items as $item) {
                        if ($item->resourceCatalog && $item->resourceCatalog->Type === 'Materials') {
                            $hasMaterials = true;
                        } elseif ($item->resourceCatalog && $item->resourceCatalog->Type === 'Equipment') {
                            $hasEquipment = true;
                        }
                    }
                    
                    // Determine RequestType
                    if ($hasMaterials && $hasEquipment) {
                        $requestType = 'Mixed';
                    } elseif ($hasEquipment) {
                        $requestType = 'Equipment';
                    } else {
                        $requestType = 'Material'; // Default to Material
                    }
                }

                $inventoryRequest = InventoryRequest::create([
                    'ProjectID' => $validated['ProjectID'],
                    'EmployeeID' => $user->EmployeeID,
                    'RequestType' => $requestType,
                    'Reason' => $validated['Reason'] ?? null,
                    'MilestoneID' => $validated['MilestoneID'],
                    'Status' => 'Pending',
                    'IsAdditionalRequest' => $isAdditionalRequest,
                ]);

                foreach ($normalizedItems as $line) {
                    $item = InventoryItem::lockForUpdate()->find($line['inventory_item_id']);

                    if (!$item) {
                        throw ValidationException::withMessages([
                            'items' => 'One or more selected items no longer exist.',
                        ]);
                    }

                    $available = $this->calculateAvailableStock($item);
                    $needsPurchase = $line['quantity'] > $available;

                    if ($needsPurchase) {
                        $shortages[] = [
                            'name' => $item->ItemName,
                            'available' => $available,
                            'requested' => $line['quantity'],
                            'unit' => $item->Unit,
                        ];
                    }

                    InventoryRequestItem::create([
                        'InventoryRequestID' => $inventoryRequest->RequestID,
                        'InventoryItemID' => $item->ItemID,
                        'QuantityRequested' => $line['quantity'],
                        'UnitOfMeasure' => $item->Unit,
                        'CommittedQuantity' => 0,
                        'NeedsPurchase' => (bool) $needsPurchase,
                    ]);
                }

                if (!empty($shortages)) {
                    $inventoryRequest->Status = 'Pending - To Order';
                    $inventoryRequest->save();
                }
            });
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);
            \Log::error('Inventory Request Store Error', [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
                'user' => $user->id,
                'payload' => $request->except(['_token', 'items'])
            ]);
            
            // Show user-friendly error message
            $errorMessage = 'Unable to submit request. Please try again.';
            if (config('app.debug')) {
                $errorMessage .= ' Error: ' . $exception->getMessage();
            }
            
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }

        if (!empty($shortages)) {
            $summary = collect($shortages)->map(function ($row) {
                return "{$row['name']} (Requested: " . number_format($row['requested'], 2) . " {$row['unit']}, Available: " . number_format($row['available'], 2) . " {$row['unit']})";
            })->implode('; ');

            $message = $isAdditionalRequest 
                ? 'Additional request submitted for this milestone. Some items need purchasing: ' . $summary
                : 'Request submitted. Some items need purchasing: ' . $summary;

            return redirect()->route('inventory.requests.index')
                ->with('warning', $message);
        }

        $message = $isAdditionalRequest
            ? 'Additional request submitted successfully for this milestone.'
            : 'Inventory request submitted successfully.';

        return redirect()->route('inventory.requests.index')
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(InventoryRequest $inventoryRequest)
    {
        $inventoryRequest->load(['project', 'employee', 'items.item.resourceCatalog', 'milestone', 'approver', 'purchaseOrders.items']);
        $shortageItems = $inventoryRequest->items->where('NeedsPurchase', true);

        // Calculate current stock availability for each item for admin verification
        $stockVerification = [];
        $hasInsufficientStock = false;
        
        foreach ($inventoryRequest->items as $line) {
            if ($line->item) {
                $available = $this->calculateAvailableStock($line->item);
                $sufficient = $available >= $line->QuantityRequested;
                
                if (!$sufficient) {
                    $hasInsufficientStock = true;
                }
                
                $stockVerification[$line->InventoryItemID] = [
                    'available' => $available,
                    'requested' => $line->QuantityRequested,
                    'sufficient' => $sufficient,
                    'unit' => $line->item->resourceCatalog->Unit ?? 'units',
                ];
            }
        }

        return view('inventory.requests.show', compact('inventoryRequest', 'shortageItems', 'stockVerification', 'hasInsufficientStock'));
    }

    /**
     * Display purchase order form with shortage items.
     */
    public function purchaseOrderForm(InventoryRequest $inventoryRequest)
    {
        $user = Auth::user();

        if ($user->UserTypeID != 2) {
            abort(403, 'Only Admin/GM accounts can create purchase orders.');
        }

        if ($inventoryRequest->Status !== 'Pending - To Order') {
            return redirect()->route('inventory.requests.show', $inventoryRequest)
                ->with('error', 'This request does not require a purchase order.');
        }

        $inventoryRequest->load(['project', 'employee', 'items.item.resourceCatalog', 'milestone']);
        $shortageItems = $inventoryRequest->items->where('NeedsPurchase', true);

        if ($shortageItems->isEmpty()) {
            return redirect()->route('inventory.requests.show', $inventoryRequest)
                ->with('error', 'No shortage items found for this request.');
        }

        // Get all suppliers
        $suppliers = \App\Models\Supplier::orderBy('SupplierName')->get();

        return view('inventory.requests.purchase-order', compact('inventoryRequest', 'shortageItems', 'suppliers'));
    }

    /**
     * Save Purchase Order from Inventory Request
     */
    public function savePurchaseOrder(Request $request, InventoryRequest $inventoryRequest)
    {
        $user = Auth::user();

        if ($user->UserTypeID != 2) {
            return redirect()->back()->with('error', 'Only Admin/GM accounts can create purchase orders.');
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:inventory_items,ItemID',
            'items.*.supplier_id' => 'required|exists:suppliers,SupplierID',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Group items by supplier to create separate POs if needed
            // For now, we'll use the first supplier as main PO supplier
            $firstItem = collect($request->items)->first();
            $mainSupplierID = $firstItem['supplier_id'];

            // Create Purchase Order
            $purchaseOrder = \App\Models\PurchaseOrder::create([
                'SupplierID' => $mainSupplierID,
                'RequestID' => $inventoryRequest->RequestID,
                'OrderDate' => now()->format('Y-m-d'),
                'Status' => 'Sent',  // Set to Sent so it appears in Receiving
                'CreatedBy' => $user->EmployeeID,
            ]);

            // Create Purchase Order Items with individual suppliers
            foreach ($request->items as $item) {
                \App\Models\PurchaseOrderItem::create([
                    'POID' => $purchaseOrder->POID,
                    'ItemID' => $item['item_id'],
                    'SupplierID' => $item['supplier_id'],
                    'QuantityOrdered' => $item['quantity'],
                    'Unit' => $item['unit'],
                    'Specifications' => $item['specifications'] ?? null,
                ]);
            }

            // Generate PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.purchase-order-pdf', [
                'purchaseOrder' => $purchaseOrder->load(['supplier', 'items.item.resourceCatalog', 'items.supplier', 'creator']),
                'items' => $purchaseOrder->items,
                'totalQuantity' => $purchaseOrder->items->sum('QuantityOrdered'),
            ]);

            // Save PDF
            $pdfPath = 'purchase_orders/PO_' . str_pad($purchaseOrder->POID, 4, '0', STR_PAD_LEFT) . '_' . time() . '.pdf';
            $fullPath = storage_path('app/public/' . $pdfPath);
            
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }
            
            $pdf->save($fullPath);

            // Update PO with PDF path
            $purchaseOrder->update(['PDFPath' => $pdfPath]);

            // Update Inventory Request status to "Ordered" (not Approved - that's a separate action)
            $inventoryRequest->update(['Status' => 'Ordered']);

            DB::commit();

            return redirect()->route('inventory.requests.show', $inventoryRequest)
                ->with('success', 'Purchase Order created successfully! PO #' . str_pad($purchaseOrder->POID, 4, '0', STR_PAD_LEFT))
                ->with('pdf_path', asset('storage/' . $pdfPath));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create purchase order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Approve a request.
     */
    public function approve(Request $request, InventoryRequest $inventoryRequest)
    {
        $user = Auth::user();

        if ($user->UserTypeID != 2) {
            return redirect()->back()
                ->with('error', 'Only Admin/GM accounts can approve inventory requests.');
        }

        // Allow approval for 'Pending', 'Pending - To Order', 'Ordered', and 'Ready for Approval' statuses
        // Admin can approve even if stock is low - they verify stock before approving
        if (!in_array($inventoryRequest->Status, ['Pending', 'Pending - To Order', 'Ordered', 'Ready for Approval'])) {
            return redirect()->back()
                ->with('error', 'Only pending requests can be approved.');
        }

        $inventoryRequest->load('items.item.resourceCatalog');

        DB::beginTransaction();

        try {
            $lockedItems = [];
            $insufficient = [];

            foreach ($inventoryRequest->items as $line) {
                $item = InventoryItem::lockForUpdate()->find($line->InventoryItemID);

                if (!$item) {
                    throw ValidationException::withMessages([
                        'items' => 'One or more inventory items no longer exist.',
                    ]);
                }

                $available = $this->calculateAvailableStock($item);

                if ($line->QuantityRequested > $available) {
                    $insufficient[] = [
                        'name' => $line->item->resourceCatalog->ItemName ?? 'Unknown Item',
                        'available' => $available,
                        'requested' => $line->QuantityRequested,
                        'unit' => $line->item->resourceCatalog->Unit ?? 'units',
                    ];
                }

                $lockedItems[$line->InventoryItemID] = $item;
            }

            // Allow approval even with insufficient stock - admin has verified
            // If there's insufficient stock, we'll still approve but show a warning
            $hasInsufficientStock = !empty($insufficient);

            foreach ($inventoryRequest->items as $line) {
                $item = $lockedItems[$line->InventoryItemID];
                
                // Only commit stock if available, otherwise it will need purchasing
                $available = $this->calculateAvailableStock($item);
                if ($line->QuantityRequested <= $available) {
                    $item->CommittedQuantity = ($item->CommittedQuantity ?? 0) + $line->QuantityRequested;
                    $item->save();
                    $line->CommittedQuantity = $line->QuantityRequested;
                } else {
                    // Mark that this item needs purchase - committed quantity stays 0
                    $line->CommittedQuantity = 0;
                    $line->NeedsPurchase = true;
                }
                $line->save();
            }

            $inventoryRequest->Status = 'Approved';
            $inventoryRequest->ApprovedBy = $user->id;
            $inventoryRequest->ApprovedAt = now();
            $inventoryRequest->RejectionReason = null;
            $inventoryRequest->save();

            DB::commit();

            // Show appropriate message based on stock availability
            if ($hasInsufficientStock) {
                $message = collect($insufficient)->map(function ($row) {
                    return "{$row['name']} (Available: " . number_format($row['available'], 2) . ", Requested: " . number_format($row['requested'], 2) . " {$row['unit']})";
                })->implode('; ');

                return redirect()->back()
                    ->with('warning', 'Request approved. Some items have insufficient stock and may require purchasing: ' . $message);
            }

            return redirect()->back()
                ->with('success', 'Request approved and stock reserved successfully.');
        } catch (ValidationException $exception) {
            DB::rollBack();
            throw $exception;
        } catch (\Throwable $exception) {
            DB::rollBack();
            report($exception);

            return redirect()->back()
                ->with('error', 'Unable to approve the request. Please try again.');
        }
    }

    /**
     * Reject a request.
     */
    public function reject(Request $request, InventoryRequest $inventoryRequest)
    {
        $request->validate([
            'RejectionReason' => 'required|string|max:500',
        ]);

        if (!in_array($inventoryRequest->Status, ['Pending', 'Pending - To Order'])) {
            return redirect()->back()
                ->with('error', 'Only pending requests can be rejected.');
        }

        $inventoryRequest->Status = 'Rejected';
        $inventoryRequest->ApprovedBy = Auth::id();
        $inventoryRequest->ApprovedAt = now();
        $inventoryRequest->RejectionReason = $request->RejectionReason;
        $inventoryRequest->save();

        return redirect()->back()
            ->with('success', 'Request rejected successfully.');
    }

    /**
     * Return inventory items with available stock for selectors.
     */
    public function availableItems(Request $request)
    {
        $type = $request->get('type', 'Materials');

        return response()->json($this->availableItemsCollection($type));
    }

    protected function availableItemsCollection(string $type)
    {
        $typeName = $type === 'Equipment' ? 'Equipment' : 'Materials';

        // Get all items from resource catalog (not just those with inventory items)
        $resourceCatalogItems = \App\Models\ResourceCatalog::where('Type', $typeName)
            ->orderBy('ItemName')
            ->get();

        // Get all inventory items for this type
        $inventoryItems = InventoryItem::with('resourceCatalog')
            ->whereHas('resourceCatalog', function ($q) use ($typeName) {
                $q->where('Type', $typeName);
            })
            ->where('Status', 'Active')
            ->get()
            ->keyBy('ResourceCatalogID'); // Key by ResourceCatalogID for quick lookup

        // Map all resource catalog items, including those without inventory items
        return $resourceCatalogItems->map(function ($resourceItem) use ($inventoryItems) {
            $inventoryItem = $inventoryItems->get($resourceItem->ResourceCatalogID);
            
            return [
                'id' => $inventoryItem ? $inventoryItem->ItemID : null,
                'resource_catalog_id' => $resourceItem->ResourceCatalogID,
                'name' => $resourceItem->ItemName,
                'unit' => $resourceItem->Unit,
                'available_stock' => $inventoryItem ? $this->calculateAvailableStock($inventoryItem) : 0,
            ];
        });
    }

    protected function calculateAvailableStock(InventoryItem $item): float
    {
        $physical = (float) ($item->AvailableQuantity ?? 0);
        $committed = (float) ($item->CommittedQuantity ?? 0);

        $available = $physical - $committed;

        return $available > 0 ? $available : 0;
    }

    protected function triggerPurchaseOrderWorkflow(InventoryRequest $inventoryRequest, array $insufficientItems): void
    {
        $summary = collect($insufficientItems)->map(function ($row) {
            return "{$row['name']} (Available: " . number_format($row['available'], 2) . ", Requested: " . number_format($row['requested'], 2) . ")";
        })->implode('; ');

        $inventoryRequest->Status = 'Needs PO';
        $inventoryRequest->ApprovedBy = Auth::id();
        $inventoryRequest->ApprovedAt = now();
        $inventoryRequest->RejectionReason = 'PO required for: ' . $summary;
        $inventoryRequest->save();

        // Placeholder hook for actual PO workflow integration
        // event(new PurchaseOrderRequired($inventoryRequest, $insufficientItems));
    }
}

