<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id', 'child_id', 'title', 'description', 'type',
        'content', 'assigned_at', 'due_date', 'completed_at',
        'status', 'blocks_content'
    ];

    protected $casts = [
        'content' => 'array',
        'assigned_at' => 'datetime',
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function child()
    {
        return $this->belongsTo(User::class, 'child_id');
    }

    public function activities()
    {
        return $this->hasMany(ChildActivity::class, 'assignment_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeBlocking($query)
    {
        return $query->where('blocks_content', true)->pending();
    }

    // Methods
    public function isBlocking()
    {
        return $this->blocks_content && $this->status === 'assigned';
    }

    public function markCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }
}
