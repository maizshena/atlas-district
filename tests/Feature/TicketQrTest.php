<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketQrTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_receives_png_for_own_ticket(): void
    {
        [$user, $ticket] = $this->paidTicket();

        $response = $this->actingAs($user)->get(route('tickets.qr', $ticket));

        $response->assertOk()->assertHeader('Content-Type', 'image/png');
        $this->assertStringStartsWith("\x89PNG", $response->getContent());
    }

    public function test_other_user_cannot_fetch_ticket_qr(): void
    {
        [, $ticket] = $this->paidTicket();

        $this->actingAs(User::factory()->create())
            ->get(route('tickets.qr', $ticket))
            ->assertNotFound();
    }

    public function test_ticket_page_does_not_inline_qr_images(): void
    {
        [$user] = $this->paidTicket();

        $this->actingAs($user)
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertDontSee('data:image/png;base64,', escape: false);
    }

    /**
     * @return array{0: User, 1: Ticket}
     */
    private function paidTicket(): array
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'PAID', 'paid_at' => now()]);
        $item = OrderItem::factory()->create([
            'order_id' => $order->id,
            'ticket_category_id' => TicketCategory::factory()->create()->id,
        ]);

        $ticket = Ticket::factory()->create(['order_id' => $order->id, 'order_item_id' => $item->id]);

        return [$user, $ticket];
    }
}
