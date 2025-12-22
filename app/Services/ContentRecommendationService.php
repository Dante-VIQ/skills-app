<?php

namespace App\Services;

use App\Models\User;
use App\Models\ContentModule;

class ContentRecommendationService
{
    public function getPersonalizedFeed(User $child, $limit = 25)
    {
        // Get child's top 3 interests by affinity score
        $topInterests = $child->interests()
            ->orderBy('affinity_score', 'desc')
            ->limit(3)
            ->pluck('id');
            
        // Get content matching top interests
        $interestBasedContent = ContentModule::whereHas('interests', function($query) use ($topInterests) {
            $query->whereIn('interests.id', $topInterests);
        })
        ->where('age_min', '<=', $child->age)
        ->where('age_max', '>=', $child->age)
        ->inRandomOrder()
        ->limit($limit)
        ->get();
        
        return $interestBasedContent;
    }
    
    public function getUniversalSkillContent(User $child, $categoryId, $limit = 5)
    {
        return ContentModule::where('category_id', $categoryId)
            ->where('age_min', '<=', $child->age)
            ->where('age_max', '>=', $child->age)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}