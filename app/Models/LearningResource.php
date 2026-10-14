<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningResource extends Model
{
      /**
     * Get all interactions for this resource
     */
    public function interactions()
    {
        return $this->hasMany(UserInteraction::class);
    }

    /**
     * Get positive interactions
     */
    public function positiveInteractions()
    {
        return $this->interactions()->positive();
    }

    /**
     * Get users who interacted with this resource
     */
    public function interactingUsers()
    {
        return $this->belongsToMany(User::class, 'user_interactions')
                    ->withPivot('interaction_type', 'rating', 'completed')
                    ->withTimestamps();
    }

    /**
     * Get average rating
     */
    public function getAverageRatingAttribute()
    {
        return $this->interactions()
            ->whereNotNull('rating')
            ->avg('rating') ?? 0;
    }

    /**
     * Get completion rate
     */
    public function getCompletionRateAttribute()
    {
        $total = $this->interactions()->count();
        if ($total === 0) return 0;
        
        $completed = $this->interactions()->where('completed', true)->count();
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Get engagement score
     */
    public function getEngagementScoreAttribute()
    {
        return $this->interactions()->avg('engagement_score') ?? 0;
    }

    /**
     * Get demographics of users who liked this
     */
    public function getAudienceDemographicsAttribute()
    {
        return [
            'age_groups' => $this->getAgeGroupBreakdown(),
            'interest_areas' => $this->getInterestBreakdown(),
            'completion_by_difficulty' => $this->getCompletionByDifficulty()
        ];
    }

    protected function getAgeGroupBreakdown()
    {
        // Implementation depends on your user age data
        return [];
    }

    protected function getInterestBreakdown()
    {
        return $this->interactions()
            ->with('user')
            ->get()
            ->flatMap(function ($interaction) {
                return $interaction->user->interests ?? [];
            })
            ->countBy()
            ->toArray();
    }

    protected function getCompletionByDifficulty()
    {
        return [
            'beginner' => $this->interactions()
                ->where('completed', true)
                ->whereHas('user', fn($q) => $q->where('skill_level->general', 'beginner'))
                ->count(),
            'intermediate' => $this->interactions()
                ->where('completed', true)
                ->whereHas('user', fn($q) => $q->where('skill_level->general', 'intermediate'))
                ->count(),
            'advanced' => $this->interactions()
                ->where('completed', true)
                ->whereHas('user', fn($q) => $q->where('skill_level->general', 'advanced'))
                ->count()
        ];
    }

    /**
     * Increment counters
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function incrementLikesCount()
    {
        $this->increment('likes_count');
    }

    public function incrementCompletionCount()
    {
        $this->increment('completion_count');
    }
    /**
 * Get external resources linked to this learning resource
 */
public function externalResources()
{
    return $this->belongsToMany(
        CuratedResource::class,
        'learning_resource_external',
        'learning_resource_id',
        'external_resource_id'
    )->withPivot('relationship_type', 'display_order')
     ->withTimestamps()
     ->orderByPivot('display_order');
}

/**
 * Get primary external resources
 */
public function primaryExternalResources()
{
    return $this->externalResources()
        ->wherePivot('relationship_type', 'primary');
}

/**
 * Get supplemental external resources
 */
public function supplementalExternalResources()
{
    return $this->externalResources()
        ->wherePivot('relationship_type', 'supplemental');
}

/**
 * Get prerequisite external resources
 */
public function prerequisiteExternalResources()
{
    return $this->externalResources()
        ->wherePivot('relationship_type', 'prerequisite');
}
// app/Models/UserInteraction.php
public function resource()
{
    return $this->belongsTo(LearningResource::class, 'learning_resource_id');
}

public function learningResource()
{
    return $this->belongsTo(LearningResource::class, 'learning_resource_id');
}
}
