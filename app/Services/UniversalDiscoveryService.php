<?php
// app/Services/Discovery/UniversalDiscoveryService.php

namespace App\Services\Discovery;

use App\Models\User;
use App\Models\CuratedResource;
use App\Models\LearningResource;
use App\Services\YouTubeService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class UniversalDiscoveryService
{
    protected $youtubeService;
    protected $sources = [];
    
    public function __construct(YouTubeService $youtubeService)
    {
        $this->youtubeService = $youtubeService;
    }

    /**
     * Search across ALL sources based on user profile
     */
    public function searchMultiSource(
        array $interests,
        string $difficulty = 'beginner',
        int $limit = 10,
        ?User $user = null
    ): Collection {
        $results = collect();
        
        // 1. Search YouTube
        $youtubeResults = $this->searchYouTube($interests, $difficulty, $user);
        $results = $results->concat($youtubeResults);
        
        // 2. Search Curated Resources
        $curatedResults = $this->searchCurated($interests, $difficulty);
        $results = $results->concat($curatedResults);
        
        // 3. Search Internal Resources
        $internalResults = $this->searchInternal($interests, $difficulty);
        $results = $results->concat($internalResults);
        
        // 4. Score and rank
        $scored = $this->scoreResults($results, $user);
        
        return $scored->take($limit);
    }

    /**
     * Search by specific topic
     */
    public function searchByTopic(
        string $topic,
        ?string $subTopic = null,
        int $limit = 5,
        ?User $user = null
    ): Collection {
        $interests = [$topic];
        if ($subTopic) {
            $interests[] = $subTopic;
        }
        
        return $this->searchMultiSource(
            interests: $interests,
            limit: $limit,
            user: $user
        );
    }

    /**
     * Search YouTube with educational filters
     */
    protected function searchYouTube(array $interests, string $difficulty, ?User $user): Collection
    {
        try {
            $query = implode(' ', $interests);
            
            // Add educational context based on age
            if ($user && $this->isChild($user)) {
                $query .= " educational for kids";
            } else {
                $query .= " tutorial";
            }
            
            $results = $this->youtubeService->searchEducationalVideos($query, 10);
            
            return collect($results)->map(function ($video) use ($interests) {
                return [
                    'type' => 'youtube',
                    'title' => $video['snippet']['title'],
                    'description' => $video['snippet']['description'],
                    'url' => "https://youtube.com/watch?v={$video['id']['videoId']}",
                    'thumbnail' => $video['snippet']['thumbnails']['high']['url'],
                    'source' => 'YouTube',
                    'source_icon' => '📺',
                    'topics' => $interests,
                    'raw_score' => 0.7,
                ];
            });
        } catch (\Exception $e) {
            Log::error('YouTube search failed', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    /**
     * Search curated resources database
     */
    protected function searchCurated(array $interests, string $difficulty): Collection
    {
        return CuratedResource::where('is_active', true)
            ->where('is_safe', true)
            ->where('difficulty_level', $difficulty)
            ->where(function ($query) use ($interests) {
                foreach ($interests as $interest) {
                    $query->orWhere('topic', 'like', "%{$interest}%")
                          ->orWhereJsonContains('tags', $interest);
                }
            })
            ->with('source')
            ->limit(15)
            ->get()
            ->map(function ($resource) {
                return [
                    'type' => 'curated',
                    'id' => $resource->id,
                    'title' => $resource->title,
                    'description' => $resource->summary ?? $resource->description,
                    'url' => route('resources.redirect', $resource->id),
                    'thumbnail' => $resource->thumbnail_url,
                    'source' => $resource->source->name,
                    'source_icon' => $this->getSourceIcon($resource->source->slug),
                    'difficulty' => $resource->difficulty_level,
                    'estimated_minutes' => $resource->estimated_minutes,
                    'topics' => $resource->tags,
                    'raw_score' => $resource->quality_score / 100,
                ];
            });
    }

    /**
     * Search internal learning resources
     */
    protected function searchInternal(array $interests, string $difficulty): Collection
    {
        return LearningResource::where('is_published', true)
            ->where('difficulty_level', $difficulty)
            ->where(function ($query) use ($interests) {
                foreach ($interests as $interest) {
                    $query->orWhere('title', 'like', "%{$interest}%")
                          ->orWhere('description', 'like', "%{$interest}%")
                          ->orWhereJsonContains('tags', $interest);
                }
            })
            ->limit(10)
            ->get()
            ->map(function ($resource) {
                return [
                    'type' => 'internal',
                    'id' => $resource->id,
                    'title' => $resource->title,
                    'description' => $resource->summary ?? $resource->description,
                    'url' => route('learning.show', $resource->id),
                    'thumbnail' => $resource->thumbnail_url,
                    'source' => 'SkillDNA',
                    'source_icon' => '📚',
                    'difficulty' => $resource->difficulty_level,
                    'estimated_minutes' => $resource->estimated_minutes,
                    'topics' => $resource->tags,
                    'raw_score' => 0.8,
                ];
            });
    }

    /**
     * Score and rank results for specific user
     */
    protected function scoreResults(Collection $results, ?User $user): Collection
    {
        return $results->map(function ($item) use ($user) {
            $score = $item['raw_score'] ?? 0.5;
            
            // Boost based on user's past behavior
            if ($user) {
                $score *= $this->getBehaviorBoost($item, $user);
            }
            
            // Boost based on source trust
            $score *= $this->getSourceTrust($item['source'] ?? '');
            
            // Boost based on recency (if available)
            if (isset($item['created_at'])) {
                $score *= $this->getRecencyBoost($item['created_at']);
            }
            
            $item['score'] = min($score, 1.0);
            $item['final_score'] = round($item['score'] * 100);
            
            return $item;
        })->sortByDesc('score');
    }

    protected function isChild($user): bool
    {
        return $user && $user->role === 'child';
    }

    protected function getSourceIcon($slug): string
    {
        $icons = [
            'w3schools' => '🌐',
            'khan-academy' => '📚',
            'mdn' => '📘',
            'freecodecamp' => '🔥',
            'youtube' => '📺',
        ];
        
        return $icons[$slug] ?? '🔗';
    }

    protected function getBehaviorBoost($item, $user): float
    {
        // Check if user has interacted with similar content
        $similarCount = $user->interactions()
            ->whereHas('resource', function ($q) use ($item) {
                $q->whereJsonContains('tags', $item['topics'] ?? []);
            })->count();
        
        return 1 + ($similarCount * 0.05);
    }

    protected function getSourceTrust($source): float
    {
        $trustScores = [
            'Khan Academy' => 1.2,
            'MDN Web Docs' => 1.1,
            'W3Schools' => 1.0,
            'freeCodeCamp' => 1.1,
            'YouTube' => 0.9,
            'SkillDNA' => 1.0,
        ];
        
        return $trustScores[$source] ?? 1.0;
    }

    protected function getRecencyBoost($date): float
    {
        $days = now()->diffInDays($date);
        return max(0.8, 1 - ($days * 0.01));
    }
}