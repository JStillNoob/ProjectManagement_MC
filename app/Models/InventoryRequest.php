<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryRequest extends Model
{
    protected $table = 'inventory_requests';
    protected $primaryKey = 'RequestID';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'ProjectID',
        'EmployeeID',
        'RequestType',
        'Reason',
        'Status',
        'ApprovedBy',
        'ApprovedAt',
        'RejectionReason',
        'MilestoneID',
    ];

    protected $casts = [
        'ApprovedAt' => 'datetime',
    ];

    // Relationship with project
    public function project()
    {
        return $this->belongsTo(Project::class, 'ProjectID', 'ProjectID');
    }

    // Relationship with employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'EmployeeID', 'id');
    }

    // Relationship with request items (cart lines)
    public function items()
    {
        return $this->hasMany(InventoryRequestItem::class, 'InventoryRequestID', 'RequestID');
    }

    // Alias for backward compatibility
    public function requestItems()
    {
        return $this->items();
    }

    // Relationship with approver
    public function approver()
    {
        return $this->belongsTo(User::class, 'ApprovedBy', 'id');
    }

    // Relationship with milestone
    public function milestone()
    {
        return $this->belongsTo(ProjectMilestone::class, 'MilestoneID', 'milestone_id');
    }

    // Relationship with purchase orders
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'RequestID', 'RequestID');
    }
}
