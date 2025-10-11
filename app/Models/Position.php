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
        'Salary',
    ];

    protected $casts = [
        'Salary' => 'decimal:2',
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

}
