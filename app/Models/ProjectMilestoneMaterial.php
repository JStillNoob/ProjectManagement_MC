<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMilestoneMaterial extends Model
{
    protected $table = 'project_milestone_materials';
    protected $primaryKey = 'MaterialUsageID';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'milestone_id',
        'ItemID',
        'QuantityUsed',
        'DateUsed',
        'Remarks',
    ];

    protected $casts = [
        'QuantityUsed' => 'decimal:2',
        'DateUsed' => 'date',
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
}
