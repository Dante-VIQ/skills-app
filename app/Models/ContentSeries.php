<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentSeries extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'thumbnail',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function modules()
    {
        return $this->hasMany(ContentModule::class, 'series_id')->orderBy('order');
    }
}
