<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MilestoneResourcePlan extends Model
{
    use HasFactory;

    protected $table = 'milestone_resource_plans';
    protected $primaryKey = 'PlanID';

    protected $fillable = [
        'milestone_id',
        'ItemID',
        'PlannedQuantity',
        'Unit',
        'NeededDate',
        'ResourceType',
        'WorkDescription',
        'Status',
        'Notes',
    ];

    protected $casts = [
        'NeededDate' => 'date',
        'PlannedQuantity' => 'integer',
    ];

    public function milestone()
    {
        return $this->belongsTo(ProjectMilestone::class, 'milestone_id', 'milestone_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'ItemID', 'ItemID');
    }

    /**
     * Check if planned quantity exceeds current stock
     */
    public function hasStockWarning()
    {
        $item = $this->inventoryItem;
        if (!$item) return false;
        
        return $this->PlannedQuantity > $item->AvailableQuantity;
    }

    /**
     * Get stock availability percentage
     */
    public function getStockAvailabilityPercentage()
    {
        $item = $this->inventoryItem;
        if (!$item || $this->PlannedQuantity == 0) return 0;
        
        return round(($item->AvailableQuantity / $this->PlannedQuantity) * 100, 2);
    }
}
