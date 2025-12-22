<?php

namespace App\Models;

use App\Models\User;
use App\Models\ParentAssignment;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Child extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'parent_id',
        'first_name',
        'last_name',
        'username',
        'email',
        'birth_date',
        'password',
        'onboarding_completed',
        'is_active',
        'avatar',
        'settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'onboarding_completed' => 'boolean',
        'is_active' => 'boolean',
        'settings' => 'array',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationship to parent
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    // Relationship to assignments/tasks
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

    // Full name accessor
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Age calculation
    public function getAgeAttribute()
    {
        return $this->birth_date->age;
    }

    // Scope for active children
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}