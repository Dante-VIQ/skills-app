<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorVerification extends Model
{
    protected $fillable = [
        'tutor_id', 'document_type', 'document_path', 'document_name',
        'notes', 'status', 'reviewed_by', 'review_notes', 'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function tutor() { return $this->belongsTo(User::class, 'tutor_id'); }
}