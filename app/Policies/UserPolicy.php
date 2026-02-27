<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function viewChildActivities(User $parent, User $child)
    {
        return $parent->hasRole('parent') && 
               $parent->children()->where('id', $child->id)->exists();
    }
}
