<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'order_item_id' => OrderItem::factory(),
            'ticket_code' => $this->code(),
            'is_used' => false,
            'used_at' => null,
        ];
    }

    public function used(): static
    {
        return $this->state(fn (array $attributes) => ['is_used' => true, 'used_at' => now()->subMinutes(5)]);
    }

    private function code(): string
    {
        return sprintf('%04d-%04d-%04d', random_int(0, 9999), random_int(0, 9999), random_int(0, 9999));
    }
}
