<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!$request->user()->hasRole($role)) {
            // Redirect based on user's actual role
            $userRole = $request->user()->getRoleNames()->first() ?? 'guest';
            
            return match($userRole) {
                'parent' => redirect()->route('dashboard'),
                'child' => $request->user()->requiresOnboarding() 
                    ? redirect()->route('child.onboarding')
                    : redirect()->route('child.dashboard'),
                'tutor' => redirect()->route('tutor.dashboard'),
                'master' => redirect()->route('master.dashboard'),
                default => redirect()->route('home'),
            };
        }

        return $next($request);
    }
}