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
        'time_out',
        'status',
        'remarks',
        'is_active'
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'time_in' => 'datetime',
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
            return $this->time_in->format('H:i A');
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
            return $this->time_out->format('H:i A');
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

    // Method to check if employee is late (assuming 8:30 AM is standard time)
    public function isLate()
    {
        if (!$this->time_in) {
            return false;
        }
        
        $standardTime = \Carbon\Carbon::createFromTime(8, 30, 0);
        return $this->time_in->format('H:i:s') > $standardTime->format('H:i:s');
    }

    // Method to check if employee worked overtime (after 5:30 PM)
    public function isOvertime()
    {
        if (!$this->time_out) {
            return false;
        }
        
        $standardEndTime = \Carbon\Carbon::createFromTime(17, 30, 0); // 5:30 PM
        return $this->time_out->format('H:i:s') > $standardEndTime->format('H:i:s');
    }

    // Method to calculate overtime hours
    public function getOvertimeHoursAttribute()
    {
        if (!$this->time_out || !$this->isOvertime()) {
            return '00:00';
        }
        
        $standardEndTime = \Carbon\Carbon::createFromTime(17, 30, 0); // 5:30 PM
        $timeOut = \Carbon\Carbon::parse($this->time_out);
        
        // Calculate overtime minutes (time_out - standard_end_time)
        $overtimeMinutes = $timeOut->diffInMinutes($standardEndTime);
        $hours = floor($overtimeMinutes / 60);
        $minutes = $overtimeMinutes % 60;
        
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    // Method to calculate working hours
    public function getWorkingHoursAttribute()
    {
        if (!$this->time_in || !$this->time_out) {
            return 'N/A';
        }

        $timeIn = \Carbon\Carbon::parse($this->time_in);
        $timeOut = \Carbon\Carbon::parse($this->time_out);
        
        $hours = $timeIn->diffInHours($timeOut);
        $minutes = $timeIn->diffInMinutes($timeOut) % 60;
        
        return sprintf('%02d:%02d', $hours, $minutes);
    }
}