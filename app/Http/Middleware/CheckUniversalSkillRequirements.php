<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\WatchTimeRecord;
use App\Models\UniversalSkillRequirement;
use Symfony\Component\HttpFoundation\Response;

class CheckUniversalSkillRequirements
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->isChild()) {
            $pendingRequirements = $this->getPendingUniversalRequirements($user);

            if ($pendingRequirements->isNotEmpty() && !$request->routeIs('child.universal.skills')) {
                return redirect()->route('child.universal.skills');
            }
        }

        return $next($request);
    }

    private function getPendingUniversalRequirements(User $child)
    {
        return UniversalSkillRequirement::where('child_id', $child->id)
            ->where('is_active', true)
            ->where('blocks_content', true)
            ->get()
            ->filter(function($requirement) use ($child) {
                $watchedThisWeek = WatchTimeRecord::where('child_id', $child->id)
                    ->where('category_id', $requirement->category_id)
                    ->where('watch_date', '>=', now()->startOfWeek())
                    ->sum('minutes_watched');

                return $watchedThisWeek < $requirement->required_minutes_per_week;
            });
    }
}
