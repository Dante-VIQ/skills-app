<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ChildActivity;
use App\Models\ParentAssignment;

class ChildAssignmentPage extends Component
{
    public $assignment;
    public $answers = [];
    public $isCompleted = false;

    public function mount(ParentAssignment $assignment)
    {
        $this->assignment = $assignment;
        
        // Initialize answers array
        foreach ($assignment->content as $index => $question) {
            $this->answers[$index] = '';
        }
    }

    public function submitAssignment()
    {
        // Save child's responses
        $activity = ChildActivity::create([
            'child_id' => auth()->id(),
            'assignment_id' => $this->assignment->id,
            'activity_type' => 'parent_assignment',
            'activity_data' => $this->assignment->content,
            'child_responses' => $this->answers,
            'completed_at' => now(),
        ]);

        // Mark assignment as completed
        $this->assignment->markCompleted();

        $this->isCompleted = true;
        
        session()->flash('message', 'Great job! Assignment completed!');
    }

    public function render()
    {
        return view('livewire.child-assignment-page');
    }
}