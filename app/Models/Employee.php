<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'birthday',
        'age',
        'house_number',
        'street',
        'barangay',
        'city',
        'province',
        'postal_code',
        'country',
        'status',
        'PositionID',
        'base_salary',
        'start_date',
        'image_name',
        'qr_code',
        'flag_deleted',
        'EmployeeTypeID',
        'contact_number'
    ];

    protected $casts = [
        'birthday' => 'date',
        'start_date' => 'date',
        'flag_deleted' => 'boolean',
        'base_salary' => 'decimal:2',
    ];

    // Scope to get only non-deleted employees
    public function scopeActive($query)
    {
        return $query->where('flag_deleted', 0);
    }

    // Accessor for full name
    public function getFullNameAttribute()
    {
        $middleName = $this->middle_name ? ' ' . $this->middle_name . ' ' : ' ';
        return $this->first_name . $middleName . $this->last_name;
    }

    // Accessor for full address
    public function getFullAddressAttribute()
    {
        $addressParts = array_filter([
            $this->house_number,
            $this->street,
            $this->barangay,
            $this->city,
            $this->province,
            $this->postal_code,
            $this->country
        ]);
        
        return implode(', ', $addressParts);
    }

    // Accessor for formatted salary
    public function getFormattedSalaryAttribute()
    {
        if (!$this->base_salary) {
            return 'Not set';
        }
        
        return '₱' . number_format($this->base_salary, 2);
    }

    // Method to get monthly salary (same as base salary)
    public function getMonthlySalaryAttribute()
    {
        return $this->base_salary ?? 0;
    }

    // Accessor for image path
    public function getImagePathAttribute()
    {
        if ($this->image_name) {
            // Check if the file exists in storage
            $filePath = storage_path('app/public/' . $this->image_name);
            if (file_exists($filePath)) {
                return asset('storage/' . $this->image_name);
            }
        }
        // Use a placeholder service for default avatar with pastel green background
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&background=a8e6cf&color=2d5a3d&size=200&bold=true';
    }

    // Accessor for QR code path
    public function getQrCodePathAttribute()
    {
        if ($this->qr_code) {
            // If it's a URL (starts with http), return it directly
            if (str_starts_with($this->qr_code, 'http')) {
                return $this->qr_code;
            }
            // If it's a file path, check if it exists in storage
            $filePath = storage_path('app/public/' . $this->qr_code);
            if (file_exists($filePath)) {
                return asset('storage/' . $this->qr_code);
            }
        }
        return null;
    }

    // Relationship with employee type
    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class, 'EmployeeTypeID', 'EmployeeTypeID');
    }

    // Relationship with position
    public function position()
    {
        return $this->belongsTo(Position::class, 'PositionID', 'PositionID');
    }

    // Accessor for position name (for backward compatibility)
    public function getPositionAttribute()
    {
        return $this->position()->first()?->PositionName;
    }

    // Relationship with projects (many-to-many through project_employees)
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_employees', 'EmployeeID', 'ProjectID', 'id', 'ProjectID');
    }

    // Relationship with users
    public function users()
    {
        return $this->hasMany(User::class, 'EmployeeID', 'id');
    }

    // Relationship with benefits through employee_benefits
    public function benefits()
    {
        return $this->belongsToMany(Benefit::class, 'employee_benefits', 'EmployeeID', 'BenefitID')
                    ->withPivot(['EffectiveDate', 'ExpiryDate', 'Amount', 'Percentage', 'IsActive'])
                    ->withTimestamps();
    }

    // Relationship with employee_benefits
    public function employeeBenefits()
    {
        return $this->hasMany(EmployeeBenefit::class, 'EmployeeID', 'id');
    }

    // Method to automatically assign benefits based on employee type
    public function assignBenefitsBasedOnType()
    {
        if ($this->employeeType && $this->employeeType->hasBenefits) {
            // Get all active benefits
            $benefits = Benefit::active()->get();
            
            foreach ($benefits as $benefit) {
                // Check if employee already has this benefit
                $existingBenefit = $this->employeeBenefits()
                    ->where('BenefitID', $benefit->BenefitID)
                    ->where('IsActive', true)
                    ->first();

                if (!$existingBenefit) {
                    // Assign the benefit
                    $this->employeeBenefits()->create([
                        'BenefitID' => $benefit->BenefitID,
                        'EffectiveDate' => now(),
                        'Amount' => $benefit->Amount,
                        'Percentage' => $benefit->Percentage,
                        'IsActive' => true,
                    ]);
                }
            }
        }
    }

    // Method to get current active benefits
    public function getCurrentBenefits()
    {
        return $this->employeeBenefits()
            ->with('benefit')
            ->active()
            ->current()
            ->get();
    }

    // Method to check if employee is eligible for benefits
    public function isEligibleForBenefits()
    {
        return $this->employeeType && $this->employeeType->hasBenefits;
    }
}