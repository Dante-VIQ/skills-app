<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'url',
        'duration_minutes',
        'difficulty_level',
        'age_min',
        'age_max',
        'category_id',
        'source',
        'external_id',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'duration_minutes' => 'integer',
        'difficulty_level' => 'integer',
        'age_min' => 'integer',
        'age_max' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(ContentCategory::class);
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'content_module_interests');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'content_module_skills');
    }

    public function watchRecords()
    {
        return $this->hasMany(WatchTimeRecord::class);
    }
}
