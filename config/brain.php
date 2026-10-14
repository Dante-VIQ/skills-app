<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Personalization Brain
    |--------------------------------------------------------------------------
    |
    | The Brain orchestrates personalization across the platform. It is
    | disabled by default so that existing code paths remain untouched.
    | Enable per-environment or per-user as you roll out.
    |
    */

    'enabled' => env('BRAIN_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Rollout Percentage
    |--------------------------------------------------------------------------
    |
    | Percentage of users (0-100) that will use the Brain. Useful for A/B
    | testing without changing code. Set to 100 to enable for everyone.
    |
    */
    'rollout_percentage' => env('BRAIN_ROLLOUT_PERCENTAGE', 0),

    /*
    |--------------------------------------------------------------------------
    | Cache TTL
    |--------------------------------------------------------------------------
    |
    | How long to cache the entire Brain output. The underlying services
    | still have their own caches, so this is only for the aggregator.
    | Set to 0 to disable caching (recommended during rollout).
    |
    */
    'cache_ttl_minutes' => env('BRAIN_CACHE_TTL', 0),
];