<?php
// app/Services/ML/ResourceReferralService.php

namespace App\Services\ML;

use App\Models\User;
use App\Models\CuratedResource;
use App\Models\LearningResource;
use App\Models\ResourceReferral;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResourceReferralService
{
    /**
     * Get external resource recommendations for a user
     */
    public function getRecommendations(User $user, $topic = null, $limit = 5)
    {
        $query = CuratedResource::where('is_active', true)
            ->where('is_safe', true)
            ->with('source');
        
        if ($topic) {
            $query->where('topic', $topic);
        }
        
        // ML scoring: combine quality, popularity, and relevance
        $resources = $query->select('*')
            ->selectRaw('
                (quality_score * 0.4 + 
                 popularity_score * 0.3 + 
                 (1 - (referral_count / (referral_count + 100))) * 0.3
                ) as ml_score
            ')
            ->orderByDesc('ml_score')
            ->limit($limit * 2) // Get more for filtering
            ->get();
        
        // Filter by user's skill level
        $userLevel = $user->skill_level ?? 'beginner';
        $filtered = $resources->filter(function ($resource) use ($userLevel) {
            return $this->matchesDifficulty($resource->difficulty_level, $userLevel);
        });
        
        return $filtered->take($limit);
    }

    /**
     * Get resources related to a learning resource
     */
    public function getRelatedResources(LearningResource $resource, $limit = 3)
    {
        // Find external resources on similar topics
        return CuratedResource::where('is_active', true)
            ->where('topic', $resource->topic)
            ->orWhereJsonContains('tags', $resource->tags)
            ->with('source')
            ->orderByDesc('quality_score')
            ->limit($limit)
            ->get();
    }

    /**
     * Track a referral click
     */
    public function trackReferral(User $user, CuratedResource $resource, $context = [])
    {
        try {
            $referral = ResourceReferral::create([
                'user_id' => $user->id,
                'curated_resource_id' => $resource->id,
                'referral_type' => $context['type'] ?? 'ml_recommendation',
                'ml_score' => $context['score'] ?? null,
                'context' => $context,
                'clicked' => true,
                'clicked_at' => now(),
            ]);
            
            // Update resource stats
            $resource->increment('referral_count');
            $resource->last_referred_at = now();
            $resource->save();
            
            // Update source stats
            $resource->source->increment('referral_count');
            $resource->source->save();
            
            return $referral;
        } catch (\Exception $e) {
            Log::error('Failed to track referral', [
                'user' => $user->id,
                'resource' => $resource->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Check if difficulty matches user level
     */
    protected function matchesDifficulty($resourceLevel, $userLevel)
    {
        $levels = ['beginner' => 1, 'intermediate' => 2, 'advanced' => 3];
        
        $resourceRank = $levels[$resourceLevel] ?? 1;
        $userRank = $levels[$userLevel] ?? 1;
        
        // Allow resources at or one level above user's level
        return $resourceRank <= $userRank + 1;
    }

    /**
     * Update popularity scores based on referral data
     */
    public function updatePopularityScores()
    {
        // Calculate popularity based on click-through rates
        $resources = CuratedResource::withCount('referrals')
            ->withCount(['referrals as click_count' => function ($query) {
                $query->where('clicked', true);
            }])
            ->get();
        
        foreach ($resources as $resource) {
            if ($resource->referrals_count > 0) {
                $clickRate = $resource->click_count / $resource->referrals_count;
                $resource->popularity_score = $clickRate * 100;
                $resource->save();
            }
        }
        
        return true;
    }
}