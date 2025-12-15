<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentIncident extends Model
{
    use HasFactory;

    protected $table = 'equipment_incidents';
    protected $primaryKey = 'IncidentID';

    protected $fillable = [
        'ItemID',
        'ProjectID',
        'EquipmentAssignmentID',
        'IncidentType',
        'IncidentDate',
        'ResponsibleEmployeeID',
        'Description',
        'EstimatedCost',
        'Status',
        'PhotoPath',
        'ActionTaken',
    ];

    protected $casts = [
        'IncidentDate' => 'date',
        'EstimatedCost' => 'decimal:2',
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'ItemID', 'ItemID');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'ProjectID', 'ProjectID');
    }

    public function equipmentAssignment()
    {
        return $this->belongsTo(ProjectMilestoneEquipment::class, 'EquipmentAssignmentID', 'EquipmentAssignmentID');
    }

    public function responsibleEmployee()
    {
        return $this->belongsTo(Employee::class, 'ResponsibleEmployeeID', 'EmployeeID');
    }
}
