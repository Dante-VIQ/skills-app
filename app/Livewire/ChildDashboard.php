<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Services\ContentRecommendationService;
use App\Services\ContentService;

class ChildDashboard extends Component
{
    public $feed = [];
    public $showOnboarding = false;
    public $onboardingData = [];

    protected $contentRecommendationService;
    protected $contentService;

    public function mount(ContentRecommendationService $contentRecommendationService)
    {
        $this->contentRecommendationService = $contentRecommendationService;
        // $this->contentService = $contentService;
        
        // Check if child needs onboarding
        $child = Auth::guard('child')->user();
        
        if ($child && empty($child->onboarding_completed)) {
            $this->showOnboarding = true;
        } else {
            $this->loadFeed();
        }
    }

public function completeOnboarding($onboardingData)
    {
        // Validate and save onboarding data
        $child = Auth::guard('child')->user();
        
        if ($child) {
            $child->update([
                'onboarding_completed' => true,
                'interests' => json_decode($onboardingData, true)['interests'] ?? [],
                'onboarding_data' => json_decode($onboardingData, true),
            ]);
            
            $this->showOnboarding = false;
            $this->loadFeed();
            
            // Show success message
            $this->dispatch('onboarding-completed');
        }
    }

    public function loadFeed()
    {
        $child = Auth::guard('child')->user();

        if (!$child) {
            $this->feed = [];
            return;
        }

        // Check if child has universal skill requirements to fulfill
        $universalRequirements = $this->getPendingUniversalRequirements($child);

        if ($universalRequirements->isNotEmpty()) {
            // Show universal skill content first (enforced learning)
            $category = $universalRequirements->first()->category;
            $this->feed = $this->contentService->getUniversalSkillContent($child, $category->id, 10);
        } else {
            // Show normal interest-based feed
            $this->feed = $this->contentService->getPersonalizedFeed($child, 20);
        }
    }

    private function getPendingUniversalRequirements($child)
    {
        return collect();
    }

    public function render()
    {
        return view('livewire.child-dashboard');
    }
}