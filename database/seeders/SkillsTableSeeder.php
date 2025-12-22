<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SkillsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run()
{
    $skills = [
        ['name' => 'Creativity', 'key' => 'creativity', 'description' => 'Generating new ideas and solutions'],
        ['name' => 'Logical Thinking', 'key' => 'logic', 'description' => 'Solving problems step by step'],
        ['name' => 'Empathy', 'key' => 'empathy', 'description' => 'Understanding others feelings'],
        ['name' => 'Resilience', 'key' => 'resilience', 'description' => 'Bouncing back from challenges'],
        ['name' => 'Curiosity', 'key' => 'curiosity', 'description' => 'Asking questions and exploring'],
        ['name' => 'Collaboration', 'key' => 'collaboration', 'description' => 'Working well with others'],
        ['name' => 'Communication', 'key' => 'communication', 'description' => 'Expressing ideas clearly'],
    ];

    foreach ($skills as $skill) {
        Skill::create($skill);
    }
}
}
