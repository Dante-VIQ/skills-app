<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TutorAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutor_id',
        'student_id',
        'assigned_by',
        'assigned_at',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function tutor()
    {
        return $this->belongsTo(User::class, 'tutor_id')->whereHas('roles', function ($q) {
            $q->where('name', 'tutor');
        });
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id')->whereHas('roles', function ($q) {
            $q->where('name', 'child');
        });
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}