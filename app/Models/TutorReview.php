<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorReview extends Model
{
    protected $fillable = [
        'tutor_id', 'parent_id', 'child_id', 'rating', 'review',
        'strengths', 'areas_for_improvement', 'is_verified', 'is_public', 'is_featured',
    ];

    protected $casts = [
        'strengths'               => 'array',
        'areas_for_improvement'   => 'array',
        'is_verified'             => 'boolean',
        'is_public'               => 'boolean',
        'is_featured'             => 'boolean',
    ];

    public function tutor()  { return $this->belongsTo(User::class, 'tutor_id'); }
    public function parent() { return $this->belongsTo(User::class, 'parent_id'); }
    public function child()  { return $this->belongsTo(User::class, 'child_id'); }
}