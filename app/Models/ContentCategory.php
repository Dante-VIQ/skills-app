<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'color',
        'is_active',
        'min_age',
        'max_age',
        'parent_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

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
