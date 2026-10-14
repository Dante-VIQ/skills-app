<?php

namespace App\Services\Brain\DTO;

class BrainOutputDTO
{
    public function __construct(
        public readonly array $profile,
        public readonly array $nextContent,
        public readonly array $tutorMatches,
        public readonly array $gamification,
        public readonly array $insights,
        public readonly array $metadata,
    ) {}

    public function toArray(): array
    {
        return [
            'profile'         => $this->profile,
            'next_content'    => $this->nextContent,
            'tutor_matches'   => $this->tutorMatches,
            'gamification'    => $this->gamification,
            'insights'        => $this->insights,
            'metadata'        => $this->metadata,
        ];
    }

    public function content(): array
    {
        return $this->nextContent;
    }

    public function hasContent(): bool
    {
        return !empty($this->nextContent);
    }
}