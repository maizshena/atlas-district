<?php

namespace Database\Factories;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->sentence(3);
        $start = fake()->dateTimeBetween('+1 week', '+3 months');

        return [
            'venue_id' => Venue::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(5)),
            'description' => fake()->paragraph(),
            'start_date' => $start,
            'end_date' => (clone $start)->modify('+4 hours'),
            'is_closed' => false,
            'banner_url' => null,
        ];
    }

    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->subDays(2),
            'end_date' => now()->subDays(2)->addHours(4),
        ]);
    }
}
