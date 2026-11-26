<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Mail\OtpMail;
use App\Mail\TicketConfirmationMail;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// tests/Feature/UiComponentRenderingTest.php uji render halaman email dan gate setelah refaktor komponen
class UiComponentRenderingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_event_page_renders_ticket_picker_for_guest_and_member(): void
    {
        $event = Event::factory()->create(['status' => EventStatus::Published, 'is_closed' => false, 'end_date' => now()->addMonth()]);
        $category = TicketCategory::factory()->create(['event_id' => $event->id, 'quota' => 10, 'sold' => 0]);

        $this->get(route('events.show', $event->slug))
            ->assertOk()
            ->assertSee('Pilih tiket')
            ->assertSee('Masuk untuk membeli')
            ->assertDontSee('WIB WIB');

        $this->actingAs(User::factory()->create())->get(route('events.show', $event->slug))
            ->assertOk()
            ->assertSee('Tambah tiket '.$category->name)
            ->assertSee('Beli tiket');
    }

    public function test_pending_order_page_passes_countdown_data_as_number(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'expires_at' => now()->addHour()]);
        OrderItem::factory()->create(['order_id' => $order->id, 'ticket_category_id' => TicketCategory::factory()->create()->id]);

        $this->actingAs($user)->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('countdown('.($order->expires_at->timestamp * 1000).')', false)
            ->assertSee('Bayar sekarang')
            ->assertSee('Batalkan pesanan');
    }

    public function test_gate_page_renders_each_result_card(): void
    {
        $admin = User::factory()->admin()->create();
        $cases = [
            [['result' => 'used', 'used_at' => '01/01/2027 10:00 WIB', 'used_at_iso' => null], 'Tiket sudah digunakan'],
            [['result' => 'wrong_event', 'event' => 'Event Lain', 'gate_event' => 'Event Ini'], 'Tiket bukan untuk event ini'],
            [['result' => 'not_found'], 'Tiket tidak ditemukan'],
        ];

        foreach ($cases as [$result, $title]) {
            $this->actingAs($admin)
                ->withSession(['gate_result' => $result])
                ->get(route('admin.gate'))
                ->assertOk()
                ->assertSee($title);
        }
    }

    public function test_emails_render_with_shared_layout(): void
    {
        $otp = (new OtpMail('123456', now()->addMinutes(10)))->render();

        $this->assertStringContainsString('123456', $otp);
        $this->assertStringContainsString('Kode berlaku sampai', $otp);

        $order = Order::factory()->paid()->create();
        $item = OrderItem::factory()->create(['order_id' => $order->id, 'ticket_category_id' => TicketCategory::factory()->create()->id]);
        $ticket = Ticket::factory()->create(['order_id' => $order->id, 'order_item_id' => $item->id]);

        $confirmation = (new TicketConfirmationMail($order->fresh(['user', 'items.ticketCategory.event.venue', 'tickets'])))->render();

        $this->assertStringContainsString($order->order_number, $confirmation);
        $this->assertStringContainsString($ticket->ticket_code, $confirmation);
    }
}
