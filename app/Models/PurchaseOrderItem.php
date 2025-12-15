<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_items';
    protected $primaryKey = 'POItemID';

    protected $fillable = [
        'POID',
        'ItemID',
        'SupplierID',
        'QuantityOrdered',
        'QuantityReceived',
        'Unit',
        'Specifications',
        'Remarks',
    ];

    protected $casts = [
        'QuantityOrdered' => 'integer',
        'QuantityReceived' => 'integer',
    ];

    /**
     * Get the purchase order this item belongs to
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'POID', 'POID');
    }

    /**
     * Get the inventory item
     */
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'ItemID', 'ItemID');
    }

    /**
     * Get the inventory item (alias)
     */
    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'ItemID', 'ItemID');
    }

    /**
     * Get the supplier for this item
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'SupplierID', 'SupplierID');
    }

    /**
     * Get remaining quantity to be received
     */
    public function getRemainingQuantityAttribute()
    {
        return $this->QuantityOrdered - $this->QuantityReceived;
    }

    /**
     * Check if item is fully received
     */
    public function isFullyReceived()
    {
        return $this->QuantityReceived >= $this->QuantityOrdered;
    }

    /**
     * Get percentage received
     */
    public function getPercentageReceivedAttribute()
    {
        if ($this->QuantityOrdered == 0) {
            return 0;
        }
        return round(($this->QuantityReceived / $this->QuantityOrdered) * 100, 2);
    }
}
