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

    /**
     * Units that should only accept whole numbers (no decimals)
     */
    protected static $integerUnits = [
        'pcs', 'pc', 'piece', 'pieces',
        'unit', 'units',
        'set', 'sets',
        'sheet', 'sheets',
        'bag', 'bags',
        'roll', 'rolls',
        'box', 'boxes',
        'can', 'cans',
        'bottle', 'bottles',
        'pair', 'pairs',
        'bundle', 'bundles',
        'length', 'lengths',
    ];

    /**
     * Check if this item's unit requires integer quantities (no decimals)
     */
    public function requiresIntegerQuantity(): bool
    {
        // Equipment always requires integer quantities
        if ($this->Type === 'Equipment') {
            return true;
        }

        // Check if unit is in the integer units list
        $unit = strtolower(trim($this->Unit ?? ''));
        return in_array($unit, self::$integerUnits);
    }

    /**
     * Get the step value for quantity inputs (1 for integers, 0.01 for decimals)
     */
    public function getQuantityStepAttribute(): string
    {
        return $this->requiresIntegerQuantity() ? '1' : '0.01';
    }

    /**
     * Format quantity based on unit type
     */
    public function formatQuantity($quantity): string
    {
        if ($this->requiresIntegerQuantity()) {
            return number_format((int) $quantity, 0);
        }
        return number_format($quantity, 2);
    }

    /**
     * Static method to check if a unit requires integer quantities
     */
    public static function unitRequiresInteger(?string $unit): bool
    {
        if (empty($unit)) {
            return false;
        }
        return in_array(strtolower(trim($unit)), self::$integerUnits);
    }

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
