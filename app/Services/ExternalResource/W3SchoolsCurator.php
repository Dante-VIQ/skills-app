<?php
// app/Services/ExternalResource/W3SchoolsCurator.php

namespace App\Services\ExternalResource;

use App\Models\EducationalSource;
use App\Models\CuratedResource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class W3SchoolsCurator
{
    protected $source;
    protected $baseUrl = 'https://www.w3schools.com';
    
    public function __construct()
    {
        $this->source = EducationalSource::where('slug', 'w3schools')->first();
        
        if (!$this->source) {
            // Initialize sources if not exists
            app(SourceManager::class)->initializeSources();
            $this->source = EducationalSource::where('slug', 'w3schools')->first();
        }
    }

    /**
     * Curate W3Schools tutorials by topic
     */
    public function curateTutorials()
    {
        $tutorials = [
            // HTML Tutorials
            [
                'title' => 'HTML Tutorial',
                'description' => 'Learn HTML, the standard markup language for Web pages.',
                'resource_url' => 'https://www.w3schools.com/html/default.asp',
                'topic' => 'html',
                'subtopic' => 'basics',
                'difficulty_level' => 'beginner',
                'content_type' => 'tutorial',
                'estimated_minutes' => 180,
                'tags' => ['html', 'web', 'markup', 'elements'],
                'thumbnail_url' => 'https://www.w3schools.com/images/w3schools_logo_500_04AA6D.png',
            ],
            [
                'title' => 'HTML5 Introduction',
                'description' => 'Learn about the new elements and features in HTML5.',
                'resource_url' => 'https://www.w3schools.com/html/html5_intro.asp',
                'topic' => 'html',
                'subtopic' => 'html5',
                'difficulty_level' => 'intermediate',
                'content_type' => 'tutorial',
                'estimated_minutes' => 60,
                'tags' => ['html5', 'semantic', 'multimedia'],
                'thumbnail_url' => 'https://www.w3schools.com/images/w3schools_logo_500_04AA6D.png',
            ],
            
            // CSS Tutorials
            [
                'title' => 'CSS Tutorial',
                'description' => 'Learn how to style and layout web pages with CSS.',
                'resource_url' => 'https://www.w3schools.com/css/default.asp',
                'topic' => 'css',
                'subtopic' => 'basics',
                'difficulty_level' => 'beginner',
                'content_type' => 'tutorial',
                'estimated_minutes' => 150,
                'tags' => ['css', 'styling', 'selectors', 'properties'],
                'thumbnail_url' => 'https://www.w3schools.com/images/w3schools_logo_500_04AA6D.png',
            ],
            [
                'title' => 'CSS Flexbox',
                'description' => 'Learn how to create flexible layouts with CSS Flexbox.',
                'resource_url' => 'https://www.w3schools.com/css/css3_flexbox.asp',
                'topic' => 'css',
                'subtopic' => 'flexbox',
                'difficulty_level' => 'intermediate',
                'content_type' => 'tutorial',
                'estimated_minutes' => 45,
                'tags' => ['flexbox', 'layout', 'responsive'],
                'thumbnail_url' => 'https://www.w3schools.com/images/w3schools_logo_500_04AA6D.png',
            ],
            [
                'title' => 'CSS Grid',
                'description' => 'Learn how to create complex grid-based layouts with CSS Grid.',
                'resource_url' => 'https://www.w3schools.com/css/css_grid.asp',
                'topic' => 'css',
                'subtopic' => 'grid',
                'difficulty_level' => 'intermediate',
                'content_type' => 'tutorial',
                'estimated_minutes' => 60,
                'tags' => ['grid', 'layout', 'responsive'],
                'thumbnail_url' => 'https://www.w3schools.com/images/w3schools_logo_500_04AA6D.png',
            ],
            
            // JavaScript Tutorials
            [
                'title' => 'JavaScript Tutorial',
                'description' => 'Learn JavaScript, the programming language of the Web.',
                'resource_url' => 'https://www.w3schools.com/js/default.asp',
                'topic' => 'javascript',
                'subtopic' => 'basics',
                'difficulty_level' => 'beginner',
                'content_type' => 'tutorial',
                'estimated_minutes' => 200,
                'tags' => ['javascript', 'programming', 'web'],
                'thumbnail_url' => 'https://www.w3schools.com/images/w3schools_logo_500_04AA6D.png',
            ],
            [
                'title' => 'JavaScript DOM',
                'description' => 'Learn how to manipulate HTML with JavaScript DOM methods.',
                'resource_url' => 'https://www.w3schools.com/js/js_htmldom.asp',
                'topic' => 'javascript',
                'subtopic' => 'dom',
                'difficulty_level' => 'intermediate',
                'content_type' => 'tutorial',
                'estimated_minutes' => 90,
                'tags' => ['dom', 'manipulation', 'events'],
                'thumbnail_url' => 'https://www.w3schools.com/images/w3schools_logo_500_04AA6D.png',
            ],
            
            // Python Tutorials
            [
                'title' => 'Python Tutorial',
                'description' => 'Learn Python, a popular programming language.',
                'resource_url' => 'https://www.w3schools.com/python/default.asp',
                'topic' => 'python',
                'subtopic' => 'basics',
                'difficulty_level' => 'beginner',
                'content_type' => 'tutorial',
                'estimated_minutes' => 180,
                'tags' => ['python', 'programming'],
                'thumbnail_url' => 'https://www.w3schools.com/images/w3schools_logo_500_04AA6D.png',
            ],
            
            // SQL Tutorials
            [
                'title' => 'SQL Tutorial',
                'description' => 'Learn SQL, a standard language for accessing databases.',
                'resource_url' => 'https://www.w3schools.com/sql/default.asp',
                'topic' => 'sql',
                'subtopic' => 'basics',
                'difficulty_level' => 'beginner',
                'content_type' => 'tutorial',
                'estimated_minutes' => 120,
                'tags' => ['sql', 'database', 'queries'],
                'thumbnail_url' => 'https://www.w3schools.com/images/w3schools_logo_500_04AA6D.png',
            ],
            
            // PHP Tutorials
            [
                'title' => 'PHP Tutorial',
                'description' => 'Learn PHP, a server-side scripting language.',
                'resource_url' => 'https://www.w3schools.com/php/default.asp',
                'topic' => 'php',
                'subtopic' => 'basics',
                'difficulty_level' => 'beginner',
                'content_type' => 'tutorial',
                'estimated_minutes' => 150,
                'tags' => ['php', 'server-side', 'web'],
                'thumbnail_url' => 'https://www.w3schools.com/images/w3schools_logo_500_04AA6D.png',
            ],
        ];

        $count = 0;
        foreach ($tutorials as $tutorial) {
            try {
                CuratedResource::updateOrCreate(
                    [
                        'source_id' => $this->source->id,
                        'resource_url' => $tutorial['resource_url']
                    ],
                    array_merge($tutorial, [
                        'slug' => Str::slug('w3schools-' . $tutorial['title']),
                        'summary' => $this->generateSummary($tutorial['description']),
                        'attribution' => $this->source->attribution_format,
                        'source_id' => $this->source->id,
                        'is_active' => true,
                        'quality_score' => 90,
                    ])
                );
                $count++;
            } catch (\Exception $e) {
                Log::error('Failed to curate W3Schools tutorial', [
                    'title' => $tutorial['title'],
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $count;
    }

    /**
     * Generate a kid-friendly summary
     */
    protected function generateSummary($description)
    {
        // Add educational context
        return "Learn " . strtolower($description) . " This tutorial from W3Schools makes it easy for beginners to understand.";
    }
}