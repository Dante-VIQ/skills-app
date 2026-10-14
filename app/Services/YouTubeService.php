<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class YouTubeService
{
    protected $apiKey;
    protected $apiUrl;
    protected $maxResults;
    protected $safeSearch;
    
    public function __construct()
    {
        $this->apiKey = config('services.youtube.api_key');
        $this->apiUrl = config('services.youtube.api_url');
        $this->maxResults = config('services.youtube.max_results');
        $this->safeSearch = config('services.youtube.safe_search');
        
        if (!$this->apiKey) {
            Log::warning('YouTube API key is not configured');
        }
    }
    
    /**
     * Search for educational videos
     */
    public function searchVideos($query, $maxResults = null)
    {
        $maxResults = $maxResults ?? $this->maxResults;
        
        // Cache key based on query
        $cacheKey = 'youtube_search_' . md5($query . $maxResults);
        
        return Cache::remember($cacheKey, now()->addHours(1), function () use ($query, $maxResults) {
            try {
                $response = Http::timeout(config('services.youtube.timeout', 30))
                    ->get("{$this->apiUrl}/search", [
                        'part' => 'snippet',
                        'q' => $query,
                        'type' => 'video',
                        'maxResults' => $maxResults,
                        'key' => $this->apiKey,
                        'safeSearch' => $this->safeSearch,
                        'videoEmbeddable' => 'true',
                        'videoSyndicated' => 'true',
                        'relevanceLanguage' => 'en',
                    ]);
                
                if ($response->failed()) {
                    Log::error('YouTube API search failed', [
                        'query' => $query,
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                    return [];
                }
                
                $items = $response->json()['items'] ?? [];
                
                // Filter results
                return $this->filterResults($items);
                
            } catch (Exception $e) {
                Log::error('YouTube API exception', [
                    'message' => $e->getMessage(),
                    'query' => $query
                ]);
                return [];
            }
        });
    }
    
    /**
     * Get video details by ID
     */
    public function getVideoDetails($videoId)
    {
        $cacheKey = 'youtube_video_' . $videoId;
        
        return Cache::remember($cacheKey, now()->addDays(7), function () use ($videoId) {
            try {
                $response = Http::get("{$this->apiUrl}/videos", [
                    'part' => 'snippet,contentDetails,statistics,topicDetails',
                    'id' => $videoId,
                    'key' => $this->apiKey
                ]);
                
                if ($response->failed()) {
                    Log::error('YouTube API video details failed', [
                        'video_id' => $videoId,
                        'status' => $response->status()
                    ]);
                    return null;
                }
                
                $items = $response->json()['items'] ?? [];
                return $items[0] ?? null;
                
            } catch (Exception $e) {
                Log::error('YouTube API exception', [
                    'message' => $e->getMessage(),
                    'video_id' => $videoId
                ]);
                return null;
            }
        });
    }
    
    /**
     * Get videos from a specific channel
     */
    public function getChannelVideos($channelId, $maxResults = null)
    {
        $maxResults = $maxResults ?? $this->maxResults;
        $cacheKey = 'youtube_channel_' . $channelId . '_' . $maxResults;
        
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($channelId, $maxResults) {
            try {
                // First get the channel's upload playlist ID
                $channel = Http::get("{$this->apiUrl}/channels", [
                    'part' => 'contentDetails',
                    'id' => $channelId,
                    'key' => $this->apiKey
                ]);
                
                if ($channel->failed()) {
                    return [];
                }
                
                $uploadPlaylistId = $channel->json()['items'][0]['contentDetails']['relatedPlaylists']['uploads'] ?? null;
                
                if (!$uploadPlaylistId) {
                    return [];
                }
                
                // Then get videos from that playlist
                $response = Http::get("{$this->apiUrl}/playlistItems", [
                    'part' => 'snippet',
                    'playlistId' => $uploadPlaylistId,
                    'maxResults' => $maxResults,
                    'key' => $this->apiKey
                ]);
                
                if ($response->failed()) {
                    return [];
                }
                
                return $response->json()['items'] ?? [];
                
            } catch (Exception $e) {
                Log::error('YouTube API channel videos failed', [
                    'message' => $e->getMessage(),
                    'channel_id' => $channelId
                ]);
                return [];
            }
        });
    }
    
    /**
     * Import a video into your database
     */
    public function importVideo($videoId, $categoryId = null, $customSummary = null)
    {
        $videoDetails = $this->getVideoDetails($videoId);
        
        if (!$videoDetails) {
            throw new Exception("Video not found or inaccessible");
        }
        
        $snippet = $videoDetails['snippet'];
        $contentDetails = $videoDetails['contentDetails'];
        $statistics = $videoDetails['statistics'] ?? [];
        
        // Check if video is safe for kids
        $safetyCheck = $this->checkVideoSafety($videoDetails);
        
        if (!$safetyCheck['safe']) {
            throw new Exception("Video failed safety check: " . implode(', ', $safetyCheck['reasons']));
        }
        
        // Calculate duration in minutes
        $duration = $this->iso8601ToMinutes($contentDetails['duration']);
        
        return [
            'title' => $snippet['title'],
            'description' => $snippet['description'],
            'summary' => $customSummary ?? $this->generateSummary($snippet['description']),
            'source_type' => 'youtube',
            'external_url' => "https://youtube.com/watch?v={$videoId}",
            'source_id' => $videoId,
            'source_metadata' => [
                'thumbnail' => $snippet['thumbnails']['high']['url'] ?? null,
                'medium_thumbnail' => $snippet['thumbnails']['medium']['url'] ?? null,
                'default_thumbnail' => $snippet['thumbnails']['default']['url'] ?? null,
                'duration' => $contentDetails['duration'],
                'duration_seconds' => $this->durationToSeconds($contentDetails['duration']),
                'view_count' => $statistics['viewCount'] ?? 0,
                'like_count' => $statistics['likeCount'] ?? 0,
                'comment_count' => $statistics['commentCount'] ?? 0,
                'channel' => $snippet['channelTitle'],
                'channel_id' => $snippet['channelId'],
                'published_at' => $snippet['publishedAt'],
                'category_id' => $snippet['categoryId'] ?? null,
            ],
            'attribution' => "YouTube: {$snippet['channelTitle']}",
            'estimated_minutes' => $duration,
            'tags' => $snippet['tags'] ?? [],
            'category_id' => $categoryId,
            'is_published' => $safetyCheck['safe'],
            'requires_review' => $safetyCheck['requires_review'] ?? false,
            'safety_flags' => $safetyCheck['reasons'] ?? []
        ];
    }
    
    /**
     * Check if video is safe for children
     */
    protected function checkVideoSafety($videoDetails)
    {
        $reasons = [];
        $snippet = $videoDetails['snippet'];
        $contentDetails = $videoDetails['contentDetails'];
        $topicDetails = $videoDetails['topicDetails'] ?? [];
        
        // Check content rating
        if (isset($contentDetails['contentRating'])) {
            $rating = $contentDetails['contentRating'];
            if (isset($rating['ytRating']) && $rating['ytRating'] === 'ytAgeRestricted') {
                $reasons[] = 'Age-restricted content';
            }
        }
        
        // Check category
        $categoryId = $snippet['categoryId'] ?? null;
        $blockedCategories = config('services.youtube.blocked_categories', []);
        
        if ($categoryId && in_array($categoryId, $blockedCategories)) {
            $reasons[] = "Content from blocked category: {$categoryId}";
        }
        
        // Check title and description for unsafe keywords
        $text = $snippet['title'] . ' ' . ($snippet['description'] ?? '');
        $keywordViolations = $this->checkUnsafeKeywords($text);
        
        if (!empty($keywordViolations)) {
            $reasons = array_merge($reasons, $keywordViolations);
        }
        
        return [
            'safe' => empty($reasons),
            'reasons' => $reasons,
            'requires_review' => count($reasons) > 0 && count($reasons) < 3
        ];
    }
    
    /**
     * Check for unsafe keywords
     */
    protected function checkUnsafeKeywords($text)
    {
        $unsafeKeywords = [
            'kill', 'murder', 'steal', 'weapon', 'gun', 'bomb',
            'sexy', 'hot', 'dating', 'romantic', 'adult', '18+',
            'prank', 'scam', 'troll', 'bully', 'hack',
            'beer', 'wine', 'alcohol', 'smoke', 'drug',
            'horror', 'scary', 'ghost', 'zombie', 'haunted',
            'bet', 'casino', 'poker', 'gamble'
        ];
        
        $found = [];
        $lowerText = strtolower($text);
        
        foreach ($unsafeKeywords as $keyword) {
            if (str_contains($lowerText, $keyword)) {
                $found[] = "Contains unsafe keyword: {$keyword}";
            }
        }
        
        return $found;
    }
    
    /**
     * Convert ISO 8601 duration to minutes
     */
    protected function iso8601ToMinutes($duration)
    {
        try {
            $interval = new \DateInterval($duration);
            return ($interval->h * 60) + $interval->i + ceil($interval->s / 60);
        } catch (Exception $e) {
            return 5; // Default to 5 minutes if parsing fails
        }
    }
    
    /**
     * Convert ISO 8601 duration to seconds
     */
    protected function durationToSeconds($duration)
    {
        try {
            $interval = new \DateInterval($duration);
            return ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
        } catch (Exception $e) {
            return 300; // Default to 5 minutes
        }
    }
    
    /**
     * Generate a summary from description
     */
    protected function generateSummary($description, $length = 200)
    {
        // Remove extra whitespace and URLs
        $text = preg_replace('/\s+/', ' ', $description);
        $text = preg_replace('/https?:\/\/[^\s]+/', '', $text);
        
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . '...';
    }
    
    /**
     * Filter search results for educational content
     */
    protected function filterResults($items)
    {
        $educationalCategories = config('services.youtube.educational_categories', []);
        
        return array_filter($items, function ($item) use ($educationalCategories) {
            // We'll need to fetch full details for category filtering
            // For now, just return all items that passed the search
            return true;
        });
    }
    
    /**
     * Check API quota usage
     */
    public function checkQuota()
    {
        // You'd need to implement quota tracking
        // This could be done by storing request counts in cache
        $key = 'youtube_api_quota_' . date('Y-m-d');
        $requests = Cache::get($key, 0);
        
        return [
            'used' => $requests,
            'limit' => config('services.youtube.rate_limit.max_requests', 10000),
            'remaining' => max(0, config('services.youtube.rate_limit.max_requests', 10000) - $requests)
        ];
    }
}