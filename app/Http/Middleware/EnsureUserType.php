<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserType
{
    public function handle(Request $request, Closure $next, string $type = null): Response
    {
        $user = $request->user();
        
        // if (!$user) {
        //     return redirect()->route('login');
        // }
        
        // If no type specified, just continue
        if (!$type) {
            return $next($request);
        }
        
        // Check if user matches expected type
        // For parent routes, user should be from 'web' guard and type should be 'parent'
        if ($type === 'parent') {
            // Check if user is authenticated via web guard and is parent
            if ($request->user('web') && $user->type === 'parent') {
                return $next($request);
            }
            abort(403, 'This area is for parents only.');
        }
        
        // For child routes, user should be from 'child' guard
        if ($type === 'child') {
            // Check if user is authenticated via child guard
            if ($request->user('child')) {
                return $next($request);
            }
            abort(403, 'This area is for children only.');
        }

        return $next($request);
    }
}