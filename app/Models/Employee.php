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
        'house_number',
        'street',
        'barangay',
        'city',
        'province',
        'postal_code',
        'PositionID',
        'employee_status_id',
        'base_salary',
        'start_date',
        'image_name',
        'flag_deleted',
        'contact_number'
    ];

    protected $casts = [
        'birthday' => 'date',
        'start_date' => 'date',
        'flag_deleted' => 'boolean',
        'base_salary' => 'decimal:2',
    ];

    // Scope to get only non-deleted employees (not archived)
    public function scopeActive($query)
    {
        return $query->where('flag_deleted', 0)
                     ->where(function($q) {
                         $q->whereNull('employee_status_id')
                           ->orWhere('employee_status_id', '!=', EmployeeStatus::ARCHIVED);
                     });
    }

    // Scope to get only non-archived employees
    public function scopeNotArchived($query)
    {
        return $query->where(function($q) {
            $q->whereNull('employee_status_id')
              ->orWhere('employee_status_id', '!=', EmployeeStatus::ARCHIVED);
        });
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
            $this->postal_code
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

    // Method to get project-specific QR code
    public function getProjectQrCode($projectId)
    {
        $projectEmployee = $this->projects()
            ->where('projects.ProjectID', $projectId)
            ->wherePivot('status', 'Active')
            ->first();
            
        if ($projectEmployee) {
            return $projectEmployee->pivot->qr_code;
        }
        
        return null;
    }

    // Relationship with position
    public function position()
    {
        return $this->belongsTo(Position::class, 'PositionID', 'PositionID');
    }

    // Relationship with employee status
    public function employeeStatus()
    {
        return $this->belongsTo(EmployeeStatus::class, 'employee_status_id', 'EmployeeStatusID');
    }

    // Accessor for status name
    public function getStatusNameAttribute()
    {
        if ($this->employeeStatus) {
            return $this->employeeStatus->StatusName;
        }
        return 'Inactive'; // Default if no status set
    }

    // Check if employee is active (assigned to project)
    public function isActive()
    {
        return $this->employee_status_id === EmployeeStatus::ACTIVE;
    }

    // Check if employee is inactive (not assigned to project)
    public function isInactive()
    {
        return $this->employee_status_id === EmployeeStatus::INACTIVE || $this->employee_status_id === null;
    }

    // Check if employee is archived
    public function isArchived()
    {
        return $this->employee_status_id === EmployeeStatus::ARCHIVED;
    }

    // Accessor for position (returns Position object, not string)
    public function getPositionAttribute()
    {
        return $this->position()->first();
    }

    // Accessor for age (calculated from birthday)
    public function getAgeAttribute()
    {
        if (!$this->birthday) {
            return null;
        }
        
        try {
            $today = now();
            $age = $today->year - $this->birthday->year;
            $monthDiff = $today->month - $this->birthday->month;
            
            if ($monthDiff < 0 || ($monthDiff === 0 && $today->day < $this->birthday->day)) {
                $age--;
            }
            
            return $age;
        } catch (\Exception $e) {
            return null;
        }
    }

    // Accessor for formatted birthday
    public function getFormattedBirthdayAttribute()
    {
        if (!$this->birthday) {
            return 'N/A';
        }
        
        try {
            return $this->birthday->format('F d, Y');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    // Accessor for formatted start date
    public function getFormattedStartDateAttribute()
    {
        if (!$this->start_date) {
            return 'N/A';
        }
        
        try {
            return $this->start_date->format('F d, Y');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    // Accessor for formatted created at
    public function getFormattedCreatedAtAttribute()
    {
        if (!$this->created_at) {
            return 'N/A';
        }
        
        try {
            return $this->created_at->format('F d, Y \a\t g:i A');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    // Accessor for formatted updated at
    public function getFormattedUpdatedAtAttribute()
    {
        if (!$this->updated_at) {
            return 'N/A';
        }
        
        try {
            return $this->updated_at->format('F d, Y \a\t g:i A');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    // Relationship with projects (many-to-many through project_employees)
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_employees', 'EmployeeID', 'ProjectID', 'id', 'ProjectID')
                    ->withPivot(['status', 'created_at', 'updated_at', 'qr_code']);
    }

    // Relationship with users
    public function users()
    {
        return $this->hasMany(User::class, 'EmployeeID', 'id');
    }

    // Generate QR code image URL for display (project-specific)
    public function generateQrCodeImageUrl($projectId, $size = 300)
    {
        $qrCode = $this->getProjectQrCode($projectId);
        if ($qrCode) {
            return 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . urlencode($qrCode);
        }
        return null;
    }
}
