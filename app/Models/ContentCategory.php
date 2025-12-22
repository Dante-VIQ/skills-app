<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_universal', 'parent_enforced', 'required_watch_minutes'];

    protected $casts = [
        'is_universal' => 'boolean',
        'parent_enforced' => 'boolean',
    ];

    public function contentModules()
    {
        return $this->hasMany(ContentModule::class);
    }

    public function universalRequirements()
    {
        return $this->hasMany(UniversalSkillRequirement::class);
    }
}
