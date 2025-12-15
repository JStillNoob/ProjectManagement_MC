<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';
    protected $primaryKey = 'ClientID';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'ClientName',
        'FirstName',
        'LastName',
        'ContactNumber',
        'Email',
    ];

    // Relationship with projects
    public function projects()
    {
        return $this->hasMany(Project::class, 'ClientID', 'ClientID');
    }
}