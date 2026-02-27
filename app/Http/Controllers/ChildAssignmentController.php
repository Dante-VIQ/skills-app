<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChildActivity;
use App\Models\ParentAssignment;
use Illuminate\Routing\Controller;

class ChildAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:child']);
    }
    
    public function show(ParentAssignment $assignment)
    {
        // Use policy to check if assignment belongs to this child
        $this->authorize('view', $assignment);

        return view('child.assignment.show', compact('assignment'));
    }

    public function submit(Request $request, ParentAssignment $assignment)
    {
        $this->authorize('submit', $assignment);

        $request->validate([
            'response' => 'required|string|min:10',
            'attachments.*' => 'nullable|file|max:5120',
        ]);

        // Create activity record
        $activity = ChildActivity::create([
            'child_id' => auth()->id(),
            'assignment_id' => $assignment->id,
            'type' => 'assignment_submission',
            'title' => 'Assignment: ' . $assignment->title,
            'description' => $request->response,
            'status' => 'completed',
        ]);

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('assignment-submissions');
                $activity->attachments()->create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        // Update assignment status
        $assignment->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->route('child.dashboard')
            ->with('success', 'Assignment submitted successfully!');
    }
}
