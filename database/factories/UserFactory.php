<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function staff(): static
    {
        return $this->state(fn() => [
            'nidn' => null,
            'nim' => null,
        ])->afterCreating(function (User $user) {
            $user->assignRole('staff');
        });
    }

    public function dosen(): static
    {
        return $this->state(fn() => [
            'nidn' => fake()->unique()->numerify('########'),
            'nim' => null,
        ])->afterCreating(function (User $user) {
            $user->assignRole('dosen');
        });
    }

    public function mahasiswa(): static
    {
        return $this->state(fn() => [
            'nidn' => null,
            'nim' => fake()->unique()->numerify('#########'),
        ])->afterCreating(function (User $user) {
            $user->assignRole('mahasiswa');
        });
    }

    public function unverified(): static
    {
        return $this->state(fn() => [
            'email_verified_at' => null,
        ]);
    }
}