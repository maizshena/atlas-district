<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= 'password',
            'role' => 'CUSTOMER',
            'is_verified' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => ['is_verified' => false]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'ADMIN', 'is_verified' => true]);
    }
}
