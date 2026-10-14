<?php

namespace Tests\Feature\Brain;

use App\Models\User;
use App\Services\Brain\PersonalizationBrain;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PersonalizationBrainTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles exist for the test (Spatie needs them)
        Role::firstOrCreate(['name' => 'child', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'tutor', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'master', 'guard_name' => 'web']);
    }

    public function test_brain_is_disabled_by_default()
    {
        config(['brain.enabled' => false]);

        $user = User::factory()->create();
        $user->assignRole('child');

        $brain = app(PersonalizationBrain::class);
        $this->assertFalse($brain->shouldUseBrain($user));
    }

    public function test_brain_can_be_fully_enabled()
    {
        config([
            'brain.enabled' => true,
            'brain.rollout_percentage' => 100,
        ]);

        $user = User::factory()->create();
        $user->assignRole('child');

        $brain = app(PersonalizationBrain::class);
        $this->assertTrue($brain->shouldUseBrain($user));
    }

    public function test_brain_returns_dto_even_on_failure()
    {
        config(['brain.enabled' => true]);

        $user = User::factory()->create();
        $user->assignRole('child');

        $brain = app(PersonalizationBrain::class);
        $output = $brain->think($user);

        $array = $output->toArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('profile', $array);
        $this->assertArrayHasKey('next_content', $array);
        $this->assertArrayHasKey('metadata', $array);
    }

    public function test_rollout_is_deterministic()
    {
        config([
            'brain.enabled' => true,
            'brain.rollout_percentage' => 50,
        ]);

        $user = User::factory()->create();
        $user->assignRole('child');

        $brain = app(PersonalizationBrain::class);

        $result1 = $brain->shouldUseBrain($user);
        $result2 = $brain->shouldUseBrain($user);

        $this->assertSame($result1, $result2, 'Rollout must be deterministic per user.');
    }
}