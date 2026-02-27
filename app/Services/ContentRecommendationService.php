<?php

namespace App\Services;

use App\Models\User;
use App\Models\ContentModule;
use App\Models\ContentCategory;
use App\Models\ChildActivity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ContentRecommendationService
{
    /**
     * Get personalized content recommendations for a child
     */
    public function getRecommendations(User $child, int $limit = 10): Collection
    {
        // Cache recommendations for 1 hour to improve performance
        $cacheKey = "recommendations.{$child->id}";

        return Cache::remember($cacheKey, now()->addHour(), function () use ($child, $limit) {
            return $this->generateRecommendations($child, $limit);
        });
    }

    /**
     * Generate recommendations using multiple strategies
     */
    protected function generateRecommendations(User $child, int $limit): Collection
    {
        // Collect different recommendation sources
        $recommendations = collect();

        // Strategy 1: Based on interests (40% weight)
        $interestBased = $this->getInterestBasedRecommendations($child, ceil($limit * 0.4));
        $recommendations = $recommendations->merge($interestBased);

        // Strategy 2: Collaborative filtering (30% weight)
        $collaborative = $this->getCollaborativeRecommendations($child, ceil($limit * 0.3));
        $recommendations = $recommendations->merge($collaborative);

        // Strategy 3: Popular content (20% weight)
        $popular = $this->getPopularRecommendations($child, ceil($limit * 0.2));
        $recommendations = $recommendations->merge($popular);

        // Strategy 4: New content (10% weight)
        $new = $this->getNewContentRecommendations($child, ceil($limit * 0.1));
        $recommendations = $recommendations->merge($new);

        // Remove duplicates and limit
        return $recommendations->unique('id')->take($limit)->values();
    }

    /**
     * Get recommendations based on child's interests
     */
    protected function getInterestBasedRecommendations(User $child, int $limit): Collection
    {
        $interests = $child->interests ?? [];

        if (empty($interests)) {
            return collect();
        }

        // Get content from categories matching interests
        return ContentModule::where('is_active', true)
            ->whereHas('category', function ($query) use ($interests) {
                $query->whereIn('name', $interests);
            })
            ->whereNotIn('id', $this->getWatchedContentIds($child))
            ->with(['category', 'skills'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Get collaborative filtering recommendations (what similar kids like)
     */
    protected function getCollaborativeRecommendations(User $child, int $limit): Collection
    {
        // Find similar children based on interests and watch history
        $similarChildren = $this->findSimilarChildren($child);

        if ($similarChildren->isEmpty()) {
            return collect();
        }

        // Get content that similar children watched but this child hasn't
        $watchedBySimilar = ChildActivity::whereIn('child_id', $similarChildren->pluck('id'))
            ->where('type', 'module_completed')
            ->pluck('content_module_id')
            ->unique();

        $watchedByChild = $this->getWatchedContentIds($child);

        $recommendedIds = $watchedBySimilar->diff($watchedByChild)->take($limit);

        return ContentModule::whereIn('id', $recommendedIds)
            ->where('is_active', true)
            ->with(['category', 'skills'])
            ->get();
    }

    /**
     * Get popular content recommendations
     */
    protected function getPopularRecommendations(User $child, int $limit): Collection
    {
        return ContentModule::where('is_active', true)
            ->whereNotIn('id', $this->getWatchedContentIds($child))
            ->whereHas('activities', function ($query) {
                $query->where('type', 'module_completed')
                      ->where('created_at', '>=', now()->subDays(30));
            })
            ->withCount(['activities as popularity_score' => function ($query) {
                $query->where('type', 'module_completed');
            }])
            ->with(['category', 'skills'])
            ->orderByDesc('popularity_score')
            ->limit($limit)
            ->get();
    }

    /**
     * Get new content recommendations
     */
    protected function getNewContentRecommendations(User $child, int $limit): Collection
    {
        return ContentModule::where('is_active', true)
            ->whereNotIn('id', $this->getWatchedContentIds($child))
            ->where('created_at', '>=', now()->subDays(14))
            ->with(['category', 'skills'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get content based on skill gaps
     */
    public function getSkillGapRecommendations(User $child, int $limit = 5): Collection
    {
        $currentSkills = $child->skills ?? [];

        // Find categories where child has low progress
        $weakCategories = ContentCategory::whereHas('watchTimeRecords', function ($query) use ($child) {
            $query->where('child_id', $child->id);
        })->get()->filter(function ($category) use ($child) {
            $totalMinutes = $category->watchTimeRecords()
                ->where('child_id', $child->id)
                ->sum('duration');

            // If less than 30 minutes watched in this category, it's a gap
            return $totalMinutes < 30;
        });

        if ($weakCategories->isEmpty()) {
            return collect();
        }

        return ContentModule::where('is_active', true)
            ->whereIn('content_category_id', $weakCategories->pluck('id'))
            ->whereNotIn('id', $this->getWatchedContentIds($child))
            ->with(['category', 'skills'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Get next in series recommendations (for series-based content)
     */
    public function getNextInSeriesRecommendations(User $child, int $limit = 3): Collection
    {
        // Get the last watched content in each series
        $lastWatched = ChildActivity::where('child_id', $child->id)
            ->where('type', 'module_completed')
            ->whereHas('contentModule', function ($query) {
                $query->whereNotNull('series_id');
            })
            ->with('contentModule')
            ->latest()
            ->get()
            ->groupBy('contentModule.series_id')
            ->map(function ($activities) {
                return $activities->first();
            });

        $nextInSeries = collect();

        foreach ($lastWatched as $activity) {
            $nextModule = ContentModule::where('series_id', $activity->contentModule->series_id)
                ->where('order', '>', $activity->contentModule->order)
                ->where('is_active', true)
                ->orderBy('order')
                ->first();

            if ($nextModule) {
                $nextInSeries->push($nextModule);
            }
        }

        return $nextInSeries->take($limit);
    }

    /**
     * Get trending content across the platform
     */
    public function getTrendingContent(int $limit = 10): Collection
    {
        return ContentModule::where('is_active', true)
            ->whereHas('activities', function ($query) {
                $query->where('type', 'module_completed')
                      ->where('created_at', '>=', now()->subDays(7));
            })
            ->withCount(['activities as weekly_views' => function ($query) {
                $query->where('created_at', '>=', now()->subDays(7));
            }])
            ->with(['category', 'skills'])
            ->orderByDesc('weekly_views')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recommendations based on age appropriateness
     */
    protected function filterByAge(Collection $content, User $child): Collection
    {
        return $content->filter(function ($module) use ($child) {
            $age = $child->age;

            if (!$module->min_age && !$module->max_age) {
                return true;
            }

            if ($module->min_age && $age < $module->min_age) {
                return false;
            }

            if ($module->max_age && $age > $module->max_age) {
                return false;
            }

            return true;
        })->values();
    }

    /**
     * Find children similar to this child
     */
    protected function findSimilarChildren(User $child): Collection
    {
        $interests = $child->interests ?? [];
        $age = $child->age;

        if (empty($interests)) {
            return collect();
        }

        // Find children with similar interests and age range
        return User::role('child')
            ->where('id', '!=', $child->id)
            ->where('is_active', true)
            ->whereNotNull('interests')
            ->where(function ($query) use ($interests) {
                foreach ($interests as $interest) {
                    $query->orWhereJsonContains('interests', $interest);
                }
            })
            ->whereBetween('date_of_birth', [
                now()->subYears($age + 2),
                now()->subYears(max(0, $age - 2))
            ])
            ->limit(20)
            ->get();
    }

    /**
     * Get IDs of content the child has already watched
     */
    protected function getWatchedContentIds(User $child): Collection
    {
        return $child->activities()
            ->where('type', 'module_completed')
            ->pluck('content_module_id');
    }

    /**
     * Clear recommendations cache for a child
     */
    public function clearCache(User $child): void
    {
        Cache::forget("recommendations.{$child->id}");
    }
}
