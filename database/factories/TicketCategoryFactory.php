<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketCategory>
 */
class TicketCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'name' => fake()->randomElement(['VIP', 'Reguler', 'Festival', 'Tribun']),
            'price' => fake()->numberBetween(100000, 2000000),
            'quota' => fake()->numberBetween(50, 500),
            'sold' => 0,
        ];
    }
}
