<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';
    protected $primaryKey = 'ProjectID';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'ProjectName',
        'ProjectDescription',
        'Client',
        'StartDate',
        'EndDate',
        'ClientID',
        'WarrantyEndDate',
        'StreetAddress',
        'Barangay',
        'City',
        'StateProvince',
        'ZipCode',
        'StatusID', // add this so we can update it directly
    ];

    protected $casts = [
        'StartDate'      => 'date',
        'EndDate'        => 'date',
        'WarrantyEndDate'=> 'date',
    ];

    // Relationship with project status
    public function status()
    {
        return $this->belongsTo(ProjectStatus::class, 'StatusID', 'StatusID');
    }

    // Relationship with client
    public function client()
    {
        return $this->belongsTo(Client::class, 'ClientID', 'ClientID');
    }

    // Relationship with employees (many-to-many through project_employees)
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'project_employees', 'ProjectID', 'EmployeeID', 'ProjectID', 'id');
    }

    // Relationship with project employees (direct relationship)
    public function projectEmployees()
    {
        return $this->hasMany(ProjectEmployee::class, 'ProjectID', 'ProjectID');
    }

    // Accessor for full address
    public function getFullAddressAttribute()
    {
        $addressParts = array_filter([
            $this->StreetAddress,
            $this->Barangay,
            $this->City,
            $this->StateProvince,
            $this->ZipCode,
        ]);

        return implode(', ', $addressParts);
    }

    // Scope for active projects
    public function scopeActive($query)
    {
        return $query->whereHas('status', function($q) {
            $q->whereIn('StatusName', ['Active', 'In Progress', 'Planning']);
        });
    }

    // Scope for completed projects
    public function scopeCompleted($query)
    {
        return $query->whereHas('status', function($q) {
            $q->whereIn('StatusName', ['Completed', 'Under Warranty']);
        });
    }

    /**
     * Automatic status management:
     * - Only auto-calculate if we are NOT manually setting status
     *   to "On Hold" or "Cancelled".
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            $project->StatusID = self::calculateStatus(
                $project->StartDate,
                $project->EndDate,
                $project->WarrantyEndDate
            );
        });

        static::updating(function ($project) {
            // If the *new* value is On Hold or Cancelled, do nothing
            $newStatusName = ProjectStatus::find($project->StatusID)?->StatusName;
            if (in_array($newStatusName, ['On Hold', 'Cancelled'])) {
                return;
            }

            // If the *current* value is On Hold or Cancelled, leave it alone
            $currentStatus = $project->getOriginal('StatusID');
            $currentStatusName = ProjectStatus::find($currentStatus)?->StatusName;
            if (in_array($currentStatusName, ['On Hold', 'Cancelled'])) {
                return;
            }

            // Otherwise, auto-calculate based on dates
            $project->StatusID = self::calculateStatus(
                $project->StartDate,
                $project->EndDate,
                $project->WarrantyEndDate
            );
        });
    }

    // Calculate status based on dates
    public static function calculateStatus($startDate, $endDate, $warrantyEndDate = null)
    {
        $today = now()->toDateString();

        $startDateStr     = is_string($startDate) ? $startDate : $startDate->toDateString();
        $endDateStr       = is_string($endDate) ? $endDate : $endDate->toDateString();
        $warrantyEndDateStr = $warrantyEndDate
            ? (is_string($warrantyEndDate) ? $warrantyEndDate : $warrantyEndDate->toDateString())
            : null;

        if ($startDateStr > $today) {
            return ProjectStatus::where('StatusName', 'Upcoming')->first()->StatusID;
        }

        if ($startDateStr <= $today && $endDateStr >= $today) {
            return ProjectStatus::where('StatusName', 'On Going')->first()->StatusID;
        }

        if ($endDateStr < $today) {
            if ($warrantyEndDateStr && $warrantyEndDateStr >= $today) {
                return ProjectStatus::where('StatusName', 'Under Warranty')->first()->StatusID;
            } else {
                return ProjectStatus::where('StatusName', 'Completed')->first()->StatusID;
            }
        }

        return ProjectStatus::where('StatusName', 'Upcoming')->first()->StatusID;
    }

    // Optional: command/cron helper
    public static function updateAllStatuses()
    {
        foreach (self::all() as $project) {
            $newStatusId = self::calculateStatus(
                $project->StartDate,
                $project->EndDate,
                $project->WarrantyEndDate
            );
            if ($project->StatusID != $newStatusId) {
                $project->update(['StatusID' => $newStatusId]);
            }
        }
    }
}
