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
        
        // Get all skills the child has interacted with
        $skills = $this->getChildSkillsReport($child);
        
        // Get universal skill requirements progress
        $universalRequirements = $child->skillRequirements()
            ->with('category')
            ->get()
            ->map(function ($requirement) {
                $requirement->progress_percentage = $requirement->progressPercentage();
                return $requirement;
            });
        
        return view('child.progress.skill-report', compact('skills', 'universalRequirements'));
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
                ->sum('amount'),
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
        // This would query skills from completed modules
        // For now, return the child's skills array
        return collect($child->skills ?? [])->map(function ($skill) {
            return [
                'name' => $skill,
                'level' => 'beginner', // This would be calculated
                'progress' => rand(20, 100), // This would be calculated
                'last_practiced' => now()->subDays(rand(0, 30))->format('Y-m-d'),
            ];
        });
    }
}