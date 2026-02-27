<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ChildActivity;
use App\Models\ParentAssignment;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class TutorDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:tutor']);
    }
    
    public function index()
    {
        $tutor = Auth::user();
        
        // Get assigned students
        $students = $tutor->students()
            ->withCount([
                'pendingAssignments',
                'activities as weekly_activities' => function ($query) {
                    $query->where('created_at', '>=', now()->subWeek());
                }
            ])
            ->with('parent')
            ->get();
        
        // Recent activities across all students
        $recentActivities = ChildActivity::whereIn('child_id', $students->pluck('id'))
            ->with(['child', 'contentModule'])
            ->latest()
            ->take(10)
            ->get();
        
        // Assignments needing review
        $assignmentsNeedingReview = ParentAssignment::whereIn('child_id', $students->pluck('id'))
            ->where('status', 'submitted_for_review')
            ->with('child')
            ->get();
        
        // Calculate average progress
        $totalStudents = $students->count();
        $activeStudents = $students->where('weekly_activities', '>', 0)->count();
        $averageProgress = $totalStudents > 0 ? ($activeStudents / $totalStudents) * 100 : 0;
        
        return view('tutor.dashboard', compact(
            'students',
            'recentActivities',
            'assignmentsNeedingReview',
            'totalStudents',
            'activeStudents',
            'averageProgress'
        ));
    }
    
    public function students()
    {
        $this->authorize('manage assigned children');
        
        $tutor = Auth::user();
        $students = $tutor->students()
            ->with('parent')
            ->withCount(['pendingAssignments', 'activities'])
            ->paginate(20);
        
        return view('tutor.students.index', compact('students'));
    }
    
    public function viewStudent(User $student)
    {
        $this->authorize('manage assigned children');
        
        // Verify this student is assigned to this tutor
        if ($student->tutor_id !== Auth::id()) {
            abort(403, 'You are not assigned to this student.');
        }
        
        // Get student progress data
        $progress = $this->getStudentProgress($student);
        $recentActivities = $student->activities()
            ->with('contentModule')
            ->latest()
            ->take(15)
            ->get();
        
        return view('tutor.students.view', compact('student', 'progress', 'recentActivities'));
    }
    
    public function reviewAssignment(ParentAssignment $assignment)
    {
        $this->authorize('review assignments');
        
        // Verify assignment belongs to a student of this tutor
        if ($assignment->child->tutor_id !== Auth::id()) {
            abort(403, 'You cannot review this assignment.');
        }
        
        return view('tutor.assignments.review', compact('assignment'));
    }
    
    public function submitReview(Request $request, ParentAssignment $assignment)
    {
        $this->authorize('review assignments');
        
        if ($assignment->child->tutor_id !== Auth::id()) {
            abort(403, 'You cannot review this assignment.');
        }
        
        $request->validate([
            'feedback' => 'required|string|min:10',
            'rating' => 'required|integer|min:1|max:5',
        ]);
        
        $assignment->update([
            'status' => 'completed',
            'feedback' => $request->feedback,
            'rating' => $request->rating,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);
        
        return redirect()->route('tutor.dashboard')
            ->with('success', 'Assignment reviewed successfully.');
    }
    
    private function getStudentProgress(User $student)
    {
        return [
            'total_activities' => $student->activities()->count(),
            'completed_assignments' => $student->receivedAssignments()->where('status', 'completed')->count(),
            'total_watch_time' => $student->totalWatchTime(),
            'skills_learned' => $student->skills ? count($student->skills) : 0,
            'weekly_activity' => $student->activities()->where('created_at', '>=', now()->subWeek())->count(),
        ];
    }
}