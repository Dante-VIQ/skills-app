<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorCertification extends Model
{
    protected $fillable = [
        'tutor_id', 'name', 'issuing_organization', 'issue_date',
        'expiry_date', 'credential_id', 'credential_url', 'description',
    ];

    protected $casts = [
        'issue_date'  => 'date',
        'expiry_date' => 'date',
    ];

    public function tutor() { return $this->belongsTo(User::class, 'tutor_id'); }
}