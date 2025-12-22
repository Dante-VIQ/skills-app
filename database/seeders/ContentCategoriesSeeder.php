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
                'description' => 'Understanding money, budgeting, saving, and basic financial concepts',
                'is_universal' => true,
                'parent_enforced' => true,
                'required_watch_minutes' => 30,
                'icon' => '💰',
                'color' => 'green',
            ],
            [
                'name' => 'Health & Safety',
                'description' => 'Personal health, hygiene, safety awareness, and emergency procedures',
                'is_universal' => true,
                'parent_enforced' => true,
                'required_watch_minutes' => 20,
                'icon' => '🏥',
                'color' => 'red',
            ],
            [
                'name' => 'Digital Citizenship',
                'description' => 'Online safety, digital etiquette, and responsible technology use',
                'is_universal' => true,
                'parent_enforced' => true,
                'required_watch_minutes' => 25,
                'icon' => '💻',
                'color' => 'blue',
            ],
            [
                'name' => 'Environmental Awareness',
                'description' => 'Understanding ecosystems, conservation, and sustainable practices',
                'is_universal' => true,
                'parent_enforced' => false,
                'required_watch_minutes' => 15,
                'icon' => '🌍',
                'color' => 'teal',
            ],
            [
                'name' => 'Emotional Intelligence',
                'description' => 'Recognizing emotions, empathy, self-regulation, and social skills',
                'is_universal' => true,
                'parent_enforced' => false,
                'required_watch_minutes' => 20,
                'icon' => '❤️',
                'color' => 'pink',
            ],
            [
                'name' => 'Robotics & AI',
                'description' => 'Building robots, understanding artificial intelligence',
                'is_universal' => false,
                'parent_enforced' => false,
                'icon' => '🤖',
                'color' => 'purple',
            ],
            [
                'name' => 'Art & Creativity',
                'description' => 'Drawing, painting, design, and creative expression',
                'is_universal' => false,
                'parent_enforced' => false,
                'icon' => '🎨',
                'color' => 'orange',
            ],
            [
                'name' => 'Science & Experiments',
                'description' => 'Chemistry, physics, biology, and hands-on experiments',
                'is_universal' => false,
                'parent_enforced' => false,
                'icon' => '🔬',
                'color' => 'indigo',
            ],
            [
                'name' => 'Music & Sound',
                'description' => 'Playing instruments, singing, music theory, and sound engineering',
                'is_universal' => false,
                'parent_enforced' => false,
                'icon' => '🎵',
                'color' => 'yellow',
            ],
            [
                'name' => 'Sports & Fitness',
                'description' => 'Physical activities, sports rules, and fitness training',
                'is_universal' => false,
                'parent_enforced' => false,
                'icon' => '⚽',
                'color' => 'lime',
            ],
        ];

        foreach ($categories as $category) {
            ContentCategory::create($category);
        }
    }
}