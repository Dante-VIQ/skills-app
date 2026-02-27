<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ChildActivity;
use App\Models\ParentAssignment;
use Illuminate\Routing\Controller;

class ParentAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:parent']);
    }
    
    public function index()
    {
        $this->authorize('create assignments');
        
        $parent = auth()->user();
        $children = $parent->children;
        
        // Get assignments created by this parent
        $assignments = ParentAssignment::where('parent_id', $parent->id)
            ->with('child')
            ->latest()
            ->paginate(10);
        
        return view('parent.assignments.index', compact('assignments', 'children'));
    }
    
    public function store(Request $request)
    {
        $this->authorize('create assignments');
        
        $request->validate([
            'child_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date|after:today',
        ]);
        
        // Verify the child belongs to this parent
        $parent = auth()->user();
        if (!$parent->children()->where('id', $request->child_id)->exists()) {
            abort(403, 'You can only assign tasks to your own children.');
        }
        
        $assignment = ParentAssignment::create([
            'parent_id' => $parent->id,
            'child_id' => $request->child_id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => 'pending',
        ]);
        
        return redirect()->route('parent.assignments')
            ->with('success', 'Assignment created successfully.');
    }
    
    public function viewActivities(User $child)
    {
        // Use Laravel's policy for authorization
        $this->authorize('viewChildActivities', $child);
        
        $activities = ChildActivity::where('child_id', $child->id)
            ->with('contentModule.category')
            ->latest()
            ->paginate(20);
        
        return view('parent.activities.index', compact('child', 'activities'));
    }
}