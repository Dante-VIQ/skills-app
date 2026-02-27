<?php

use App\Models\User;
use App\Models\Family;
use App\Models\TutorInvitation;
use Livewire\Livewire;

test('parent can register with unique parent code', function () {
    $component = Livewire::test('pages.auth.register')
        ->set('user_role', 'parent')
        ->set('first_name', 'John')
        ->set('last_name', 'Smith')
        ->set('email', 'john@example.com')
        ->set('username', 'johnsmith')
        ->set('password', 'Password123!')
        ->set('password_confirmation', 'Password123!')
        ->call('register');

    $component->assertNoErrors();
    $component->assertRedirectToRoute('parent.dashboard');

    $user = User::where('email', 'john@example.com')->first();
    $this->assertNotNull($user);
    $this->assertTrue($user->hasRole('parent'));
    $this->assertNotNull($user->parent_code);
    $this->assertEquals(8, strlen($user->parent_code));
});

test('parent registration creates family record', function () {
    $component = Livewire::test('pages.auth.register')
        ->set('user_role', 'parent')
        ->set('first_name', 'Jane')
        ->set('last_name', 'Doe')
        ->set('email', 'jane@example.com')
        ->set('username', 'janedoe')
        ->set('password', 'Password123!')
        ->set('password_confirmation', 'Password123!')
        ->call('register');

    $user = User::where('email', 'jane@example.com')->first();

    $family = Family::where('parent_id', $user->id)->first();
    $this->assertNotNull($family);
    $this->assertEquals($user->id, $family->parent_id);
});

test('child can register with valid parent code', function () {
    $parent = User::factory()->create(['parent_code' => 'PARENTCD']);
    $parent->assignRole('parent');

    $component = Livewire::test('pages.auth.register')
        ->set('user_role', 'child')
        ->set('first_name', 'Child')
        ->set('last_name', 'User')
        ->set('email', 'child@example.com')
        ->set('username', 'childuser')
        ->set('parent_invite_code', 'PARENTCD')
        ->set('password', 'Password123!')
        ->set('password_confirmation', 'Password123!')
        ->call('register');

    $component->assertNoErrors();

    $child = User::where('email', 'child@example.com')->first();
    $this->assertNotNull($child);
    $this->assertTrue($child->hasRole('child'));
    $this->assertEquals($parent->id, $child->parent_id);
});

test('child registration fails with invalid parent code', function () {
    $component = Livewire::test('pages.auth.register')
        ->set('user_role', 'child')
        ->set('first_name', 'Child')
        ->set('last_name', 'User')
        ->set('email', 'child@example.com')
        ->set('username', 'childuser')
        ->set('parent_invite_code', 'INVALID1')
        ->set('password', 'Password123!')
        ->set('password_confirmation', 'Password123!')
        ->call('register');

    $component->assertHasErrors('parent_invite_code');
    $this->assertNull(User::where('email', 'child@example.com')->first());
});

test('tutor can register with valid invitation code', function () {
    $invitation = TutorInvitation::create([
        'code' => '123456789012',
        'email' => 'tutor@example.com',
        'created_by' => 1,
        'used' => false,
        'expires_at' => now()->addDays(30),
    ]);

    $component = Livewire::test('pages.auth.register')
        ->set('user_role', 'tutor')
        ->set('first_name', 'Tutor')
        ->set('last_name', 'User')
        ->set('email', 'tutor@example.com')
        ->set('username', 'tutoruser')
        ->set('tutor_invite_code', '123456789012')
        ->set('password', 'Password123!')
        ->set('password_confirmation', 'Password123!')
        ->call('register');

    $component->assertNoErrors();

    $tutor = User::where('email', 'tutor@example.com')->first();
    $this->assertNotNull($tutor);
    $this->assertTrue($tutor->hasRole('tutor'));

    $invitation->refresh();
    $this->assertTrue($invitation->used);
});

test('tutor registration fails with expired invitation', function () {
    $invitation = TutorInvitation::create([
        'code' => '999999999999',
        'email' => 'expired@example.com',
        'created_by' => 1,
        'used' => false,
        'expires_at' => now()->subDays(1),
    ]);

    $component = Livewire::test('pages.auth.register')
        ->set('user_role', 'tutor')
        ->set('first_name', 'Tutor')
        ->set('last_name', 'User')
        ->set('email', 'expired@example.com')
        ->set('username', 'expiredtutor')
        ->set('tutor_invite_code', '999999999999')
        ->set('password', 'Password123!')
        ->set('password_confirmation', 'Password123!')
        ->call('register');

    $component->assertHasErrors('tutor_invite_code');
    $this->assertNull(User::where('email', 'expired@example.com')->first());
});

test('parent code is unique across registrations', function () {
    $parent1 = User::factory()->create(['parent_code' => 'UNIQUE01']);
    $parent1->assignRole('parent');

    $parent2 = User::factory()->create();
    $parent2->assignRole('parent');

    // Parent code is already taken, so next generation should not match
    $this->assertNotEquals($parent1->parent_code, $parent2->parent_code);
});
