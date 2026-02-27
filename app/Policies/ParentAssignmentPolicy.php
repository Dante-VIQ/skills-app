<?php

namespace App\Policies;

use App\Models\ParentAssignment;
use App\Models\User;
use Illuminate\Auth\Access\Response;


   class ParentAssignmentPolicy
{
    public function view(User $user, ParentAssignment $assignment)
    {
        // Child can view if it's assigned to them
        if ($user->hasRole('child')) {
            return $assignment->child_id === $user->id;
        }
        
        // Parent can view if they created it
        if ($user->hasRole('parent')) {
            return $assignment->parent_id === $user->id;
        }
        
        // Tutor can view if student is assigned to them
        if ($user->hasRole('tutor')) {
            return $user->assignedStudents()->contains($assignment->child_id);
        }
        
        return $user->hasRole('master');
    }
    
    public function submit(User $user, ParentAssignment $assignment)
    {
        return $user->hasRole('child') && 
               $assignment->child_id === $user->id &&
               $assignment->status === 'pending';
    }
}
