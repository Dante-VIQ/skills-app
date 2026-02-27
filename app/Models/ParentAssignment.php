<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParentAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'child_id',
        'title',
        'description',
        'due_date',
        'status', // pending, in_progress, submitted_for_review, completed, overdue
        'completed_at',
        'feedback',
        'rating',
        'priority', // low, medium, high
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected $appends = [
        'is_overdue',
        'days_until_due',
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id')->whereHas('roles', function ($q) {
            $q->where('name', 'parent');
        });
    }

    public function child()
    {
        return $this->belongsTo(User::class, 'child_id')->whereHas('roles', function ($q) {
            $q->where('name', 'child');
        });
    }

    public function activities()
    {
        return $this->hasMany(ChildActivity::class, 'assignment_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', 'pending');
    }

    public function scopeForChild($query, $childId)
    {
        return $query->where('child_id', $childId);
    }

    public function scopeForParent($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    // Attributes
    public function getIsOverdueAttribute()
    {
        return $this->status === 'pending' && $this->due_date < now();
    }

    public function getDaysUntilDueAttribute()
    {
        return now()->diffInDays($this->due_date, false);
    }

    // Methods
    public function markAsCompleted($feedback = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'feedback' => $feedback,
        ]);
    }

    public function submitForReview()
    {
        $this->update(['status' => 'submitted_for_review']);
    }

    public function isAccessibleBy(User $user)
    {
        if ($user->isMaster()) {
            return true;
        }

        if ($user->isParent()) {
            return $this->parent_id === $user->id;
        }

        if ($user->isChild()) {
            return $this->child_id === $user->id;
        }

        if ($user->isTutor()) {
            return $user->students()->contains($this->child_id);
        }

        return false;
    }
}