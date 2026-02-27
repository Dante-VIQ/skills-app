<?php

namespace App\Http\Controllers;

use App\Models\ChildActivity;
use App\Models\ContentModule;
use App\Models\ParentAssignment;
use App\Models\User;
use App\Services\ContentRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ChildDashboardController extends Controller
{
    protected $recommendationService;

    public function __construct(ContentRecommendationService $recommendationService)
    {
        $this->middleware(['auth', 'verified', 'role:child', 'check.onboarding']);
        $this->recommendationService = $recommendationService;
    }

    public function index()
    {
        $child = auth()->user();

        // Get pending assignments
        $pendingAssignments = ParentAssignment::where('child_id', $child->id)
            ->where('status', 'pending')
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->take(5)
            ->get();

        // Get recommended content using the service
        $recommendedContent = $this->recommendationService->getRecommendations($child, 6);

        // Get trending content
        $trendingContent = $this->recommendationService->getTrendingContent(3);

        // Get skill gap recommendations
        $skillGapContent = $this->recommendationService->getSkillGapRecommendations($child, 3);

        // Get next in series recommendations
        $nextInSeries = $this->recommendationService->getNextInSeriesRecommendations($child, 2);

        // Get recent activities
        $recentActivities = ChildActivity::where('child_id', $child->id)
            ->with('contentModule.category')
            ->latest()
            ->take(5)
            ->get();

        // Calculate learning stats
        $stats = $this->calculateStats($child);

        return view('child.home', compact(
            'pendingAssignments',
            'recommendedContent',
            'trendingContent',
            'skillGapContent',
            'nextInSeries',
            'recentActivities',
            'stats'
        ));
    }

    public function refreshRecommendations()
    {
        $child = auth()->user();
        $this->recommendationService->clearCache($child);

        return redirect()->route('child.dashboard')
            ->with('success', 'Recommendations refreshed!');
    }

    protected function calculateStats(User $child): array
    {
        $totalWatchTime = $child->watchTimeRecords()->sum('duration');
        $completedModules = $child->activities()
            ->where('type', 'module_completed')
            ->count();

        // Calculate streak
        $streak = $this->calculateLearningStreak($child);

        // Calculate total impact
        // $totalImpact = $child->impactContributions()->sum('amount');

        // Get category distribution
        $categoryDistribution = $child->activities()
            ->where('type', 'module_completed')
            ->with('contentModule.category')
            ->get()
            ->groupBy('contentModule.category.name')
            ->map(function ($activities) {
                return $activities->count();
            });

        return [
            'total_watch_time' => $totalWatchTime,
            'completed_modules' => $completedModules,
            'streak_days' => $streak,
            // 'impact_contributed' => $totalImpact,
            'category_distribution' => $categoryDistribution,
            'average_score' => $this->calculateAverageScore($child),
        ];
    }

    protected function calculateLearningStreak(User $child): int
    {
        $streak = 0;
        $date = now()->startOfDay();

        while ($streak < 30) {
            $hasActivity = $child->activities()
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->exists();

            if ($hasActivity) {
                $streak++;
                $date->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }

    protected function calculateAverageScore(User $child): float
    {
        $activities = $child->activities()
            ->whereNotNull('score')
            ->whereNotNull('max_score')
            ->get();

        if ($activities->isEmpty()) {
            return 0;
        }

        $totalPercentage = $activities->sum(function ($activity) {
            return ($activity->score / $activity->max_score) * 100;
        });

        return round($totalPercentage / $activities->count(), 1);
    }
}
