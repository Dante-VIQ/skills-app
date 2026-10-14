<?php

namespace App\Services\Brain;

use App\Models\User;
use App\Services\Brain\DTO\BrainOutputDTO;
use App\Services\Brain\DTO\UserProfileDTO;
use App\Services\Discovery\AutomatedContentDiscovery;
use App\Services\TutorMatchingService;
use Illuminate\Support\Facades\Log;

class PersonalizationBrain
{
    /**
     * Optional services – resolved at runtime.
     * The Brain works even if gamification isn't installed.
     */
    protected ?object $gamification = null;

    public function __construct(
        protected AutomatedContentDiscovery $discovery,
        protected TutorMatchingService $tutorMatcher,
    ) {
        // Try to resolve gamification if the class exists
        if (class_exists(\App\Services\Gamification\GamificationService::class)) {
            try {
                $this->gamification = app(\App\Services\Gamification\GamificationService::class);
            } catch (\Throwable $e) {
                Log::info('GamificationService not available, continuing without it.');
            }
        }
    }

    /**
     * The single entry point.
     * Returns a unified BrainOutputDTO – no side effects.
     */
    public function think(User $user): BrainOutputDTO
    {
        $startedAt = microtime(true);

        try {
            $profile      = $this->buildProfile($user);
            $nextContent  = $this->getNextContent($user);
            $tutorMatches = $this->getTutorMatches($user);
            $gamification = $this->getGamification($user);
            $insights     = $this->getInsights($user);

            return new BrainOutputDTO(
                profile:      $profile->toArray(),
                nextContent:  $nextContent,
                tutorMatches: $tutorMatches,
                gamification: $gamification,
                insights:     $insights,
                metadata: [
                    'generated_at'    => now()->toIso8601String(),
                    'execution_ms'    => round((microtime(true) - $startedAt) * 1000, 2),
                    'role'            => $user->getRoleNames()->first(),
                    'brain_version'   => '1.0',
                ],
            );
        } catch (\Throwable $e) {
            Log::error('PersonalizationBrain failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            // Graceful degradation – never crash the page
            return new BrainOutputDTO(
                profile:      [],
                nextContent:  [],
                tutorMatches: [],
                gamification: [],
                insights:     [],
                metadata:     ['error' => true, 'message' => $e->getMessage()],
            );
        }
    }

    // ================================================================
    // PROFILE BUILDER (only new logic – aggregates existing signals)
    // ================================================================

    protected function buildProfile(User $user): UserProfileDTO
    {
        return new UserProfileDTO(
            role:              $user->getRoleNames()->first() ?? 'guest',
            age:               $user->age ?? null,
            interests:         $this->getUserInterests($user),
            learningStyle:     $user->learning_style ?? 'visual',
            streak:            $user->streak ?? 0,
            points:            $user->total_points ?? 0,
            completedCount:    $this->countCompleted($user),
            avgSessionMinutes: $this->calculateAvgSession($user),
            peakLearningTime:  $this->calculatePeakLearningTime($user),
        );
    }

    protected function getUserInterests(User $user): array
    {
        try {
            if (method_exists($user, 'interests')) {
                return $user->interests()->pluck('category')->toArray();
            }
        } catch (\Throwable $e) {
            // Ignore
        }
        return [];
    }

    protected function countCompleted(User $user): int
    {
        try {
            if (method_exists($user, 'interactions')) {
                return $user->interactions()->where('completed', true)->count();
            }
        } catch (\Throwable $e) {
            // Ignore
        }
        return 0;
    }

    protected function calculateAvgSession(User $user): float
    {
        try {
            if (method_exists($user, 'interactions')) {
                $avg = $user->interactions()->avg('watch_duration') ?? 0;
                return round($avg / 60, 2);
            }
        } catch (\Throwable $e) {
            // Ignore
        }
        return 0.0;
    }

    /**
     * Analyze when the user typically learns.
     * Returns 'morning', 'afternoon', 'evening', or 'night'.
     */
    protected function calculatePeakLearningTime(User $user): ?string
    {
        try {
            if (!method_exists($user, 'interactions')) {
                return null;
            }

            $hour = $user->interactions()
                ->selectRaw('HOUR(created_at) as hour')
                ->groupBy('hour')
                ->orderByRaw('COUNT(*) DESC')
                ->limit(1)
                ->value('hour');

            return match (true) {
                $hour === null     => null,
                $hour < 6          => 'night',
                $hour < 12         => 'morning',
                $hour < 17         => 'afternoon',
                $hour < 22         => 'evening',
                default            => 'night',
            };
        } catch (\Throwable $e) {
            return null;
        }
    }

    // ================================================================
    // CONTENT DELEGATION (no duplication – uses existing service)
    // ================================================================

    protected function getNextContent(User $user): array
    {
        // Only children and parents consume content
        if (!$user->hasRole('child') && !$user->hasRole('parent')) {
            return [];
        }

        try {
            $interests = method_exists($user, 'interests')
                ? $user->interests
                : collect();

            // Delegate entirely to the existing service
            return $this->discovery
                ->discoverForUser($user, $interests, 5)
                ->toArray();
        } catch (\Throwable $e) {
            Log::warning('Content discovery failed in Brain', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            return [];
        }
    }

    // ================================================================
    // TUTOR MATCHING DELEGATION
    // ================================================================

    protected function getTutorMatches(User $user): array
    {
        if (!$user->hasRole('parent')) {
            return [];
        }

        try {
            if (!method_exists($user, 'children')) {
                return [];
            }

            return $user->children->map(fn ($child) => [
                'child' => [
                    'id'         => $child->id,
                    'first_name' => $child->first_name,
                    'age'        => $child->age ?? null,
                ],
                'matches' => $this->tutorMatcher
                    ->findMatches($child, [], 3)
                    ->map(fn ($tutor) => [
                        'id'            => $tutor->id,
                        'name'          => $tutor->user->name ?? null,
                        'headline'      => $tutor->headline ?? null,
                        'hourly_rate'   => $tutor->hourly_rate ?? null,
                        'rating'        => $tutor->rating ?? null,
                        'match_score'   => round(($tutor->match_score ?? 0) * 100, 1),
                    ])
                    ->toArray(),
            ])->toArray();
        } catch (\Throwable $e) {
            Log::warning('Tutor matching failed in Brain', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            return [];
        }
    }

    // ================================================================
    // GAMIFICATION DELEGATION (optional)
    // ================================================================

    protected function getGamification(User $user): array
    {
        if (!$this->gamification) {
            return [];
        }

        try {
            return [
                'level'       => $this->gamification->getLevel($user) ?? 1,
                'points'      => $this->gamification->getPoints($user) ?? 0,
                'next_badge'  => $this->gamification->getNextBadge($user) ?? null,
                'daily_quest' => $this->gamification->getDailyQuest($user) ?? null,
            ];
        } catch (\Throwable $e) {
            return [];
        }
    }

    // ================================================================
    // INSIGHTS (lightweight – no heavy computation)
    // ================================================================

    protected function getInsights(User $user): array
    {
        // Don't compute insights for users without interests
        $interests = $this->getUserInterests($user);
        if (empty($interests)) {
            return [];
        }

        return [
            'primary_interest' => $interests[0] ?? null,
            'total_interests'  => count($interests),
            'trending_topic'   => $this->getTrendingTopic($user, $interests[0] ?? null),
        ];
    }

    protected function getTrendingTopic(User $user, ?string $topic): ?array
    {
        if (!$topic) return null;

        try {
            // Delegate to existing discovery service
            return $this->discovery
                ->searchByTopic($topic, null, 1, $user)
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
 * Determine whether the Brain should be used for this user.
 * Supports gradual rollout via config.
 */
public function shouldUseBrain(User $user): bool
{
    // Global kill switch
    if (!config('brain.enabled', false)) {
        return false;
    }

    $percentage = (int) config('brain.rollout_percentage', 0);
    if ($percentage <= 0) {
        return false;
    }
    if ($percentage >= 100) {
        return true;
    }

    // Deterministic hash – same user always gets the same result
    $bucket = crc32((string) $user->id) % 100;
    return $bucket < $percentage;
}

/**
 * Cached version of think() – optional wrapper.
 * Only used when cache_ttl_minutes > 0.
 */
public function thinkCached(User $user): BrainOutputDTO
{
    $ttl = (int) config('brain.cache_ttl_minutes', 0);

    if ($ttl <= 0) {
        return $this->think($user);
    }

    $cacheKey = "brain:user:{$user->id}";
    $cached = \Illuminate\Support\Facades\Cache::remember(
        $cacheKey,
        now()->addMinutes($ttl),
        fn () => $this->think($user)->toArray()
    );

    // Rehydrate DTO from cached array
    return new BrainOutputDTO(
        profile:      $cached['profile'] ?? [],
        nextContent:  $cached['next_content'] ?? [],
        tutorMatches: $cached['tutor_matches'] ?? [],
        gamification: $cached['gamification'] ?? [],
        insights:     $cached['insights'] ?? [],
        metadata:     $cached['metadata'] ?? [],
    );
}
}