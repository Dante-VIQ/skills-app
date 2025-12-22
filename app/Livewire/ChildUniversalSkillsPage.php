<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ContentCategory;
use App\Models\WatchTimeRecord;
use App\Models\UniversalSkillRequirement;
use App\Services\ContentRecommendationService;

class ChildUniversalSkillsPage extends Component
{
    public $requirements;
    public $categoryContent = [];

    public function mount()
    {
        $this->loadRequirements();
    }

    public function loadRequirements()
    {
        $this->requirements = UniversalSkillRequirement::where('child_id', auth()->id())
            ->where('is_active', true)
            ->with('category')
            ->get()
            ->map(function ($requirement) {
                $watchedThisWeek = WatchTimeRecord::where('child_id', auth()->id())
                    ->where('category_id', $requirement->category_id)
                    ->where('watch_date', '>=', now()->startOfWeek())
                    ->sum('minutes_watched');

                $requirement->watched_minutes = $watchedThisWeek;
                $requirement->remaining_minutes = max(0, $requirement->required_minutes_per_week - $watchedThisWeek);
                $requirement->progress = $requirement->required_minutes_per_week > 0 ? min(100, ($watchedThisWeek / $requirement->required_minutes_per_week) * 100) : 0;

                return $requirement;
            });
    }

    public function getCategoryContent($categoryId)
    {
        // Load content for this universal skill category
        $this->categoryContent[$categoryId] = ContentRecommendationService::getUniversalSkillContent(auth()->user(), $categoryId, 10);
    }

    public function recordWatchTime($contentId, $categoryId, $minutes)
    {
        WatchTimeRecord::create([
            'child_id' => auth()->id(),
            'content_id' => $contentId,
            'category_id' => $categoryId,
            'minutes_watched' => $minutes,
            'watch_date' => now(),
        ]);

        $this->loadRequirements();

        // Check if all requirements are now met
        if (
            $this->requirements->every(function ($req) {
                return $req->remaining_minutes === 0;
            })
        ) {
            session()->flash('message', 'All requirements completed! You can now explore your interests.');
            return redirect()->route('child.dashboard');
        }
    }

    public function render()
    {
        return view('livewire.child-universal-skills-page');
    }
}
