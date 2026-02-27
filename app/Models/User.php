<?php

namespace App\Models;

use App\Models\ChildActivity;
use App\Models\ImpactContribution;
use App\Models\ParentAssignment;
use App\Models\UserInteraction;
use App\Models\WatchTimeRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = ['first_name', 'last_name', 'username', 'email', 'password', 'parent_id', 'parent_code', 'date_of_birth', 'avatar', 'interests', 'skills', 'onboarding_completed', 'is_active', 'last_login_at', 'learning_goals'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'date_of_birth' => 'date',
        'onboarding_completed' => 'boolean',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'interests' => 'array',
        'skills' => 'array',
        'learning_goals' => 'array',
        'interests' => 'array',
        'skill_levels' => 'array',
        'badges' => 'array',
        'settings' => 'array',
        'profile' => 'array'
    ];

    protected $appends = ['full_name', 'initials', 'role_name'];

    // ====================== RELATIONSHIPS ======================

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function activities()
    {
        return $this->hasMany(ChildActivity::class, 'child_id');
    }

    public function receivedAssignments()
    {
        return $this->hasMany(ParentAssignment::class, 'child_id');
    }

    public function assignedAssignments()
    {
        return $this->hasMany(ParentAssignment::class, 'parent_id');
    }

    public function watchTimeRecords()
    {
        return $this->hasMany(WatchTimeRecord::class, 'child_id');
    }

    public function impactContributions()
    {
        return $this->hasMany(ImpactContribution::class, 'user_id');
    }

    // ====================== ATTRIBUTES ======================

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    public function getRoleNameAttribute(): ?string
    {
        return $this->roles->first()?->name;
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    // ====================== ROLE CHECKS ======================

    public function isParent(): bool
    {
        return $this->hasRole('parent');
    }

    public function isChild(): bool
    {
        return $this->hasRole('child');
    }

    public function isTutor(): bool
    {
        return $this->hasRole('tutor');
    }

    public function isMaster(): bool
    {
        return $this->hasRole('master');
    }

    public function requiresOnboarding(): bool
    {
        return $this->isChild() && !$this->onboarding_completed;
    }

    // ====================== SCOPES ======================

    public function scopeParents($query)
    {
        return $query->role('parent');
    }

    public function scopeChildren($query)
    {
        return $query->role('child');
    }

    public function scopeTutors($query)
    {
        return $query->role('tutor');
    }

    public function scopeMasters($query)
    {
        return $query->role('master');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNeedsOnboarding($query)
    {
        return $query->role('child')->where('onboarding_completed', false);
    }


    // Add helper method
    public function hasCompletedOnboarding(): bool
    {
        return $this->onboarding_completed;
    }

    public function getAvatarUrlAttribute(): string
    {
        if (!$this->avatar) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&background=667eea&color=fff&size=200';
        }

        if (str_starts_with($this->avatar, 'emoji:')) {
            $emoji = substr($this->avatar, 6);
            return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='50' fill='%23667eea'/%3E%3Ctext x='50' y='70' font-size='50' text-anchor='middle' fill='white'%3E{$emoji}%3C/text%3E%3C/svg%3E";
        }

        return Storage::url($this->avatar);
    }

public function getInterestsAttribute()
{
    // Get user's interests from profile or derive from interactions
    return $this->profile['interests'] ?? [];
}

 /**
     * Get all interactions for this user
     */
    public function interactions()
    {
        return $this->hasMany(UserInteraction::class);
    }

    /**
     * Get completed resources
     */
    public function completedResources()
    {
        return $this->interactions()
            ->where('completed', true)
            ->with('resource');
    }

    /**
     * Get liked resources
     */
    public function likedResources()
    {
        return $this->interactions()
            ->where('interaction_type', 'like')
            ->with('resource');
    }

    /**
     * Get saved resources
     */
    public function savedResources()
    {
        return $this->interactions()
            ->where('interaction_type', 'save')
            ->with('resource');
    }

    /**
     * Get user's learning stats
     */
    public function getLearningStatsAttribute()
    {
        return [
            'total_interactions' => $this->interactions()->count(),
            'total_watch_time' => $this->interactions()->sum('watch_duration') / 3600, // hours
            'completion_rate' => $this->calculateCompletionRate(),
            'avg_engagement' => $this->interactions()->avg('engagement_score'),
            'favorite_category' => $this->getFavoriteCategory(),
            'skill_levels' => $this->skill_levels ?? [],
            'points' => $this->points ?? 0,
            'badges' => $this->badges ?? []
        ];
    }

    /**
     * Calculate completion rate
     */
    protected function calculateCompletionRate()
    {
        $total = $this->interactions()->count();
        if ($total === 0) return 0;
        
        $completed = $this->interactions()->where('completed', true)->count();
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Get user's favorite category
     */
    protected function getFavoriteCategory()
    {
        return $this->interactions()
            ->join('learning_resources', 'user_interactions.learning_resource_id', '=', 'learning_resources.id')
            ->join('categories', 'learning_resources.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('count(*) as total'))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->first()?->name;
    }

}
