<?php

namespace App\Services;

class RoboticsContentService
{
    /**
     * Robotics project templates
     */
    public function createRoboticsProject($data)
    {
        $project = [
            'title' => $data['title'],
            'difficulty' => $data['difficulty'], // beginner/intermediate/advanced
            'materials' => [
                'required' => $data['materials'],
                'alternative' => $data['alternatives'] ?? []
            ],
            'concepts' => [
                'coding' => $data['coding_concepts'],
                'engineering' => $data['engineering_concepts'],
                'math' => $data['math_concepts']
            ],
            'steps' => $this->formatSteps($data['steps']),
            'code' => $data['code_samples'] ?? null,
            'safety' => $data['safety_notes'] ?? [],
            'external_resources' => [
                'youtube_tutorials' => $data['youtube_links'] ?? [],
                'simulation' => $data['simulator_link'] ?? null
            ]
        ];

        return LearningResource::create([
            'title' => $project['title'],
            'content_type' => 'robotics_project',
            'source_type' => 'original',
            'original_content' => json_encode($project),
            'difficulty_level' => $data['difficulty'],
            'materials_needed' => $data['materials'],
            'estimated_minutes' => $data['estimated_time'],
            'category_id' => $this->getRoboticsCategory(),
            'learning_objectives' => $data['objectives']
        ]);
    }

    /**
     * Integrate with online robotics simulators
     */
    public function getRoboticsSimulator($platform, $projectId)
    {
        $simulators = [
            'tinkercad' => "https://www.tinkercad.com/things/{$projectId}/embed",
            'codecombat' => "https://codecombat.com/play/level/{$projectId}",
            'robotmesh' => "https://app.robotmesh.com/studio/{$projectId}"
        ];

        return $simulators[$platform] ?? null;
    }
}
