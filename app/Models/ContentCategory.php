<?php

namespace App\Models;

use App\Models\ContentModule;
use App\Models\WatchTimeRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContentCategory extends Model
{
    use HasFactory;

   protected $fillable = [
        'name',
        'slug',
        'description',
        'is_universal',
        'parent_enforced',
        'required_watch_minutes',
        'icon',
        'color',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_universal' => 'boolean',
        'parent_enforced' => 'boolean',
    ];

        /**
     * Auto-generate slug from name on creating.
     */
    protected static function booted(): void
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
    
    public function modules()
    {
        return $this->hasMany(ContentModule::class, 'content_category_id');
    }

    public function children()
    {
        return $this->hasMany(ContentCategory::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(ContentCategory::class, 'parent_id');
    }

    public function watchTimeRecords()
    {
        return $this->hasMany(WatchTimeRecord::class, 'content_category_id');
    }
}
