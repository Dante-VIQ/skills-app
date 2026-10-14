<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChildActivity extends Model
{
    use HasFactory;

   protected $fillable = [
        'child_id',
        'content_module_id',
        'assignment_id',
        'type',
        'title',
        'description',
        'data',
        'duration',
        'score',
        'max_score',
        'percentage_score',
        'status',
        'responses',
        'metadata',
        'reviewed_by',
        'reviewed_at',
        'feedback',
    ];

    protected $casts = [
        'data'       => 'array',
        'responses'  => 'array',
        'metadata'   => 'array',
        'reviewed_at' => 'datetime',
    ];

    // Relationships - UPDATED
    public function child()
    {
        return $this->belongsTo(User::class, 'child_id')->whereHas('roles', function ($q) {
            $q->where('name', 'child');
        });
    }

    public function assignment()
    {
        return $this->belongsTo(ParentAssignment::class, 'assignment_id');
    }

    public function contentModule()
    {
        return $this->belongsTo(ContentModule::class, 'content_module_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function attachments()
    {
        return $this->hasMany(ActivityAttachment::class);
    }

    // Scopes
    public function scopeForChild($query, $childId)
    {
        return $query->where('child_id', $childId);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeThisWeek($query)
    {
        return $query->where('created_at', '>=', now()->subWeek());
    }

    // Methods
    public function calculateImpact()
    {
        // Each activity generates impact based on duration/score
        $baseAmount = 0.10; // $0.10 per activity
        
        if ($this->duration) {
            $baseAmount += ($this->duration / 60) * 0.05; // $0.05 per minute
        }
        
        if ($this->score && $this->max_score) {
            $scorePercentage = ($this->score / $this->max_score) * 100;
            $baseAmount += ($scorePercentage / 100) * 0.10; // Bonus for high scores
        }
        
        return round($baseAmount, 2);
    }
}