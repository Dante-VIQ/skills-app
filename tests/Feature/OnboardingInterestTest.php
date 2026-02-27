<?php

use App\Models\User;

it('renders the interests onboarding page with the enabling script', function () {
    $user = User::factory()->create();
    $user->assignRole('child');

    $this->actingAs($user)
        ->get(route('onboarding.interests'))
        ->assertOk()
        ->assertSee('id="submitBtn"')
        ->assertSee('updateButtonState()');
});
