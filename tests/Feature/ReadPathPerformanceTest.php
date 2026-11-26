<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Services\DashboardStatsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

// tests/Feature/ReadPathPerformanceTest.php uji qr kondisional paginasi tiket angka dashboard dan pencarian aman
class ReadPathPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_qr_supports_conditional_requests_and_hides_existence_from_others(): void
    {
        [$user, $ticket] = $this->paidTicketFor(User::factory()->create(), now());

        $response = $this->actingAs($user)->get(route('tickets.qr', $ticket))->assertOk();
        $etag = $response->headers->get('ETag');

        $this->assertStringContainsString('immutable', $response->headers->get('Cache-Control'));
        $this->actingAs($user)->get(route('tickets.qr', $ticket), ['If-None-Match' => $etag])->assertStatus(304);
        $this->actingAs(User::factory()->create())->get(route('tickets.qr', $ticket), ['If-None-Match' => $etag])->assertNotFound();
    }

    public function test_ticket_list_is_paginated_per_order(): void
    {
        $user = User::factory()->create();
        $orders = collect(range(1, 7))->map(fn (int $day): Order => $this->paidTicketFor($user, now()->subDays($day))[2]);

        $first = $this->actingAs($user)->get(route('tickets.index'))->assertOk();
        $orders->take(5)->each(fn (Order $order) => $first->assertSee($order->order_number));
        $orders->slice(5)->each(fn (Order $order) => $first->assertDontSee($order->order_number));

        $second = $this->actingAs($user)->get(route('tickets.index', ['page' => 2]))->assertOk();
        $orders->slice(5)->each(fn (Order $order) => $second->assertSee($order->order_number));
    }

    public function test_dashboard_sold_count_ignores_unpaid_reservations_and_is_cached(): void
    {
        $category = TicketCategory::factory()->create();
        $pending = Order::factory()->create();
        OrderItem::factory()->create(['order_id' => $pending->id, 'ticket_category_id' => $category->id, 'quantity' => 5]);
        $paid = Order::factory()->paid()->create();
        OrderItem::factory()->create(['order_id' => $paid->id, 'ticket_category_id' => $category->id, 'quantity' => 2]);

        $stats = app(DashboardStatsService::class);

        $this->assertSame(2, $stats->summary()['soldCount']);

        $morePaid = Order::factory()->paid()->create();
        OrderItem::factory()->create(['order_id' => $morePaid->id, 'ticket_category_id' => $category->id, 'quantity' => 4]);

        $this->assertSame(2, $stats->summary()['soldCount']);

        Cache::forget(DashboardStatsService::CACHE_KEY);

        $this->assertSame(6, $stats->summary()['soldCount']);
    }

    public function test_event_search_is_case_insensitive_and_treats_wildcards_literally(): void
    {
        Event::factory()->create(['name' => 'Alpha Fest']);
        Event::factory()->create(['name' => 'Beta Night']);

        $this->assertSame(1, Event::query()->search('alpha')->count());
        $this->assertSame(0, Event::query()->search('%')->count());
        $this->assertSame(0, Event::query()->search('_')->count());
    }

    /**
     * @return array{0: User, 1: Ticket, 2: Order}
     */
    private function paidTicketFor(User $user, \DateTimeInterface $paidAt): array
    {
        $order = Order::factory()->paid()->create(['user_id' => $user->id, 'paid_at' => $paidAt]);
        $item = OrderItem::factory()->create(['order_id' => $order->id, 'ticket_category_id' => TicketCategory::factory()->create()->id]);
        $ticket = Ticket::factory()->create(['order_id' => $order->id, 'order_item_id' => $item->id]);

        return [$user, $ticket, $order];
    }
}
