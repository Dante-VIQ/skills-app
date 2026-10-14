<?php

namespace App\Http\Controllers\Child;

use App\Models\ChildActivity;
use App\Models\WatchTimeRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProgressController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:child', 'check.onboarding']);
    }

    public function index()
    {
        $child = auth()->user();

        // Weekly progress
        $weeklyProgress = $this->getWeeklyProgress($child);

        // Skills progress
        $skillsProgress = $this->getSkillsProgress($child);

        // Impact contributions
        $impactContributions = $child->impactContributions()
            ->with('project')
            ->latest()
            ->take(10)
            ->get();

        // Learning streak
        $streak = $this->calculateLearningStreak($child);

        return view('child.progress.index', compact(
            'weeklyProgress',
            'skillsProgress',
            'impactContributions',
            'streak'
        ));
    }

    public function activityLog()
    {
        $child = auth()->user();

        $activities = $child->activities()
            ->with(['contentModule.category', 'assignment'])
            ->latest()
            ->paginate(20);

        return view('child.progress.activity-log', compact('activities'));
    }

public function skillReport()
{
    $child = auth()->user();

    // Get skills from activity analysis (no separate requirements table yet)
    $skills = $this->getChildSkillsReport($child);

    return view('child.progress.skills', compact('skills'));
}

    private function getWeeklyProgress($child)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return [
            'activities_count' => $child->activities()
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->count(),
            'watch_time' => $child->watchTimeRecords()
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->sum('duration'),
            'assignments_completed' => $child->receivedAssignments()
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$startOfWeek, $endOfWeek])
                ->count(),
            'impact_generated' => $child->impactContributions()
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->sum('contribution_value'),   // ✅ matches migration
        ];
    }

    private function getSkillsProgress($child)
    {
        $skills = $child->skills ?? [];

        return collect($skills)->map(function ($skill) use ($child) {
            return [
                'name' => $skill,
                'activities_count' => $child->activities()
                    ->whereHas('contentModule', function ($query) use ($skill) {
                        $query->whereHas('skills', function ($q) use ($skill) {
                            $q->where('name', $skill);
                        });
                    })
                    ->count(),
                'last_activity' => $child->activities()
                    ->whereHas('contentModule', function ($query) use ($skill) {
                        $query->whereHas('skills', function ($q) use ($skill) {
                            $q->where('name', $skill);
                        });
                    })
                    ->latest()
                    ->first()
                    ->created_at ?? null,
            ];
        });
    }

    private function calculateLearningStreak($child)
    {
        $streak = 0;
        $date = Carbon::today();

        while (true) {
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

    private function getChildSkillsReport($child)
    {
        // Report observed activity only. Skill mastery should not be invented
        // until the application has a validated assessment model.
        return collect($child->skills ?? [])->map(function ($skill) use ($child) {
            $activities = $child->activities()
                ->whereHas('contentModule', function ($query) use ($skill) {
                    $query->whereHas('skills', function ($query) use ($skill) {
                        $query->where('name', $skill);
                    });
                });

            $activityCount = (clone $activities)->count();
            $lastPracticed = (clone $activities)->latest()->value('created_at');

            return [
                'name' => $skill,
                'level' => 'Not assessed',
                'progress' => null,
                'activities_count' => $activityCount,
                'last_practiced' => $lastPracticed ? Carbon::parse($lastPracticed)->format('Y-m-d') : null,
            ];
        });
    }

    
}
