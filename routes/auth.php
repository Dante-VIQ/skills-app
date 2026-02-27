<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\Auth\ChildLoginController;

// ======================
// CHILD AUTHENTICATION ROUTES
// ======================

// Guest routes (accessible only when not logged in as child)
Route::middleware('guest:child')->group(function () {
    
    // Show child login form
    Route::get('child/login', [ChildLoginController::class, 'showLoginForm'])
        ->name('child.login');
    
    // Process child login submission
    Route::post('child/login', [ChildLoginController::class, 'login'])
        ->name('child.login.submit');
    
    // Optional: Show forgot password form for child
    Route::get('child/forgot-password', [ChildLoginController::class, 'showForgotForm'])
        ->name('child.password.request');
    
    // Optional: Send reset link for child
    Route::post('child/forgot-password', [ChildLoginController::class, 'sendResetLink'])
        ->name('child.password.email');
    
    // Optional: Show reset password form
    Route::get('child/reset-password/{token}', [ChildLoginController::class, 'showResetForm'])
        ->name('child.password.reset');
    
    // Optional: Process reset password
    Route::post('child/reset-password', [ChildLoginController::class, 'resetPassword'])
        ->name('child.password.update');
});

// Authenticated routes (accessible only when logged in as child)
Route::middleware('auth:child')->group(function () {
    
    // Child logout
    Route::post('child/logout', [ChildLoginController::class, 'logout'])
        ->name('child.logout');
    
    // Child dashboard (redirect to main child dashboard)
    Route::get('child/dashboard', function () {
        return redirect()->route('child.dashboard');
    })->name('child.dashboard.redirect');
    
    // Optional: Child profile update
    Route::get('child/profile', [ChildLoginController::class, 'showProfile'])
        ->name('child.profile');
    
    Route::post('child/profile', [ChildLoginController::class, 'updateProfile'])
        ->name('child.profile.update');
    
    // // Optional: Change password
    // Route::get('child/change-password', [ChildLoginController::class, 'showChangePasswordForm'])
    //     ->name('child.password.change');
    
    // Route::post('child/change-password', [ChildLoginController::class, 'changePassword'])
    //     ->name('child.password.update');
});

Route::middleware('guest')->group(function () {
    Volt::route('register', 'pages.auth.register')->name('register');

    Volt::route('login', 'pages.auth.login')->name('login');

    Volt::route('forgot-password', 'pages.auth.forgot-password')->name('password.request');

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')->name('password.reset');

});

Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'pages.auth.verify-email')->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'pages.auth.confirm-password')->name('password.confirm');

    Route::post('logout', Logout::class)->name('logout');
});

   


