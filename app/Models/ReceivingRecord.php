<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivingRecord extends Model
{
    use HasFactory;

    protected $table = 'receiving_records';
    protected $primaryKey = 'ReceivingID';

    protected $fillable = [
        'POID',
        'ReceivedDate',
        'ReceivedBy',
        'DeliveryReceiptNumber',
        'OverallCondition',
        'Remarks',
        'AttachmentPath',
    ];

    protected $casts = [
        'ReceivedDate' => 'date',
    ];

    /**
     * Get the purchase order
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'POID', 'POID');
    }

    /**
     * Get the employee who received the items
     */
    public function receiver()
    {
        return $this->belongsTo(Employee::class, 'ReceivedBy', 'id');
    }

    /**
     * Get all items in this receiving record
     */
    public function items()
    {
        return $this->hasMany(ReceivingRecordItem::class, 'ReceivingID', 'ReceivingID');
    }

    /**
     * Update inventory quantities after receiving
     */
    public function updateInventory()
    {
        foreach ($this->items as $item) {
            $poItem = $item->purchaseOrderItem;
            
            if ($poItem && $poItem->inventoryItem) {
                $inventoryItem = $poItem->inventoryItem;
                
                // Increase total quantity for good items
                if ($item->Condition == 'Good' || ($item->QuantityReceived - $item->QuantityDamaged) > 0) {
                    $goodQuantity = $item->QuantityReceived - $item->QuantityDamaged;
                    $inventoryItem->TotalQuantity += $goodQuantity;
                    $inventoryItem->AvailableQuantity += $goodQuantity;
                    $inventoryItem->save();
                }
                
                // Update PO item received quantity
                $poItem->QuantityReceived += $item->QuantityReceived;
                $poItem->save();
            }
        }
        
        // Update PO status if fully received
        $this->updatePOStatus();
    }

    /**
     * Update purchase order status based on received quantities
     */
    protected function updatePOStatus()
    {
        $po = $this->purchaseOrder;
        
        if ($po->isFullyReceived()) {
            $po->update(['Status' => 'Completed']);
        } else {
            $po->update(['Status' => 'Partially Received']);
        }
    }
}
