<?php
// app/Services/ExternalResource/KhanAcademyCurator.php

namespace App\Services\ExternalResource;

use App\Models\CuratedResource;
use App\Models\EducationalResource;
use App\Models\EducationalSource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KhanAcademyCurator
{
    protected $source;
    protected $apiBaseUrl = 'https://www.khanacademy.org/api/v1';
    
    public function __construct()
    {
        $this->source = EducationalResource::where('slug', 'khan_academy')->first();
        
        if (!$this->source) {
            app(SourceManager::class)->initializeSources();
            $this->source = EducationalResource::where('slug', 'khan_academy')->first();
        }
    }

    /**
     * Curate Khan Academy topics (manual curation since API access is limited)
     */
    public function curateTopics()
    {
        $topics = [
            // Math
            [
                'title' => 'Early Math',
                'description' => 'Learn numbers, counting, addition, and subtraction.',
                'resource_url' => 'https://www.khanacademy.org/math/early-math',
                'topic' => 'math',
                'subtopic' => 'early-math',
                'difficulty_level' => 'beginner',
                'content_type' => 'course',
                'estimated_minutes' => 600,
                'tags' => ['counting', 'addition', 'subtraction', 'numbers'],
                'thumbnail_url' => 'https://cdn.kastatic.org/images/khan-logo-dark-background.new.png',
            ],
            [
                'title' => 'Arithmetic',
                'description' => 'Learn addition, subtraction, multiplication, and division.',
                'resource_url' => 'https://www.khanacademy.org/math/arithmetic',
                'topic' => 'math',
                'subtopic' => 'arithmetic',
                'difficulty_level' => 'beginner',
                'content_type' => 'course',
                'estimated_minutes' => 900,
                'tags' => ['addition', 'subtraction', 'multiplication', 'division'],
                'thumbnail_url' => 'https://cdn.kastatic.org/images/khan-logo-dark-background.new.png',
            ],
            [
                'title' => 'Pre-Algebra',
                'description' => 'Learn the basics of algebra, including variables and equations.',
                'resource_url' => 'https://www.khanacademy.org/math/pre-algebra',
                'topic' => 'math',
                'subtopic' => 'pre-algebra',
                'difficulty_level' => 'intermediate',
                'content_type' => 'course',
                'estimated_minutes' => 1200,
                'tags' => ['algebra', 'variables', 'equations'],
                'thumbnail_url' => 'https://cdn.kastatic.org/images/khan-logo-dark-background.new.png',
            ],
            [
                'title' => 'Algebra Basics',
                'description' => 'Learn the fundamentals of algebra, including equations and inequalities.',
                'resource_url' => 'https://www.khanacademy.org/math/algebra-basics',
                'topic' => 'math',
                'subtopic' => 'algebra',
                'difficulty_level' => 'intermediate',
                'content_type' => 'course',
                'estimated_minutes' => 900,
                'tags' => ['algebra', 'equations', 'inequalities'],
                'thumbnail_url' => 'https://cdn.kastatic.org/images/khan-logo-dark-background.new.png',
            ],
            [
                'title' => 'Geometry',
                'description' => 'Learn about shapes, angles, and spatial reasoning.',
                'resource_url' => 'https://www.khanacademy.org/math/geometry',
                'topic' => 'math',
                'subtopic' => 'geometry',
                'difficulty_level' => 'intermediate',
                'content_type' => 'course',
                'estimated_minutes' => 1200,
                'tags' => ['geometry', 'shapes', 'angles', 'theorems'],
                'thumbnail_url' => 'https://cdn.kastatic.org/images/khan-logo-dark-background.new.png',
            ],
            
            // Science
            [
                'title' => 'Biology',
                'description' => 'Learn about life, cells, genetics, and evolution.',
                'resource_url' => 'https://www.khanacademy.org/science/biology',
                'topic' => 'science',
                'subtopic' => 'biology',
                'difficulty_level' => 'intermediate',
                'content_type' => 'course',
                'estimated_minutes' => 1500,
                'tags' => ['biology', 'cells', 'genetics', 'evolution'],
                'thumbnail_url' => 'https://cdn.kastatic.org/images/khan-logo-dark-background.new.png',
            ],
            [
                'title' => 'Chemistry',
                'description' => 'Learn about atoms, molecules, and chemical reactions.',
                'resource_url' => 'https://www.khanacademy.org/science/chemistry',
                'topic' => 'science',
                'subtopic' => 'chemistry',
                'difficulty_level' => 'intermediate',
                'content_type' => 'course',
                'estimated_minutes' => 1500,
                'tags' => ['chemistry', 'atoms', 'molecules', 'reactions'],
                'thumbnail_url' => 'https://cdn.kastatic.org/images/khan-logo-dark-background.new.png',
            ],
            [
                'title' => 'Physics',
                'description' => 'Learn about motion, forces, energy, and waves.',
                'resource_url' => 'https://www.khanacademy.org/science/physics',
                'topic' => 'science',
                'subtopic' => 'physics',
                'difficulty_level' => 'intermediate',
                'content_type' => 'course',
                'estimated_minutes' => 1500,
                'tags' => ['physics', 'motion', 'forces', 'energy'],
                'thumbnail_url' => 'https://cdn.kastatic.org/images/khan-logo-dark-background.new.png',
            ],
            
            // Computing
            [
                'title' => 'Computer Programming',
                'description' => 'Learn the basics of programming with JavaScript.',
                'resource_url' => 'https://www.khanacademy.org/computing/computer-programming',
                'topic' => 'computing',
                'subtopic' => 'programming',
                'difficulty_level' => 'beginner',
                'content_type' => 'course',
                'estimated_minutes' => 600,
                'tags' => ['programming', 'javascript', 'coding'],
                'thumbnail_url' => 'https://cdn.kastatic.org/images/khan-logo-dark-background.new.png',
            ],
            [
                'title' => 'Computer Science',
                'description' => 'Learn algorithms, cryptography, and information theory.',
                'resource_url' => 'https://www.khanacademy.org/computing/computer-science',
                'topic' => 'computing',
                'subtopic' => 'computer-science',
                'difficulty_level' => 'advanced',
                'content_type' => 'course',
                'estimated_minutes' => 1200,
                'tags' => ['algorithms', 'cryptography', 'theory'],
                'thumbnail_url' => 'https://cdn.kastatic.org/images/khan-logo-dark-background.new.png',
            ],
            
            // Economics
            [
                'title' => 'Macroeconomics',
                'description' => 'Learn about the economy at a national level.',
                'resource_url' => 'https://www.khanacademy.org/economics-finance-domain/macroeconomics',
                'topic' => 'economics',
                'subtopic' => 'macroeconomics',
                'difficulty_level' => 'intermediate',
                'content_type' => 'course',
                'estimated_minutes' => 900,
                'tags' => ['economics', 'gdp', 'inflation', 'unemployment'],
                'thumbnail_url' => 'https://cdn.kastatic.org/images/khan-logo-dark-background.new.png',
            ],
            [
                'title' => 'Microeconomics',
                'description' => 'Learn about individual economic decisions.',
                'resource_url' => 'https://www.khanacademy.org/economics-finance-domain/microeconomics',
                'topic' => 'economics',
                'subtopic' => 'microeconomics',
                'difficulty_level' => 'intermediate',
                'content_type' => 'course',
                'estimated_minutes' => 900,
                'tags' => ['economics', 'supply', 'demand', 'markets'],
                'thumbnail_url' => 'https://cdn.kastatic.org/images/khan-logo-dark-background.new.png',
            ],
        ];

        $count = 0;
        foreach ($topics as $topic) {
            try {
                CuratedResource::updateOrCreate(
                    [
                        'source_id' => $this->source->id,
                        'resource_url' => $topic['resource_url']
                    ],
                    array_merge($topic, [
                        'slug' => Str::slug('khan-academy-' . $topic['title']),
                        'summary' => $this->generateSummary($topic['description']),
                        'attribution' => $this->source->attribution_format,
                        'source_id' => $this->source->id,
                        'is_active' => true,
                        'quality_score' => 95,
                    ])
                );
                $count++;
            } catch (\Exception $e) {
                Log::error('Failed to curate Khan Academy topic', [
                    'title' => $topic['title'],
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
        return "📚 " . $description . " Khan Academy offers free, world-class education for anyone, anywhere.";
    }
}