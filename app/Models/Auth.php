<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Auth extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'FirstName',
        'MiddleName',
        'LastName',
        'Sex',
        'ContactNumber',
        'Email',
        'Password',
    ];

    protected $hidden = [
        'Password',
        'remember_token',
    ];

    // Tell Laravel to use 'Password' instead of 'password'
    public function getAuthPassword()
    {
        return $this->Password;
    }

    // Tell Laravel to use 'Email' instead of 'email'
    public function getEmailForPasswordReset()
    {
        return $this->Email;
    }

    // If you're using email verification
    public function getEmailForVerification()
    {
        return $this->Email;
    }
}