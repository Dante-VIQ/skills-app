<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialImpactProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'organization',
        'goal_amount',
        'current_amount',
        'status', // planning, active, completed, cancelled
        'start_date',
        'completion_date',
        'image_url',
        'updates',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'completion_date' => 'datetime',
        'updates' => 'array',
    ];

    // UPDATED Relationship name
    public function contributions()
    {
        return $this->hasMany(ImpactContribution::class);
    }

    public function contributors()
    {
        return $this->hasManyThrough(
            User::class,
            ImpactContribution::class,
            'social_impact_project_id',
            'id',
            'id',
            'user_id'
        )->distinct();
    }

    // Methods
    public function progressPercentage()
    {
        if ($this->goal_amount <= 0) return 0;
        
        return min(100, ($this->current_amount / $this->goal_amount) * 100);
    }

    public function addContribution($userId, $amount, $source = null, $sourceId = null)
    {
        $contribution = $this->contributions()->create([
            'user_id' => $userId,
            'amount' => $amount,
            'source' => $source,
            'source_id' => $sourceId,
            'transaction_id' => 'SKD' . time() . rand(100, 999),
        ]);
        
        $this->increment('current_amount', $amount);
        
        return $contribution;
    }
}