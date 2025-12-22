<?php

namespace App\Http\Controllers;

use App\Models\SocialImpactProject;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LandingPageController extends Controller
{
    public function index()
    {
        // Get impact statistics for the hero section
        $impactStats = [
            'children_learning' => User::where('type', 'child')->count(),
            'projects_funded' => SocialImpactProject::where('amount_raised', '>=', DB::raw('target_amount'))->count(),
            'total_impact' => SocialImpactProject::sum('children_impacted'),
            'active_projects' => SocialImpactProject::where('is_active', true)->count(),
        ];

        return view('landing', compact('impactStats'));
    }

    public function impact()
    {
        $projects = SocialImpactProject::where('is_active', true)
            ->orderBy('amount_raised', 'desc')
            ->get();

        return view('impact', compact('projects'));
    }
}
