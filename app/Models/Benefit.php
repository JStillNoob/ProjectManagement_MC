<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Benefit extends Model
{
    use HasFactory;

    protected $table = 'benefits';
    protected $primaryKey = 'BenefitID';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'BenefitName',
        'Description',
        'Amount',
        'Percentage',
        'BenefitType',
        'IsActive',
    ];

    protected $casts = [
        'Amount' => 'decimal:2',
        'Percentage' => 'decimal:2',
        'IsActive' => 'boolean',
    ];

    // Relationship with employees through employee_benefits
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_benefits', 'BenefitID', 'EmployeeID')
                    ->withPivot(['EffectiveDate', 'ExpiryDate', 'Amount', 'Percentage', 'IsActive'])
                    ->withTimestamps();
    }

    // Relationship with employee_benefits
    public function employeeBenefits()
    {
        return $this->hasMany(EmployeeBenefit::class, 'BenefitID', 'BenefitID');
    }

    // Scope for active benefits
    public function scopeActive($query)
    {
        return $query->where('IsActive', true);
    }
}
