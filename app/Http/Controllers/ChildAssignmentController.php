<?php
// app/Http/Controllers/ChildAssignmentController.php

namespace App\Http\Controllers;

use App\Models\ParentAssignment;
use App\Models\ChildActivity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChildAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:child']);
    }

    /**
     * List assignments assigned to the authenticated child.
     */
    public function index()
    {
        $child = Auth::user();

        $assignments = ParentAssignment::where('child_id', $child->id)
            ->latest()
            ->paginate(10);

        // Group for convenience in the view
        $pending = ParentAssignment::where('child_id', $child->id)
            ->where('status', 'pending')
            ->count();

        $completed = ParentAssignment::where('child_id', $child->id)
            ->where('status', 'completed')
            ->count();

        return view('child.assignments.index', compact('assignments', 'pending', 'completed'));
    }

    /**
     * Show a single assignment with submission form.
     */
    public function show(ParentAssignment $assignment)
    {
        $child = Auth::user();

        // Ensure the assignment belongs to this child
        if ($assignment->child_id !== $child->id) {
            abort(403, 'You cannot view this assignment.');
        }

        return view('child.assignments.show', compact('assignment'));
    }

    /**
     * Submit the assignment for review.
     */
    public function submit(Request $request, ParentAssignment $assignment)
    {
        $child = Auth::user();

        // Ensure the assignment belongs to this child
        if ($assignment->child_id !== $child->id) {
            abort(403, 'You cannot submit this assignment.');
        }

        // Prevent resubmission after completion
        if ($assignment->status === 'completed') {
            return back()->with('warning', 'This assignment is already completed.');
        }

        $validated = $request->validate([
            'submission_text' => 'required|string|min:5|max:5000',
            'attachment'      => 'nullable|file|max:10240', // up to 10MB
        ]);

        // Handle optional attachment upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')
                ->store('assignments/submissions/' . $child->id, 'public');
        }

        // Update assignment status
        $assignment->update([
            'status'       => 'submitted_for_review',
            'completed_at' => now(),
        ]);

        // Record activity
        ChildActivity::create([
            'child_id'      => $child->id,
            'assignment_id' => $assignment->id,
            'type'          => 'assignment_submitted',
            'title'         => 'Submitted: ' . $assignment->title,
            'description'   => $validated['submission_text'],
            'status'        => 'completed',
            'metadata'      => [
                'submission_text' => $validated['submission_text'],
                'attachment_path' => $attachmentPath,
            ],
        ]);

        return redirect()->route('child.assignments')
            ->with('success', 'Assignment submitted successfully! Your tutor or parent will review it soon.');
    }
}