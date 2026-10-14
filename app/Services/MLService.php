<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MLService
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.ml.url', 'http://127.0.0.1:5000'), '/');
        $this->timeout = (int) config('services.ml.timeout', 5);
    }

    /**
     * Check whether the Python ML service is reachable.
     */
    public function isHealthy(): bool
    {
        try {
            $response = Http::timeout(2)->get("{$this->baseUrl}/health");
            return $response->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Get personalized recommendations for a user.
     * Returns an array of content IDs (or an empty array on failure).
     */
    public function getRecommendations(int $userId, string $userType = 'child', int $limit = 10): array
    {
        return $this->safeGet(
            "/api/recommendations/{$userId}",
            ['user_type' => $userType, 'limit' => $limit],
            'recommendations'
        );
    }

    /**
     * Get similar content by content ID.
     */
    public function getSimilarContent(int $contentId, int $limit = 5): array
    {
        return $this->safeGet(
            "/api/similar/{$contentId}",
            ['limit' => $limit],
            'recommendations'
        );
    }

    /**
     * Get popular content globally.
     */
    public function getPopularContent(int $limit = 10): array
    {
        return $this->safeGet(
            "/api/recommendations/popular",
            ['limit' => $limit],
            'recommendations'
        );
    }

    /**
     * Trigger a full model retrain (typically called from a scheduled job).
     */
    public function train(): bool
    {
        try {
            $response = Http::timeout($this->timeout)->post("{$this->baseUrl}/api/train");
            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('MLService::train failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get model health stats.
     */
    public function stats(): array
    {
        try {
            $response = Http::timeout($this->timeout)->get("{$this->baseUrl}/api/stats");
            return $response->successful() ? ($response->json('stats') ?? []) : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Generate an offline action for a piece of content.
     */
    public function generateOfflineAction(array $payload): array
    {
        try {
            $response = Http::timeout($this->timeout)->post(
                "{$this->baseUrl}/api/offline-action/generate",
                $payload
            );

            if ($response->successful()) {
                return $response->json();
            }

            return ['success' => false, 'prompt' => 'Try applying what you learned today.', 'duration_minutes' => 10, 'requires_material' => []];
        } catch (\Throwable $e) {
            Log::warning('MLService::generateOfflineAction failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'prompt' => 'Try applying what you learned today.', 'duration_minutes' => 10, 'requires_material' => []];
        }
    }

    /**
     * Shared safe GET handler with logging + cache.
     */
    protected function safeGet(string $endpoint, array $query, string $extractKey): array
    {
        $cacheKey = 'ml:' . md5($endpoint . json_encode($query));

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($endpoint, $query, $extractKey) {
            try {
                $response = Http::timeout($this->timeout)
                    ->acceptJson()
                    ->get($this->baseUrl . $endpoint, $query);

                if ($response->successful()) {
                    $data = $response->json();
                    return $data[$extractKey] ?? [];
                }

                Log::warning('MLService GET returned non-success', [
                    'endpoint' => $endpoint,
                    'status'   => $response->status(),
                ]);

                return [];
            } catch (\Throwable $e) {
                Log::warning('MLService GET failed', [
                    'endpoint' => $endpoint,
                    'error'    => $e->getMessage(),
                ]);
                return [];
            }
        });
    }
}