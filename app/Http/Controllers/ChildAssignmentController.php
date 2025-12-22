<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChildActivity;
use App\Models\ParentAssignment;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ChildAssignmentController extends Controller
{
    public function show(ParentAssignment $assignment)
    {
        // Verify child owns this assignment
        if ($assignment->child_id !== Auth::id()) {
            abort(403);
        }

        return view('child.assignment.show', compact('assignment'));
    }

    public function submit(Request $request, ParentAssignment $assignment)
    {
        // Verify child owns this assignment
        if ($assignment->child_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'answers' => 'required|array',
        ]);

        // Save child's responses
        ChildActivity::create([
            'child_id' => Auth::id(),
            'assignment_id' => $assignment->id,
            'activity_type' => 'parent_assignment',
            'activity_data' => $assignment->content,
            'child_responses' => $request->answers,
            'completed_at' => now(),
        ]);

        // Mark assignment as completed
        $assignment->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->route('child.dashboard')->with('success', 'Assignment submitted successfully!');
    }
}