<?php
// app/Services/RecommendationService.php

namespace App\Services;

use App\Models\User;
use App\Models\LearningResource;
use App\Models\UserInteraction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class RecommendationService
{
    protected $pythonScriptPath;
    protected $modelPath;

    public function __construct()
    {
        $this->pythonScriptPath = base_path('app/Services/Python/recommend.py');
        $this->modelPath = storage_path('app/models/skilldna_recommender.pkl');
    }

    /**
     * Get personalized feed for a user
     */
    public function getPersonalizedFeed($user, $limit = 20)
    {
        // Try cache first (5 minutes)
        $cacheKey = "recommendations:{$user->id}";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($user, $limit) {
            return $this->fetchRecommendations($user, $limit);
        });
    }

    /**
     * Get more recommendations (for infinite scroll)
     */
    public function getMoreRecommendations($user, $offset = 0, $limit = 10)
    {
        $all = $this->fetchRecommendations($user, 50);
        return array_slice($all, $offset, $limit);
    }

    /**
     * Get similar content to a specific resource
     */
    public function getSimilarContent($resourceId, $limit = 5)
    {
        // Use content-based similarity via Python
        $resource = LearningResource::find($resourceId);

        if (!$resource) {
            return collect();
        }

        // Get similar IDs from model
        $similarIds = $this->fetchSimilarContent($resourceId, $limit);

        // Fetch full models
        return LearningResource::whereIn('id', $similarIds)
            ->with('category')
            ->get();
    }

    /**
     * Queue a user update for background processing
     */
    public function queueUserUpdate($userId)
    {
        // Dispatch job to update user profile in model
        // For now, just clear cache
        Cache::forget("recommendations:{$userId}");
    }

    /**
     * Train/recommendation model with latest data
     */
    public function trainModel()
    {
        $process = new Process([
            'python3',
            base_path('app/Services/Python/skilldna_recommender.py'),
            '--train',
            '--output', $this->modelPath
        ]);

        $process->setTimeout(300); // 5 minutes

        try {
            $process->mustRun();

            Log::info('Recommendation model trained successfully', [
                'output' => $process->getOutput()
            ]);

            return true;
        } catch (ProcessFailedException $e) {
            Log::error('Model training failed', [
                'error' => $e->getMessage(),
                'output' => $process->getErrorOutput()
            ]);

            return false;
        }
    }

    /**
     * Fetch recommendations from Python model
     */
    protected function fetchRecommendations($user, $limit)
    {
        try {
            // Get user interactions for the last 30 days
            $interactions = $this->getUserInteractionsForModel($user->id);

            // Prepare input data
            $inputData = [
                'user_id' => $user->id,
                'user_type' => $user->role,
                'user_interests' => $user->interests ?? [],
                'interactions' => $interactions,
                'n_recommendations' => $limit,
                'model_path' => $this->modelPath
            ];

            // Write to temp file
            $inputFile = storage_path("app/temp/recommend_input_{$user->id}.json");
            file_put_contents($inputFile, json_encode($inputData));

            // Call Python script
            $process = new Process([
                'python3',
                $this->pythonScriptPath,
                $inputFile
            ]);

            $process->setTimeout(30);
            $process->run();

            // Clean up
            if (file_exists($inputFile)) {
                unlink($inputFile);
            }

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $output = json_decode($process->getOutput(), true);

            if (!isset($output['success']) || !$output['success']) {
                throw new \Exception($output['error'] ?? 'Unknown error');
            }

            // Get full resource details
            $recommendations = $output['recommendations'] ?? [];
            $resourceIds = collect($recommendations)->pluck('id')->toArray();

            $resources = LearningResource::whereIn('id', $resourceIds)
                ->with('category')
                ->get()
                ->keyBy('id');

            // Merge scores with resources
            $result = [];
            foreach ($recommendations as $rec) {
                if (isset($resources[$rec['id']])) {
                    $resource = $resources[$rec['id']];
                    $result[] = [
                        'id' => $resource->id,
                        'title' => $resource->title,
                        'description' => $resource->description,
                        'thumbnail' => $resource->source_metadata['thumbnail'] ?? null,
                        'category' => $resource->category->name ?? null,
                        'content_type' => $resource->content_type,
                        'difficulty' => $resource->difficulty_level,
                        'estimated_minutes' => $resource->estimated_minutes,
                        'score' => $rec['score'] ?? 0.5,
                        'reason' => $this->getReasonText($rec['reason'] ?? 'personalized'),
                        'recommendation_type' => $rec['type'] ?? 'hybrid'
                    ];
                }
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Recommendation fetch failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            // Fallback: return popular content
            return $this->getFallbackRecommendations($limit);
        }
    }

    /**
     * Get similar content via Python
     */
    protected function fetchSimilarContent($resourceId, $limit)
    {
        try {
            $inputData = [
                'resource_id' => $resourceId,
                'n_recommendations' => $limit,
                'type' => 'similar',
                'model_path' => $this->modelPath
            ];

            $inputFile = storage_path("app/temp/similar_input_{$resourceId}.json");
            file_put_contents($inputFile, json_encode($inputData));

            $process = new Process([
                'python3',
                $this->pythonScriptPath,
                $inputFile
            ]);

            $process->run();
            unlink($inputFile);

            if (!$process->isSuccessful()) {
                return [];
            }

            $output = json_decode($process->getOutput(), true);

            return collect($output['recommendations'] ?? [])
                ->pluck('id')
                ->toArray();

        } catch (\Exception $e) {
            Log::error('Similar content fetch failed', [
                'resource_id' => $resourceId,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Get user interactions formatted for the model
     */
    protected function getUserInteractionsForModel($userId)
    {
        $interactions = UserInteraction::where('user_id', $userId)
            ->with('resource')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return $interactions->map(function ($interaction) {
            return [
                'content_id' => $interaction->learning_resource_id,
                'type' => $interaction->interaction_type,
                'duration' => $interaction->duration,
                'rating' => $interaction->rating,
                'progress' => $interaction->progress_percentage,
                'completed' => $interaction->completed,
                'timestamp' => $interaction->created_at->timestamp,
                'content_metadata' => [
                    'category' => $interaction->resource->category_id ?? null,
                    'tags' => $interaction->resource->tags ?? [],
                    'difficulty' => $interaction->resource->difficulty_level ?? null
                ]
            ];
        })->toArray();
    }

    /**
     * Fallback recommendations when ML model fails
     */
    protected function getFallbackRecommendations($limit)
    {
        // Return popular content
        $popular = LearningResource::where('is_published', true)
            ->with('category')
            ->orderBy('points', 'desc')
            ->limit($limit)
            ->get();

        return $popular->map(function ($resource) {
            return [
                'id' => $resource->id,
                'title' => $resource->title,
                'description' => $resource->description,
                'thumbnail' => $resource->source_metadata['thumbnail'] ?? null,
                'category' => $resource->category->name ?? null,
                'content_type' => $resource->content_type,
                'difficulty' => $resource->difficulty_level,
                'estimated_minutes' => $resource->estimated_minutes,
                'score' => 0.5,
                'reason' => 'Popular with other learners',
                'recommendation_type' => 'fallback_popular'
            ];
        })->toArray();
    }

    /**
     * Get human-readable reason for recommendation
     */
    protected function getReasonText($reason)
    {
        $reasons = [
            'content_based' => 'Because you watched similar content',
            'user_based' => 'Other learners like you enjoyed this',
            'item_based' => 'Based on your recent activity',
            'popular' => 'Popular in this category',
            'cold_start_interest' => 'Matches your interests',
            'collaborative' => 'Recommended for you',
            'personalized' => 'Personalized just for you',
            'trending' => 'Trending now',
            'new' => 'Newly added',
            'skill_gap' => 'Helps with a skill you\'re building'
        ];

        return $reasons[$reason] ?? 'Recommended for you';
    }
}
