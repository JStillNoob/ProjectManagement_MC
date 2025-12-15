<?php

namespace App\Http\Controllers;

use App\Models\ReceivingRecord;
use App\Models\ReceivingRecordItem;
use App\Models\PurchaseOrder;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReceivingController extends Controller
{
    /**
     * Display a listing of receiving records
     */
    public function index(Request $request)
    {
        $query = ReceivingRecord::with(['purchaseOrder.supplier', 'receiver']);

        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('ReceivedDate', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('ReceivedDate', '<=', $request->date_to);
        }

        // Filter by PO
        if ($request->has('po_id') && $request->po_id != '') {
            $query->where('POID', $request->po_id);
        }

        // Search by delivery receipt number
        if ($request->has('search') && $request->search != '') {
            $query->where('DeliveryReceiptNumber', 'like', '%' . $request->search . '%');
        }

        $receivingRecords = $query->orderBy('ReceivedDate', 'desc')->paginate(15);
        $purchaseOrders = PurchaseOrder::whereIn('Status', ['Sent', 'Partially Received'])
            ->orderBy('POID')
            ->get();

        return view('receiving.index', compact('receivingRecords', 'purchaseOrders'));
    }

    /**
     * Show the form for creating a new receiving record
     */
    public function create(Request $request)
    {
        $poId = $request->get('po_id');
        
        if (!$poId) {
            // Show PO selection page
            $purchaseOrders = PurchaseOrder::with('supplier')
                ->whereIn('Status', ['Sent', 'Partially Received'])
                ->orderBy('POID', 'desc')
                ->get();
            
            return view('receiving.select-po', compact('purchaseOrders'));
        }

        $purchaseOrder = PurchaseOrder::with([
            'supplier',
            'items' => function($query) {
                $query->where('QuantityReceived', '<', DB::raw('QuantityOrdered'))
                      ->with('inventoryItem.resourceCatalog');
            }
        ])->findOrFail($poId);

        // Check if PO has items to receive
        if ($purchaseOrder->items->isEmpty()) {
            return redirect()->route('purchase-orders.show', $poId)
                ->with('error', 'All items in this PO have been fully received.');
        }

        return view('receiving.create', compact('purchaseOrder'));
    }

    /**
     * Store a newly created receiving record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'POID' => 'required|exists:purchase_orders,POID',
            'ReceivedDate' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.POItemID' => 'required|exists:purchase_order_items,POItemID',
            'items.*.QuantityReceived' => 'required|integer|min:1',
            'items.*.Condition' => 'required|in:Good,Damaged',
            'items.*.QuantityDamaged' => 'nullable|integer|min:0',
            'items.*.ItemRemarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Get current user's employee ID
            $user = Auth::user();
            $employeeId = $user->getAttributeValue('EmployeeID');
            
            if (!$employeeId) {
                return back()->with('error', 'Employee record not found for current user.');
            }

            // Create receiving record
            $receivingRecord = ReceivingRecord::create([
                'POID' => $validated['POID'],
                'ReceivedDate' => $validated['ReceivedDate'],
                'ReceivedBy' => $employeeId,
            ]);

            // Create receiving record items
            foreach ($validated['items'] as $itemData) {
                // Validate quantity doesn't exceed remaining
                $poItem = \App\Models\PurchaseOrderItem::find($itemData['POItemID']);
                $remaining = $poItem->QuantityOrdered - $poItem->QuantityReceived;
                
                if ($itemData['QuantityReceived'] > $remaining) {
                    DB::rollBack();
                    return back()->withInput()->with('error', 
                        'Quantity received for item exceeds remaining quantity (' . $remaining . ')');
                }

                $quantityDamaged = $itemData['QuantityDamaged'] ?? 0;
                
                // Ensure damaged quantity doesn't exceed received quantity
                if ($quantityDamaged > $itemData['QuantityReceived']) {
                    DB::rollBack();
                    return back()->withInput()->with('error', 
                        'Quantity damaged cannot exceed quantity received');
                }

                ReceivingRecordItem::create([
                    'ReceivingID' => $receivingRecord->ReceivingID,
                    'POItemID' => $itemData['POItemID'],
                    'QuantityReceived' => $itemData['QuantityReceived'],
                    'Condition' => $itemData['Condition'],
                    'QuantityDamaged' => $quantityDamaged,
                    'ItemRemarks' => $itemData['ItemRemarks'] ?? null,
                ]);
            }

            // Update inventory and PO status
            $receivingRecord->updateInventory();

            DB::commit();

            return redirect()->route('receiving.show', $receivingRecord->ReceivingID)
                ->with('success', 'Items received successfully and inventory updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()->with('error', 'Error creating receiving record: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified receiving record
     */
    public function show($id)
    {
        $receivingRecord = ReceivingRecord::with([
            'purchaseOrder.supplier',
            'receiver',
            'items.purchaseOrderItem.inventoryItem'
        ])->findOrFail($id);

        return view('receiving.show', compact('receivingRecord'));
    }

    /**
     * Delete the receiving record (admin only)
     */
    public function destroy($id)
    {
        $receivingRecord = ReceivingRecord::findOrFail($id);

        DB::beginTransaction();
        try {
            // Reverse inventory updates
            foreach ($receivingRecord->items as $item) {
                $poItem = $item->purchaseOrderItem;
                
                if ($poItem && $poItem->inventoryItem) {
                    $inventoryItem = $poItem->inventoryItem;
                    
                    // Decrease total quantity
                    $goodQuantity = $item->QuantityReceived - $item->QuantityDamaged;
                    $inventoryItem->TotalQuantity -= $goodQuantity;
                    $inventoryItem->AvailableQuantity -= $goodQuantity;
                    $inventoryItem->save();
                    
                    // Update PO item
                    $poItem->QuantityReceived -= $item->QuantityReceived;
                    $poItem->save();
                }
            }

            // Delete attachment if exists
            if ($receivingRecord->AttachmentPath) {
                Storage::disk('public')->delete($receivingRecord->AttachmentPath);
            }

            // Update PO status
            $po = $receivingRecord->purchaseOrder;
            if ($po->isFullyReceived()) {
                $po->update(['Status' => 'Completed']);
            } else if ($po->items()->where('QuantityReceived', '>', 0)->exists()) {
                $po->update(['Status' => 'Partially Received']);
            } else {
                $po->update(['Status' => 'Sent']);
            }

            // Delete receiving record
            $receivingRecord->delete();

            DB::commit();

            return redirect()->route('receiving.index')
                ->with('success', 'Receiving record deleted and inventory updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting receiving record: ' . $e->getMessage());
        }
    }
}
