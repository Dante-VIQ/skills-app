<?php

namespace Database\Seeders;

use App\Models\Interest;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InterestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run()
{
    $interests = [
        ['name' => 'Robots', 'icon' => '🤖', 'description' => 'Building and programming robots'],
        ['name' => 'Art', 'icon' => '🎨', 'description' => 'Drawing, painting, and creative expression'],
        ['name' => 'Nature', 'icon' => '🌿', 'description' => 'Animals, plants, and outdoor exploration'],
        ['name' => 'Music', 'icon' => '🎵', 'description' => 'Playing instruments, singing, and composing'],
        ['name' => 'Building', 'icon' => '🏗️', 'description' => 'Construction, engineering, and design'],
        ['name' => 'Stories', 'icon' => '📚', 'description' => 'Reading, writing, and storytelling'],
        ['name' => 'Science', 'icon' => '🔬', 'description' => 'Experiments, discovery, and innovation'],
        ['name' => 'Sports', 'icon' => '⚽', 'description' => 'Physical activity, teamwork, and competition'],
        ['name' => 'Animals', 'icon' => '🐾', 'description' => 'Pets, wildlife, and animal care'],
        ['name' => 'Space', 'icon' => '🚀', 'description' => 'Planets, stars, and space exploration'],
    ];

    foreach ($interests as $interest) {
        Interest::create($interest);
    }
}
}
