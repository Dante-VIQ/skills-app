<?php

namespace App\Http\Controllers;

use App\Models\ChildActivity;
use App\Models\ParentAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ParentAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:parent']);
        $this->middleware('permission:create assignments')->only(['index', 'create', 'store']);
    }

    public function index()
    {
        $parent = auth()->user();
        $children = $parent->children;

        $assignments = ParentAssignment::where('parent_id', $parent->id)
            ->with('child')
            ->latest()
            ->paginate(10);

        return view('parent.assignments.index', compact('assignments', 'children'));
    }

    public function create()
    {
        $parent = auth()->user();
        $children = $parent->children;

        if ($children->isEmpty()) {
            return redirect()->route('parent.add-child')
                ->with('warning', 'Add a child first before creating assignments.');
        }

        return view('parent.assignments.create', compact('children'));
    }

    public function store(Request $request)
    {
        $parent = auth()->user();

        $request->validate([
            'child_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date|after:today',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        // Verify the child belongs to this parent
        if (!$parent->children()->where('id', $request->child_id)->exists()) {
            abort(403, 'You can only assign tasks to your own children.');
        }

        $assignment = ParentAssignment::create([
            'parent_id' => $parent->id,
            'child_id' => $request->child_id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'priority' => $request->priority ?? 'medium',
            'status' => 'pending',
        ]);

        return redirect()->route('parent.assignments')
            ->with('success', 'Assignment created successfully.');
    }

    public function show(ParentAssignment $assignment)
    {
        // Ensure the parent owns this assignment
        if ($assignment->parent_id !== auth()->id()) {
            abort(403);
        }

        return view('parent.assignments.show', compact('assignment'));
    }

        public function viewActivities(User $child)
    {
        // Use Laravel's policy for authorization
        // $this->authorize('viewChildActivities', $child);

        $activities = ChildActivity::where('child_id', $child->id)
            ->with('contentModule.category')
            ->latest()
            ->paginate(20);

        return view('parent.activities.index', compact('child', 'activities'));
    }
}
