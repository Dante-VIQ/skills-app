<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillsTableSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            ['name' => 'Creativity',        'key' => 'creativity',    'slug' => 'creativity',        'description' => 'Generating new ideas and solutions'],
            ['name' => 'Logical Thinking',  'key' => 'logic',         'slug' => 'logical-thinking',  'description' => 'Solving problems step by step'],
            ['name' => 'Empathy',           'key' => 'empathy',       'slug' => 'empathy',           'description' => 'Understanding others feelings'],
            ['name' => 'Resilience',        'key' => 'resilience',    'slug' => 'resilience',        'description' => 'Bouncing back from challenges'],
            ['name' => 'Curiosity',         'key' => 'curiosity',     'slug' => 'curiosity',         'description' => 'Asking questions and exploring'],
            ['name' => 'Collaboration',     'key' => 'collaboration', 'slug' => 'collaboration',     'description' => 'Working well with others'],
            ['name' => 'Communication',     'key' => 'communication', 'slug' => 'communication',     'description' => 'Expressing ideas clearly'],
        ];

        foreach ($skills as $skill) {
            Skill::updateOrCreate(
                ['key' => $skill['key']],
                $skill
            );
        }
    }
}