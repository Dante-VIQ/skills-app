<?php

namespace App\Services\Brain\DTO;

class UserProfileDTO
{
    public function __construct(
        public readonly string $role,
        public readonly ?int $age,
        public readonly array $interests,
        public readonly string $learningStyle,
        public readonly int $streak,
        public readonly int $points,
        public readonly int $completedCount,
        public readonly float $avgSessionMinutes,
        public readonly ?string $peakLearningTime,
    ) {}

    public function toArray(): array
    {
        return [
            'role'                => $this->role,
            'age'                 => $this->age,
            'interests'           => $this->interests,
            'learning_style'      => $this->learningStyle,
            'streak'              => $this->streak,
            'points'              => $this->points,
            'completed_count'     => $this->completedCount,
            'avg_session_minutes' => $this->avgSessionMinutes,
            'peak_learning_time'  => $this->peakLearningTime,
        ];
    }
}