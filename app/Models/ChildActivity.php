<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChildActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id', 'assignment_id', 'activity_type', 
        'activity_data', 'child_responses', 'score', 'completed_at'
    ];

    protected $casts = [
        'activity_data' => 'array',
        'child_responses' => 'array',
        'completed_at' => 'datetime',
    ];

    public function child()
    {
        return $this->belongsTo(User::class, 'child_id');
    }

    public function assignment()
    {
        return $this->belongsTo(ParentAssignment::class);
    }
}