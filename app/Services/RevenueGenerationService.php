<?php

namespace App\Services;

use App\Models\User;
use App\Models\ChildActivity;
use App\Models\SocialImpactProject;
use App\Models\ChildImpactContribution;

class RevenueGenerationService
{
    private $revenueModels = [
        'advertising' => 0.25, // $0.25 per engaged video view
        'premium_content' => 2.00, // $2 per premium activity
        'corporate_sponsors' => 0.10, // $0.10 per skill mastered (sponsored by companies)
        'data_insights' => 0.05, // Anonymized learning pattern data (ethical)
    ];

    public function trackActivityRevenue(User $child, $activityType, $engagementLevel)
    {
        $revenue = $this->calculateRevenue($activityType, $engagementLevel);
        
        // Allocate to active projects
        $activeProject = SocialImpactProject::where('is_active', true)->first();
        
        if ($activeProject) {
            ChildImpactContribution::create([
                'child_id' => $child->id,
                'project_id' => $activeProject->id,
                'amount_generated' => $revenue,
                'impact_points' => $this->calculateImpactPoints($activityType),
                'contribution_type' => $this->getContributionType($activityType)
            ]);
            
            // Update project funding
            $activeProject->increment('amount_raised', $revenue);
        }
        
        return $revenue;
    }
    
    private function calculateRevenue($activityType, $engagementLevel)
    {
        $baseRate = $this->revenueModels['advertising']; // Default
        
        switch($activityType) {
            case 'premium_creative_project':
                return $this->revenueModels['premium_content'];
            case 'skill_mastery':
                return $this->revenueModels['corporate_sponsors'];
            case 'video_completion':
                return $baseRate * $engagementLevel;
            default:
                return $baseRate * 0.5; // Reduced for basic activities
        }
    }
}