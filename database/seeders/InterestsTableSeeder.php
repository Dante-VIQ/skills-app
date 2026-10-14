<?php

namespace Database\Seeders;

use App\Models\Interest;
use Illuminate\Database\Seeder;

class InterestsTableSeeder extends Seeder
{
    public function run(): void
    {
        $interests = [
            ['name' => 'Robots', 'slug' => 'robots', 'icon' => '🤖', 'description' => 'Building and programming robots'],
            ['name' => 'Art', 'slug' => 'art', 'icon' => '🎨', 'description' => 'Drawing, painting, and creative expression'],
            ['name' => 'Nature', 'slug' => 'nature', 'icon' => '🌿', 'description' => 'Animals, plants, and outdoor exploration'],
            ['name' => 'Music', 'slug' => 'music', 'icon' => '🎵', 'description' => 'Playing instruments, singing, and composing'],
            ['name' => 'Building', 'slug' => 'building', 'icon' => '🏗️', 'description' => 'Construction, engineering, and design'],
            ['name' => 'Stories', 'slug' => 'stories', 'icon' => '📚', 'description' => 'Reading, writing, and storytelling'],
            ['name' => 'Science', 'slug' => 'science', 'icon' => '🔬', 'description' => 'Experiments, discovery, and innovation'],
            ['name' => 'Sports', 'slug' => 'sports', 'icon' => '⚽', 'description' => 'Physical activity, teamwork, and competition'],
            ['name' => 'Animals', 'slug' => 'animals', 'icon' => '🐾', 'description' => 'Pets, wildlife, and animal care'],
            ['name' => 'Space', 'slug' => 'space', 'icon' => '🚀', 'description' => 'Planets, stars, and space exploration'],
        ];

        foreach ($interests as $interest) {
            Interest::updateOrCreate(
                ['slug' => $interest['slug']],
                $interest
            );
        }
    }
}