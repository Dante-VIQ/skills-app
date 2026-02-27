<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'youtube' => [
        'api_key' => env('YOUTUBE_API_KEY'),
        'api_url' => env('YOUTUBE_API_URL', 'https://www.googleapis.com/youtube/v3'),
        'max_results' => env('YOUTUBE_MAX_RESULTS', 50),
        'safe_search' => env('YOUTUBE_SAFE_SEARCH', 'strict'),
        
        // Additional configuration
        'cache_duration' => 3600, // Cache results for 1 hour
        'timeout' => 30, // Request timeout in seconds
        
        // Video categories to block (by category ID)
        'blocked_categories' => [
            '1',  // Film & Animation
            '10', // Music (entertainment)
            '20', // Gaming
            '22', // Comedy
            '23', // Entertainment
            '24', // News & Politics
            '25', // How-to & Style (some content may be unsafe)
        ],
        
        // Allowed educational categories
        'educational_categories' => [
            '27', // Education
            '28', // Science & Technology
            '26', // How-to & Style (filtered)
            '29', // Nonprofits & Activism
            '35', // Documentary
        ],
        
        // Rate limiting
        'rate_limit' => [
            'max_requests' => 10000, // Daily quota
            'per_minute' => 60, // Requests per minute
        ],
    ],
    
    // Safe Browsing API (optional, for additional safety)
    'safebrowsing' => [
        'key' => env('GOOGLE_SAFE_BROWSING_KEY'),
        'enabled' => env('SAFE_BROWSING_ENABLED', false),
    ],
    
];
