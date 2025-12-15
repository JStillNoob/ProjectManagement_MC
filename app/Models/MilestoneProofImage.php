<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MilestoneProofImage extends Model
{
    protected $table = 'milestone_proof_images';
    
    protected $fillable = [
        'milestone_id',
        'image_path',
    ];

    // Relationship with milestone
    public function milestone()
    {
        return $this->belongsTo(ProjectMilestone::class, 'milestone_id', 'milestone_id');
    }

    // Accessor for full image URL
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}

