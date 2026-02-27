<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WatchTimeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'content_module_id',
        'content_category_id',
        'duration', // in minutes
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // UPDATED Relationship
    public function child()
    {
        return $this->belongsTo(User::class, 'child_id')->whereHas('roles', function ($q) {
            $q->where('name', 'child');
        });
    }

    public function contentModule()
    {
        return $this->belongsTo(ContentModule::class);
    }

    public function category()
    {
        return $this->belongsTo(ContentCategory::class, 'content_category_id');
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->where('created_at', '>=', now()->startOfWeek());
    }

    public function scopeForCategory($query, $categoryId)
    {
        return $query->where('content_category_id', $categoryId);
    }
}