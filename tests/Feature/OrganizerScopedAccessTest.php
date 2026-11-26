<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// tests/Feature/OrganizerScopedAccessTest.php uji pembatasan pesanan dan gate organizer ke event miliknya
class OrganizerScopedAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_organizer_sees_only_orders_of_own_events_with_masked_email(): void
    {
        $organizer = User::factory()->eventOrganizer()->create();
        $buyer = User::factory()->create(['email' => 'pembeli.rahasia@example.com']);
        $own = $this->orderFor(Event::factory()->create(['organizer_id' => $organizer->id]), $buyer);
        $foreign = $this->orderFor(Event::factory()->create(), User::factory()->create());

        $this->actingAs($organizer)->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee($own->order_number)
            ->assertDontSee($foreign->order_number)
            ->assertDontSee('pembeli.rahasia@example.com')
            ->assertSee($buyer->maskedEmail());
    }

    public function test_admin_sees_every_order_with_full_email(): void
    {
        $admin = User::factory()->admin()->create();
        $buyer = User::factory()->create(['email' => 'pembeli.rahasia@example.com']);
        $first = $this->orderFor(Event::factory()->create(), $buyer);
        $second = $this->orderFor(Event::factory()->create(), User::factory()->create());

        $this->actingAs($admin)->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee($first->order_number)
            ->assertSee($second->order_number)
            ->assertSee('pembeli.rahasia@example.com');
    }

    public function test_organizer_validates_ticket_for_own_event(): void
    {
        $organizer = User::factory()->eventOrganizer()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $ticket = $this->paidTicket($event);

        $this->actingAs($organizer)->post(route('admin.gate.validate'), [
            'event_id' => $event->id,
            'ticket_code' => $ticket->ticket_code,
        ])->assertSessionHas('gate_result', fn (array $result): bool => $result['result'] === 'valid');

        $this->assertTrue($ticket->fresh()->is_used);
    }

    public function test_organizer_cannot_use_gate_for_foreign_event(): void
    {
        $organizer = User::factory()->eventOrganizer()->create();
        $foreignEvent = Event::factory()->create();
        $ticket = $this->paidTicket($foreignEvent);

        $this->actingAs($organizer)->post(route('admin.gate.validate'), [
            'event_id' => $foreignEvent->id,
            'ticket_code' => $ticket->ticket_code,
        ])->assertNotFound();

        $this->assertFalse($ticket->fresh()->is_used);
    }

    public function test_wrong_event_result_hides_other_event_name_from_organizer(): void
    {
        $organizer = User::factory()->eventOrganizer()->create();
        $ownEvent = Event::factory()->create(['organizer_id' => $organizer->id]);
        $ticket = $this->paidTicket(Event::factory()->create());

        $this->actingAs($organizer)->post(route('admin.gate.validate'), [
            'event_id' => $ownEvent->id,
            'ticket_code' => $ticket->ticket_code,
        ])->assertSessionHas('gate_result', fn (array $result): bool => $result['result'] === 'wrong_event' && $result['event'] === 'event lain');

        $this->assertFalse($ticket->fresh()->is_used);
    }

    public function test_gate_event_list_only_contains_own_events(): void
    {
        $organizer = User::factory()->eventOrganizer()->create();
        $own = Event::factory()->create(['organizer_id' => $organizer->id]);
        $foreign = Event::factory()->create();

        $this->actingAs($organizer)->get(route('admin.gate'))
            ->assertOk()
            ->assertSee($own->name)
            ->assertDontSee($foreign->name);
    }

    public function test_customer_cannot_open_orders_or_gate(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)->get(route('admin.orders.index'))->assertForbidden();
        $this->actingAs($customer)->get(route('admin.gate'))->assertForbidden();
    }

    private function orderFor(Event $event, User $buyer): Order
    {
        $category = TicketCategory::factory()->create(['event_id' => $event->id]);
        $order = Order::factory()->paid()->create(['user_id' => $buyer->id]);
        OrderItem::factory()->create(['order_id' => $order->id, 'ticket_category_id' => $category->id]);

        return $order;
    }

    private function paidTicket(Event $event): Ticket
    {
        $category = TicketCategory::factory()->create(['event_id' => $event->id]);
        $order = Order::factory()->paid()->create();
        $item = OrderItem::factory()->create(['order_id' => $order->id, 'ticket_category_id' => $category->id]);

        return Ticket::factory()->create(['order_id' => $order->id, 'order_item_id' => $item->id]);
    }
}
