<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectEmployee extends Model
{
    use HasFactory;

    protected $table = 'project_employees';
    protected $primaryKey = 'ProjectEmployeeID';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'ProjectID',
        'EmployeeID',
        'role_in_project',
        'assigned_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'end_date' => 'date',
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
}
