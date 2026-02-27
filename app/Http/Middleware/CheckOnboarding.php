<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOnboarding
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Only check for child users
        if (!$user || !$user->isChild()) {
            return $next($request);
        }
        
        // Skip if already on onboarding routes
        if ($request->routeIs('child.onboarding.*')) {
            return $next($request);
        }
        
        // Redirect to onboarding if not completed
        if (!$user->onboarding_completed) {
            return redirect()->route('child.onboarding.index')
                ->with('info', 'Welcome! Let\'s complete your profile first!');
        }
        
        return $next($request);
    }
}