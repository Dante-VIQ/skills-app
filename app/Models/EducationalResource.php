<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducationalResource extends Model
{
    use HasFactory;

    protected $table = 'educational_sources';

    protected $fillable = [
        'name',
        'slug',
        'website_url',
        'description',
        'logo_url',
        'favicon_url',
        'is_trusted',
        'trust_score',
        'safety_rating',
        'requires_attribution',
        'attribution_format',
        'referral_link_format',
        'available_topics',
        'content_types',
        'has_api',
        'api_base_url',
        'api_key_required',
        'api_config',
        'referral_count',
        'click_through_rate',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'available_topics' => 'array',
        'content_types' => 'array',
        'safety_rating' => 'array',
        'api_config' => 'array',
        'is_trusted' => 'boolean',
        'requires_attribution' => 'boolean',
        'has_api' => 'boolean',
        'api_key_required' => 'boolean',
        'is_active' => 'boolean',
        'trust_score' => 'integer',
        'referral_count' => 'integer',
        'click_through_rate' => 'float',
        'display_order' => 'integer',
    ];

    /**
     * Get all curated resources from this source
     */
    public function curatedResources(): HasMany
    {
        return $this->hasMany(CuratedResource::class, 'source_id');
    }

    /**
     * Get active curated resources
     */
    public function activeResources()
    {
        return $this->curatedResources()->where('is_active', true);
    }

    /**
     * Get resources by topic
     */
    public function resourcesByTopic($topic)
    {
        return $this->activeResources()->where('topic', $topic);
    }

    /**
     * Get resources by difficulty
     */
    public function resourcesByDifficulty($level)
    {
        return $this->activeResources()->where('difficulty_level', $level);
    }

    /**
     * Format attribution text
     */
    public function formatAttribution($customText = null)
    {
        if ($customText) {
            return $customText;
        }
        
        return $this->attribution_format ?? "Via {$this->name}";
    }

    /**
     * Generate referral link
     */
    public function generateReferralLink($path)
    {
        $base = $this->referral_link_format ?? $this->website_url;
        return str_replace('{path}', ltrim($path, '/'), $base);
    }

    /**
     * Update click-through rate
     */
    public function updateClickThroughRate()
    {
        if ($this->referral_count > 0) {
            $clicks = ResourceReferral::whereHas('curatedResource', function ($query) {
                $query->where('source_id', $this->id);
            })->where('clicked', true)->count();
            
            $this->click_through_rate = ($clicks / $this->referral_count) * 100;
            $this->save();
        }
    }

    /**
     * Scope for trusted sources
     */
    public function scopeTrusted($query)
    {
        return $query->where('is_trusted', true);
    }

    /**
     * Scope for sources with API
     */
    public function scopeWithApi($query)
    {
        return $query->where('has_api', true);
    }

    /**
     * Scope for sources by topic
     */
    public function scopeWithTopic($query, $topic)
    {
        return $query->whereJsonContains('available_topics', $topic);
    }

    /**
     * Get favicon URL
     */
    public function getFaviconUrlAttribute()
    {
        return $this->attributes['favicon_url'] ?? $this->website_url . '/favicon.ico';
    }

    /**
     * Get logo URL with fallback
     */
    public function getLogoUrlAttribute($value)
    {
        return $value ?? $this->favicon_url;
    }
}