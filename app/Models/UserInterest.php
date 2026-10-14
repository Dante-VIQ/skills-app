<?php
// app/Models/UserInterest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInterest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category',        // e.g., 'programming', 'math', 'robotics'
        'subcategory',      // e.g., 'python', 'algebra', 'arduino'
        'source',          // 'explicit' (user selected) or 'implicit' (ML detected)
        'last_updated_at'
    ];

    protected $casts = [
        'last_updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get active interests (level >= 3 or explicitly selected)
     */
   public function scopeActive($query)
    {
        return $query;
    }

        /**
     * Scope by category
     */
    public function scopeForCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get interests that need content discovery
     */
    public function scopeNeedsDiscovery($query)
    {
        return $query->where('last_updated_at', '<', now()->subDays(7))
                    ->orWhereNull('last_updated_at');
    }
}