<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Interest extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon', 'description'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_interests')
                    ->withPivot('affinity_score')
                    ->withTimestamps();
    }
}

