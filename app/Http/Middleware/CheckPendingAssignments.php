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
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Only apply to children
        if ($user && $user->isChild()) {
            $blockingAssignment = ParentAssignment::where('child_id', $user->id)
                ->where('blocks_content', true)
                ->where('status', 'assigned')
                ->first();

            if ($blockingAssignment && !$request->routeIs('child.assignment.*')) {
                return redirect()->route('child.assignment.show', $blockingAssignment);
            }
        }

        return $next($request);
    }
}
