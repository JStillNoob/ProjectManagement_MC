<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'RoleID';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'RoleName',
    ];

    // Relationship with users
    public function users()
    {
        return $this->hasMany(User::class, 'RoleID', 'RoleID');
    }
}
