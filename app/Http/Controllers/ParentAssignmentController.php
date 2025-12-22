<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ChildActivity;
use App\Models\ParentAssignment;
use Illuminate\Support\Facades\Auth;

class ParentAssignmentController extends Controller
{
    public function index()
    {
        $children = Auth::user()->children;
        $assignments = ParentAssignment::whereIn('child_id', $children->pluck('id'))
            ->with('child')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('parent.assignments.index', compact('assignments', 'children'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'child_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:quiz,activity,challenge,reflection',
            'questions' => 'required_if:type,quiz|array',
            'due_date' => 'nullable|date|after:now',
            'blocks_content' => 'boolean',
        ]);

        // Verify parent owns this child
        $child = auth()->user()->children()->find($request->child_id);
        if (!$child) {
            return redirect()->back()->with('error', 'Invalid child selected.');
        }

        ParentAssignment::create([
            'parent_id' => Auth::id(),
            'child_id' => $request->child_id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'content' => $request->questions ?? [],
            'due_date' => $request->due_date,
            'blocks_content' => $request->boolean('blocks_content', true),
            'status' => 'assigned',
        ]);

        return redirect()->route('parent.assignments')->with('success', 'Assignment created successfully.');
    }

    public function viewActivities(User $child)
    {
        // Verify parent owns this child
        if (!Auth::user()->children->contains($child)) {
            abort(403);
        }

        $activities = ChildActivity::where('child_id', $child->id)
            ->orderBy('created_at', 'desc')
            ->with('assignment')
            ->paginate(20);

        return view('parent.activities', compact('child', 'activities'));
    }
}
