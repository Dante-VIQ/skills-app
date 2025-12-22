<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WatchTimeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id', 'content_id', 'category_id',
        'minutes_watched', 'watch_date'
    ];

    protected $casts = [
        'watch_date' => 'date',
    ];

    public function child()
    {
        return $this->belongsTo(User::class, 'child_id');
    }

    public function content()
    {
        return $this->belongsTo(ContentModule::class, 'content_id');
    }

    public function category()
    {
        return $this->belongsTo(ContentCategory::class, 'category_id');
    }
}
