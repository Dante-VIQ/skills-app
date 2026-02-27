<?php
// app/Http/Controllers/YouTubeController.php

namespace App\Http\Controllers;

use App\Services\YouTubeService;
use App\Models\LearningResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class YouTubeController extends Controller
{
    protected $youtubeService;
    
    public function __construct(YouTubeService $youtubeService)
    {
        $this->youtubeService = $youtubeService;
    }
    
    /**
     * Search for YouTube videos
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3',
            'max_results' => 'nullable|integer|min:1|max:50'
        ]);
        
        $results = $this->youtubeService->searchVideos(
            $request->query('query'),
            $request->query('max_results', 10)
        );
        
        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }
    
    /**
     * Preview a video before importing
     */
    public function preview($videoId)
    {
        try {
            $videoData = $this->youtubeService->importVideo($videoId);
            
            return response()->json([
                'success' => true,
                'video' => $videoData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Import a video into your database
     */
    public function import(Request $request)
    {
        $request->validate([
            'video_id' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'summary' => 'nullable|string|max:500'
        ]);
        
        try {
            $videoData = $this->youtubeService->importVideo(
                $request->video_id,
                $request->category_id,
                $request->summary
            );
            
            // Create learning resource
            $resource = LearningResource::create($videoData);
            
            Log::info('YouTube video imported', [
                'video_id' => $request->video_id,
                'resource_id' => $resource->id,
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Video imported successfully',
                'resource' => $resource
            ]);
            
        } catch (\Exception $e) {
            Log::error('YouTube import failed', [
                'video_id' => $request->video_id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Get channel videos
     */
    public function channelVideos($channelId)
    {
        $videos = $this->youtubeService->getChannelVideos($channelId);
        
        return response()->json([
            'success' => true,
            'videos' => $videos
        ]);
    }
}