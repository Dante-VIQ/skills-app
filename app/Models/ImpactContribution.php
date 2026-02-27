<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImpactContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // Changed from child_id
        'social_impact_project_id',
        'amount',
        'source', // learning_activity, assignment, parent_donation, etc.
        'source_id', // ID of the source activity
        'transaction_id',
    ];

    // UPDATED Relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(SocialImpactProject::class);
    }

    public function sourceActivity()
    {
        return match($this->source) {
            'learning_activity' => $this->belongsTo(ChildActivity::class, 'source_id'),
            'assignment' => $this->belongsTo(ParentAssignment::class, 'source_id'),
            default => null,
        };
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForProject($query, $projectId)
    {
        return $query->where('social_impact_project_id', $projectId);
    }

    public function scopeThisMonth($query)
    {
        return $query->where('created_at', '>=', now()->startOfMonth());
    }
}