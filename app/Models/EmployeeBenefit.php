<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeBenefit extends Model
{
    use HasFactory;

    protected $table = 'employee_benefits';
    protected $primaryKey = 'EmployeeBenefitID';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'EmployeeID',
        'BenefitID',
        'EffectiveDate',
        'ExpiryDate',
        'Amount',
        'Percentage',
        'IsActive',
    ];

    protected $casts = [
        'EffectiveDate' => 'date',
        'ExpiryDate' => 'date',
        'Amount' => 'decimal:2',
        'Percentage' => 'decimal:2',
        'IsActive' => 'boolean',
    ];

    // Relationship with Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'EmployeeID', 'id');
    }

    // Relationship with Benefit
    public function benefit()
    {
        return $this->belongsTo(Benefit::class, 'BenefitID', 'BenefitID');
    }

    // Scope for active employee benefits
    public function scopeActive($query)
    {
        return $query->where('IsActive', true);
    }

    // Scope for current benefits (not expired)
    public function scopeCurrent($query)
    {
        return $query->where(function($q) {
            $q->whereNull('ExpiryDate')
              ->orWhere('ExpiryDate', '>=', now());
        });
    }
}
