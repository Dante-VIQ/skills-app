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
        'content_category_id',
        'content_type',
        'duration',
        'difficulty',
        'min_age',
        'max_age',
        'is_active',
        'thumbnail',
        'content_url',
        'embedded_content',
        'series_id',
        'order',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(ContentCategory::class, 'content_category_id');
    }

    public function activities()
    {
        return $this->hasMany(ChildActivity::class, 'content_module_id');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'content_module_skills');
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'content_module_interests');
    }

    public function series()
    {
        return $this->belongsTo(ContentSeries::class, 'series_id');
    }

    public function prerequisites()
    {
        return $this->belongsToMany(ContentModule::class, 'content_prerequisites', 'module_id', 'prerequisite_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForAge($query, $age)
    {
        return $query->where(function ($q) use ($age) {
            $q->whereNull('min_age')
              ->orWhere('min_age', '<=', $age);
        })->where(function ($q) use ($age) {
            $q->whereNull('max_age')
              ->orWhere('max_age', '>=', $age);
        });
    }
}
