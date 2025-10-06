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
        'address',
        'status',
        'position',
        'start_date',
        'image_name',
        'qr_code',
        'flag_deleted',
        'EmployeeTypeID',
        'daily_salary',
        'hourly_rate',
        'monthly_salary',
        'availability',
        'contact_number',
        'emergency_contact',
        'emergency_phone',
        'benefits',
        'health_insurance',
        'retirement_plan',
        'vacation_days',
        'sick_days'
    ];

    protected $casts = [
        'birthday' => 'date',
        'start_date' => 'date',
        'flag_deleted' => 'boolean',
        'daily_salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'monthly_salary' => 'decimal:2',
        'vacation_days' => 'integer',
        'sick_days' => 'integer',
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
}