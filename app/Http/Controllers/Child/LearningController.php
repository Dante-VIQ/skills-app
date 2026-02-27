<?php

namespace App\Http\Controllers\Child;

use App\Models\User;
use App\Models\LearningResource;
use App\Models\UserInteraction;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;

class LearningController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Main learning dashboard - personalized for each user type
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Log the access
        Log::info('Learning dashboard accessed', [
            'user_id' => $user->id,
            'user_type' => $user->role
        ]);

        // Get personalized recommendations
        $recommendations = $this->recommendationService->getPersonalizedFeed($user);

        // Get user's current assignments/progress
        $inProgress = $this->getInProgressContent($user);

        // Get stats based on user type
        $stats = $this->getUserStats($user);

        // Return appropriate view based on user role
        switch($user->role) {
            case 'child':
                return view('Learn.dashboard', [
                    'recommendations' => $recommendations,
                    'assignments'=> $inProgress,
                    'stats' => $stats,
                    'child' => $user
                ]);

            case 'parent':
                // Get all children for this parent
                $children = User::where('parent_id', $user->id)->get();

                return view('Learn.dashboard', [
                    'children' => $children,
                    'familyProgress' => $this->getFamilyProgress($children),
                    'recommendations' => $recommendations,
                    'pendingReviews' => $this->getPendingReviews($children)
                ]);

            case 'tutor':
                return view('Learn.tutor-dashboard', [
                    'students' => $this->getTutorStudents($user),
                    'teachingMaterials' => $this->getTeachingMaterials($user),
                    'recommendations' => $recommendations,
                    'classProgress' => $this->getClassProgress($user)
                ]);

            default:
                return view('Learn.index', [
                    'recommendations' => $recommendations
                ]);
        }
    }

    /**
     * Show a specific learning resource
     */
    public function show($id)
    {
        $user = Auth::user();
        $resource = LearningResource::with('category')->findOrFail($id);

        // Track the view
        $this->trackInteraction($user->id, $resource->id, 'view');

        // Get similar resources
        $similar = $this->recommendationService->getSimilarContent($resource->id, 5);

        // Check if user has already interacted
        $userProgress = UserInteraction::where('user_id', $user->id)
            ->where('learning_resource_id', $resource->id)
            ->where('interaction_type', 'watch')
            ->first();

        return view('learning.show', [
            'resource' => $resource,
            'similar' => $similar,
            'progress' => $userProgress,
            'isCompleted' => $this->isResourceCompleted($user->id, $resource->id)
        ]);
    }

    /**
     * Track user interaction (called via AJAX)
     */
    public function trackInteraction(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'resource_id' => 'required|exists:learning_resources,id',
            'interaction_type' => 'required|in:view,watch,complete,like,save',
            'duration' => 'nullable|integer',
            'progress' => 'nullable|numeric|min:0|max:100',
            'rating' => 'nullable|integer|min:1|max:5'
        ]);

        // Create or update interaction
        $interaction = UserInteraction::updateOrCreate(
            [
                'user_id' => $user->id,
                'learning_resource_id' => $validated['resource_id'],
                'interaction_type' => $validated['interaction_type'],
                'created_at' => now()->toDateString()
            ],
            [
                'duration' => $validated['duration'] ?? null,
                'progress_percentage' => $validated['progress'] ?? 0,
                'rating' => $validated['rating'] ?? null,
                'completed' => ($validated['progress'] ?? 0) >= 90
            ]
        );

        // If this is a completion, award points
        if ($validated['interaction_type'] === 'complete') {
            $this->awardPoints($user, $validated['resource_id']);
        }

        // Trigger recommendation model update (async)
        $this->recommendationService->queueUserUpdate($user->id);

        return response()->json([
            'success' => true,
            'points_earned' => $validated['interaction_type'] === 'complete' ? 10 : 0
        ]);
    }

    /**
     * Get recommendations for API (for infinite scroll)
     */
    public function getMoreRecommendations(Request $request)
    {
        $user = Auth::user();
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 10);

        $recommendations = $this->recommendationService->getMoreRecommendations(
            $user,
            $offset,
            $limit
        );

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations,
            'has_more' => count($recommendations) === $limit
        ]);
    }

    /**
     * Private helper methods
     */
    private function getInProgressContent($user)
    {
        if ($user->role !== 'child') {
            return collect();
        }

        return UserInteraction::where('user_id', $user->id)
            ->where('interaction_type', 'watch')
            ->where('completed', false)
            ->where('progress_percentage', '>', 0)
            ->with('resource')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function getUserStats($user)
    {
        $stats = [
            'total_watched' => 0,
            'total_minutes' => 0,
            'completed_count' => 0,
            'points' => 0
        ];

        if ($user->role === 'child') {
            $stats['total_watched'] = UserInteraction::where('user_id', $user->id)
                ->where('interaction_type', 'watch')
                ->count();

            $stats['total_minutes'] = UserInteraction::where('user_id', $user->id)
                ->where('interaction_type', 'watch')
                ->sum('duration') / 60;

            $stats['completed_count'] = UserInteraction::where('user_id', $user->id)
                ->where('interaction_type', 'complete')
                ->count();

            $stats['points'] = $user->points ?? 0;
        }

        return $stats;
    }

    private function getFamilyProgress($children)
    {
        $progress = [];
        foreach ($children as $child) {
            $progress[$child->id] = [
                'name' => $child->name,
                'completed' => UserInteraction::where('user_id', $child->id)
                    ->where('interaction_type', 'complete')
                    ->count(),
                'in_progress' => UserInteraction::where('user_id', $child->id)
                    ->where('interaction_type', 'watch')
                    ->where('completed', false)
                    ->count(),
                'last_active' => UserInteraction::where('user_id', $child->id)
                    ->latest()
                    ->first()?->created_at
            ];
        }
        return $progress;
    }

    private function getPendingReviews($children)
    {
        // Get assignments that need parent review
        return []; // Implement based on your assignment system
    }

    private function getTutorStudents($tutor)
    {
        // Get students assigned to this tutor
        return User::where('tutor_id', $tutor->id)->get();
    }

    private function getTeachingMaterials($tutor)
    {
        // Get materials the tutor has created or uses
        return LearningResource::where('created_by', $tutor->id)
            ->orWhereJsonContains('tags', 'teaching_material')
            ->limit(10)
            ->get();
    }

    private function getClassProgress($tutor)
    {
        // Aggregate progress for all students
        return []; // Implement based on your needs
    }

    // private function trackInteraction($userId, $resourceId, $type)
    // {
    //     try {
    //         UserInteraction::create([
    //             'user_id' => $userId,
    //             'learning_resource_id' => $resourceId,
    //             'interaction_type' => $type,
    //             'created_at' => now()
    //         ]);

    //         // Increment view count
    //         if ($type === 'view') {
    //             LearningResource::where('id', $resourceId)->increment('view_count');
    //         }
    //     } catch (\Exception $e) {
    //         Log::error('Failed to track interaction', [
    //             'error' => $e->getMessage(),
    //             'user_id' => $userId,
    //             'resource_id' => $resourceId
    //         ]);
    //     }
    // }

    private function isResourceCompleted($userId, $resourceId)
    {
        return UserInteraction::where('user_id', $userId)
            ->where('learning_resource_id', $resourceId)
            ->where('interaction_type', 'complete')
            ->exists();
    }

    private function awardPoints($user, $resourceId)
    {
        $resource = LearningResource::find($resourceId);
        $points = $resource->points ?? 10;

        $user->points = ($user->points ?? 0) + $points;
        $user->save();

        // Could also trigger badge checks here
    }
}
