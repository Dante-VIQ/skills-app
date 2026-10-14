<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class TutorProfile extends Model
{
    use HasFactory, Searchable;

    protected $table = 'tutor_profiles';

    protected $fillable = [
        'user_id',
        'headline',
        'bio',
        'profile_photo',
        'video_intro',
        'expertise_areas',
        'teaching_styles',
        'languages',
        'timezone',
        'years_experience',
        'highest_education',
        'institution',
        'is_verified',
        'verified_at',
        'verified_by',
        'whatsapp_number',
        'show_whatsapp',
        'allow_direct_calls',
        'allow_messages',
        'availability',
        'hourly_rate',
        'package_rates',
        'rating',
        'total_reviews',
        'total_students',
        'total_hours',
        'profile_views',
        'is_active',
        'is_featured',
        'accepting_students',
    ];

    protected $casts = [
        'expertise_areas'      => 'array',
        'teaching_styles'      => 'array',
        'languages'            => 'array',
        'availability'         => 'array',
        'package_rates'        => 'array',
        'is_verified'          => 'boolean',
        'show_whatsapp'        => 'boolean',
        'allow_direct_calls'   => 'boolean',
        'allow_messages'       => 'boolean',
        'is_active'            => 'boolean',
        'is_featured'          => 'boolean',
        'accepting_students'   => 'boolean',
        'verified_at'          => 'datetime',
        'hourly_rate'          => 'decimal:2',
    ];

    // ==================== RELATIONSHIPS ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(TutorReview::class, 'tutor_id', 'user_id');
    }

    public function verifications()
    {
        return $this->hasMany(TutorVerification::class, 'tutor_id', 'user_id');
    }

    public function education()
    {
        return $this->hasMany(TutorEducation::class, 'tutor_id', 'user_id');
    }

    public function certifications()
    {
        return $this->hasMany(TutorCertification::class, 'tutor_id', 'user_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ==================== ACCESSORS ====================

    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->profile_photo
            ? asset('storage/' . $this->profile_photo)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->user->name ?? 'Tutor')
                . '&size=200&background=6366f1&color=fff';
    }

    public function getCompletionScoreAttribute(): int
    {
        $score = 0;
        $total = 8;

        if ($this->headline)                                       $score++;
        if ($this->bio && strlen($this->bio) > 100)                $score++;
        if ($this->profile_photo)                                  $score++;
        if (!empty($this->expertise_areas))                        $score++;
        if (($this->years_experience ?? 0) > 0)                    $score++;
        if (($this->hourly_rate ?? 0) > 0)                         $score++;
        if (!empty($this->availability))                           $score++;
        if ($this->education()->count() > 0)                       $score++;

        return (int) round(($score / $total) * 100);
    }

    // ==================== SCOPES ====================

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAcceptingStudents($query)
    {
        return $query->where('accepting_students', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByExpertise($query, $area)
    {
        return $query->whereJsonContains('expertise_areas', $area);
    }

    public function scopeMaxRate($query, $amount)
    {
        return $query->where('hourly_rate', '<=', $amount);
    }

    public function scopeMinRating($query, $rating)
    {
        return $query->where('rating', '>=', $rating);
    }

    // ==================== SEARCH ====================

    public function toSearchableArray(): array
    {
        return [
            'id'               => $this->id,
            'headline'         => $this->headline,
            'bio'              => $this->bio,
            'expertise_areas'  => implode(' ', $this->expertise_areas ?? []),
            'teaching_styles'  => implode(' ', $this->teaching_styles ?? []),
            'languages'        => implode(' ', $this->languages ?? []),
            'years_experience' => $this->years_experience,
            'hourly_rate'      => $this->hourly_rate,
            'rating'           => $this->rating,
            'is_verified'      => $this->is_verified,
        ];
    }

    // ==================== METHODS ====================

    public function updateRating(): void
    {
        $this->rating = $this->reviews()->avg('rating') ?? 0;
        $this->total_reviews = $this->reviews()->count();
        $this->save();
    }

    public function incrementViews(): void
    {
        $this->increment('profile_views');
    }
}