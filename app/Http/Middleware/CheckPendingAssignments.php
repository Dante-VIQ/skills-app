<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ParentAssignment;
use Illuminate\Support\Facades\Auth;
use App\Services\ContentRecommendationService;
use Symfony\Component\HttpFoundation\Response;

class CheckPendingAssignments
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        
        // Only apply to child users
        if (!$user || $user->type !== 'child') {
            return $next($request);
        }
        
        // Routes where assignments should be allowed/visible
        $allowedRoutes = [
            'child.assignment.show',
            'child.assignment.submit',
            'child.onboarding',
            'logout', // Allow logout
        ];
        
        if ($request->routeIs($allowedRoutes)) {
            return $next($request);
        }
        
        if ($user->hasPendingAssignments()) {
            return redirect()->route('child.assignment.show', $user->pendingAssignments()->first())
                ->with('info', 'Please complete your pending assignment first.');
        }
        
        return $next($request);
    }
}