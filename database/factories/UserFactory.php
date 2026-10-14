<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'first_name'         => fake()->firstName(),
            'last_name'          => fake()->lastName(),
            'email'              => fake()->unique()->safeEmail(),
            'email_verified_at'  => now(),
            'password'           => static::$password ??= Hash::make('password'),
            'remember_token'     => Str::random(10),
            'type'               => 'child',  // default; override with ->role() in tests
            'is_active'          => true,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function parent(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'parent',
        ]);
    }

    public function child(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'child',
        ]);
    }

    public function tutor(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'tutor',
        ]);
    }
}