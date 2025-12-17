<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';
    
    protected $fillable = [
        'employee_id',
        'attendance_date',
        'time_in',
        'lunch_out',
        'lunch_in',
        'time_out',
        'status',
        'is_active'
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'time_in' => 'datetime',
        'lunch_out' => 'datetime',
        'lunch_in' => 'datetime',
        'time_out' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationship with employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    // Scope to get only active attendance records
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope to get attendance for a specific date
    public function scopeForDate($query, $date)
    {
        return $query->where('attendance_date', $date);
    }

    // Scope to get attendance for a date range
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('attendance_date', [$startDate, $endDate]);
    }

    // Scope to get attendance by status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Accessor for formatted time in
    public function getFormattedTimeInAttribute()
    {
        if (!$this->time_in) {
            return 'N/A';
        }
        
        try {
            return $this->time_in->format('h:i A');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    // Accessor for formatted time out
    public function getFormattedTimeOutAttribute()
    {
        if (!$this->time_out) {
            return 'N/A';
        }
        
        try {
            return $this->time_out->format('h:i A');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    // Accessor for formatted date
    public function getFormattedDateAttribute()
    {
        if (!$this->attendance_date) {
            return 'N/A';
        }
        
        try {
            return $this->attendance_date->format('M d, Y');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    // Method to check if employee is late (after 8:15 AM grace period - 8:16 AM or later is late)
    public function isLate()
    {
        if (!$this->time_in) {
            return false;
        }
        
        // 15 minute grace period: 8:00 AM - 8:15 AM is on time, 8:16 AM+ is late
        $graceEndTime = \Carbon\Carbon::createFromTime(8, 15, 0);
        return $this->time_in->format('H:i:s') > $graceEndTime->format('H:i:s');
    }

    // Method to check if employee worked overtime (5:31 PM - 9:00 PM)
    public function isOvertime()
    {
        if (!$this->time_out) {
            return false;
        }
        
        $overtimeStart = \Carbon\Carbon::createFromTime(17, 30, 0); // 5:30 PM
        $overtimeEnd = \Carbon\Carbon::createFromTime(21, 0, 0); // 9:00 PM
        $timeOutTime = $this->time_out->format('H:i:s');
        
        // Overtime if clocked out between 5:31 PM and 9:00 PM
        return $timeOutTime > $overtimeStart->format('H:i:s') && $timeOutTime <= $overtimeEnd->format('H:i:s');
    }

    // Method to check if employee should be marked as half day (no time_out by 9:30 PM)
    public function isHalfDay()
    {
        // If no time_out is recorded and the attendance date is today or past
        if (!$this->time_out && $this->time_in) {
            $today = \Carbon\Carbon::today();
            $attendanceDate = \Carbon\Carbon::parse($this->attendance_date);
            
            // If attendance date is in the past (not today), it's a half day (forgot to clock out)
            if ($attendanceDate->lt($today)) {
                return true;
            }
            
            // If it's today, check if it's past 9:30 PM
            if ($attendanceDate->eq($today)) {
                $now = \Carbon\Carbon::now();
                $halfDayCutoff = \Carbon\Carbon::createFromTime(21, 30, 0); // 9:30 PM
                return $now->format('H:i:s') > $halfDayCutoff->format('H:i:s');
            }
        }
        
        return false;
    }

    // Method to calculate overtime hours (capped at 9:00 PM - max 3.5 hours)
    public function getOvertimeHoursAttribute()
    {
        if (!$this->time_out || !$this->isOvertime()) {
            return '00:00';
        }
        
        $standardEndTime = \Carbon\Carbon::createFromTime(17, 30, 0); // 5:30 PM
        $overtimeCap = \Carbon\Carbon::createFromTime(21, 0, 0); // 9:00 PM
        $timeOut = \Carbon\Carbon::parse($this->time_out);
        
        // Cap the overtime at 9:00 PM
        if ($timeOut->format('H:i:s') > $overtimeCap->format('H:i:s')) {
            $timeOut = $overtimeCap;
        }
        
        // Calculate overtime minutes (time_out - standard_end_time)
        $overtimeMinutes = $timeOut->diffInMinutes($standardEndTime);
        $hours = floor($overtimeMinutes / 60);
        $minutes = $overtimeMinutes % 60;
        
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    // Method to calculate working hours (accounting for 1 hour lunch break)
    public function getWorkingHoursAttribute()
    {
        if (!$this->time_in || !$this->time_out) {
            return 'N/A';
        }

        $timeIn = \Carbon\Carbon::parse($this->time_in);
        $timeOut = \Carbon\Carbon::parse($this->time_out);
        
        // Calculate total minutes worked
        $totalMinutes = $timeIn->diffInMinutes($timeOut);
        
        // Subtract 1 hour (60 minutes) for lunch break if both lunch times are recorded
        // or if the total work time spans across lunch period (12:00 - 1:00 PM)
        if ($this->lunch_out && $this->lunch_in) {
            // Calculate actual lunch break duration
            $lunchOut = \Carbon\Carbon::parse($this->lunch_out);
            $lunchIn = \Carbon\Carbon::parse($this->lunch_in);
            $lunchDuration = $lunchOut->diffInMinutes($lunchIn);
            $totalMinutes -= $lunchDuration;
        } elseif ($totalMinutes > 240) { // More than 4 hours, assume standard 1 hour lunch
            $totalMinutes -= 60;
        }
        
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    // Accessor for formatted lunch out time
    public function getFormattedLunchOutAttribute()
    {
        if (!$this->lunch_out) {
            return 'N/A';
        }
        
        try {
            return $this->lunch_out->format('h:i A');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    // Accessor for formatted lunch in time
    public function getFormattedLunchInAttribute()
    {
        if (!$this->lunch_in) {
            return 'N/A';
        }
        
        try {
            return $this->lunch_in->format('h:i A');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    // Determine the next expected action for an employee
    public function getNextExpectedAction()
    {
        $now = \Carbon\Carbon::now();
        $currentHour = $now->hour;
        $currentMinute = $now->minute;
        
        // No time_in yet - expect time_in
        if (!$this->time_in) {
            return 'time_in';
        }
        
        // Has time_in, no lunch_out - expect lunch_out (around 12 PM)
        if ($this->time_in && !$this->lunch_out) {
            if ($currentHour >= 11 && $currentHour < 13) {
                return 'lunch_out';
            }
            // If past 1 PM and no lunch recorded, skip to time_out
            if ($currentHour >= 13) {
                return 'time_out';
            }
            return 'lunch_out';
        }
        
        // Has lunch_out, no lunch_in - expect lunch_in (around 1 PM)
        if ($this->lunch_out && !$this->lunch_in) {
            return 'lunch_in';
        }
        
        // Has lunch_in, no time_out - expect time_out
        if ($this->lunch_in && !$this->time_out) {
            return 'time_out';
        }
        
        // All recorded
        return 'complete';
    }
}