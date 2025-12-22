<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserInterest extends Pivot
{
    protected $table = 'user_interests';
    
    protected $fillable = ['user_id', 'interest_id', 'affinity_score'];
    
    protected $casts = [
        'affinity_score' => 'integer',
    ];
}