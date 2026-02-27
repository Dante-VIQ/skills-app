<?php
// app/Http/Controllers/UniversalLearningController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserInterest;
use App\Models\CuratedResource;
use App\Models\LearningResource;
use App\Services\Discovery\AutomatedContentDiscovery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UniversalLearningController extends Controller
{
    protected $discoveryService;

    public function __construct(AutomatedContentDiscovery $discoveryService)
    {
        $this->discoveryService = $discoveryService;
    }

    /**
     * THE UNIVERSAL LEARNING PAGE - Same for everyone!
     */
    public function index()
    {
        $user = Auth::user();

        // Log the access
        Log::info('Universal learning page accessed', [
            'user_id' => $user->id,
            'user_type' => $user->role,
            'age' => $this->getUserAge($user)
        ]);

        // Step 1: Check if user has interests, if not redirect to onboarding
        if (!$this->userHasInterests($user)) {
            return redirect()->route('onboarding.interests')
                ->with('message', 'Tell us what you like to learn!');
        }

        // Step 2: Get personalized content based on user type and age
        $content = $this->getPersonalizedContent($user);

        // Step 3: Get user's learning context
        $context = $this->getUserContext($user);

        // Step 4: Return the universal view
        return view('learning.universal', [
            'content' => $content,
            'user' => $user,
            'context' => $context,
            'userType' => $this->getUserTypeLabel($user),
            'ageGroup' => $this->getAgeGroup($user),
        ]);
    }

    /**
     * Load more recommendations (AJAX endpoint)
     */
    public function loadMore(Request $request)
    {
        $user = Auth::user();
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 6);

        $interests = UserInterest::where('user_id', $user->id)
            ->active()
            ->get();

        $moreContent = $this->discoveryService->discoverForUser(
            user: $user,
            interests: $interests,
            limit: $offset + $limit
        )->slice($offset, $limit);

        return response()->json([
            'success' => true,
            'html' => view('partials.content-cards', ['items' => $moreContent])->render(),
            'has_more' => $moreContent->count() == $limit
        ]);
    }

    /**
     * Refresh recommendations
     */
    public function refresh()
    {
        $user = Auth::user();

        // Clear cache
        Cache::forget("user:{$user->id}:learning:content");

        // Get fresh content
        $interests = UserInterest::where('user_id', $user->id)->active()->get();
        $freshContent = $this->discoveryService->discoverForUser($user, $interests, 20);

        return response()->json([
            'success' => true,
            'html' => view('partials.content-sections', ['content' => $freshContent])->render()
        ]);
    }

    /**
     * Check if user has interests
     */
    protected function userHasInterests($user): bool
    {
        return UserInterest::where('user_id', $user->id)->exists();
    }

    /**
     * Get personalized content
     */
    protected function getPersonalizedContent($user)
    {
        $cacheKey = "user:{$user->id}:learning:content";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($user) {
            // Get user's active interests
            $interests = UserInterest::where('user_id', $user->id)
                ->active()
                ->get();

            // Discover content based on interests
            $discovered = $this->discoveryService->discoverForUser(
                user: $user,
                interests: $interests,
                limit: 30
            );

            // Organize content by type
            return [
                'recommended' => $discovered->take(8),
                'videos' => $discovered->where('type', 'youtube')->take(4),
                'tutorials' => $discovered->where('type', 'curated')->take(4),
                'interactive' => $discovered->where('type', 'internal')->take(4),
                'popular' => $this->getPopularContent($user),
                'trending' => $this->getTrendingContent(),
                'by_interest' => $this->getContentByInterest($user, $interests),
            ];
        });
    }

    /**
     * Get user context (stats, progress, etc.)
     */
    protected function getUserContext($user): array
    {
        $interactions = $user->interactions();

        $totalWatchTime = $interactions->sum('watch_duration') / 60; // minutes
        $completedCount = $interactions->where('completed', true)->count();

        return [
            'total_learning_time' => round($totalWatchTime),
            'completed_count' => $completedCount,
            'current_streak' => $this->calculateStreak($user),
            'recent_activity' => $this->getRecentActivity($user),
            'next_milestone' => $this->getNextMilestone($completedCount),
        ];
    }

    /**
     * Calculate learning streak
     */
    protected function calculateStreak($user): int
    {
        $dates = $user->interactions()
            ->selectRaw('DATE(created_at) as date')
            ->distinct()
            ->orderBy('date', 'desc')
            ->pluck('date');

        if ($dates->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $currentDate = now()->toDateString();

        foreach ($dates as $date) {
            if ($date == $currentDate) {
                $streak++;
                $currentDate = date('Y-m-d', strtotime($currentDate . ' -1 day'));
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Get recent activity
     */
    protected function getRecentActivity($user)
    {
        return $user->interactions()
            ->with('resource')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($i) => [
                'title' => $i->resource->title ?? 'Unknown',
                'type' => $i->interaction_type,
                'time' => $i->created_at->diffForHumans()
            ]);
    }

    /**
     * Get next milestone
     */
    protected function getNextMilestone($completed)
    {
        $milestones = [10, 25, 50, 100, 250, 500];

        foreach ($milestones as $milestone) {
            if ($completed < $milestone) {
                return [
                    'next' => $milestone,
                    'remaining' => $milestone - $completed,
                    'progress' => ($completed / $milestone) * 100
                ];
            }
        }

        return null;
    }

    /**
     * Get popular content for user's age group
     */
    protected function getPopularContent($user)
    {
        $difficulty = $this->ageToDifficulty($this->getUserAge($user));

        return CuratedResource::where('difficulty_level', $difficulty)
            ->where('is_safe', true)
            ->with('source')
            ->orderBy('popularity_score', 'desc')
            ->limit(6)
            ->get()
            ->map(fn($r) => [
                'type' => 'curated',
                'id' => $r->id,
                'title' => $r->title,
                'description' => $r->summary,
                'url' => route('resources.redirect', $r->id),
                'thumbnail' => $r->thumbnail_url,
                'source' => $r->source->name,
                'source_icon' => $this->getSourceIcon($r->source->slug),
                'reason' => 'Popular in your age group'
            ]);
    }

    /**
     * Get trending content
     */
    protected function getTrendingContent()
    {
        return LearningResource::where('is_published', true)
            ->orderBy('view_count', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($r) => [
                'type' => 'internal',
                'id' => $r->id,
                'title' => $r->title,
                'description' => $r->summary,
                'url' => route('learning.show', $r->id),
                'thumbnail' => $r->thumbnail_url,
                'views' => $r->view_count,
                'reason' => 'Trending now'
            ]);
    }

    /**
     * Get content grouped by interest
     */
    protected function getContentByInterest($user, $interests)
    {
        $result = [];

        foreach ($interests->take(3) as $interest) {
            $content = $this->discoveryService->searchByTopic(
                topic: $interest->category,
                subTopic: $interest->subcategory,
                limit: 3,
                user: $user
            );

            if ($content->isNotEmpty()) {
                $result[$interest->category] = $content;
            }
        }

        return $result;
    }

    /**
     * Helper methods
     */
    protected function getUserAge($user): int
    {
        if ($user->role === 'child' && $user->age) {
            return $user->age;
        }

        return $user->role === 'child' ? 10 : 25;
    }

    protected function getAgeGroup($user): string
    {
        $age = $this->getUserAge($user);

        return match(true) {
            $age < 8 => 'Early Learner (5-7)',
            $age < 12 => 'Elementary (8-11)',
            $age < 15 => 'Middle School (12-14)',
            $age < 18 => 'High School (15-17)',
            default => 'Adult Learner (18+)'
        };
    }

    protected function ageToDifficulty($age): string
    {
        return match(true) {
            $age < 8 => 'beginner',
            $age < 13 => 'intermediate',
            default => 'advanced'
        };
    }

    protected function getUserTypeLabel($user): string
    {
        return match($user->role) {
            'child' => 'Student',
            'parent' => 'Parent',
            'tutor' => 'Educator',
            default => 'Learner'
        };
    }

    protected function getSourceIcon($slug): string
    {
        return match($slug) {
            'w3schools' => '🌐',
            'khan-academy' => '📚',
            'mdn' => '📘',
            'freecodecamp' => '🔥',
            default => '🔗'
        };
    }
}
