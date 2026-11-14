<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_number' => 'INV-'.now()->format('Ymd').'-'.str_pad((string) fake()->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'status' => OrderStatus::Pending,
            'total_amount' => fake()->numberBetween(100000, 5000000),
            'expires_at' => now()->addHour(),
            'paid_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => ['status' => OrderStatus::Paid, 'paid_at' => now()]);
    }
}
