<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeType extends Model
{
    use HasFactory;

    protected $table = 'employee_types';
    protected $primaryKey = 'EmployeeTypeID';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'EmployeeTypeName',
    ];

    // Relationship with employees
    public function employees()
    {
        return $this->hasMany(Employee::class, 'EmployeeTypeID', 'EmployeeTypeID');
    }
}
