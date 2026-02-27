<?php
// app/Services/Discovery/AutomatedContentDiscovery.php

namespace App\Services\Discovery;

use App\Models\User;
use App\Models\UserInterest;
use App\Models\CuratedResource;
use App\Models\LearningResource;
use App\Services\YouTubeService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AutomatedContentDiscovery
{
    protected $youtubeService;

    public function __construct(YouTubeService $youtubeService)
    {
        $this->youtubeService = $youtubeService;
    }

    /**
     * Discover content for a user based on interests
     */
    public function discoverForUser(User $user, Collection $interests, int $limit = 20): Collection
    {
        $cacheKey = "discovery:user:{$user->id}:" . md5($interests->pluck('id')->join(','));

        return Cache::remember($cacheKey, now()->addHours(3), function () use ($user, $interests, $limit) {
            $allContent = collect();

            foreach ($interests as $interest) {
                $content = $this->discoverByInterest($interest, $user);
                $allContent = $allContent->concat($content);
            }

            // Remove duplicates
            $unique = $allContent->unique('url');

            // Score and rank
            $scored = $this->scoreContent($unique, $user);

            return $scored->take($limit);
        });
    }

    /**
     * Search by specific topic
     */
    public function searchByTopic(string $topic, ?string $subTopic = null, int $limit = 5, ?User $user = null): Collection
    {
        $results = collect();

        // Search YouTube
        $youtubeResults = $this->searchYouTube($topic, $subTopic, $user);
        $results = $results->concat($youtubeResults);

        // Search curated resources
        $curatedResults = $this->searchCurated($topic, $subTopic);
        $results = $results->concat($curatedResults);

        // Search internal resources
        $internalResults = $this->searchInternal($topic, $subTopic);
        $results = $results->concat($internalResults);

        // Score and rank
        $scored = $this->scoreContent($results, $user);

        return $scored->take($limit);
    }

    /**
     * Discover content for a single interest
     */
    protected function discoverByInterest(UserInterest $interest, User $user): Collection
    {
        $results = collect();

        // YouTube search
        $youtubeVideos = $this->searchYouTube($interest->category, $interest->subcategory, $user);
        $results = $results->concat($youtubeVideos);

        // Curated resources
        $curatedResources = $this->searchCurated($interest->category, $interest->subcategory);
        $results = $results->concat($curatedResources);

        // Internal resources
        $internalResources = $this->searchInternal($interest->category, $interest->subcategory);
        $results = $results->concat($internalResources);

        return $results;
    }

    /**
     * Search YouTube
     */
    protected function searchYouTube(string $category, ?string $subcategory, ?User $user): Collection
    {
        try {
            $query = $category;
            if ($subcategory) {
                $query .= " $subcategory";
            }

            // Add educational context based on user age
            if ($user && $this->isChild($user)) {
                $query .= " educational for kids";
            } else {
                $query .= " tutorial";
            }

            $results = $this->youtubeService->searchEducationalVideos($query, 5);

            return collect($results)->map(function ($video) use ($category) {
                return [
                    'type' => 'youtube',
                    'title' => $video['snippet']['title'],
                    'description' => $video['snippet']['description'],
                    'url' => "https://youtube.com/watch?v={$video['id']['videoId']}",
                    'thumbnail' => $video['snippet']['thumbnails']['high']['url'],
                    'source' => 'YouTube',
                    'source_icon' => '📺',
                    'category' => $category,
                    'raw_score' => 0.7,
                ];
            });
        } catch (\Exception $e) {
            Log::error('YouTube search failed', [
                'category' => $category,
                'error' => $e->getMessage()
            ]);
            return collect();
        }
    }

    /**
     * Search curated resources
     */
    protected function searchCurated(string $category, ?string $subcategory): Collection
    {
        $query = CuratedResource::where('is_active', true)
            ->where('is_safe', true)
            ->with('source');

        if ($subcategory) {
            $query->where(function ($q) use ($category, $subcategory) {
                $q->where('topic', $category)
                  ->where('subtopic', $subcategory);
            });
        } else {
            $query->where('topic', $category);
        }

        return $query->limit(5)
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
                    'category' => $resource->topic,
                    'raw_score' => $resource->quality_score / 100,
                ];
            });
    }

    /**
     * Search internal resources
     */
    protected function searchInternal(string $category, ?string $subcategory): Collection
    {
        $query = LearningResource::where('is_published', true);

        if ($subcategory) {
            $query->where(function ($q) use ($category, $subcategory) {
                $q->where('topic', $category)
                  ->orWhereJsonContains('tags', $subcategory);
            });
        } else {
            $query->where('topic', $category);
        }

        return $query->limit(3)
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
                    'category' => $resource->topic,
                    'raw_score' => 0.8,
                ];
            });
    }

    /**
     * Score and rank content
     */
    protected function scoreContent(Collection $content, ?User $user): Collection
    {
        return $content->map(function ($item) use ($user) {
            $score = $item['raw_score'] ?? 0.5;

            // Boost based on user's past behavior
            if ($user) {
                $score *= $this->getBehaviorBoost($item, $user);
            }

            // Boost based on source trust
            $score *= $this->getSourceTrust($item['source'] ?? '');

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
        return match($slug) {
            'w3schools' => '🌐',
            'khan-academy' => '📚',
            'mdn' => '📘',
            'freecodecamp' => '🔥',
            default => '🔗'
        };
    }

    protected function getBehaviorBoost($item, $user): float
    {
        // Check if user has interacted with similar content
        $similarCount = $user->interactions()
            ->whereHas('resource', function ($q) use ($item) {
                $q->where('topic', $item['category'] ?? '');
            })->count();

        return 1 + min($similarCount * 0.05, 0.3);
    }

    protected function getSourceTrust($source): float
    {
        return match($source) {
            'Khan Academy' => 1.2,
            'MDN Web Docs' => 1.1,
            'W3Schools' => 1.0,
            'freeCodeCamp' => 1.1,
            'YouTube' => 0.9,
            'SkillDNA' => 1.0,
            default => 1.0
        };
    }
}
