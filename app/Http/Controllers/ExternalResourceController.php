<?php
// app/Http/Controllers/ExternalResourceController.php

namespace App\Http\Controllers;

use App\Models\CuratedResource;
use App\Models\LearningResource;
use App\Services\ML\ResourceReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExternalResourceController extends Controller
{
    protected $referralService;
    
    public function __construct(ResourceReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Display external resources for a topic
     */
    public function index(Request $request)
    {
        $topic = $request->get('topic');
        $difficulty = $request->get('difficulty');
        
        $query = CuratedResource::where('is_active', true)
            ->with('source');
        
        if ($topic) {
            $query->where('topic', $topic);
        }
        
        if ($difficulty) {
            $query->where('difficulty_level', $difficulty);
        }
        
        $resources = $query->orderBy('quality_score', 'desc')
            ->paginate(12);
        
        return view('external-resources.index', [
            'resources' => $resources,
            'topic' => $topic,
            'difficulty' => $difficulty
        ]);
    }

    /**
     * Show a specific external resource
     */
    public function show($slug)
    {
        $resource = CuratedResource::where('slug', $slug)
            ->with('source')
            ->firstOrFail();
        
        // Track the view/referral
        if (Auth::check()) {
            $this->referralService->trackReferral(
                Auth::user(),
                $resource,
                ['type' => 'direct_view']
            );
        }
        
        return view('external-resources.show', [
            'resource' => $resource
        ]);
    }

    /**
     * Redirect to external resource (with tracking)
     */
    public function redirect($id)
    {
        $resource = CuratedResource::findOrFail($id);
        
        if (Auth::check()) {
            $this->referralService->trackReferral(
                Auth::user(),
                $resource,
                ['type' => 'click_through']
            );
        }
        
        // Add UTM parameters for tracking
        $url = $resource->resource_url;
        $separator = parse_url($url, PHP_URL_QUERY) ? '&' : '?';
        $trackingUrl = $url . $separator . http_build_query([
            'utm_source' => 'skilldna',
            'utm_medium' => 'referral',
            'utm_campaign' => 'external_resource',
            'utm_content' => $resource->id
        ]);
        
        return redirect()->away($trackingUrl);
    }

    /**
     * Get recommendations for a learning resource
     */
    public function related(LearningResource $learningResource)
    {
        $resources = $this->referralService->getRelatedResources($learningResource);
        
        return response()->json([
            'success' => true,
            'resources' => $resources
        ]);
    }

    /**
     * Get personalized recommendations
     */
    public function recommendations(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        $topic = $request->get('topic');
        $limit = $request->get('limit', 5);
        
        $resources = $this->referralService->getRecommendations(
            $user,
            $topic,
            $limit
        );
        
        return response()->json([
            'success' => true,
            'resources' => $resources
        ]);
    }

    /**
     * Provide feedback on resource helpfulness
     */
    public function feedback(Request $request, $id)
    {
        $request->validate([
            'helpful' => 'required|boolean'
        ]);
        
        $referral = ResourceReferral::where('user_id', Auth::id())
            ->where('curated_resource_id', $id)
            ->latest()
            ->first();
        
        if ($referral) {
            $referral->found_helpful = $request->helpful;
            $referral->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Feedback recorded'
        ]);
    }
}