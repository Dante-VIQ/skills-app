<?php

namespace App\Services;

use App\Models\TutorProfile;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class TutorMatchingService
{
    const WEIGHTS = [
        'subject_match'        => 0.35,
        'experience_match'     => 0.20,
        'teaching_style_match' => 0.15,
        'budget_match'         => 0.15,
        'availability_match'   => 0.10,
        'rating_match'         => 0.05,
    ];

    /**
     * Find smart matches for a child.
     */
    public function findMatches(User $child, array $filters = [], int $limit = 20): Collection
    {
        if (!$child->hasRole('child')) {
            return collect();
        }

        $query = TutorProfile::with('user')
            ->where('is_active', true)
            ->where('accepting_students', true)
            ->where('is_verified', true);

        $query = $this->applyFilters($query, $filters);
        $tutors = $query->get();

        if ($tutors->isEmpty()) {
            return collect();
        }

        return $tutors->map(function ($tutor) use ($child) {
            $score = $this->calculateMatchScore($child, $tutor);
            $tutor->match_score = $score['total'];
            $tutor->match_breakdown = $score['breakdown'];
            return $tutor;
        })->sortByDesc('match_score')->take($limit)->values();
    }

    protected function calculateMatchScore(User $child, TutorProfile $tutor): array
    {
        $breakdown = [];
        $total = 0;

        $subjectScore = $this->calculateSubjectMatch(
            $this->getChildInterests($child),
            $tutor->expertise_areas ?? []
        );
        $breakdown['subject_match'] = $subjectScore;
        $total += $subjectScore * self::WEIGHTS['subject_match'];

        $experienceScore = $this->calculateExperienceMatch(
            $this->getChildAge($child),
            $tutor->years_experience ?? 0
        );
        $breakdown['experience_match'] = $experienceScore;
        $total += $experienceScore * self::WEIGHTS['experience_match'];

        $styleScore = $this->calculateStyleMatch(
            $this->getChildLearningStyle($child),
            $tutor->teaching_styles ?? []
        );
        $breakdown['teaching_style_match'] = $styleScore;
        $total += $styleScore * self::WEIGHTS['teaching_style_match'];

        $budgetScore = $this->calculateBudgetMatch(
            $this->getBudgetRange($child),
            $tutor->hourly_rate ?? 50
        );
        $breakdown['budget_match'] = $budgetScore;
        $total += $budgetScore * self::WEIGHTS['budget_match'];

        $availabilityScore = $this->calculateAvailabilityMatch(
            $this->getPreferredTimes($child),
            $tutor->availability ?? []
        );
        $breakdown['availability_match'] = $availabilityScore;
        $total += $availabilityScore * self::WEIGHTS['availability_match'];

        $ratingScore = ($tutor->rating ?? 0) / 5;
        $breakdown['rating_match'] = $ratingScore;
        $total += $ratingScore * self::WEIGHTS['rating_match'];

        return ['total' => min($total, 1.0), 'breakdown' => $breakdown];
    }

    protected function getChildInterests(User $child): array
    {
        if (!method_exists($child, 'interests')) return [];
        return $child->interests()->pluck('category')->toArray();
    }

    protected function getChildAge(User $child): int
    {
        return $child->age ?? 10;
    }

    protected function getChildLearningStyle(User $child): string
    {
        return $child->learning_style ?? 'visual';
    }

    protected function getBudgetRange(User $child): array
    {
        return ['min' => 20, 'max' => 50];
    }

    protected function getPreferredTimes(User $child): array
    {
        return ['weekdays' => ['monday', 'wednesday', 'friday'], 'time_of_day' => 'evening'];
    }

    protected function calculateSubjectMatch(array $childInterests, array $tutorExpertise): float
    {
        if (empty($childInterests) || empty($tutorExpertise)) return 0.3;
        $matches = array_intersect(
            array_map('strtolower', $childInterests),
            array_map('strtolower', $tutorExpertise)
        );
        return count($matches) / max(count($childInterests), 1);
    }

    protected function calculateExperienceMatch(int $age, int $experience): float
    {
        if ($age < 10) {
            return $experience >= 5 ? 1.0 : ($experience >= 3 ? 0.8 : ($experience >= 1 ? 0.5 : 0.3));
        }
        if ($age <= 14) {
            return $experience >= 3 ? 1.0 : ($experience >= 1 ? 0.7 : 0.4);
        }
        return $experience >= 2 ? 1.0 : ($experience >= 1 ? 0.8 : 0.5);
    }

    protected function calculateStyleMatch(string $childStyle, array $tutorStyles): float
    {
        $compat = [
            'visual'      => ['visual', 'interactive', 'demonstration'],
            'auditory'    => ['lecture', 'discussion', 'storytelling'],
            'kinesthetic' => ['hands-on', 'experiment', 'project-based'],
            'reading'     => ['text-based', 'worksheets', 'research'],
        ];
        $compatible = $compat[$childStyle] ?? ['visual', 'interactive'];
        $matches = array_intersect(
            array_map('strtolower', $compatible),
            array_map('strtolower', $tutorStyles)
        );
        return count($matches) > 0 ? 0.9 : 0.3;
    }

    protected function calculateBudgetMatch(array $budget, float $rate): float
    {
        if ($rate <= ($budget['min'] ?? 0)) return 1.0;
        if ($rate <= ($budget['max'] ?? 100)) return 0.8;
        $excess = $rate - ($budget['max'] ?? 100);
        $penalty = min($excess / ($budget['max'] ?? 100), 0.5);
        return max(0.5 - $penalty, 0);
    }

    protected function calculateAvailabilityMatch(array $preferred, array $avail): float
    {
        if (empty($preferred) || empty($avail)) return 0.5;
        $score = 0;
        $checks = 0;
        if (!empty($preferred['weekdays']) && !empty($avail['days'])) {
            $checks++;
            if (count(array_intersect($preferred['weekdays'], $avail['days'])) > 0) $score++;
        }
        if (!empty($preferred['time_of_day']) && !empty($avail['times'])) {
            $checks++;
            if (in_array($preferred['time_of_day'], $avail['times'])) $score++;
        }
        return $checks > 0 ? $score / $checks : 0.5;
    }

    protected function applyFilters($query, array $filters)
    {
        if (!empty($filters['expertise'])) $query->whereJsonContains('expertise_areas', $filters['expertise']);
        if (isset($filters['min_price']) && $filters['min_price'] !== '') $query->where('hourly_rate', '>=', $filters['min_price']);
        if (isset($filters['max_price']) && $filters['max_price'] !== '') $query->where('hourly_rate', '<=', $filters['max_price']);
        if (isset($filters['min_rating']) && $filters['min_rating'] !== '') $query->where('rating', '>=', $filters['min_rating']);
        if (isset($filters['min_experience']) && $filters['min_experience'] !== '') $query->where('years_experience', '>=', $filters['min_experience']);
        if (!empty($filters['language'])) $query->whereJsonContains('languages', $filters['language']);
        return $query;
    }

    public function getRecommendedTutors(User $child, int $limit = 6): Collection
    {
        $cacheKey = "tutor_recommendations_child_{$child->id}";
        return Cache::remember($cacheKey, 3600, function () use ($child, $limit) {
            return $this->findMatches($child, [], $limit);
        });
    }
}