<?php

namespace App\Http\Controllers\Child;


use App\Models\UserInterest;
use App\Services\Discovery\AutomatedContentDiscovery;
use App\Services\ML\ResourceReferralService;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LearningController extends Controller
{
     protected $discoveryService;
    protected $referralService;
    
    public function __construct(
        AutomatedContentDiscovery $discoveryService,
        ResourceReferralService $referralService
    ) {
        $this->discoveryService = $discoveryService;
        $this->referralService = $referralService;
    }

    /**
     * THE UNIVERSAL LEARNING PAGE - Same for everyone!
     */
    public function index()
    {
        $user = Auth::user();
        
        // Step 1: Check if user has interests, if not redirect to onboarding
        if (!$this->userHasInterests($user)) {
            return redirect()->route('onboarding.interests')
                ->with('message', 'Tell us what you like to learn!');
        }
        
        // Step 2: Get personalized content based on user type and age
        $content = $this->getPersonalizedContent($user);
        
        // Step 3: Get user's learning context
        $context = $this->getUserContext($user);
        
        // Step 4: Return the SAME view for everyone
        return view('Learn.index', [
            'content' => $content,
            'user' => $user,
            'context' => $context,
            'userType' => $this->getUserTypeLabel($user),
            'ageGroup' => $this->getAgeGroup($user),
        ]);
    }

    /**
     * Get personalized content for ANY user
     */
    protected function getPersonalizedContent($user)
    {
        $cacheKey = "user:{$user->id}:learning:content";
        
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($user) {
            // Get user's active interests
            $interests = UserInterest::where('user_id', $user->id)
                ->active()
                ->get();
            
            $content = [
                'recommended' => $this->getRecommendedContent($user, $interests),
                'popular_in_age' => $this->getPopularForAgeGroup($user),
                'based_on_interests' => $this->getInterestBasedContent($user, $interests),
                'trending' => $this->getTrendingContent($user),
                'skill_gaps' => $this->getSkillGapContent($user),
            ];
            
            // Add user-type specific content
            switch ($user->role) {
                case 'parent':
                    $content['for_parents'] = $this->getParentingContent($user);
                    break;
                case 'tutor':
                    $content['teaching_materials'] = $this->getTeachingMaterials($user);
                    break;
            }
            
            return $content;
        });
    }

    /**
     * Get user context (progress, stats, etc.)
     */
    protected function getUserContext($user)
    {
        $context = [
            'total_learning_time' => $this->getTotalLearningTime($user),
            'completed_count' => $this->getCompletedCount($user),
            'current_streak' => $this->getLearningStreak($user),
            'next_milestone' => $this->getNextMilestone($user),
            'recent_activity' => $this->getRecentActivity($user),
        ];
        
        // Add user-type specific context
        if ($user->role === 'parent') {
            $context['children_progress'] = $this->getChildrenProgress($user);
        }
        
        if ($user->role === 'tutor') {
            $context['students_count'] = $this->getStudentCount($user);
        }
        
        return $context;
    }

    /**
     * Check if user has interests
     */
    protected function userHasInterests($user)
    {
        return UserInterest::where('user_id', $user->id)->exists();
    }

    /**
     * Get content based on age group
     */
    protected function getRecommendedContent($user, $interests)
    {
        $age = $this->getUserAge($user);
        $difficulty = $this->ageToDifficulty($age);
        
        // Search across all sources
        return $this->discoveryService->searchMultiSource(
            interests: $interests->pluck('category')->toArray(),
            difficulty: $difficulty,
            limit: 10,
            user: $user
        );
    }

    /**
     * Get popular content for user's age group
     */
    protected function getPopularForAgeGroup($user)
    {
        $age = $this->getUserAge($user);
        $difficulty = $this->ageToDifficulty($age);
        
        return \App\Models\CuratedResource::where('difficulty_level', $difficulty)
            ->where('is_safe', true)
            ->orderBy('popularity_score', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($resource) {
                return [
                    'type' => 'curated',
                    'title' => $resource->title,
                    'description' => $resource->summary,
                    'url' => route('resources.redirect', $resource->id),
                    'source' => $resource->source->name,
                    'thumbnail' => $resource->thumbnail_url,
                    'reason' => 'Popular in your age group'
                ];
            });
    }

    /**
     * Get content based on specific interests
     */
    protected function getInterestBasedContent($user, $interests)
    {
        $results = [];
        
        foreach ($interests->take(3) as $interest) {
            $content = $this->discoveryService->searchByTopic(
                topic: $interest->category,
                subTopic: $interest->subcategory,
                limit: 3,
                user: $user
            );
            
            $results[$interest->category] = $content;
        }
        
        return $results;
    }

    /**
     * Get trending content
     */
    protected function getTrendingContent($user)
    {
        return \App\Models\LearningResource::where('is_published', true)
            ->orderBy('view_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($resource) {
                return [
                    'type' => 'internal',
                    'id' => $resource->id,
                    'title' => $resource->title,
                    'description' => $resource->summary,
                    'url' => route('learning.show', $resource->id),
                    'views' => $resource->view_count,
                    'reason' => 'Trending now'
                ];
            });
    }

    /**
     * Get content to fill skill gaps
     */
    protected function getSkillGapContent($user)
    {
        // Analyze user's learning history to find gaps
        $completedTopics = $user->completedResources()
            ->pluck('topic')
            ->unique()
            ->toArray();
        
        $allTopics = ['math', 'science', 'programming', 'robotics', 'language'];
        $gaps = array_diff($allTopics, $completedTopics);
        
        if (empty($gaps)) {
            return collect();
        }
        
        return $this->discoveryService->searchMultiSource(
            interests: $gaps,
            difficulty: 'beginner',
            limit: 3
        );
    }

    /**
     * Helper methods
     */
    protected function getUserAge($user)
    {
        if ($user->role === 'child') {
            return $user->age ?? 10;
        }
        
        // For parents/tutors, return adult age
        return 25;
    }

    protected function getAgeGroup($user)
    {
        $age = $this->getUserAge($user);
        
        if ($age < 8) return 'Early Learner (5-7)';
        if ($age < 12) return 'Elementary (8-11)';
        if ($age < 15) return 'Middle School (12-14)';
        if ($age < 18) return 'High School (15-17)';
        return 'Adult Learner (18+)';
    }

    protected function ageToDifficulty($age)
    {
        if ($age < 8) return 'beginner';
        if ($age < 12) return 'intermediate';
        return 'advanced';
    }

    protected function getUserTypeLabel($user)
    {
        $labels = [
            'child' => 'Student',
            'parent' => 'Parent',
            'tutor' => 'Educator',
        ];
        
        return $labels[$user->role] ?? 'Learner';
    }

    protected function getTotalLearningTime($user)
    {
        return $user->interactions()
            ->sum('watch_duration') / 3600; // hours
    }

    protected function getCompletedCount($user)
    {
        return $user->interactions()
            ->where('completed', true)
            ->count();
    }

    protected function getLearningStreak($user)
    {
        // Calculate consecutive days with learning
        $dates = $user->interactions()
            ->selectRaw('DATE(created_at) as date')
            ->distinct()
            ->orderBy('date', 'desc')
            ->pluck('date');
        
        $streak = 0;
        $current = now()->toDateString();
        
        foreach ($dates as $date) {
            if ($date == $current) {
                $streak++;
                $current = date('Y-m-d', strtotime($current . ' -1 day'));
            } else {
                break;
            }
        }
        
        return $streak;
    }

    protected function getNextMilestone($user)
    {
        $completed = $this->getCompletedCount($user);
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

    protected function getRecentActivity($user)
    {
        return $user->interactions()
            ->with('resource')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($interaction) {
                return [
                    'title' => $interaction->resource->title,
                    'type' => $interaction->interaction_type,
                    'time' => $interaction->created_at->diffForHumans()
                ];
            });
    }
}
