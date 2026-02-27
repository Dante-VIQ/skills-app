<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UniversalSkillRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'child_id',
        'content_category_id',
        'required_minutes',
        'completed_minutes',
        'is_required',
        'deadline',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'deadline' => 'datetime',
    ];

    // UPDATED Relationships
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id')->whereHas('roles', function ($q) {
            $q->where('name', 'parent');
        });
    }

    public function child()
    {
        return $this->belongsTo(User::class, 'child_id')->whereHas('roles', function ($q) {
            $q->where('name', 'child');
        });
    }

    public function category()
    {
        return $this->belongsTo(ContentCategory::class, 'content_category_id');
    }

    // Methods
    public function updateProgress()
    {
        $totalMinutes = $this->child->watchTimeRecords()
            ->where('content_category_id', $this->content_category_id)
            ->sum('duration');
        
        $this->completed_minutes = $totalMinutes;
        $this->save();
        
        return $this;
    }

    public function isMet()
    {
        return $this->completed_minutes >= $this->required_minutes;
    }

    public function progressPercentage()
    {
        if ($this->required_minutes <= 0) return 100;
        
        return min(100, ($this->completed_minutes / $this->required_minutes) * 100);
    }
}