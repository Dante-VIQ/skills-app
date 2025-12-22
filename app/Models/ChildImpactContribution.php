<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChildImpactContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id', 'project_id', 'amount_generated',
        'impact_points', 'contribution_type'
    ];

    protected $casts = [
        'amount_generated' => 'decimal:2',
    ];

    public function child()
    {
        return $this->belongsTo(User::class, 'child_id');
    }

    public function project()
    {
        return $this->belongsTo(SocialImpactProject::class, 'project_id');
    }
}
