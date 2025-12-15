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

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'ProjectID';
    }

    protected $fillable = [
        'ProjectName',
        'ProjectDescription',
        'Client',
        'StartDate',
        'EndDate',
        'ClientID',
        'WarrantyDays',
        'StreetAddress',
        'Barangay',
        'City',
        'StateProvince',
        'ZipCode',
        'StatusID', // add this so we can update it directly
        'EstimatedAccomplishDays',
        'NTPStartDate',
        'NTPAttachment',
        'BlueprintPath',
        'FloorPlanPath',
    ];

    protected $casts = [
        'StartDate'      => 'date',
        'EndDate'        => 'date',
        'WarrantyDays'   => 'integer',
        'EstimatedAccomplishDays' => 'integer',
        'NTPStartDate'   => 'date',
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

    // Accessor for formatted start date
    public function getFormattedStartDateAttribute()
    {
        if (!$this->StartDate) {
            return 'N/A';
        }
        
        try {
            return $this->StartDate->format('M d, Y');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    // Accessor for formatted end date
    public function getFormattedEndDateAttribute()
    {
        if (!$this->EndDate) {
            return 'N/A';
        }
        
        try {
            return $this->EndDate->format('M d, Y');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    // Accessor for warranty end date (calculated from EndDate + WarrantyDays)
    public function getWarrantyEndDateAttribute()
    {
        if (!$this->EndDate || !$this->WarrantyDays || (int)$this->WarrantyDays <= 0) {
            return null;
        }
        
        try {
            return $this->EndDate->copy()->addDays((int)$this->WarrantyDays);
        } catch (\Exception $e) {
            return null;
        }
    }

    // Accessor for formatted warranty end date
    public function getFormattedWarrantyEndDateAttribute()
    {
        $warrantyEndDate = $this->warranty_end_date;
        
        if (!$warrantyEndDate) {
            return 'N/A';
        }
        
        try {
            return $warrantyEndDate->format('M d, Y');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    // Accessor for formatted NTP start date
    public function getFormattedNTPStartDateAttribute()
    {
        if (!$this->NTPStartDate) {
            return 'N/A';
        }
        
        try {
            return $this->NTPStartDate->format('M d, Y');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    // Relationship with employees (many-to-many through project_employees)
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'project_employees', 'ProjectID', 'EmployeeID', 'ProjectID', 'id')
                    ->withPivot(['status', 'created_at', 'updated_at', 'qr_code']);
    }

    // Relationship with project employees (direct relationship)
    public function projectEmployees()
    {
        return $this->hasMany(ProjectEmployee::class, 'ProjectID', 'ProjectID');
    }

    // Relationship with milestones
    public function milestones()
    {
        return $this->hasMany(ProjectMilestone::class, 'project_id', 'ProjectID')->orderBy('order')->orderBy('target_date');
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
            $q->whereIn('StatusName', ['On Going', 'Pending', 'Pre-Construction', 'Under Warranty']);
        });
    }

    // Scope for completed projects
    public function scopeCompleted($query)
    {
        return $query->whereHas('status', function($q) {
            $q->whereIn('StatusName', ['Completed', 'Under Warranty']);
        });
    }

    // Accessor for milestone progress percentage
    public function getProgressPercentageAttribute()
    {
        $totalMilestones = $this->milestones()->count();
        if ($totalMilestones === 0) {
            return 0;
        }
        
        $completedMilestones = $this->milestones()->where('status', 'Completed')->count();
        return round(($completedMilestones / $totalMilestones) * 100, 2);
    }

    // Get milestone counts
    public function getMilestoneCountsAttribute()
    {
        return [
            'total' => $this->milestones()->count(),
            'completed' => $this->milestones()->where('status', 'Completed')->count(),
            'in_progress' => $this->milestones()->where('status', 'In Progress')->count(),
            'pending' => $this->milestones()->where('status', 'Pending')->count(),
            'backlog' => $this->milestones()->where('status', 'Backlog')->count(),
        ];
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
            // If StartDate is null, set status to "Pending"
            if (!$project->StartDate) {
                $status = ProjectStatus::where('StatusName', 'Pending')->first();
                if ($status) {
                    $project->StatusID = $status->StatusID;
                }
            } else {
                $project->StatusID = self::calculateStatus(
                    $project->StartDate,
                    $project->EndDate,
                    $project->WarrantyDays,
                    $project->NTPStartDate,
                    $project->ProjectID ?? null
                );
            }
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

            // Otherwise, auto-calculate based on dates and milestone progress
            $project->StatusID = self::calculateStatus(
                $project->StartDate,
                $project->EndDate,
                $project->WarrantyDays,
                $project->NTPStartDate,
                $project->ProjectID
            );
        });
    }

    // Calculate status based on dates and milestone progress
    public static function calculateStatus($startDate, $endDate, $warrantyDays = 0, $ntpStartDate = null, $projectId = null)
    {
        $today = now()->toDateString();

        // Helper function to safely get status ID
        $getStatusId = function($statusName) {
            $status = ProjectStatus::where('StatusName', $statusName)->first();
            if (!$status) {
                throw new \Exception("ProjectStatus '{$statusName}' not found in database. Please run database seeders.");
            }
            return $status->StatusID;
        };

        // If start date is null, project is pending
        if (!$startDate) {
            return $getStatusId('Pending');
        }

        $startDateStr = is_string($startDate) ? $startDate : $startDate->toDateString();
        
        // Check if NTP is approved but start date is in the future - Pre-Construction status
        if ($ntpStartDate) {
            $ntpStartDateStr = is_string($ntpStartDate) ? $ntpStartDate : $ntpStartDate->toDateString();
            // If NTP is approved and start date is in the future, it's Pre-Construction
            if ($startDateStr > $today) {
                return $getStatusId('Pre-Construction');
            }
        }

        // Check if any milestone is in progress - if so, project should be On Going
        // This only applies if start date has arrived (checked above)
        if ($projectId) {
            $hasInProgressMilestone = \DB::table('project_milestones')
                ->where('project_id', $projectId)
                ->where('status', 'In Progress')
                ->exists();
            
            if ($hasInProgressMilestone) {
                return $getStatusId('On Going');
            }
        }
        
        // If end date is null, calculate it or return pending
        if (!$endDate) {
            // If we have start date but no end date, check if it's in the future
            if ($startDateStr > $today) {
                // If NTP is set, it's Pre-Construction, otherwise Pending
                if ($ntpStartDate) {
                    return $getStatusId('Pre-Construction');
                }
                return $getStatusId('Pending');
            }
            // If start date is today or past, it should be "On Going" but we need end date
            // For now, return "On Going" if start date has passed
            if ($startDateStr <= $today) {
                return $getStatusId('On Going');
            }
            return $getStatusId('Pending');
        }

        $endDateStr = is_string($endDate) ? $endDate : $endDate->toDateString();
        
        // Calculate warranty end date from end date + warranty days
        $warrantyEndDateStr = null;
        if ($warrantyDays && $warrantyDays > 0) {
            try {
                $endDateObj = is_string($endDate) ? \Carbon\Carbon::parse($endDate) : $endDate;
                $warrantyEndDate = $endDateObj->copy()->addDays($warrantyDays);
                $warrantyEndDateStr = $warrantyEndDate->toDateString();
            } catch (\Exception $e) {
                // If calculation fails, set to null
                $warrantyEndDateStr = null;
            }
        }

        // If start date is in the future and NTP is approved, it's Pre-Construction
        if ($startDateStr > $today) {
            if ($ntpStartDate) {
                return $getStatusId('Pre-Construction');
            }
            return $getStatusId('Pending');
        }

        if ($startDateStr <= $today && $endDateStr >= $today) {
            return $getStatusId('On Going');
        }

        if ($endDateStr < $today) {
            if ($warrantyEndDateStr && $warrantyEndDateStr >= $today) {
                return $getStatusId('Under Warranty');
            } else {
                return $getStatusId('Completed');
            }
        }

        return $getStatusId('Pending');
    }

    // Optional: command/cron helper
    public static function updateAllStatuses()
    {
        foreach (self::all() as $project) {
            $newStatusId = self::calculateStatus(
                $project->StartDate,
                $project->EndDate,
                $project->WarrantyDays,
                $project->NTPStartDate,
                $project->ProjectID
            );
            if ($project->StatusID != $newStatusId) {
                $project->update(['StatusID' => $newStatusId]);
            }
        }
    }
}
