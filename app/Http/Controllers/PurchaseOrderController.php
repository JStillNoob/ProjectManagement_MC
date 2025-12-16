<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\InventoryRequest;
use App\Models\InventoryItem;
use App\Models\ResourceCatalog;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of purchase orders
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'creator', 'inventoryRequest']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('Status', $request->status);
        }

        // Filter by supplier
        if ($request->has('supplier_id') && $request->supplier_id != '') {
            $query->where('SupplierID', $request->supplier_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('OrderDate', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('OrderDate', '<=', $request->date_to);
        }

        // Search by PO ID
        if ($request->has('search') && $request->search != '') {
            $query->where('POID', 'like', '%' . $request->search . '%');
        }

        $purchaseOrders = $query->orderBy('created_at', 'desc')->paginate(15);
        $suppliers = Supplier::active()->orderBy('SupplierName')->get();

        return view('purchase-orders.index', compact('purchaseOrders', 'suppliers'));
    }

    /**
     * Show the form for creating a new purchase order
     */
    public function create(Request $request)
    {
        $suppliers = Supplier::active()->orderBy('SupplierName')->get();
        $resourceCatalog = ResourceCatalog::active()->orderBy('ItemName')->get();
        
        $inventoryRequest = null;
        $requestItems = collect([]); // Initialize as empty collection

        // If creating from an inventory request
        if ($request->has('request_id')) {
            // Load the request with items using the same pattern as the show method
            $inventoryRequest = InventoryRequest::with(['items.item.resourceCatalog'])
                ->findOrFail($request->request_id);
            
            // Get all items from the request (use the items relationship directly)
            $allItems = $inventoryRequest->items;
            
            // Filter to only include items with valid inventory items and resource catalogs
            $requestItems = $allItems->filter(function($requestItem) {
                try {
                    // Use item() method (same as show method uses)
                    $inventoryItem = $requestItem->item;
                    
                    if (!$inventoryItem || !is_object($inventoryItem)) {
                        return false;
                    }
                    
                    // Get resource catalog (should already be loaded via eager loading)
                    $resourceCatalog = $inventoryItem->resourceCatalog;
                    
                    // Validate resource catalog
                    if (!$resourceCatalog || !is_object($resourceCatalog) || !isset($resourceCatalog->ResourceCatalogID)) {
                        return false;
                    }
                    
                    return true;
                } catch (\Exception $e) {
                    \Log::error('Error filtering request items: ' . $e->getMessage());
                    return false;
                } catch (\Error $e) {
                    \Log::error('Error filtering request items: ' . $e->getMessage());
                    return false;
                }
            })->values();
        }

        return view('purchase-orders.create', compact('suppliers', 'resourceCatalog', 'inventoryRequest', 'requestItems'));
    }

    /**
     * Store a newly created purchase order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'SupplierID' => 'required|exists:suppliers,SupplierID',
            'items' => 'required|array|min:1',
            'items.*.ItemID' => 'required|exists:inventory_items,ItemID',
            'items.*.QuantityOrdered' => 'required|integer|min:1',
            'items.*.Specifications' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Get current user's employee ID
            $user = Auth::user();
            
            if (!$user->EmployeeID) {
                return back()->with('error', 'Employee record not found for current user.');
            }

            // Create purchase order with automatic order date
            $po = PurchaseOrder::create([
                'SupplierID' => $validated['SupplierID'],
                'RequestID' => $request->RequestID ?? null,
                'OrderDate' => now()->toDateString(),
                'Status' => 'Draft',
                'CreatedBy' => $user->EmployeeID,
            ]);

            // Create PO items
            foreach ($validated['items'] as $itemData) {
                $inventoryItem = InventoryItem::with('resourceCatalog')->find($itemData['ItemID']);

                PurchaseOrderItem::create([
                    'POID' => $po->POID,
                    'ItemID' => $itemData['ItemID'],
                    'QuantityOrdered' => $itemData['QuantityOrdered'],
                    'Unit' => $inventoryItem->resourceCatalog->Unit ?? 'unit',
                    'Specifications' => $itemData['Specifications'] ?? null,
                ]);
            }

            // Update inventory request status if linked
            if ($request->RequestID) {
                $inventoryRequest = InventoryRequest::find($request->RequestID);
                if ($inventoryRequest) {
                    $inventoryRequest->update(['Status' => 'Ordered']);
                }
            }

            // Generate PDF
            $po->load(['supplier', 'creator', 'items.inventoryItem']);
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('purchase-orders.pdf', ['purchaseOrder' => $po]);
            
            $pdfPath = 'purchase_orders/PO-' . $po->POID . '.pdf';
            $pdf->save(storage_path('app/public/' . $pdfPath));
            
            $po->update(['PDFPath' => $pdfPath]);

            DB::commit();

            return redirect()->route('purchase-orders.show', $po->POID)
                ->with('success', 'Purchase order created successfully and PDF generated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified purchase order
     */
    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with([
            'supplier',
            'inventoryRequest',
            'creator',
            'approver',
            'items.inventoryItem.resourceCatalog',
            'receivingRecords'
        ])->findOrFail($id);

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the purchase order
     */
    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::with('items.inventoryItem')->findOrFail($id);

        if (!$purchaseOrder->isEditable()) {
            return back()->with('error', 'This purchase order cannot be edited.');
        }

        $suppliers = Supplier::active()->orderBy('SupplierName')->get();
        $inventoryItems = InventoryItem::with('resourceCatalog')
            ->where('Status', 'Active')
            ->get();

        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'inventoryItems'));
    }

    /**
     * Update the purchase order
     */
    public function update(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if (!$purchaseOrder->isEditable()) {
            return back()->with('error', 'This purchase order cannot be edited.');
        }

        $validated = $request->validate([
            'SupplierID' => 'required|exists:suppliers,SupplierID',
            'OrderDate' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.ItemID' => 'required|exists:inventory_items,ItemID',
            'items.*.QuantityOrdered' => 'required|integer|min:1',
            'items.*.UnitPrice' => 'required|numeric|min:0',
            'items.*.Specifications' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update purchase order
            $purchaseOrder->update([
                'SupplierID' => $validated['SupplierID'],
                'OrderDate' => $validated['OrderDate'],
            ]);

            // Delete old items
            $purchaseOrder->items()->delete();

            // Create new items
            foreach ($validated['items'] as $itemData) {
                $inventoryItem = InventoryItem::find($itemData['ItemID']);
                
                $totalPrice = $itemData['QuantityOrdered'] * $itemData['UnitPrice'];

                PurchaseOrderItem::create([
                    'POID' => $purchaseOrder->POID,
                    'ItemID' => $itemData['ItemID'],
                    'QuantityOrdered' => $itemData['QuantityOrdered'],
                    'Unit' => $inventoryItem->Unit,
                    'UnitPrice' => $itemData['UnitPrice'],
                    'TotalPrice' => $totalPrice,
                    'Specifications' => $itemData['Specifications'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('purchase-orders.show', $purchaseOrder->POID)
                ->with('success', 'Purchase order updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Approve the purchase order
     */
    public function approve($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if ($purchaseOrder->Status !== 'Draft') {
            return back()->with('error', 'Only draft purchase orders can be approved.');
        }

        $user = Auth::user();

        if (!$user->EmployeeID) {
            return back()->with('error', 'Employee record not found.');
        }

        $purchaseOrder->approve($user->EmployeeID);

        return back()->with('success', 'Purchase order approved successfully.');
    }

    /**
     * Mark PO as sent
     */
    public function markAsSent($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if (!in_array($purchaseOrder->Status, ['Draft', 'Approved'])) {
            return back()->with('error', 'This purchase order cannot be marked as sent.');
        }

        $purchaseOrder->markAsSent();

        return back()->with('success', 'Purchase order marked as sent.');
    }

    /**
     * Cancel the purchase order
     */
    public function cancel($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if ($purchaseOrder->Status === 'Completed') {
            return back()->with('error', 'Completed purchase orders cannot be cancelled.');
        }

        $purchaseOrder->update(['Status' => 'Cancelled']);

        return back()->with('success', 'Purchase order cancelled.');
    }

    /**
     * Generate and download PDF
     */
    public function generatePDF($id)
    {
        $purchaseOrder = PurchaseOrder::with([
            'supplier',
            'creator',
            'approver',
            'items.inventoryItem'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('purchase-orders.pdf', compact('purchaseOrder'));
        
        $filename = 'PO-' . $purchaseOrder->POID . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Delete the purchase order
     */
    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if (!$purchaseOrder->isEditable()) {
            return back()->with('error', 'This purchase order cannot be deleted.');
        }

        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase order deleted successfully.');
    }
}
