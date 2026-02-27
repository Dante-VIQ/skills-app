<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Child;

class FamilyAccess
{
public function handle($request, Closure $next)
    {
        $childId = $request->route('child');
        $parent = auth()->user();
        
        // Simple check - parent can only access their own children
        if (!$parent->children()->where('id', $childId)->exists()) {
            abort(403, 'You can only access your own children\'s data.');
        }
        
        return $next($request);
    }
}
