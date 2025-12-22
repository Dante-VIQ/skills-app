<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\ImpactController;
use App\Livewire\ChildUniversalSkillsPage;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\Auth\ChildLoginController;
use App\Http\Controllers\ChildAssignmentController;
use App\Http\Controllers\ParentDashboardController;
use App\Http\Controllers\ParentAssignmentController;

// Route::view('/', 'welcome');

// Route::view('/', 'components.landing-page')
//     ->name('components.landing-page');

Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::get('/impact', [LandingPageController::class, 'impact'])->name('impact');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';

// Parent routes - only accessible by parents
Route::middleware(['auth', 'verified'])
    ->prefix('parent')
    ->group(function () {
        // Apply user type middleware to all parent routes
        //    Route::middleware(['ensure.user.type:parent'])->group(function () {
        Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');

        Route::get('/add-child', [FamilyController::class, 'create'])->name('parent.add-child');
        Route::post('/children', [FamilyController::class, 'store'])->name('parent.children.store');

        // Assignment routes
        Route::get('/assignments', [ParentAssignmentController::class, 'index'])->name('parent.assignments');
        Route::post('/assignments', [ParentAssignmentController::class, 'store'])->name('parent.assignments.store');

        // Activities route with family access check
        Route::get('/activities/{child}', [ParentAssignmentController::class, 'viewActivities'])
            ->middleware('family.access')
            ->name('parent.activities');
        // });
    });

// Child routes - only accessible by children who completed onboarding
Route::middleware(['auth', 'verified'])
    ->prefix('child')
    ->group(function () {
        // Apply user type middleware to all child routes
        // Route::middleware(['user.type:child', 'child.onboarding'])->group(function () {
        // Route::get('/home', \App\Livewire\ChildDashboard::class);
    Route::get('/child/onboarding', function () {
        return view('child.onboarding');
    })->name('child.onboarding');
        Route::view('home', 'home')
            ->middleware(['auth', 'verified'])
            ->name('child.home');
        // Universal skills page
        Route::get('/universal-skills', \App\Livewire\ChildUniversalSkillsPage::class)->name('child.universal.skills');
        // });
    });

// Child logout
// Route::post('/child/logout', [ChildLoginController::class, 'logout'])
//     ->name('child.logout')
//     ->middleware('auth:child');
// Child assignment routes - accessible by children (bypasses onboarding check)
Route::middleware(['auth', 'verified'])
    ->prefix('child')
    ->group(function () {
        // Apply user type middleware
        // Route::middleware(['user.type:child'])->group(function () {
        Route::get('/assignment/{assignment}', [ChildAssignmentController::class, 'show'])->name('child.assignment.show');
        Route::post('/assignment/{assignment}/submit', [ChildAssignmentController::class, 'submit'])->name('child.assignment.submit');
        // });
    });

// Onboarding route - accessible by children only
// Route::middleware(['auth', 'verified'])->group(function () {

// });

// Show onboarding if not completed, otherwise dashboard
// Route::get('/child/home', function () {
//     $child = auth()->guard('child')->user();
    
//     if ($child && !$child->onboarding_completed) {
//         Route::get('/child/onboarding', function () {
//         return view('child.onboarding');
//     })->name('child.onboarding');
//     }
    
//     return view('child-dashboard'); // Your main dashboard view
// })->name('child.home');

// Public impact portal routes
Route::get('/impact-portal', [ImpactController::class, 'show'])->name('impact.portal');
Route::get('/impact-projects', [ImpactController::class, 'projects'])->name('impact.projects');
Route::get('/project/{project}/updates', [ImpactController::class, 'projectUpdates'])->name('project.updates');

// Child authentication routes

// Child dashboard routes
// Route::middleware(['auth:child'])
//     ->prefix('child')
//     ->name('child.')
//     ->group(function () {
//         Route::get('/dashboard', function () {
//             return view('child.dashboard');
//         })->name('dashboard');

//         // ... other child routes
//     });
