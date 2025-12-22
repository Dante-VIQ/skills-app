<?php

use App\Http\Middleware\FamilyAccess;
use Illuminate\Foundation\Application;
use App\Http\Middleware\EnsureUserType;
use App\Http\Middleware\EnsureChildOnboarding;
use App\Http\Middleware\CheckPendingAssignments;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckUniversalSkillRequirements;
use App\Http\Middleware\UserType;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web([
            \App\Http\Middleware\EnsureChildOnboarding::class,
            \App\Http\Middleware\CheckPendingAssignments::class,
            \App\Http\Middleware\CheckUniversalSkillRequirements::class,
            EnsureUserType::class,
            FamilyAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
