// app/Livewire/ParentCategoryManager.php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\ContentCategory;
use App\Models\UniversalSkillRequirement;
use App\Models\WatchTimeRecord;

class ParentCategoryManager extends Component
{
    public $selectedChild;
    public $children;
    public $categories;
    public $childWatchTime = [];
    public $universalRequirements = [];

    public function mount()
    {
        $this->children = auth()->user()->children;
        $this->selectedChild = $this->children->first();
        $this->categories = ContentCategory::where('is_universal', true)->get();
        $this->loadChildData();
    }

    public function selectChild($childId)
    {
        $this->selectedChild = User::find($childId);
        $this->loadChildData();
    }

    public function loadChildData()
    {
        if ($this->selectedChild) {
            // Load watch time per category for the current week
            $this->childWatchTime = WatchTimeRecord::where('child_id', $this->selectedChild->id)
                ->where('watch_date', '>=', now()->startOfWeek())
                ->selectRaw('category_id, SUM(minutes_watched) as total_minutes')
                ->groupBy('category_id')
                ->pluck('total_minutes', 'category_id')
                ->toArray();
                
            // Load existing requirements
            $this->universalRequirements = UniversalSkillRequirement::where('child_id', $this->selectedChild->id)
                ->get()
                ->keyBy('category_id');
        }
    }

    public function updateRequirement($categoryId, $minutesPerWeek)
    {
        if ($minutesPerWeek > 0) {
            UniversalSkillRequirement::updateOrCreate(
                [
                    'child_id' => $this->selectedChild->id,
                    'category_id' => $categoryId
                ],
                [
                    'parent_id' => auth()->id(),
                    'required_minutes_per_week' => $minutesPerWeek,
                    'blocks_content' => true,
                    'is_active' => true
                ]
            );
        } else {
            // Remove requirement if set to 0
            UniversalSkillRequirement::where('child_id', $this->selectedChild->id)
                ->where('category_id', $categoryId)
                ->delete();
        }
        
        $this->loadChildData();
        session()->flash('message', 'Requirement updated for ' . $this->selectedChild->getDisplayName());
    }

    public function render()
    {
        return view('livewire.parent-category-manager');
    }
}