<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $table = 'inventory_items';
    protected $primaryKey = 'ItemID';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'ResourceCatalogID',
        'TotalQuantity',
        'AvailableQuantity',
        'MinimumStockLevel',
        'UnitPrice',
        'Status',
        'CommittedQuantity',
    ];

    protected $casts = [
        'TotalQuantity' => 'decimal:2',
        'AvailableQuantity' => 'decimal:2',
        'MinimumStockLevel' => 'decimal:2',
        'UnitPrice' => 'decimal:2',
        'CommittedQuantity' => 'decimal:2',
    ];

    // Relationship with resource catalog
    public function resourceCatalog()
    {
        return $this->belongsTo(ResourceCatalog::class, 'ResourceCatalogID', 'ResourceCatalogID');
    }

    // Relationship with milestone materials
    public function milestoneMaterials()
    {
        return $this->hasMany(ProjectMilestoneMaterial::class, 'ItemID', 'ItemID');
    }

    // Relationship with milestone equipment
    public function milestoneEquipment()
    {
        return $this->hasMany(ProjectMilestoneEquipment::class, 'ItemID', 'ItemID');
    }

    // Accessor to check if item is material
    public function getIsMaterialAttribute()
    {
        return $this->resourceCatalog && $this->resourceCatalog->Type === 'Materials';
    }

    // Accessor to check if item is equipment
    public function getIsEquipmentAttribute()
    {
        return $this->resourceCatalog && $this->resourceCatalog->Type === 'Equipment';
    }

    // Accessor to check if material is low stock
    public function getIsLowStockAttribute()
    {
        if (!$this->is_material || !$this->MinimumStockLevel) {
            return false;
        }
        return $this->AvailableQuantity < $this->MinimumStockLevel;
    }

    // Method to consume material (decrease stock)
    public function consumeMaterial($quantity, $milestoneId)
    {
        if (!$this->is_material) {
            throw new \Exception('Item is not a material.');
        }

        if ($this->AvailableQuantity < $quantity) {
            throw new \Exception('Insufficient stock available.');
        }

        $this->TotalQuantity -= $quantity;
        $this->AvailableQuantity -= $quantity;
        $this->save();

        return ProjectMilestoneMaterial::create([
            'milestone_id' => $milestoneId,
            'ItemID' => $this->ItemID,
            'QuantityUsed' => $quantity,
            'DateUsed' => now(),
        ]);
    }

    // Method to assign equipment (decrease available)
    public function assignEquipment($quantity, $milestoneId)
    {
        if (!$this->is_equipment) {
            throw new \Exception('Item is not equipment.');
        }

        if ($this->AvailableQuantity < $quantity) {
            throw new \Exception('Insufficient equipment available.');
        }

        $this->AvailableQuantity -= $quantity;
        $this->save();

        return ProjectMilestoneEquipment::create([
            'milestone_id' => $milestoneId,
            'ItemID' => $this->ItemID,
            'QuantityAssigned' => $quantity,
            'DateAssigned' => now(),
            'Status' => 'Assigned',
        ]);
    }

    // Method to return equipment (increase available)
    public function returnEquipment($assignmentId, $status, $remarks = null)
    {
        if (!$this->is_equipment) {
            throw new \Exception('Item is not equipment.');
        }

        $assignment = ProjectMilestoneEquipment::find($assignmentId);
        if (!$assignment || $assignment->ItemID != $this->ItemID) {
            throw new \Exception('Invalid equipment assignment.');
        }

        $assignment->DateReturned = now();
        $assignment->Status = $status;
        $assignment->ReturnRemarks = $remarks;
        $assignment->save();

        // Only increase available quantity if returned (not damaged/missing)
        if ($status === 'Returned') {
            $this->AvailableQuantity += $assignment->QuantityAssigned;
            $this->save();
        }

        return $assignment;
    }

    public function getAvailableStockAttribute()
    {
        $physical = $this->AvailableQuantity ?? 0;
        $committed = $this->CommittedQuantity ?? 0;

        $available = $physical - $committed;

        return $available > 0 ? $available : 0;
    }
}
