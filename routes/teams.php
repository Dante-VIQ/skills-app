<?php
// routes/web.php

use MNobre\LaravelTeams\Http\Controllers\TeamController;
use MNobre\LaravelTeams\Http\Controllers\TeamMemberController;
use MNobre\LaravelTeams\Http\Controllers\TeamInvitationController;
use Illuminate\Support\Facades\Route;

$singular = strtolower(config('teams.denomination_singular'));
$plural = strtolower(config('teams.denomination_plural'));

// Team Routes
Route::middleware(['web','auth', 'verified'])->group(function () use ($singular, $plural) {
    // Team management
    Route::get("/{$plural}", [TeamController::class, 'index'])->name("{$plural}.index");
    Route::get("/{$plural}/create", [TeamController::class, 'create'])->name("{$plural}.create");
    Route::post("/{$plural}", [TeamController::class, 'store'])->name("{$plural}.store");
    Route::get("/{$plural}/{team}", [TeamController::class, 'show'])->name("{$plural}.show");
    Route::get("/{$plural}/{team}/edit", [TeamController::class, 'edit'])->name("{$plural}.edit");
    Route::put("/{$plural}/{team}", [TeamController::class, 'update'])->name("{$plural}.update");
    Route::delete("/{$plural}/{team}", [TeamController::class, 'destroy'])->name("{$plural}.destroy");
    Route::put("/{$plural}/{team}/switch", [TeamController::class, 'switch'])->name("{$plural}.switch");

    // Team member management
    Route::post("/{$plural}/{team}/members", [TeamMemberController::class, 'store'])->name("{$plural}.members.store");
    Route::put("/{$plural}/{team}/members/{user}", [TeamMemberController::class, 'update'])->name("{$plural}.members.update");
    Route::delete("/{$plural}/{team}/members/{user}", [TeamMemberController::class, 'destroy'])->name("{$plural}.members.destroy");

    
});

Route::middleware(['web'])->group(function () {
    // Public invitation routes (no auth required)
    Route::get('/invitations/accept/{token}', [TeamInvitationController::class, 'showAcceptForm'])
        ->name('team.invitations.accept');
    Route::post('/invitations/accept/{token}', [TeamInvitationController::class, 'accept'])
        ->name('team.invitations.accept.submit');
    Route::get('/invitations/decline/{token}', [TeamInvitationController::class, 'decline'])
        ->name('team.invitations.decline');
    Route::get('/invitations/expired', function () {
        return view('team-invitations.expired', [
            'teamDenomination' => [
                'singular' => config('teams.denomination_singular', 'Team'),
                'plural' => config('teams.denomination_plural', 'Teams'),
            ],
        ]);
    })->name('team.invitations.expired');

});

// Authenticated invitation management routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/teams/{team}/invitations/pending', [TeamInvitationController::class, 'pending'])
        ->name('teams.invitations.pending');
    Route::post('/invitations/{invitation}/resend', [TeamInvitationController::class, 'resend'])
        ->name('team.invitations.resend');
    Route::delete('/invitations/{invitation}', [TeamInvitationController::class, 'cancel'])
        ->name('team.invitations.cancel');
});