<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeStatus extends Model
{
    protected $table = 'employee_status';
    protected $primaryKey = 'EmployeeStatusID';

    protected $fillable = [
        'StatusName',
        'Description',
    ];

    // Status constants
    const ACTIVE = 1;
    const INACTIVE = 2;
    const ARCHIVED = 3;

    /**
     * Get all employees with this status
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'employee_status_id', 'EmployeeStatusID');
    }

    /**
     * Check if status is Active
     */
    public function isActive()
    {
        return $this->EmployeeStatusID === self::ACTIVE;
    }

    /**
     * Check if status is Inactive
     */
    public function isInactive()
    {
        return $this->EmployeeStatusID === self::INACTIVE;
    }

    /**
     * Check if status is Archived
     */
    public function isArchived()
    {
        return $this->EmployeeStatusID === self::ARCHIVED;
    }
}
