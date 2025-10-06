<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectStatus extends Model
{
    use HasFactory;

    protected $table = 'project_status';
    protected $primaryKey = 'StatusID';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'StatusName',
    ];

    // Relationship with projects
    public function projects()
    {
        return $this->hasMany(Project::class, 'StatusID', 'StatusID');
    }
}
