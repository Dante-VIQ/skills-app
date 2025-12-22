// app/Livewire/ParentImpactDashboard.php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\ChildActivity;
use App\Models\SocialImpactProject;

class ParentImpactDashboard extends Component
{
    public $selectedChild;
    public $children;
    public $childContributions = [];
    public $globalImpact;
    public $recentProjects;

    public function mount()
    {
        $this->children = auth()->user()->children;
        $this->selectedChild = $this->children->first();
        $this->loadImpactData();
    }

    public function loadImpactData()
    {
        // Child's individual impact
        if ($this->selectedChild) {
            $this->childContributions = [
                'videos_watched' => ChildActivity::where('child_id', $this->selectedChild->id)->count(),
                'creative_projects' => ChildActivity::where('child_id', $this->selectedChild->id)
                                    ->where('activity_type', 'creative_project')->count(),
                'skills_learned' => $this->selectedChild->skills()->count(),
                'estimated_impact' => $this->calculateChildImpact($this->selectedChild)
            ];
        }

        // Global platform impact
        $this->globalImpact = [
            'total_children_helped' => SocialImpactProject::sum('children_impacted'),
            'schools_built' => SocialImpactProject::where('type', 'school')->count(),
            'labs_created' => SocialImpactProject::where('type', 'lab')->count(),
            'current_funding' => SocialImpactProject::sum('amount_raised')
        ];

        $this->recentProjects = SocialImpactProject::latest()->limit(3)->get();
    }

    private function calculateChildImpact(User $child)
    {
        // Algorithm: Each activity generates "impact points" that convert to real resources
        $totalActivities = ChildActivity::where('child_id', $child->id)->count();
        return [
            'books_provided' => floor($totalActivities * 0.1), // Every 10 activities = 1 book
            'learning_hours' => floor($totalActivities * 2), // Each activity = 2 hours of learning
            'contribution_amount' => $totalActivities * 0.25 // $0.25 per activity to projects
        ];
    }

    public function render()
    {
        return view('livewire.parent-impact-dashboard');
    }
}