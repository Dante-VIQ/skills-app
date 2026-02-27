<?php
// app/Models/UserInteraction.php

namespace App\Models;

use App\Models\LearningResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserInteraction extends Model
{
    use HasFactory;

    protected $table = 'user_interactions';

    protected $fillable = [
        'user_id',
        'learning_resource_id',
        'interaction_type',
        'watch_duration',
        'watch_percentage',
        'completed',
        'rating',
        'feedback',
        'quiz_results',
        'session_id',
        'device_type',
        'browser',
        'platform',
        'ip_address',
        'user_agent',
        'position_seconds',
        'section',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'referrer_url',
        'ai_insights',
        'engagement_score',
        'interest_score',
        'interaction_date'
    ];

    protected $casts = [
        'completed' => 'boolean',
        'quiz_results' => 'array',
        'ai_insights' => 'array',
        'interaction_date' => 'datetime',
        'watch_duration' => 'integer',
        'watch_percentage' => 'integer',
        'rating' => 'integer',
        'position_seconds' => 'integer',
        'engagement_score' => 'float',
        'interest_score' => 'float'
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($interaction) {
            // Set interaction date if not set
            if (!$interaction->interaction_date) {
                $interaction->interaction_date = now();
            }

            // Calculate engagement score
            $interaction->engagement_score = $interaction->calculateEngagementScore();

            // Log significant interactions
            if (in_array($interaction->interaction_type, ['watch_complete', 'like', 'rate'])) {
                Log::info('Significant interaction recorded', [
                    'user_id' => $interaction->user_id,
                    'resource_id' => $interaction->learning_resource_id,
                    'type' => $interaction->interaction_type
                ]);
            }
        });

        static::saved(function ($interaction) {
            // Update resource stats
            $interaction->updateResourceStats();

            // Update user profile for recommendations
            $interaction->updateUserProfile();

            // Clear recommendation cache for this user
            cache()->forget("recommendations:{$interaction->user_id}");
        });
    }

    // ============= RELATIONSHIPS =============

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(LearningResource::class, 'learning_resource_id');
    }

    // ============= SCOPES =============

    /**
     * Scope for recent interactions
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for positive interactions
     */
    public function scopePositive($query)
    {
        return $query->where(function ($q) {
            $q->where('rating', '>=', 4)
              ->orWhere('interaction_type', 'like')
              ->orWhere('watch_percentage', '>=', 80);
        });
    }

    /**
     * Scope for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for a specific resource
     */
    public function scopeForResource($query, $resourceId)
    {
        return $query->where('learning_resource_id', $resourceId);
    }

    /**
     * Scope for today's interactions
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // ============= CALCULATION METHODS =============

    /**
     * Calculate engagement score based on interaction
     */
    protected function calculateEngagementScore(): float
    {
        $baseScore = 0;

        // Base scores by interaction type
        $typeScores = [
            'view' => 0.1,
            'watch_start' => 0.2,
            'watch_progress' => 0.3,
            'watch_complete' => 1.0,
            'like' => 0.8,
            'dislike' => 0.2,
            'save' => 0.7,
            'share' => 0.9,
            'comment' => 0.6,
            'rate' => 0.5,
            'complete_quiz' => 0.8,
            'earn_badge' => 1.0
        ];

        $baseScore = $typeScores[$this->interaction_type] ?? 0.1;

        // Adjust based on watch percentage
        if ($this->watch_percentage) {
            $baseScore *= ($this->watch_percentage / 100);
        }

        // Adjust based on rating
        if ($this->rating) {
            $baseScore *= ($this->rating / 5);
        }

        // Bonus for completion
        if ($this->completed) {
            $baseScore *= 1.2;
        }

        return min($baseScore, 1.0); // Cap at 1.0
    }

    /**
     * Calculate interest score for ML model
     */
    public function calculateInterestScore(): float
    {
        $score = 0;

        // Time-based interest (more time = more interest)
        if ($this->watch_duration && $this->resource) {
            $expectedDuration = $this->resource->estimated_minutes * 60;
            if ($expectedDuration > 0) {
                $timeRatio = min($this->watch_duration / $expectedDuration, 2.0);
                $score += $timeRatio * 0.4;
            }
        }

        // Interaction type weight
        $typeWeights = [
            'like' => 0.3,
            'save' => 0.3,
            'share' => 0.4,
            'complete_quiz' => 0.3,
            'watch_complete' => 0.2
        ];

        $score += $typeWeights[$this->interaction_type] ?? 0.1;

        // Rating contribution
        if ($this->rating) {
            $score += ($this->rating / 5) * 0.2;
        }

        return min($score, 1.0);
    }

    // ============= UPDATE METHODS =============

    /**
     * Update resource statistics
     */
    protected function updateResourceStats(): void
    {
        if (!$this->resource) {
            return;
        }

        $resource = $this->resource;

        switch ($this->interaction_type) {
            case 'view':
                $resource->increment('view_count');
                break;

            case 'like':
                $resource->increment('likes_count');
                break;

            case 'watch_complete':
                $resource->increment('completion_count');
                break;

            case 'rate':
                if ($this->rating) {
                    // Update average rating
                    $resource->avg_rating = $resource->interactions()
                        ->whereNotNull('rating')
                        ->avg('rating');
                    $resource->save();
                }
                break;
        }
    }

    /**
     * Update user profile for better recommendations
     */
    protected function updateUserProfile(): void
    {
        if (!$this->user) {
            return;
        }

        $user = $this->user;

        // Update last active timestamp
        $user->last_active_at = now();

        // Update interest areas based on interactions
        if ($this->resource && $this->engagement_score > 0.5) {
            $interests = $user->interests ?? [];
            $category = $this->resource->category->name ?? null;

            if ($category && !in_array($category, $interests)) {
                $interests[] = $category;
                $user->interests = array_slice($interests, -10); // Keep last 10
            }

            // Update skill levels based on resource difficulty
            if ($this->completed) {
                $this->updateUserSkillLevel($user);
            }
        }

        $user->save();
    }

    /**
     * Update user's skill level based on completed content
     */
    protected function updateUserSkillLevel($user): void
    {
        if (!$this->resource) {
            return;
        }

        $category = $this->resource->category->name ?? 'general';
        $difficulty = $this->resource->difficulty_level;

        $skillLevels = $user->skill_levels ?? [];

        if (!isset($skillLevels[$category])) {
            $skillLevels[$category] = 'beginner';
        }

        // Progress skill level based on completed content
        $completedCount = UserInteraction::forUser($user->id)
            ->forResource($this->resource->id)
            ->where('completed', true)
            ->count();

        if ($completedCount >= 5 && $skillLevels[$category] === 'beginner') {
            $skillLevels[$category] = 'intermediate';
        } elseif ($completedCount >= 20 && $skillLevels[$category] === 'intermediate') {
            $skillLevels[$category] = 'advanced';
        }

        $user->skill_levels = $skillLevels;
    }

    // ============= ANALYTICS METHODS =============

    /**
     * Get interaction summary for a user
     */
    public static function getUserSummary($userId, $days = 30)
    {
        $interactions = self::forUser($userId)
            ->recent($days)
            ->get();

        return [
            'total_interactions' => $interactions->count(),
            'total_watch_time' => $interactions->sum('watch_duration') / 60, // minutes
            'completion_rate' => $interactions->where('completed', true)->count() / max($interactions->count(), 1),
            'avg_rating' => $interactions->whereNotNull('rating')->avg('rating'),
            'favorite_categories' => $interactions->load('resource')
                ->groupBy('resource.category.name')
                ->map->count()
                ->sortDesc()
                ->take(3)
                ->toArray(),
            'engagement_trend' => $interactions->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            })->map->avg('engagement_score'),
            'peak_hours' => $interactions->groupBy(function ($item) {
                return $item->created_at->format('H');
            })->map->count()->toArray()
        ];
    }

    /**
     * Get popular content based on interactions
     */
    public static function getPopularContent($limit = 10, $days = 30)
    {
        return self::recent($days)
            ->select('learning_resource_id',
                     DB::raw('COUNT(*) as interaction_count'),
                     DB::raw('AVG(engagement_score) as avg_engagement'),
                     DB::raw('AVG(rating) as avg_rating'))
            ->groupBy('learning_resource_id')
            ->orderByDesc('interaction_count')
            ->with('resource')
            ->limit($limit)
            ->get();
    }

    /**
     * Get content recommendations based on user's interests
     */
    public static function getContentBasedRecommendations($userId, $limit = 10)
    {
        $user = User::find($userId);

        if (!$user) {
            return collect();
        }

        // Get user's highly engaged content
        $engagedContent = self::forUser($userId)
            ->where('engagement_score', '>=', 0.7)
            ->with('resource.category', 'resource.tags')
            ->get();

        if ($engagedContent->isEmpty()) {
            return collect();
        }

        // Extract user preferences
        $preferredCategories = $engagedContent->pluck('resource.category_id')->unique();
        $preferredTags = $engagedContent->flatMap(function ($interaction) {
            return $interaction->resource->tags ?? [];
        })->unique();

        // Find similar content
        return LearningResource::whereNotIn('id', $engagedContent->pluck('learning_resource_id'))
            ->where(function ($query) use ($preferredCategories, $preferredTags) {
                $query->whereIn('category_id', $preferredCategories)
                      ->orWhereJsonContains('tags', $preferredTags->toArray());
            })
            ->with('category')
            ->limit($limit)
            ->get();
    }

    // ============= HELPER METHODS =============

    /**
     * Check if this interaction is significant for ML
     */
    public function isSignificantForML(): bool
    {
        $significantTypes = ['watch_complete', 'like', 'rate', 'save', 'share'];

        return in_array($this->interaction_type, $significantTypes)
               || $this->engagement_score > 0.7
               || $this->watch_percentage > 80;
    }

    /**
     * Get ML features for this interaction
     */
    public function getMLFeatures(): array
    {
        return [
            'user_id' => $this->user_id,
            'content_id' => $this->learning_resource_id,
            'interaction_type' => $this->interaction_type,
            'engagement_score' => $this->engagement_score,
            'watch_percentage' => $this->watch_percentage,
            'rating' => $this->rating,
            'completed' => $this->completed,
            'hour_of_day' => $this->created_at->hour,
            'day_of_week' => $this->created_at->dayOfWeek,
            'device_type' => $this->device_type,
            'platform' => $this->platform
        ];
    }
}
