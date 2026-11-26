<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use App\Mail\OtpMail;
use App\Models\Event;
use App\Models\Order;
use App\Models\TicketCategory;
use App\Models\User;
use App\Services\AuthService;
use App\Services\OrderService;
use App\Services\OtpService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

// tests/Feature/CheckoutAndAuthHardeningTest.php uji nomor pesanan batas tertunda pembayaran dan keamanan otp
class CheckoutAndAuthHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_numbers_use_date_prefix_and_stay_unique(): void
    {
        [$user, $category] = $this->buyerAndCategory();
        $service = app(OrderService::class);

        $numbers = collect(range(1, 3))->map(fn (): string => $service->create($user, [['ticket_category_id' => $category->id, 'quantity' => 1]])->order_number);

        $numbers->each(fn (string $number) => $this->assertMatchesRegularExpression('/^INV-\d{8}-[0-9A-F]{6}$/', $number));
        $this->assertCount(3, $numbers->unique());
    }

    public function test_user_cannot_exceed_pending_order_limit(): void
    {
        config(['concert.max_pending_orders_per_user' => 1]);
        [$user, $category] = $this->buyerAndCategory();
        $service = app(OrderService::class);
        $items = [['ticket_category_id' => $category->id, 'quantity' => 1]];

        $service->create($user, $items);

        $this->expectException(ValidationException::class);
        $service->create($user, $items);
    }

    public function test_payment_is_refused_when_simulation_is_disabled(): void
    {
        config(['concert.payment_simulation' => false]);
        $order = Order::factory()->create(['status' => OrderStatus::Pending, 'expires_at' => now()->addHour()]);

        try {
            app(PaymentService::class)->pay($order);
            $this->fail('Pembayaran seharusnya ditolak.');
        } catch (ValidationException) {
            $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
        }
    }

    public function test_expiry_sweep_processes_more_than_one_batch(): void
    {
        Order::factory()->count(5)->create(['status' => OrderStatus::Pending, 'expires_at' => now()->subMinute()]);

        $cancelled = app(OrderService::class)->cancelExpiredOrders(batchSize: 2);

        $this->assertSame(5, $cancelled);
        $this->assertSame(0, Order::query()->where('status', OrderStatus::Pending)->count());
    }

    public function test_otp_is_stored_hashed_and_mailed_in_plain_text(): void
    {
        Mail::fake();
        $user = User::factory()->unverified()->create();
        $otp = app(OtpService::class)->issue($user);

        $code = Mail::queued(OtpMail::class)->first()->code;

        $this->assertMatchesRegularExpression('/^\d{6}$/', $code);
        $this->assertSame(64, strlen($otp->code));
        $this->assertNotSame($code, $otp->code);
        $this->assertTrue(app(OtpService::class)->verify($user, $code));
    }

    public function test_otp_is_invalidated_after_too_many_wrong_attempts(): void
    {
        config(['concert.otp_max_attempts' => 3]);
        Mail::fake();
        $user = User::factory()->unverified()->create();
        $service = app(OtpService::class);
        $service->issue($user);

        $code = Mail::queued(OtpMail::class)->first()->code;
        $wrong = $code === '000000' ? '111111' : '000000';

        foreach (range(1, 3) as $attempt) {
            $this->assertFalse($service->verify($user, $wrong));
        }

        $this->assertFalse($service->verify($user, $code));
        $this->assertFalse($user->fresh()->is_verified);
    }

    public function test_pending_registration_cannot_be_overwritten_until_otp_expires(): void
    {
        Mail::fake();
        $auth = app(AuthService::class);
        $auth->register(['name' => 'Korban', 'email' => 'korban@example.com', 'password' => 'password-korban']);

        try {
            $auth->register(['name' => 'Penyerang', 'email' => 'korban@example.com', 'password' => 'password-penyerang']);
            $this->fail('Pendaftaran ulang seharusnya ditolak selama OTP berlaku.');
        } catch (ValidationException) {
            $user = User::query()->where('email', 'korban@example.com')->firstOrFail();
            $this->assertTrue(Hash::check('password-korban', $user->password));
        }

        $this->travel(11)->minutes();
        $auth->register(['name' => 'Pengguna Baru', 'email' => 'korban@example.com', 'password' => 'password-baru']);

        $this->assertTrue(Hash::check('password-baru', User::query()->where('email', 'korban@example.com')->firstOrFail()->password));
    }

    /**
     * @return array{0: User, 1: TicketCategory}
     */
    private function buyerAndCategory(): array
    {
        $event = Event::factory()->create([
            'start_date' => now()->addWeek(),
            'end_date' => now()->addWeek()->addHours(4),
            'is_closed' => false,
            'status' => EventStatus::Published,
        ]);

        return [
            User::factory()->create(['is_verified' => true]),
            TicketCategory::factory()->create(['event_id' => $event->id, 'price' => 100000, 'quota' => 10, 'sold' => 0]),
        ];
    }
}
