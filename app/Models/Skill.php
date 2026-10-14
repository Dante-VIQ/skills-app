<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'key', 'slug', 'description'];

    protected static function booted(): void
    {
        static::creating(function ($skill) {
            if (empty($skill->key)) {
                $skill->key = Str::slug($skill->name, '_');
            }
            if (empty($skill->slug)) {
                $skill->slug = Str::slug($skill->name);
            }
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_skills')
                    ->withPivot('proficiency_level')
                    ->withTimestamps();
    }
}