<?php
// app/Models/ResourceReferral.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceReferral extends Model
{
    use HasFactory;

    protected $table = 'resource_referrals';

    protected $fillable = [
        'user_id',
        'curated_resource_id',
        'learning_resource_id',
        'referral_type',
        'ml_score',
        'context',
        'clicked',
        'clicked_at',
        'time_spent',
        'found_helpful',
    ];

    protected $casts = [
        'context' => 'array',
        'clicked' => 'boolean',
        'found_helpful' => 'boolean',
        'ml_score' => 'float',
        'time_spent' => 'integer',
        'clicked_at' => 'datetime',
    ];

    /**
     * Get the user who made this referral
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the curated resource that was referred
     */
    public function curatedResource(): BelongsTo
    {
        return $this->belongsTo(CuratedResource::class);
    }

    /**
     * Get the learning resource context (if any)
     */
    public function learningResource(): BelongsTo
    {
        return $this->belongsTo(LearningResource::class);
    }

    /**
     * Scope for successful referrals (clicked)
     */
    public function scopeSuccessful($query)
    {
        return $query->where('clicked', true);
    }

    /**
     * Scope for helpful referrals
     */
    public function scopeHelpful($query)
    {
        return $query->where('found_helpful', true);
    }

    /**
     * Scope for today's referrals
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope by referral type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('referral_type', $type);
    }

    /**
     * Mark as clicked
     */
    public function markAsClicked()
    {
        $this->clicked = true;
        $this->clicked_at = now();
        $this->save();
        
        // Update resource stats
        if ($this->curatedResource) {
            $this->curatedResource->incrementReferralCount();
            $this->curatedResource->updatePopularityScore();
        }
    }

    /**
     * Record time spent
     */
    public function recordTimeSpent($seconds)
    {
        $this->time_spent = $seconds;
        $this->save();
    }

    /**
     * Record helpfulness feedback
     */
    public function recordHelpfulness($helpful)
    {
        $this->found_helpful = $helpful;
        $this->save();
    }
}