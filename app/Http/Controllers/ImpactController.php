<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SocialImpactProject;

class ImpactController extends Controller
{
    public function show()
    {
        $totalImpact = [
            'total_raised' => SocialImpactProject::sum('amount_raised'),
            'total_impacted' => SocialImpactProject::sum('children_impacted'),
            'completed_projects' => SocialImpactProject::where('amount_raised', '>=', DB::raw('target_amount'))->count(),
            'active_projects' => SocialImpactProject::where('is_active', true)->count(),
        ];

        return view('impact.show', compact('totalImpact'));
    }

    public function projects()
    {
        $projects = SocialImpactProject::orderBy('created_at', 'desc')->paginate(12);
        return view('impact.projects', compact('projects'));
    }

    public function projectUpdates(SocialImpactProject $project)
    {
        return view('impact.project-updates', compact('project'));
    }
}