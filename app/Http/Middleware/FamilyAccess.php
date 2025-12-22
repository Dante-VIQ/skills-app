<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Child;

class FamilyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // If no user is logged in, just continue
        if (!$user) {
            return $next($request);
        }
        
        // Skip middleware for auth routes
        if ($request->routeIs('login', 'register', 'logout', 'password.*', 'verification.*')) {
            return $next($request);
        }
        
        // Skip middleware for Livewire update requests
        if ($request->is('livewire/update') || $request->is('livewire/*')) {
            return $next($request);
        }
        
        // Only apply to parents accessing child data
        if ($user->isParent()) {
            if ($request->route('child')) {
                $child = $request->route('child');
                
                // If child is an ID, get the Child model
                if (is_numeric($child)) {
                    $child = Child::find($child);
                }
                
                // Check if child belongs to this parent
                if (!$child || $child->parent_id !== $user->id) {
                    abort(403, 'You do not have access to this child\'s data.');
                }
            }
        }
        
        // Note: Removed child accessing parent logic since children are now separate
        // Children have their own authentication via 'child' guard

        return $next($request);
    }
}