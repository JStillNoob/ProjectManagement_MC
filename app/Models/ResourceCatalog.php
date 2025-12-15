<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceCatalog extends Model
{
    protected $table = 'resource_catalog';
    protected $primaryKey = 'ResourceCatalogID';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'ItemName',
        'Unit',
        'Type',
    ];

    protected $casts = [
        //
    ];

    // Relationship with inventory items
    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class, 'ResourceCatalogID', 'ResourceCatalogID');
    }

    // Scope for active resources
    public function scopeActive($query)
    {
        return $query;
    }

    // Scope for equipment
    public function scopeEquipment($query)
    {
        return $query->where('Type', 'Equipment');
    }

    // Scope for materials
    public function scopeMaterials($query)
    {
        return $query->where('Type', 'Materials');
    }

    // Get total stock across all inventory items
    public function getTotalStockAttribute()
    {
        return $this->inventoryItems->sum('TotalQuantity');
    }

    // Get available stock across all inventory items
    public function getAvailableStockAttribute()
    {
        return $this->inventoryItems->sum('AvailableQuantity');
    }
}
