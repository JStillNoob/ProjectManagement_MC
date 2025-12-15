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
        'status',
        'qr_code'
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

    // Generate unique QR code for this project-employee combination
    public function generateQrCode()
    {
        // Create a unique identifier combining project and employee
        $uniqueId = 'PE_' . $this->ProjectID . '_' . $this->EmployeeID . '_' . uniqid();
        $this->qr_code = $uniqueId;
        $this->save();
        return $this->qr_code;
    }

    // Get QR code data (generate if not exists)
    public function getQrCodeDataAttribute()
    {
        if (!$this->qr_code) {
            return $this->generateQrCode();
        }
        return $this->qr_code;
    }

    // Boot method to auto-generate QR code when created
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($projectEmployee) {
            if (!$projectEmployee->qr_code) {
                $projectEmployee->generateQrCode();
            }
        });
    }
}
