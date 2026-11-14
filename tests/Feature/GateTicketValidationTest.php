<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GateTicketValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_for_gate_event_is_accepted_and_marked_used(): void
    {
        $category = TicketCategory::factory()->create();
        $ticket = $this->paidTicket($category);
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.gate.validate'), [
            'event_id' => $category->event_id,
            'ticket_code' => $ticket->ticket_code,
        ]);

        $response->assertRedirect()->assertSessionHas('gate_result', fn (array $result): bool => $result['result'] === 'valid');
        $this->assertTrue($ticket->fresh()->is_used);
    }

    public function test_ticket_from_another_event_is_rejected_and_not_burned(): void
    {
        $ownCategory = TicketCategory::factory()->create();
        $otherCategory = TicketCategory::factory()->create();
        $ticket = $this->paidTicket($otherCategory);
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.gate.validate'), [
            'event_id' => $ownCategory->event_id,
            'ticket_code' => $ticket->ticket_code,
        ]);

        $response->assertRedirect()->assertSessionHas('gate_result', fn (array $result): bool => $result['result'] === 'wrong_event');
        $this->assertFalse($ticket->fresh()->is_used);
    }

    public function test_same_ticket_cannot_enter_twice(): void
    {
        $category = TicketCategory::factory()->create();
        $ticket = $this->paidTicket($category);
        $admin = User::factory()->admin()->create();

        $payload = ['event_id' => $category->event_id, 'ticket_code' => $ticket->ticket_code];

        $this->actingAs($admin)->post(route('admin.gate.validate'), $payload);
        $this->actingAs($admin)->post(route('admin.gate.validate'), $payload)
            ->assertSessionHas('gate_result', fn (array $result): bool => $result['result'] === 'used');
    }

    public function test_replay_result_page_renders_without_error(): void
    {
        $category = TicketCategory::factory()->create();
        $ticket = $this->paidTicket($category);
        $admin = User::factory()->admin()->create();

        $payload = ['event_id' => $category->event_id, 'ticket_code' => $ticket->ticket_code];

        $this->actingAs($admin)->post(route('admin.gate.validate'), $payload);
        $this->get(route('admin.gate', ['event_id' => $category->event_id]))->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.gate.validate'), $payload)
            ->assertSessionHas('gate_result', fn (array $result): bool => $result['result'] === 'used');

        $this->actingAs($admin)
            ->get(route('admin.gate', ['event_id' => $category->event_id]))
            ->assertOk()
            ->assertSee('Tiket sudah digunakan');
    }

    public function test_unpaid_order_ticket_is_not_found(): void
    {
        $category = TicketCategory::factory()->create();
        $ticket = $this->paidTicket($category, paid: false);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.gate.validate'), [
            'event_id' => $category->event_id,
            'ticket_code' => $ticket->ticket_code,
        ])->assertSessionHas('gate_result', fn (array $result): bool => $result['result'] === 'not_found');
    }

    public function test_gate_requires_event_selection(): void
    {
        $ticket = $this->paidTicket(TicketCategory::factory()->create());
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.gate.validate'), [
            'ticket_code' => $ticket->ticket_code,
        ])->assertSessionHasErrors('event_id');
    }

    public function test_api_rejects_ticket_from_another_event(): void
    {
        $ownCategory = TicketCategory::factory()->create();
        $otherCategory = TicketCategory::factory()->create();
        $ticket = $this->paidTicket($otherCategory);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin, 'sanctum')->postJson('/api/v1/tickets/validate', [
            'event_id' => $ownCategory->event_id,
            'ticket_code' => $ticket->ticket_code,
        ])->assertStatus(422)->assertJsonPath('status', 'error');

        $this->assertFalse($ticket->fresh()->is_used);
    }

    private function paidTicket(TicketCategory $category, bool $paid = true): Ticket
    {
        $order = Order::factory()->create($paid ? ['status' => 'PAID', 'paid_at' => now()] : []);
        $item = OrderItem::factory()->create([
            'order_id' => $order->id,
            'ticket_category_id' => $category->id,
        ]);

        return Ticket::factory()->create([
            'order_id' => $order->id,
            'order_item_id' => $item->id,
        ]);
    }
}
