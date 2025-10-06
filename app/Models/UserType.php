<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    protected $table = 'tblusertype';
    protected $primaryKey = 'UserTypeID';
    
    protected $fillable = [
        'UserType',
        'FlagDeleted'
    ];

    protected $casts = [
        'FlagDeleted' => 'boolean',
    ];

    // Scope to get only non-deleted user types
    public function scopeActive($query)
    {
        return $query->where('FlagDeleted', 0);
    }

    // Relationship with users
    public function users()
    {
        return $this->hasMany(User::class, 'UserTypeID', 'UserTypeID');
    }
}
