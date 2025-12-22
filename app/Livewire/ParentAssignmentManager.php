<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\ParentAssignment;
use App\Models\ChildActivity;

class ParentAssignmentManager extends Component
{
    public $selectedChild;
    public $children;
    public $assignments = [];
    public $childActivities = [];

    // Assignment creation
    public $showAssignmentModal = false;
    public $newAssignment = [
        'title' => '',
        'description' => '',
        'type' => 'quiz',
        'due_date' => '',
        'blocks_content' => true,
        'questions' => []
    ];

    public function mount()
    {
        $this->children = auth()->user()->children;
        $this->selectedChild = $this->children->first();
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
            $this->assignments = ParentAssignment::where('child_id', $this->selectedChild->id)
                ->orderBy('created_at', 'desc')
                ->get();
                
            $this->childActivities = ChildActivity::where('child_id', $this->selectedChild->id)
                ->with('assignment')
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }

    public function addQuestion()
    {
        $this->newAssignment['questions'][] = [
            'question' => '',
            'type' => 'multiple_choice',
            'options' => ['', '', '', ''],
            'correct_answer' => 0
        ];
    }

    public function createAssignment()
    {
        $this->validate([
            'newAssignment.title' => 'required|string|max:255',
            'newAssignment.due_date' => 'nullable|date|after:now',
        ]);

        $assignment = ParentAssignment::create([
            'parent_id' => auth()->id(),
            'child_id' => $this->selectedChild->id,
            'title' => $this->newAssignment['title'],
            'description' => $this->newAssignment['description'],
            'type' => $this->newAssignment['type'],
            'content' => $this->newAssignment['questions'],
            'due_date' => $this->newAssignment['due_date'],
            'blocks_content' => $this->newAssignment['blocks_content'],
            'status' => 'assigned',
        ]);

        $this->showAssignmentModal = false;
        $this->reset(['newAssignment']);
        $this->loadChildData();
        
        session()->flash('message', 'Assignment created and sent to ' . $this->selectedChild->getDisplayName());
    }

    public function viewChildAnswers($activityId)
    {
        $activity = ChildActivity::find($activityId);
        // Show modal with child's detailed answers
        $this->dispatch('show-child-answers', activity: $activity);
    }

    public function render()
    {
        return view('livewire.parent-assignment-manager');
    }
}