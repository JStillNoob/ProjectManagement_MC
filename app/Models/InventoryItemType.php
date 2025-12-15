<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItemType extends Model
{
    protected $table = 'inventory_item_types';
    protected $primaryKey = 'ItemTypeID';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'TypeName',
        'Description',
    ];

    // Relationship with inventory items
    public function items()
    {
        return $this->hasMany(InventoryItem::class, 'ItemTypeID', 'ItemTypeID');
    }

    // Scope for Materials
    public function scopeMaterials($query)
    {
        return $query->where('TypeName', 'Materials');
    }

    // Scope for Equipment
    public function scopeEquipment($query)
    {
        return $query->where('TypeName', 'Equipment');
    }
}
