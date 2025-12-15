<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMilestoneEquipment extends Model
{
    protected $table = 'project_milestone_equipment';
    protected $primaryKey = 'EquipmentAssignmentID';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'milestone_id',
        'ItemID',
        'QuantityAssigned',
        'DateAssigned',
        'DateReturned',
        'Status',
        'Remarks',
        'ReturnRemarks',
    ];

    protected $casts = [
        'QuantityAssigned' => 'decimal:2',
        'DateAssigned' => 'date',
        'DateReturned' => 'date',
    ];

    // Relationship with milestone
    public function milestone()
    {
        return $this->belongsTo(ProjectMilestone::class, 'milestone_id', 'milestone_id');
    }

    // Relationship with inventory item
    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'ItemID', 'ItemID');
    }

    // Alias for inventoryItem relationship (used by EquipmentReturnController)
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'ItemID', 'ItemID');
    }

    // Method to return equipment
    public function returnEquipment($status, $remarks = null)
    {
        $this->DateReturned = now();
        $this->Status = $status;
        $this->ReturnRemarks = $remarks;
        $this->save();

        // Update item's available quantity if returned
        if ($status === 'Returned' && $this->item) {
            $this->item->AvailableQuantity += $this->QuantityAssigned;
            $this->item->save();
        }

        return $this;
    }
}
