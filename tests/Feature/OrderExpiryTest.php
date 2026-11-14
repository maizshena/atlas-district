<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\TicketCategory;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderExpiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_scheduler_marks_expired_orders_as_expired_and_releases_quota(): void
    {
        $category = TicketCategory::factory()->create(['quota' => 10]);
        $order = Order::factory()->create([
            'status' => OrderStatus::Pending,
            'expires_at' => now()->subMinutes(5),
        ]);
        $order->items()->create([
            'ticket_category_id' => $category->id,
            'quantity' => 3,
            'unit_price' => $category->price,
        ]);
        $category->update(['sold' => 3]);

        $cancelled = app(OrderService::class)->cancelExpiredOrders();

        $this->assertSame(1, $cancelled);
        $this->assertSame(OrderStatus::Expired, $order->fresh()->status);
        $this->assertSame(0, $category->fresh()->sold);
    }

    public function test_manual_cancel_stays_cancelled(): void
    {
        $category = TicketCategory::factory()->create(['quota' => 10]);
        $order = Order::factory()->create([
            'status' => OrderStatus::Pending,
            'expires_at' => now()->addHour(),
        ]);
        $order->items()->create([
            'ticket_category_id' => $category->id,
            'quantity' => 1,
            'unit_price' => $category->price,
        ]);
        $category->update(['sold' => 1]);

        $this->assertTrue(app(OrderService::class)->cancel($order));
        $this->assertSame(OrderStatus::Cancelled, $order->fresh()->status);
    }

    public function test_expiring_order_through_payment_marks_expired(): void
    {
        $category = TicketCategory::factory()->create(['quota' => 10]);
        $order = Order::factory()->create([
            'status' => OrderStatus::Pending,
            'expires_at' => now()->subMinute(),
        ]);
        $order->items()->create([
            'ticket_category_id' => $category->id,
            'quantity' => 1,
            'unit_price' => $category->price,
        ]);
        $category->update(['sold' => 1]);

        $this->actingAs($order->user)->post(route('orders.pay', $order))
            ->assertSessionHasErrors('order');

        $this->assertSame(OrderStatus::Expired, $order->fresh()->status);
    }
}
