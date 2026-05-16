<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProjectMilestone extends Model
{
    protected $table = 'project_milestones';
    protected $primaryKey = 'milestone_id';
    public $incrementing = true;
    public $timestamps = true;

    // Route key name for model binding
    public function getRouteKeyName()
    {
        return 'milestone_id';
    }

    protected $fillable = [
        'project_id',
        'milestone_name',
        'description',
        'WeightedPercentage',
        'target_date',
        'actual_date',
        'status',
        'order',
        'EstimatedDays',
        'SubmittedBy',
        'SubmittedAt',
        'ApprovedBy',
        'ApprovedAt',
        'SubmissionStatus',
    ];

    protected $casts = [
        'target_date' => 'date',
        'actual_date' => 'date',
        'EstimatedDays' => 'integer',
        'WeightedPercentage' => 'decimal:2',
        'SubmittedAt' => 'datetime',
        'ApprovedAt' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // Update project status when milestone status changes
        static::saved(function ($milestone) {
            if ($milestone->isDirty('status') || $milestone->wasRecentlyCreated) {
                $project = $milestone->project;
                if ($project) {
                    // If milestone just completed, activate next milestone in sequence
                    if ($milestone->status === 'Completed' && $milestone->isDirty('status')) {
                        $projectStatus = $project->status->StatusName ?? '';
                        
                        // Only auto-progress if project is On Going
                        if ($projectStatus === 'On Going') {
                            $currentOrder = $milestone->order ?? $milestone->milestone_id;
                            
                            // Find next pending milestone
                            $nextMilestone = $project->milestones()
                                ->where('status', 'Pending')
                                ->where(function($q) use ($currentOrder, $milestone) {
                                    $q->where('order', '>', $currentOrder)
                                      ->orWhere(function($q2) use ($currentOrder, $milestone) {
                                          $q2->where('order', '=', $currentOrder)
                                             ->where('milestone_id', '>', $milestone->milestone_id);
                                      });
                                })
                                ->orderBy('order')
                                ->orderBy('milestone_id')
                                ->first();
                            
                            if ($nextMilestone) {
                                $nextMilestone->status = 'In Progress';
                                $nextMilestone->saveQuietly();
                            }
                        }
                    }
                    
                    $newStatusId = Project::calculateStatus(
                        $project->StartDate,
                        $project->EndDate,
                        $project->WarrantyDays,
                        $project->NTPStartDate,
                        $project->ProjectID
                    );
                    
                    // Only update if status actually changed
                    if ($project->StatusID != $newStatusId) {
                        $project->update(['StatusID' => $newStatusId]);
                    }
                }
            }
        });

        // Also update when milestone is deleted
        static::deleted(function ($milestone) {
            $project = $milestone->project;
            if ($project) {
                $newStatusId = Project::calculateStatus(
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
        });
    }

    // Relationship with project
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'ProjectID');
    }

    // Accessor for formatted target date
    public function getFormattedTargetDateAttribute()
    {
        if (!$this->target_date) {
            return 'N/A';
        }
        
        try {
            return $this->target_date->format('M d, Y');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    // Accessor for formatted actual date
    public function getFormattedActualDateAttribute()
    {
        if (!$this->actual_date) {
            return 'N/A';
        }
        
        try {
            return $this->actual_date->format('M d, Y');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    // Scope for pending milestones
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Update milestone status based on target date and project status
     */
    public function updateStatusBasedOnDate()
    {
        // Don't update if already completed
        if ($this->status === 'Completed') {
            return;
        }

        $today = now()->toDateString();
        
        // If target date exists
        if ($this->target_date) {
            $targetDateStr = $this->target_date->toDateString();
            
            // If target date has passed and not completed, set to In Progress
            if ($targetDateStr <= $today && $this->status === 'Pending') {
                $this->status = 'In Progress';
                $this->saveQuietly();
            }
        } else {
            // If no target date but project has started, check project status
            if ($this->project && $this->project->StartDate) {
                $projectStatus = $this->project->status->StatusName ?? '';
                if (in_array($projectStatus, ['On Going', 'Pre-Construction']) && $this->status === 'Pending') {
                    $this->status = 'In Progress';
                    $this->saveQuietly();
                }
            }
        }
    }

    // Scope for in progress milestones
    public function scopeInProgress($query)
    {
        return $query->where('status', 'In Progress');
    }

    // Scope for completed milestones
    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    // Relationship with milestone materials
    public function materials()
    {
        return $this->hasMany(ProjectMilestoneMaterial::class, 'milestone_id', 'milestone_id');
    }

    // Relationship with milestone equipment
    public function equipment()
    {
        return $this->hasMany(ProjectMilestoneEquipment::class, 'milestone_id', 'milestone_id');
    }

    // Relationship with proof images
    public function proofImages()
    {
        return $this->hasMany(MilestoneProofImage::class, 'milestone_id', 'milestone_id');
    }

    // Relationship with required items
    public function requiredItems()
    {
        return $this->hasMany(MilestoneRequiredItem::class, 'milestone_id', 'milestone_id');
    }

    // Relationship with employee who submitted
    public function submittedBy()
    {
        return $this->belongsTo(Employee::class, 'SubmittedBy', 'id');
    }

    // Relationship with employee who approved
    public function approvedBy()
    {
        return $this->belongsTo(Employee::class, 'ApprovedBy', 'id');
    }

    // Accessor for calculated target date (based on cumulative EstimatedDays)
    public function getCalculatedTargetDateAttribute()
    {
        if (!$this->project || !$this->project->StartDate || !$this->EstimatedDays) {
            return null;
        }

        // Get all milestones before this one (by order, then milestone_id)
        $currentOrder = $this->order ?? $this->milestone_id;
        $previousMilestones = $this->project->milestones()
            ->where(function($q) use ($currentOrder) {
                $q->where('order', '<', $currentOrder)
                  ->orWhere(function($q2) use ($currentOrder) {
                      $q2->where('order', '=', $currentOrder)
                         ->where('milestone_id', '<', $this->milestone_id);
                  });
            })
            ->orderBy('order')
            ->orderBy('milestone_id')
            ->get();

        // Calculate cumulative days
        $cumulativeDays = $previousMilestones->sum('EstimatedDays') ?? 0;
        $totalDays = $cumulativeDays + $this->EstimatedDays;

        try {
            return Carbon::parse($this->project->StartDate)->addDays($totalDays);
        } catch (\Exception $e) {
            return null;
        }
    }

    // Check if employee is a foreman
    public static function isForeman($employee)
    {
        if (!$employee) {
            return false;
        }
        
        // Get the position relationship object, not the accessor
        $position = $employee->relationLoaded('position') 
            ? $employee->getRelation('position') 
            : $employee->position()->first();
            
        if (!$position) {
            return false;
        }
        
        return stripos($position->PositionName, 'Foreman') !== false;
    }

    // Check if employee is an engineer
    public static function isEngineer($employee)
    {
        if (!$employee) {
            return false;
        }
        
        // Get the position relationship object, not the accessor
        $position = $employee->relationLoaded('position') 
            ? $employee->getRelation('position') 
            : $employee->position()->first();
            
        if (!$position) {
            return false;
        }
        
        return stripos($position->PositionName, 'Engineer') !== false;
    }

    // Check if employee is a general manager (deprecated - use isAdmin instead)
    public static function isGeneralManager($employee)
    {
        if (!$employee) {
            return false;
        }
        
        // Get the position relationship object, not the accessor
        $position = $employee->relationLoaded('position') 
            ? $employee->getRelation('position') 
            : $employee->position()->first();
            
        if (!$position) {
            return false;
        }
        
        return stripos($position->PositionName, 'General Manager') !== false;
    }

    // Check if user is an Admin (UserTypeID == 2)
    public static function isAdmin($user)
    {
        if (!$user) {
            return false;
        }
        
        return $user->UserTypeID == 2; // UserTypeID 2 = HR/Admin
    }

    // Check if user can submit milestone completion
    public function canUserSubmit($user)
    {
        if (!$user || !$user->EmployeeID) {
            return false;
        }

        $employee = Employee::with('position')->find($user->EmployeeID);
        if (!$employee || !self::isForeman($employee)) {
            return false;
        }

        // Check if employee is assigned to the project
        $isAssigned = \App\Models\ProjectEmployee::where('ProjectID', $this->project_id)
            ->where('EmployeeID', $employee->id)
            ->exists();

        return $isAssigned && $this->status === 'In Progress' && ($this->SubmissionStatus === 'Not Submitted' || is_null($this->SubmissionStatus));
    }

    // Check if user can approve milestone completion
    public function canUserApprove($user)
    {
        if (!$user) {
            return false;
        }

        // Check if user is an Admin (UserTypeID == 2) - Admins can approve without being assigned
        if (self::isAdmin($user)) {
            return $this->SubmissionStatus === 'Pending Approval';
        }

        // For Engineers, check if they're assigned to the project
        if (!$user->EmployeeID) {
            return false;
        }

        $employee = Employee::with('position')->find($user->EmployeeID);
        if (!$employee) {
            return false;
        }

        // Check if employee is an Engineer
        $isEngineer = self::isEngineer($employee);
        
        if (!$isEngineer) {
            return false;
        }

        // Check if employee is assigned to the project
        $isAssigned = \App\Models\ProjectEmployee::where('ProjectID', $this->project_id)
            ->where('EmployeeID', $employee->id)
            ->where('status', 'Active')
            ->exists();

        return $isAssigned && $this->SubmissionStatus === 'Pending Approval';
    }
}
