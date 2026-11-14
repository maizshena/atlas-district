<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\TicketCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'ticket_category_id' => TicketCategory::factory(),
            'quantity' => 1,
            'unit_price' => fake()->numberBetween(100000, 2000000),
        ];
    }
}
