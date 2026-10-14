<?php
// app/Models/ImpactProject.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImpactProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'category', 'image_url',
        'goal_amount', 'raised_amount', 'goal_hours', 'contributed_hours',
        'status', 'is_featured',
    ];

    protected $casts = [
        'goal_amount'       => 'decimal:2',
        'raised_amount'     => 'decimal:2',
        'is_featured'       => 'boolean',
    ];

    public function contributions()
    {
        return $this->hasMany(ImpactContribution::class, 'project_id');
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->goal_amount <= 0) return 0;
        return min(100, round(($this->raised_amount / $this->goal_amount) * 100, 1));
    }
}