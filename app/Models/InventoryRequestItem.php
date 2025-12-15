<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryRequestItem extends Model
{
    protected $table = 'inventory_request_items';
    protected $primaryKey = 'RequestItemID';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'InventoryRequestID',
        'InventoryItemID',
        'QuantityRequested',
        'UnitOfMeasure',
        'CommittedQuantity',
        'NeedsPurchase',
    ];

    protected $casts = [
        'QuantityRequested' => 'decimal:2',
        'CommittedQuantity' => 'decimal:2',
        'NeedsPurchase' => 'boolean',
    ];

    public function request()
    {
        return $this->belongsTo(InventoryRequest::class, 'InventoryRequestID', 'RequestID');
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'InventoryItemID', 'ItemID');
    }

    // Alias for backward compatibility
    public function inventoryItem()
    {
        return $this->item();
    }
}

