<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Skill;
use App\Models\Interest;
use App\Models\FamilyRelationship;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
         'username',
        'type',
        'birth_date',
        'avatar',
        'onboarding_completed'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
     protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'onboarding_completed' => 'boolean',
        ];
    }

    // Relationships
public function children()
{
    return $this->hasMany(Child::class, 'parent_id');
}

    public function activeChildren()
{
    return $this->children()->where('is_active', true);
}
    public function parents()
    {
        return $this->hasManyThrough(
            User::class,
            FamilyRelationship::class,
            'child_id',
            'id',
            'id',
            'parent_id'
        )->where('users.type', 'parent');
    }

    public function managedChildren()
    {
        return $this->hasMany(FamilyRelationship::class, 'parent_id');
    }

    public function parentRelationships()
    {
        return $this->hasMany(FamilyRelationship::class, 'child_id');
    }

    // Interests and Skills
    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'user_interests')
                    ->withPivot('affinity_score')
                    ->withTimestamps();
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skills')
                    ->withPivot('proficiency_level')
                    ->withTimestamps();
    }

    // Assignments and Activities
    public function parentAssignments()
    {
        return $this->hasMany(ParentAssignment::class, 'child_id');
    }

    public function createdAssignments()
    {
        return $this->hasMany(ParentAssignment::class, 'parent_id');
    }

    public function activities()
    {
        return $this->hasMany(ChildActivity::class, 'child_id');
    }

    // Content and Learning
    public function watchedContent()
    {
        return $this->belongsToMany(ContentModule::class, 'watch_time_records', 'child_id', 'content_id')
                    ->withPivot('minutes_watched', 'watch_date')
                    ->withTimestamps();
    }

    public function watchTimeRecords()
    {
        return $this->hasMany(WatchTimeRecord::class, 'child_id');
    }

    // Universal Skill Requirements
    public function universalSkillRequirements()
    {
        return $this->hasMany(UniversalSkillRequirement::class, 'child_id');
    }

    // Impact Contributions
    public function impactContributions()
    {
        return $this->hasMany(ChildImpactContribution::class, 'child_id');
    }

    // ========== HELPER METHODS ==========

    public function isChild()
    {
        return $this->type === 'child';
    }

public function isParent()
{
    // Now parents are determined by type column in users table
    return $this->type === 'parent';
}


    public function getDisplayName()
    {
        return $this->isChild() ? explode(' ', $this->name)[0] : $this->name;
    }

    public function getAge()
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    public function hasPendingAssignments()
    {
        return $this->parentAssignments()
            ->where('blocks_content', true)
            ->where('status', 'assigned')
            ->exists();
    }

    public function getNextPendingAssignment()
    {
        return $this->parentAssignments()
            ->where('blocks_content', true)
            ->where('status', 'assigned')
            ->first();
    }
}
