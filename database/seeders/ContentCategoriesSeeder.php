<?php

namespace Database\Seeders;

use App\Models\ContentCategory;
use Illuminate\Database\Seeder;

class ContentCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Financial Literacy',
                'slug' => 'financial-literacy',
                'description' => 'Understanding money, budgeting, saving, and basic financial concepts',
                'is_universal' => true,
                'parent_enforced' => true,
                'required_watch_minutes' => 30,
                'icon' => '💰',
                'color' => 'green',
            ],
            [
                'name' => 'Health & Safety',
                'slug' => 'health-safety',
                'description' => 'Personal health, hygiene, safety awareness, and emergency procedures',
                'is_universal' => true,
                'parent_enforced' => true,
                'required_watch_minutes' => 20,
                'icon' => '🏥',
                'color' => 'red',
            ],
            [
                'name' => 'Digital Citizenship',
                'slug' => 'digital-citizenship',
                'description' => 'Online safety, digital etiquette, and responsible technology use',
                'is_universal' => true,
                'parent_enforced' => true,
                'required_watch_minutes' => 25,
                'icon' => '💻',
                'color' => 'blue',
            ],
            [
                'name' => 'Environmental Awareness',
                'slug' => 'environmental-awareness',
                'description' => 'Understanding ecosystems, conservation, and sustainable practices',
                'is_universal' => true,
                'parent_enforced' => false,
                'required_watch_minutes' => 15,
                'icon' => '🌍',
                'color' => 'teal',
            ],
            [
                'name' => 'Emotional Intelligence',
                'slug' => 'emotional-intelligence',
                'description' => 'Recognizing emotions, empathy, self-regulation, and social skills',
                'is_universal' => true,
                'parent_enforced' => false,
                'required_watch_minutes' => 20,
                'icon' => '❤️',
                'color' => 'pink',
            ],
            [
                'name' => 'Robotics & AI',
                'slug' => 'robotics-ai',
                'description' => 'Building robots, understanding artificial intelligence',
                'is_universal' => false,
                'parent_enforced' => false,
                'icon' => '🤖',
                'color' => 'purple',
            ],
            [
                'name' => 'Art & Creativity',
                'slug' => 'art-creativity',
                'description' => 'Drawing, painting, design, and creative expression',
                'is_universal' => false,
                'parent_enforced' => false,
                'icon' => '🎨',
                'color' => 'orange',
            ],
            [
                'name' => 'Science & Experiments',
                'slug' => 'science-experiments',
                'description' => 'Chemistry, physics, biology, and hands-on experiments',
                'is_universal' => false,
                'parent_enforced' => false,
                'icon' => '🔬',
                'color' => 'indigo',
            ],
            [
                'name' => 'Music & Sound',
                'slug' => 'music-sound',
                'description' => 'Playing instruments, singing, music theory, and sound engineering',
                'is_universal' => false,
                'parent_enforced' => false,
                'icon' => '🎵',
                'color' => 'yellow',
            ],
            [
                'name' => 'Sports & Fitness',
                'slug' => 'sports-fitness',
                'description' => 'Physical activities, sports rules, and fitness training',
                'is_universal' => false,
                'parent_enforced' => false,
                'icon' => '⚽',
                'color' => 'lime',
            ],
        ];

        foreach ($categories as $category) {
            ContentCategory::updateOrCreate(
                ['slug' => $category['slug']],   // match on slug instead of name
                $category
            );
        }
    }
}