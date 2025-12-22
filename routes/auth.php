<?php

use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ChildLoginController;
use App\Http\Controllers\Auth\VerifyEmailController;

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
});

// CHILD LOGIN ROUTES (accessible without authentication)
Route::prefix('child')->group(function () {
    // Show login form - GET
    Route::get('/login', [ChildLoginController::class, 'showLoginForm'])
        ->name('child.login')
        ->middleware('guest:child');

    // Process login - POST (different route!)
    Route::post('/login', [ChildLoginController::class, 'login'])
        ->name('child.login.submit')
        ->middleware('guest:child');

    // Logout - POST
    Route::post('/logout', [ChildLoginController::class, 'logout'])
        ->name('child.logout')
        ->middleware('auth:child');
});
