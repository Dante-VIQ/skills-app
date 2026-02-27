<?php
// app/Services/ExternalResource/SourceManager.php

namespace App\Services\ExternalResource;

use App\Models\EducationalSource;
use App\Models\CuratedResource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SourceManager
{
    protected $sources = [];
    
    /**
     * Initialize with predefined sources
     */
    public function __construct()
    {
        $this->sources = [
            'w3schools' => [
                'name' => 'W3Schools',
                'website_url' => 'https://www.w3schools.com',
                'topics' => ['html', 'css', 'javascript', 'python', 'sql', 'php'],
                'content_types' => ['tutorial', 'reference', 'exercise'],
                'attribution_format' => 'Via W3Schools',
                'trust_score' => 95,
                'requires_attribution' => true,
                'has_api' => false, // W3Schools doesn't have an official API
            ],
            'khan_academy' => [
                'name' => 'Khan Academy',
                'website_url' => 'https://www.khanacademy.org',
                'topics' => ['math', 'science', 'economics', 'computing', 'arts'],
                'content_types' => ['video', 'article', 'exercise', 'quiz'],
                'attribution_format' => 'Via Khan Academy',
                'trust_score' => 98,
                'requires_attribution' => true,
                'has_api' => true,
                'api_base_url' => 'https://www.khanacademy.org/api/v1',
            ],
            'codecademy' => [
                'name' => 'Codecademy',
                'website_url' => 'https://www.codecademy.com',
                'topics' => ['programming', 'web development', 'data science'],
                'content_types' => ['tutorial', 'interactive', 'project'],
                'attribution_format' => 'Via Codecademy',
                'trust_score' => 90,
                'requires_attribution' => true,
                'has_api' => false,
            ],
            'geeksforgeeks' => [
                'name' => 'GeeksforGeeks',
                'website_url' => 'https://www.geeksforgeeks.org',
                'topics' => ['algorithms', 'data structures', 'programming', 'computer science'],
                'content_types' => ['article', 'tutorial', 'practice'],
                'attribution_format' => 'Via GeeksforGeeks',
                'trust_score' => 85,
                'requires_attribution' => true,
                'has_api' => false,
            ],
            'coursera' => [
                'name' => 'Coursera',
                'website_url' => 'https://www.coursera.org',
                'topics' => ['computer science', 'business', 'personal development'],
                'content_types' => ['course', 'video', 'reading'],
                'attribution_format' => 'Via Coursera',
                'trust_score' => 92,
                'requires_attribution' => true,
                'has_api' => false,
            ],
            'udemy' => [
                'name' => 'Udemy',
                'website_url' => 'https://www.udemy.com',
                'topics' => ['development', 'it', 'software', 'business'],
                'content_types' => ['course', 'video'],
                'attribution_format' => 'Via Udemy',
                'trust_score' => 85,
                'requires_attribution' => true,
                'has_api' => false,
            ],
            'freecodecamp' => [
                'name' => 'freeCodeCamp',
                'website_url' => 'https://www.freecodecamp.org',
                'topics' => ['responsive web', 'javascript', 'frontend', 'backend'],
                'content_types' => ['tutorial', 'interactive', 'project'],
                'attribution_format' => 'Via freeCodeCamp',
                'trust_score' => 96,
                'requires_attribution' => true,
                'has_api' => false,
            ],
            'mdn' => [
                'name' => 'MDN Web Docs',
                'website_url' => 'https://developer.mozilla.org',
                'topics' => ['html', 'css', 'javascript', 'web apis'],
                'content_types' => ['reference', 'guide', 'tutorial'],
                'attribution_format' => 'Via MDN Web Docs',
                'trust_score' => 98,
                'requires_attribution' => true,
                'has_api' => false,
            ],
            'stack_overflow' => [
                'name' => 'Stack Overflow',
                'website_url' => 'https://stackoverflow.com',
                'topics' => ['programming', 'debugging', 'qa'],
                'content_types' => ['qa', 'discussion'],
                'attribution_format' => 'Via Stack Overflow',
                'trust_score' => 88,
                'requires_attribution' => true,
                'has_api' => true,
                'api_base_url' => 'https://api.stackexchange.com/2.3',
            ],
            'brilliant' => [
                'name' => 'Brilliant',
                'website_url' => 'https://brilliant.org',
                'topics' => ['math', 'science', 'computer science'],
                'content_types' => ['interactive', 'course'],
                'attribution_format' => 'Via Brilliant',
                'trust_score' => 94,
                'requires_attribution' => true,
                'has_api' => false,
            ],
        ];
    }

    /**
     * Initialize sources in database
     */
    public function initializeSources()
    {
        foreach ($this->sources as $slug => $config) {
            EducationalSource::updateOrCreate(
                ['slug' => $slug],
                array_merge($config, [
                    'name' => $config['name'],
                    'website_url' => $config['website_url'],
                    'available_topics' => $config['topics'],
                    'content_types' => $config['content_types'],
                    'attribution_format' => $config['attribution_format'],
                    'trust_score' => $config['trust_score'],
                    'requires_attribution' => $config['requires_attribution'],
                    'has_api' => $config['has_api'] ?? false,
                    'api_base_url' => $config['api_base_url'] ?? null,
                ])
            );
        }
        
        return EducationalSource::count();
    }

    /**
     * Get source by slug
     */
    public function getSource($slug)
    {
        return EducationalSource::where('slug', $slug)->first();
    }

    /**
     * Get all active sources
     */
    public function getActiveSources()
    {
        return EducationalSource::where('is_trusted', true)
            ->orderBy('trust_score', 'desc')
            ->get();
    }
}