<?php

use App\Http\Middleware\FamilyAccess;
use Illuminate\Foundation\Application;
use App\Http\Middleware\EnsureUserType;
use App\Http\Middleware\EnsureChildOnboarding;
use App\Http\Middleware\CheckPendingAssignments;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Http\Middleware\CheckUniversalSkillRequirements;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__ . '/../routes/web.php', commands: __DIR__ . '/../routes/console.php', health: '/up')
    ->withMiddleware(function ($middleware): void {
        // Remove ALL your custom middlewares from global web stack
        // Register your middlewares as named middleware
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'check.onboarding' => \App\Http\Middleware\CheckOnboarding::class,
            'family.access' => \App\Http\Middleware\FamilyAccess::class,
            'check.pending.assignments' => CheckPendingAssignments::class, // KEEP
            'check.universal.skills' => CheckUniversalSkillRequirements::class, // KEEP
            'family.access' => FamilyAccess::class, // SIMPLIFY
        ]);
    })
    ->withExceptions(function ($exceptions): void {
        //
    })
    ->create();
