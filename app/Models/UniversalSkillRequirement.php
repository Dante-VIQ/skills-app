<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniversalSkillRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id', 'child_id', 'category_id',
        'required_minutes_per_week', 'is_active', 'blocks_content'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'blocks_content' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function child()
    {
        return $this->belongsTo(User::class, 'child_id');
    }

    public function category()
    {
        return $this->belongsTo(ContentCategory::class, 'category_id');
    }

    public function getWatchedMinutesThisWeek()
    {
        return WatchTimeRecord::where('child_id', $this->child_id)
            ->where('category_id', $this->category_id)
            ->where('watch_date', '>=', now()->startOfWeek())
            ->sum('minutes_watched');
    }

    public function getRemainingMinutes()
    {
        $watched = $this->getWatchedMinutesThisWeek();
        return max(0, $this->required_minutes_per_week - $watched);
    }

    public function isFulfilled()
    {
        return $this->getRemainingMinutes() === 0;
    }
}
