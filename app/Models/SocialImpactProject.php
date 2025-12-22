<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialImpactProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'description', 'location',
        'target_amount', 'amount_raised', 'children_impacted',
        'progress_photos', 'completion_date', 'is_active'
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'amount_raised' => 'decimal:2',
        'progress_photos' => 'array',
        'completion_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function contributions()
    {
        return $this->hasMany(ChildImpactContribution::class, 'project_id');
    }

    public function childrenContributors()
    {
        return $this->belongsToMany(User::class, 'child_impact_contributions', 'project_id', 'child_id')
            ->withPivot('amount_generated', 'impact_points')
            ->withTimestamps();
    }

    // Methods
    public function getProgressPercentage()
    {
        if ($this->target_amount == 0) return 0;
        return min(100, ($this->amount_raised / $this->target_amount) * 100);
    }

    public function isFullyFunded()
    {
        return $this->amount_raised >= $this->target_amount;
    }

    public function addContribution($amount)
    {
        $this->increment('amount_raised', $amount);
    }
}
