<?php

namespace App\Http\Controllers;

use App\Models\SocialImpactProject;
use App\Models\ImpactContribution;
use Illuminate\Http\Request;

class ImpactController extends Controller
{
    public function show()
    {
        $totalImpact = SocialImpactProject::sum('current_amount');
        $completedProjects = SocialImpactProject::where('status', 'completed')->count();
        $activeProjects = SocialImpactProject::where('status', 'active')
            ->orderByDesc('current_amount')
            ->take(3)
            ->get();
        
        return view('impact.portal', compact('totalImpact', 'completedProjects', 'activeProjects'));
    }
    
    public function projects()
    {
        $projects = SocialImpactProject::with('contributions')
            ->where('status', 'active')
            ->paginate(12);
        
        return view('impact.projects', compact('projects'));
    }
    
    public function projectUpdates(SocialImpactProject $project)
    {
        return view('impact.project-updates', compact('project'));
    }
    
    public function familyImpact()
    {
        $this->middleware(['auth', 'verified', 'role:parent']);
        
        $parent = auth()->user();
        
        // Get impact contributions from all children
        $familyContributions = ImpactContribution::whereIn('user_id', $parent->children->pluck('id'))
            ->with(['user', 'project'])
            ->latest()
            ->paginate(20);
        
        $totalFamilyImpact = $familyContributions->sum('amount');
        
        return view('parent.impact.index', compact('familyContributions', 'totalFamilyImpact'));
    }
}