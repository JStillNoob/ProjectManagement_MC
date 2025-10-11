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
        'time_in' => 'datetime:H:i:s',
        'time_out' => 'datetime:H:i:s',
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
        return $this->time_in ? $this->time_in->format('H:i A') : 'N/A';
    }

    // Accessor for formatted time out
    public function getFormattedTimeOutAttribute()
    {
        return $this->time_out ? $this->time_out->format('H:i A') : 'N/A';
    }

    // Accessor for formatted date
    public function getFormattedDateAttribute()
    {
        return $this->attendance_date->format('M d, Y');
    }

    // Method to check if employee is late (assuming 9:00 AM is standard time)
    public function isLate()
    {
        if (!$this->time_in) {
            return false;
        }
        
        $standardTime = \Carbon\Carbon::createFromTime(9, 0, 0);
        return $this->time_in->format('H:i:s') > $standardTime->format('H:i:s');
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