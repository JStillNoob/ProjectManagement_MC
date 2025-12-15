<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MilestoneRequiredItem extends Model
{
    protected $table = 'milestone_required_items';

    protected $fillable = [
        'milestone_id',
        'item_id',
        'estimated_quantity',
    ];

    protected $casts = [
        'estimated_quantity' => 'decimal:2',
    ];

    public function milestone()
    {
        return $this->belongsTo(ProjectMilestone::class, 'milestone_id', 'milestone_id');
    }

    public function resourceCatalog()
    {
        return $this->belongsTo(ResourceCatalog::class, 'item_id', 'ResourceCatalogID');
    }

    // Legacy relationship - deprecated, use resourceCatalog instead
    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id', 'ItemID');
    }
}
