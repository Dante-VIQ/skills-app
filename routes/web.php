<?php

use App\Http\Controllers\Child\OnboardingController;
use App\Http\Controllers\Child\ProgressController;
use App\Http\Controllers\ChildAssignmentController;
use App\Http\Controllers\ChildDashboardController;
use App\Http\Controllers\ExternalResourceController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\Master\ContentController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\MasterDashboardController;
use App\Http\Controllers\ParentAssignmentController;
use App\Http\Controllers\ParentDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TutorDashboardController;
use App\Http\Controllers\UniversalLearningController;
use App\Http\Controllers\YouTubeController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Public routes
// The named landing-page route below is the canonical home route.

// Protected routes
Route::middleware(['auth'])->group(function () {

    // ============= UNIVERSAL LEARNING ROUTE =============
    Route::get('/learn', [UniversalLearningController::class, 'index'])
        ->name('learn');

    // ============= ONBOARDING ROUTES =============
    Route::get('/onboarding/interests', [OnboardingController::class, 'showInterests'])
        ->name('onboarding.interests');

    Route::post('/onboarding/interests', [OnboardingController::class, 'saveInterests'])
        ->name('onboarding.interests.save');

    // ============= API ROUTES =============
    Route::prefix('api')->group(function () {
        Route::get('/learning/more', [UniversalLearningController::class, 'loadMore'])
            ->name('api.learning.more');
        Route::post('/learning/refresh', [UniversalLearningController::class, 'refresh'])
            ->name('api.learning.refresh');
    });

    // Route::get('/learning/{id}', [App\Http\Controllers\Child\LearningController::class, 'show'])
    //     ->name('learning.show');
});

// Redirect old routes
Route::redirect('/child/learn', '/learn');
Route::redirect('/parent/learn', '/learn');
Route::redirect('/tutor/learn', '/learn');
// ======================
// PUBLIC ROUTES
// ======================
Route::get('/', [LandingPageController::class, 'index'])->name('home');

// Legal pages
Route::get('/terms', [LegalController::class, 'terms'])->name('terms');
Route::get('/privacy', [LegalController::class, 'privacy'])->name('privacy');
Route::get('/acceptable-use', [LegalController::class, 'acceptableUse'])->name('acceptable-use');
Route::get('/cookie-policy', [LegalController::class, 'cookiePolicy'])->name('cookie-policy');

// Auth routes (from Breeze)
require __DIR__ . '/auth.php';
require __DIR__ . '/teams.php';


// ======================
// DASHBOARD REDIRECT
// =====================
Route::get('/dashboard', function () {
    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login');
    }

    $role = $user->getRoleNames()->first();

    if ($user->isChild() && $user->requiresOnboarding()) {
        return redirect()->route('child.onboarding');
    }

    return match ($role) {
        'parent' => redirect()->route('parent.dashboard'),
        'child' => redirect()->route('child.dashboard'),
        'tutor' => redirect()->route('tutor.dashboard'),
        'master' => redirect()->route('master.dashboard'),
        default => redirect()->route('home'),
    };
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // External resources browsing
    Route::get('/resources', [ExternalResourceController::class, 'index'])->name('resources.index');
    Route::get('/resources/{slug}', [ExternalResourceController::class, 'show'])->name('resources.show');
    Route::get('/resources/redirect/{id}', [ExternalResourceController::class, 'redirect'])->name('resources.redirect');

    // API endpoints
    Route::get('/api/resources/related/{learningResource}', [ExternalResourceController::class, 'related']);
    Route::get('/api/resources/recommendations', [ExternalResourceController::class, 'recommendations']);
    Route::post('/api/resources/feedback/{id}', [ExternalResourceController::class, 'feedback']);
});

// Admin routes for curation
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/resources/curate', function () {
        return view('admin.resources.curate');
    })->name('admin.resources.curate');

    Route::post('/resources/curate/run', function () {
        Artisan::call('resources:curate');
        return redirect()->back()->with('success', 'Curation completed!');
    })->name('admin.resources.curate.run');
});
// Admin routes for YouTube integration
Route::middleware(['auth', 'can:manage-content'])->prefix('admin/youtube')->group(function () {
    Route::get('/search', [YouTubeController::class, 'search'])->name('admin.youtube.search');
    Route::get('/preview/{videoId}', [YouTubeController::class, 'preview'])->name('admin.youtube.preview');
    Route::post('/import', [YouTubeController::class, 'import'])->name('admin.youtube.import');
    Route::get('/channel/{channelId}', [YouTubeController::class, 'channelVideos'])->name('admin.youtube.channel');
});

Route::middleware(['auth'])->group(function () {
    // Main learning dashboard
    Route::get('/learn', [App\Http\Controllers\Child\LearningController::class, 'index'])->name('learning.index');

    // Child-specific route (if you want a separate URL)
    Route::get('/child/learn', [App\Http\Controllers\Child\LearningController::class, 'index'])->name('child.learn');

    // View specific resource
    Route::get('/learn/{id}', [App\Http\Controllers\Child\LearningController::class, 'show'])->name('learning.show');

    // API endpoints
    Route::post('/learn/track', [App\Http\Controllers\Child\LearningController::class, 'trackInteraction'])->name('learning.track');
    Route::get('/learn/more', [App\Http\Controllers\Child\LearningController::class, 'getMoreRecommendations'])->name('learning.more');
});

// ======================
// PROFILE ROUTES
// ======================
Route::middleware(['auth', 'verified'])
    ->prefix('profile')
    ->name('profile.')
    ->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar'])->name('avatar.delete');
        Route::get('/notifications', [ProfileController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/{id}/read', [ProfileController::class, 'markNotificationAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [ProfileController::class, 'markAllNotificationsAsRead'])->name('notifications.read-all');
    });

// ======================
// PARENT ROUTES
// ======================
// ======================
// PARENT ROUTES
// ======================
Route::middleware(['auth', 'verified', 'role:parent'])
    ->prefix('parent')
    ->name('parent.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');

        // Child management
        Route::middleware(['permission:manage children'])->group(function () {
            // ADD THIS LINE - The missing route for showing the add child form
            Route::get('/add-child', [ParentDashboardController::class, 'addChildForm'])->name('add-child');
            Route::post('/children/{child}/resend-credentials', [ParentDashboardController::class, 'resendCredentials'])->name('children.resend-credentials');

            Route::get('/children', [ParentDashboardController::class, 'children'])->name('children');
            Route::post('/children', [ParentDashboardController::class, 'addChild'])->name('children.store');
            Route::get('/children/{child}', [ParentDashboardController::class, 'viewChild'])->name('children.view');
        });

        // Assignment management
        Route::middleware(['permission:create assignments'])->group(function () {
            Route::get('/assignments', [ParentAssignmentController::class, 'index'])->name('assignments');
            Route::get('/assignments/create', [ParentAssignmentController::class, 'create'])->name('assignments.create');
            Route::post('/assignments', [ParentAssignmentController::class, 'store'])->name('assignments.store');
            Route::get('/assignments/{assignment}', [ParentAssignmentController::class, 'show'])->name('assignments.show');
        });

        // Activities
        Route::get('/activities/{child}', [ParentAssignmentController::class, 'viewActivities'])
            ->middleware('family.access')
            ->name('activities');
    });

// ======================
// CHILD ROUTES
// ======================

// ======================
// CHILD ONBOARDING ROUTES
// ======================
Route::middleware(['auth', 'verified', 'role:child'])
    ->prefix('child/onboarding')
    ->name('child.onboarding.')
    ->group(function () {
        // Welcome page
        Route::get('/', [App\Http\Controllers\Child\OnboardingController::class, 'index'])->name('index');

        // Step 1: Avatar
        Route::get('/step1', [App\Http\Controllers\Child\OnboardingController::class, 'step1'])->name('step1');
        Route::post('/step1', [App\Http\Controllers\Child\OnboardingController::class, 'saveAvatar'])->name('step1.save');

        // Step 2: Interests
        Route::get('/step2', [App\Http\Controllers\Child\OnboardingController::class, 'step2'])->name('step2');
        Route::post('/step2', [App\Http\Controllers\Child\OnboardingController::class, 'saveInterests'])->name('step2.save');

        // Step 3: Goals
        Route::get('/step3', [App\Http\Controllers\Child\OnboardingController::class, 'step3'])->name('step3');
        Route::post('/step3', [App\Http\Controllers\Child\OnboardingController::class, 'saveGoals'])->name('step3.save');

        // Step 4: Tour
        Route::get('/step4', [App\Http\Controllers\Child\OnboardingController::class, 'step4'])->name('step4');

        // Complete
        Route::post('/complete', [App\Http\Controllers\Child\OnboardingController::class, 'complete'])->name('complete');

        // Skip
        Route::post('/skip', [App\Http\Controllers\Child\OnboardingController::class, 'skip'])->name('skip');

        // Get current step (AJAX)
        Route::get('/current-step', [App\Http\Controllers\Child\OnboardingController::class, 'getCurrentStep'])->name('current-step');
    });
Route::middleware(['auth', 'verified', 'role:child'])
    ->prefix('child')
    ->name('child.')
    ->group(function () {
        // Onboarding routes (no onboarding check)
        Route::get('/onboarding', [OnboardingController::class, 'index'])
            ->name('onboarding')
            ->withoutMiddleware(['check.onboarding']);

        Route::post('/onboarding/complete', [OnboardingController::class, 'complete'])
            ->name('onboarding.complete')
            ->withoutMiddleware(['check.onboarding']);

        Route::post('/onboarding/skip', [OnboardingController::class, 'skip'])
            ->name('onboarding.skip')
            ->withoutMiddleware(['check.onboarding']);

        // Routes requiring completed onboarding
        Route::middleware(['check.onboarding'])->group(function () {
            // Dashboard
            Route::get('/dashboard', [ChildDashboardController::class, 'index'])->name('dashboard');

            // // Learning routes
            // Route::get('/child/learn', [App\Http\Controllers\Child\LearningController::class, 'index'])->name('Learn.index');
            // Route::get('/learn/{module}', [App\Http\Controllers\Child\LearningController::class, 'show'])->name('learn.show');
            // Route::post('/learn/{module}/complete', [App\Http\Controllers\Child\LearningController::class, 'completeModule'])->name('learn.complete');
            // Route::get('/learn/saved', [App\Http\Controllers\Child\LearningController::class, 'savedContent'])->name('learn.saved');
            // Route::post('/learn/{module}/toggle-save', [App\Http\Controllers\Child\LearningController::class, 'toggleSave'])->name('learn.toggle-save');

            // Progress routes
            Route::get('/progress', [ProgressController::class, 'index'])->name('progress');
            Route::get('/progress/activities', [ProgressController::class, 'activityLog'])->name('progress.activities');
            Route::get('/progress/skills', [ProgressController::class, 'skillReport'])->name('progress.skills');

            Route::get('/refresh-recommendations', [ChildDashboardController::class, 'refreshRecommendations'])->name('child.refresh-recommendations');
            // Assignment routes
            Route::middleware(['permission:submit assignments'])->group(function () {
                Route::get('/assignments', [ChildAssignmentController::class, 'index'])->name('assignments');
                Route::get('/assignments/{assignment}', [ChildAssignmentController::class, 'show'])->name('assignments.show');
                Route::post('/assignments/{assignment}/submit', [ChildAssignmentController::class, 'submit'])->name('assignments.submit');
            });
        });

        Route::get('/assignments', [ChildAssignmentController::class, 'index'])->name('assignments');
        Route::get('/assignments/{assignment}', [ChildAssignmentController::class, 'show'])->name('assignments.show');
        Route::post('/assignments/{assignment}/submit', [ChildAssignmentController::class, 'submit'])->name('assignments.submit');
    });


// ======================
// TUTOR ROUTES
// ======================
Route::middleware(['auth', 'verified', 'role:tutor'])
    ->prefix('tutor')
    ->name('tutor.')
    ->group(function () {
        Route::get('/dashboard', [TutorDashboardController::class, 'index'])->name('dashboard');

        Route::middleware(['permission:manage assigned children'])->group(function () {
            Route::get('/students', [TutorDashboardController::class, 'students'])->name('students');
            Route::get('/students/{student}', [TutorDashboardController::class, 'viewStudent'])->name('students.view');
            Route::get('/students/{student}/progress', [TutorDashboardController::class, 'studentProgress'])->name('students.progress');
        });

        // Assignment review
        Route::get('/assignments/review', [TutorDashboardController::class, 'pendingReviews'])->name('assignments.review');
        Route::get('/assignments/{assignment}/review', [TutorDashboardController::class, 'reviewAssignment'])->name('assignments.review.show');
        Route::post('/assignments/{assignment}/review', [TutorDashboardController::class, 'submitReview'])->name('assignments.review.submit');
    });

// ======================
// MASTER (ADMIN) ROUTES
// ======================
Route::middleware(['auth', 'verified', 'role:master'])
    ->prefix('master')
    ->name('master.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [MasterDashboardController::class, 'index'])->name('dashboard');

        // User management
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('users/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
        Route::post('stop-impersonating', [UserController::class, 'stopImpersonating'])->name('users.stop-impersonating');

        // Content management
        Route::get('content', [ContentController::class, 'index'])->name('content.index');
        Route::get('content/categories', [ContentController::class, 'categories'])->name('content.categories');
        Route::post('content/categories', [ContentController::class, 'storeCategory'])->name('content.categories.store');
        Route::get('content/modules/create', [ContentController::class, 'createModule'])->name('content.modules.create');
        Route::post('content/modules', [ContentController::class, 'storeModule'])->name('content.modules.store');
        Route::get('content/modules/{module}/edit', [ContentController::class, 'editModule'])->name('content.modules.edit');
        Route::put('content/modules/{module}', [ContentController::class, 'updateModule'])->name('content.modules.update');
        Route::delete('content/modules/{module}', [ContentController::class, 'deleteModule'])->name('content.modules.delete');

        // Analytics
        Route::get('analytics', [MasterDashboardController::class, 'analytics'])->name('analytics');
        Route::get('reports', [MasterDashboardController::class, 'reports'])->name('reports');
    });
