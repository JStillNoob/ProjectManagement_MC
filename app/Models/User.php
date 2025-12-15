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
