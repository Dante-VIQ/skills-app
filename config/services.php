<?php

return [

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // ============================================================
    // YOUTUBE
    // ============================================================
    'youtube' => [
        'api_key'     => env('YOUTUBE_API_KEY'),
        'api_url'     => env('YOUTUBE_API_URL', 'https://www.googleapis.com/youtube/v3'),
        'max_results' => env('YOUTUBE_MAX_RESULTS', 50),
        'safe_search' => env('YOUTUBE_SAFE_SEARCH', 'strict'),

        'cache_duration' => 3600,
        'timeout'        => 30,

        'blocked_categories' => [
            '1', '10', '20', '22', '23', '24', '25',
        ],

        'educational_categories' => [
            '27', '28', '26', '29', '35',
        ],

        'rate_limit' => [
            'max_requests' => 10000,
            'per_minute'   => 60,
        ],
    ],

    // ============================================================
    // SAFE BROWSING (optional)
    // ============================================================
    'safebrowsing' => [
        'key'     => env('GOOGLE_SAFE_BROWSING_KEY'),
        'enabled' => env('SAFE_BROWSING_ENABLED', false),
    ],

    // ============================================================
    // PYTHON ML SERVICE
    // ============================================================
    'ml' => [
        'url'     => env('ML_SERVICE_URL', 'http://127.0.0.1:5000'),
        'timeout' => env('ML_SERVICE_TIMEOUT', 5),
    ],

    // ============================================================
    // MESSAGING
    // ============================================================
    'twilio' => [
        'sid'           => env('TWILIO_SID'),
        'auth_token'    => env('TWILIO_AUTH_TOKEN'),
        'whatsapp_from' => env('TWILIO_WHATSAPP_FROM'),
    ],

];