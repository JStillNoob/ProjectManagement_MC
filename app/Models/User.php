<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ContactNumber',
        'Email',
        'Password',
        'UserTypeID',
        'FlagDeleted',
        'EmployeeID'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'Password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'Password' => 'hashed',
            'FlagDeleted' => 'boolean',
        ];
    }


    // Scope to get only non-deleted users
    public function scopeActive($query)
    {
        return $query->where('FlagDeleted', 0);
    }

    // Tell Laravel to use 'Email' instead of 'email' for authentication
    public function getEmailForPasswordReset()
    {
        return $this->Email;
    }

    // If you're using email verification
    public function getEmailForVerification()
    {
        return $this->Email;
    }

    // Tell Laravel to use 'Password' instead of 'password'
    public function getAuthPassword()
    {
        return $this->Password;
    }

    // Relationship with user type
    public function userType()
    {
        return $this->belongsTo(UserType::class, 'UserTypeID', 'UserTypeID');
    }

    // Alias for backward compatibility
    public function type()
    {
        return $this->userType();
    }

    // Helper method to safely get UserTypeID as integer
    public function getUserTypeId()
    {
        $value = $this->attributes['UserTypeID'] ?? null;
        return $value !== null ? (int) $value : null;
    }

    // Accessor to ensure UserTypeID always returns integer
    public function getUserTypeIDAttribute($value)
    {
        // Always get the raw attribute value directly from attributes array
        // This bypasses any relationship loading issues
        if (isset($this->attributes['UserTypeID'])) {
            $rawValue = $this->attributes['UserTypeID'];
        } else {
            $rawValue = $value;
        }
        
        // If value is null, return null
        if ($rawValue === null) {
            return null;
        }
        
        // If it's an object (shouldn't happen, but handle it), try to extract ID
        if (is_object($rawValue)) {
            // If it's a UserType model instance
            if ($rawValue instanceof \App\Models\UserType) {
                return (int) $rawValue->UserTypeID;
            }
            // Try to get UserTypeID property
            if (isset($rawValue->UserTypeID)) {
                return (int) $rawValue->UserTypeID;
            }
            // Try getAttribute method
            if (method_exists($rawValue, 'getAttribute')) {
                $id = $rawValue->getAttribute('UserTypeID');
                if ($id !== null) {
                    return (int) $id;
                }
            }
            // Last resort: try to access as array
            if (is_array($rawValue) && isset($rawValue['UserTypeID'])) {
                return (int) $rawValue['UserTypeID'];
            }
        }
        
        // Cast to integer if numeric
        if (is_numeric($rawValue)) {
            return (int) $rawValue;
        }
        
        // Fallback: return as-is (shouldn't reach here normally)
        return $rawValue;
    }

    public function scopeVoters($query)
    {
        return $query->whereHas('userType', function($q) {
            $q->where('userType_name', 'Voter');
        });
    }

    // Relationship with employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'EmployeeID', 'id');
    }

    // Relationship with position
    public function position()
    {
        return $this->belongsTo(Position::class, 'PositionID', 'PositionID');
    }

    // Keep role method for backward compatibility
    public function role()
    {
        return $this->position();
    }

    // Accessor for formatted created at
    public function getFormattedCreatedAtAttribute()
    {
        if (!$this->created_at) {
            return 'N/A';
        }
        
        try {
            return $this->created_at->format('M d, Y');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

}
