<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorEducation extends Model
{
    protected $fillable = [
        'tutor_id', 'degree', 'field_of_study', 'institution',
        'graduation_year', 'grade', 'description',
    ];

    public function tutor() { return $this->belongsTo(User::class, 'tutor_id'); }
}