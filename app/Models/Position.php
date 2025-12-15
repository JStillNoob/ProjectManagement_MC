<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Position extends Model
{
    protected $table = 'positions';
    protected $primaryKey = 'PositionID';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'PositionName',
    ];

    // Relationship with employees
    public function employees()
    {
        return $this->hasMany(Employee::class, 'PositionID', 'PositionID');
    }

    // Relationship with users
    public function users()
    {
        return $this->hasMany(User::class, 'PositionID', 'PositionID');
    }

    // Scope for active positions
    public function scopeActive($query)
    {
        return $query; // Return all records
    }

    // Accessor for formatted created at
    public function getFormattedCreatedAtAttribute()
    {
        if (!$this->created_at) {
            return 'N/A';
        }
        
        try {
            return $this->created_at->format('M d, Y g:i A');
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
            return $this->updated_at->format('M d, Y g:i A');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

}
