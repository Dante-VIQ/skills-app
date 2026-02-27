<?php
// app/Services/ExternalResource/GeneralResourceCurator.php

namespace App\Services\ExternalResource;

use App\Models\EducationalSource;
use App\Models\CuratedResource;
use Illuminate\Support\Str;

class GeneralResourceCurator
{
    protected $sourceManager;
    
    public function __construct(SourceManager $sourceManager)
    {
        $this->sourceManager = $sourceManager;
    }

    /**
     * Curate resources from multiple sources
     */
    public function curateAll()
    {
        $results = [
            'w3schools' => app(W3SchoolsCurator::class)->curateTutorials(),
            'khan_academy' => app(KhanAcademyCurator::class)->curateTopics(),
            'mdn' => $this->curateMDN(),
            'freecodecamp' => $this->curateFreeCodeCamp(),
            'geeksforgeeks' => $this->curateGeeksForGeeks(),
        ];
        
        return $results;
    }

    /**
     * Curate MDN resources
     */
    protected function curateMDN()
    {
        $source = EducationalSource::where('slug', 'mdn')->first();
        
        if (!$source) return 0;
        
        $resources = [
            [
                'title' => 'HTML: HyperText Markup Language',
                'description' => 'MDN\'s guide to HTML, the foundation of all websites.',
                'resource_url' => 'https://developer.mozilla.org/en-US/docs/Web/HTML',
                'topic' => 'html',
                'subtopic' => 'basics',
                'difficulty_level' => 'beginner',
                'content_type' => 'reference',
                'estimated_minutes' => 300,
                'tags' => ['html', 'web', 'elements', 'attributes'],
            ],
            [
                'title' => 'CSS: Cascading Style Sheets',
                'description' => 'MDN\'s comprehensive guide to CSS.',
                'resource_url' => 'https://developer.mozilla.org/en-US/docs/Web/CSS',
                'topic' => 'css',
                'subtopic' => 'basics',
                'difficulty_level' => 'beginner',
                'content_type' => 'reference',
                'estimated_minutes' => 300,
                'tags' => ['css', 'styling', 'layout'],
            ],
            [
                'title' => 'JavaScript',
                'description' => 'MDN\'s complete JavaScript documentation and tutorials.',
                'resource_url' => 'https://developer.mozilla.org/en-US/docs/Web/JavaScript',
                'topic' => 'javascript',
                'subtopic' => 'basics',
                'difficulty_level' => 'beginner',
                'content_type' => 'reference',
                'estimated_minutes' => 400,
                'tags' => ['javascript', 'programming', 'web'],
            ],
        ];

        return $this->saveResources($source, $resources);
    }

    /**
     * Curate freeCodeCamp resources
     */
    protected function curateFreeCodeCamp()
    {
        $source = EducationalSource::where('slug', 'freecodecamp')->first();
        
        if (!$source) return 0;
        
        $resources = [
            [
                'title' => 'Responsive Web Design Certification',
                'description' => 'Learn HTML and CSS to build responsive web pages.',
                'resource_url' => 'https://www.freecodecamp.org/learn/responsive-web-design/',
                'topic' => 'web',
                'subtopic' => 'responsive',
                'difficulty_level' => 'beginner',
                'content_type' => 'course',
                'estimated_minutes' => 3000,
                'tags' => ['html', 'css', 'responsive', 'web'],
            ],
            [
                'title' => 'JavaScript Algorithms and Data Structures',
                'description' => 'Learn JavaScript fundamentals and algorithms.',
                'resource_url' => 'https://www.freecodecamp.org/learn/javascript-algorithms-and-data-structures/',
                'topic' => 'javascript',
                'subtopic' => 'algorithms',
                'difficulty_level' => 'intermediate',
                'content_type' => 'course',
                'estimated_minutes' => 3000,
                'tags' => ['javascript', 'algorithms', 'data structures'],
            ],
            [
                'title' => 'Front End Development Libraries',
                'description' => 'Learn popular frontend libraries like React, Bootstrap, and jQuery.',
                'resource_url' => 'https://www.freecodecamp.org/learn/front-end-development-libraries/',
                'topic' => 'frontend',
                'subtopic' => 'libraries',
                'difficulty_level' => 'intermediate',
                'content_type' => 'course',
                'estimated_minutes' => 2000,
                'tags' => ['react', 'bootstrap', 'jquery', 'frontend'],
            ],
        ];

        return $this->saveResources($source, $resources);
    }

    /**
     * Curate GeeksforGeeks resources
     */
    protected function curateGeeksForGeeks()
    {
        $source = EducationalSource::where('slug', 'geeksforgeeks')->first();
        
        if (!$source) return 0;
        
        $resources = [
            [
                'title' => 'Data Structures',
                'description' => 'Comprehensive guide to data structures.',
                'resource_url' => 'https://www.geeksforgeeks.org/data-structures/',
                'topic' => 'computer-science',
                'subtopic' => 'data-structures',
                'difficulty_level' => 'intermediate',
                'content_type' => 'tutorial',
                'estimated_minutes' => 1200,
                'tags' => ['arrays', 'linked lists', 'trees', 'graphs'],
            ],
            [
                'title' => 'Algorithms',
                'description' => 'Learn algorithms with examples and implementations.',
                'resource_url' => 'https://www.geeksforgeeks.org/fundamentals-of-algorithms/',
                'topic' => 'computer-science',
                'subtopic' => 'algorithms',
                'difficulty_level' => 'intermediate',
                'content_type' => 'tutorial',
                'estimated_minutes' => 1200,
                'tags' => ['sorting', 'searching', 'dynamic programming'],
            ],
            [
                'title' => 'Python Programming Language',
                'description' => 'Learn Python from basics to advanced concepts.',
                'resource_url' => 'https://www.geeksforgeeks.org/python-programming-language/',
                'topic' => 'python',
                'subtopic' => 'basics',
                'difficulty_level' => 'beginner',
                'content_type' => 'tutorial',
                'estimated_minutes' => 900,
                'tags' => ['python', 'programming'],
            ],
        ];

        return $this->saveResources($source, $resources);
    }

    /**
     * Save resources to database
     */
    protected function saveResources($source, $resources)
    {
        $count = 0;
        
        foreach ($resources as $resource) {
            try {
                CuratedResource::updateOrCreate(
                    [
                        'source_id' => $source->id,
                        'resource_url' => $resource['resource_url']
                    ],
                    array_merge($resource, [
                        'slug' => Str::slug($source->slug . '-' . $resource['title']),
                        'summary' => $this->generateSummary($resource['description'], $source->name),
                        'attribution' => "Via {$source->name}",
                        'source_id' => $source->id,
                        'thumbnail_url' => $resource['thumbnail_url'] ?? $source->logo_url,
                        'is_active' => true,
                        'quality_score' => 85,
                    ])
                );
                $count++;
            } catch (\Exception $e) {
                \Log::error('Failed to save resource', [
                    'source' => $source->name,
                    'title' => $resource['title'],
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $count;
    }

    /**
     * Generate summary with attribution
     */
    protected function generateSummary($description, $sourceName)
    {
        return "🔗 " . $description . " This resource from {$sourceName} is recommended for deeper learning.";
    }
}