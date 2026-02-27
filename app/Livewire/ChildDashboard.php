<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Services\ContentService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\ContentRecommendationService;

class ChildDashboard extends Component
{
    public $feed = [];
    public $showOnboarding = false;
    public $onboardingData = [];

    protected $contentRecommendationService;
    protected $contentService;

    public function boot(ContentRecommendationService $contentRecommendationService, ContentService $contentService)
    {
        $this->contentRecommendationService = $contentRecommendationService;
        $this->contentService = $contentService;
    }

    public function mount()
    {
        // Get the authenticated user (child)
        /** @var User $child */
        $child = Auth::user();

        if (!$child || !$child->isChild()) {
            // If not a child, redirect appropriately
            return $this->redirect(route('home'), navigate: true);
        }

        // Check if child needs onboarding
        if (!$child->onboarding_completed) {
            $this->showOnboarding = true;

            // If we're on dashboard but not onboarded, redirect to onboarding page
            if (request()->routeIs('child.dashboard')) {
                return $this->redirect(route('child.onboarding'), navigate: true);
            }
        } else {
            $this->loadFeed();
        }
    }

    public function completeOnboarding($onboardingData)
    {
        /** @var User $child */
        $child = Auth::user();

        if (!$child || !$child->isChild()) {
            return;
        }

        try {
            // Decode the onboarding data safely
            $data = json_decode($onboardingData, true, 512, JSON_THROW_ON_ERROR);

            // Update child's profile
            $child->update([
                'onboarding_completed' => true,
                'onboarding_data' => $data,
            ]);

            // If interests were passed, attach them
            if (isset($data['interests']) && is_array($data['interests'])) {
                // Assuming you have an interests relationship
                // $child->interests()->sync($this->mapInterestsToIds($data['interests']));
            }

            $this->showOnboarding = false;
            $this->loadFeed();

            // Show success message
            $this->dispatch('onboarding-completed',
                message: 'Onboarding completed successfully!'
            );

            // Redirect to dashboard after successful onboarding
            return $this->redirect(route('child.home'), navigate: true);

        } catch (\JsonException $e) {
            $this->dispatch('onboarding-error',
                message: 'Invalid onboarding data format.'
            );
        } catch (\Exception $e) {
            $this->dispatch('onboarding-error',
                message: 'An error occurred: ' . $e->getMessage()
            );
        }
    }

    public function loadFeed()
    {
        /** @var User $child */
        $child = Auth::user();

        if (!$child || !$child->isChild()) {
            $this->feed = [];
            return;
        }

        // For now, use placeholder data or service
        try {
            // Check if content service is available
            if ($this->contentService) {
                // Check if child has universal skill requirements to fulfill
                $universalRequirements = $this->getPendingUniversalRequirements($child);

                if ($universalRequirements->isNotEmpty()) {
                    // Show universal skill content first
                    $category = $universalRequirements->first()->category;
                    $this->feed = $this->contentService->getUniversalSkillContent($child, $category->id, 10);
                } else {
                    // Show normal interest-based feed
                    $this->feed = $this->contentService->getPersonalizedFeed($child, 20);
                }
            } else {
                // Fallback to placeholder feed
                $this->feed = $this->getPlaceholderFeed();
            }
        } catch (\Exception $e) {
            // Log error and show placeholder
            Log::error('Error loading feed: ' . $e->getMessage());
            $this->feed = $this->getPlaceholderFeed();
        }
    }

    private function getPendingUniversalRequirements($child)
    {
        // TODO: Implement this method properly
        // For now, return empty collection
        return collect();

        // Example implementation when ready:
        /*
        return \App\Models\UniversalSkillRequirement::where('child_id', $child->id)
            ->where('is_active', true)
            ->where('blocks_content', true)
            ->get()
            ->filter(function($requirement) use ($child) {
                $watchedThisWeek = \App\Models\WatchTimeRecord::where('child_id', $child->id)
                    ->where('category_id', $requirement->category_id)
                    ->where('watch_date', '>=', now()->startOfWeek())
                    ->sum('minutes_watched');

                return $watchedThisWeek < $requirement->required_minutes_per_week;
            });
        */
    }

    private function getPlaceholderFeed()
    {
        // Return placeholder feed while services are being developed
        return [
            [
                'id' => 1,
                'title' => 'Welcome to SkillDNA!',
                'description' => 'Start your learning journey with fun activities.',
                'type' => 'welcome',
                'duration' => '5 min',
                'icon' => '🎉'
            ],
            [
                'id' => 2,
                'title' => 'Learn About Robots',
                'description' => 'Discover how robots work and how to build them.',
                'type' => 'video',
                'duration' => '10 min',
                'icon' => '🤖'
            ],
            [
                'id' => 3,
                'title' => 'Creative Drawing Challenge',
                'description' => 'Draw your favorite animal in a creative way.',
                'type' => 'activity',
                'duration' => '15 min',
                'icon' => '🎨'
            ],
        ];
    }

    private function mapInterestsToIds(array $interestNames)
    {
        // TODO: Map interest names to database IDs
        return [];

        // Example:
        /*
        return \App\Models\Interest::whereIn('name', $interestNames)
            ->pluck('id')
            ->toArray();
        */
    }

    public function render()
    {
        // Check if user is authenticated and is a child
        $user = Auth::user();

        if (!$user || !$user->isChild()) {
            // If not a child, show appropriate view or redirect
            return view('livewire.auth-redirect');
        }

        return view('livewire.child-dashboard', [
            'child' => $user,
            'isOnboarding' => $this->showOnboarding,
            'feed' => $this->feed,
        ]);
    }
}
