<?php
// app/Models/CuratedResource.php

namespace App\Models;

use App\Models\EducationalResource;
use App\Models\LearningResource;
use App\Models\ResourceReferral;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CuratedResource extends Model
{
    use HasFactory;

    protected $table = 'curated_resources';

    protected $fillable = [
        'source_id',
        'title',
        'slug',
        'description',
        'summary',
        'resource_url',
        'canonical_url',
        'external_id',
        'topic',
        'subtopic',
        'tags',
        'difficulty_level',
        'content_type',
        'estimated_minutes',
        'thumbnail_url',
        'author_name',
        'author_url',
        'license',
        'feature_vector',
        'keyword_vector',
        'popularity_score',
        'referral_count',
        'is_safe',
        'safety_check_results',
        'safety_checked_at',
        'is_active',
        'is_featured',
        'quality_score',
        'last_referred_at',
        'source_metadata',
    ];

    protected $casts = [
        'tags' => 'array',
        'feature_vector' => 'array',
        'keyword_vector' => 'array',
        'safety_check_results' => 'array',
        'source_metadata' => 'array',
        'safety_checked_at' => 'datetime',
        'last_referred_at' => 'datetime',
        'is_safe' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'popularity_score' => 'float',
        'quality_score' => 'integer',
        'referral_count' => 'integer',
        'estimated_minutes' => 'integer',
    ];

    /**
     * Get the source this resource belongs to
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(EducationalResource::class, 'source_id');
    }

    /**
     * Get all referrals for this resource
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(ResourceReferral::class, 'curated_resource_id');
    }

    /**
     * Get learning resources linked to this curated resource
     */
    public function learningResources()
    {
        return $this->belongsToMany(
            LearningResource::class,
            'learning_resource_external',
            'external_resource_id',
            'learning_resource_id'
        )->withPivot('relationship_type', 'display_order')->withTimestamps();
    }

    /**
     * Get successful referrals (clicked)
     */
    public function successfulReferrals()
    {
        return $this->referrals()->where('clicked', true);
    }

    /**
     * Scope for safe resources
     */
    public function scopeSafe($query)
    {
        return $query->where('is_safe', true);
    }

    /**
     * Scope for active resources
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured resources
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope by topic
     */
    public function scopeOfTopic($query, $topic)
    {
        return $query->where('topic', $topic);
    }

    /**
     * Scope by difficulty
     */
    public function scopeOfDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }

    /**
     * Scope by content type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('content_type', $type);
    }

    /**
     * Scope by tag
     */
    public function scopeWithTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    /**
     * Scope by quality threshold
     */
    public function scopeMinQuality($query, $minScore = 70)
    {
        return $query->where('quality_score', '>=', $minScore);
    }

    /**
     * Get full attribution text
     */
    public function getAttributionTextAttribute()
    {
        if ($this->source && $this->source->requires_attribution) {
            return $this->source->formatAttribution() . ': ' . $this->title;
        }
        
        return $this->title;
    }

    /**
     * Get embeddable link (if allowed)
     */
    public function getEmbedUrlAttribute()
    {
        // For YouTube videos, etc.
        return null;
    }

    /**
     * Increment referral count
     */
    public function incrementReferralCount()
    {
        $this->increment('referral_count');
        $this->last_referred_at = now();
        $this->save();
    }

    /**
     * Calculate and update popularity score
     */
    public function updatePopularityScore()
    {
        $totalReferrals = $this->referral_count;
        $successfulReferrals = $this->successfulReferrals()->count();
        
        if ($totalReferrals > 0) {
            $clickRate = $successfulReferrals / $totalReferrals;
            $this->popularity_score = min($clickRate * 100, 100);
        } else {
            $this->popularity_score = 50; // Default middle score
        }
        
        $this->save();
    }

    /**
     * Check if resource is age-appropriate
     */
    public function isAgeAppropriate($age)
    {
        // Implement age-appropriateness logic based on topic and difficulty
        $ageMap = [
            'beginner' => 5,
            'intermediate' => 10,
            'advanced' => 14,
        ];
        
        $minAge = $ageMap[$this->difficulty_level] ?? 5;
        
        return $age >= $minAge;
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($resource) {
            if (empty($resource->slug)) {
                $resource->slug = Str::slug($resource->title);
            }
            
            if (empty($resource->quality_score)) {
                $resource->quality_score = 70; // Default quality score
            }
        });

        static::saved(function ($resource) {
            // Update source's click-through rate when resource is updated
            if ($resource->source) {
                $resource->source->updateClickThroughRate();
            }
        });
    }
}