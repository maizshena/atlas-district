<?php

namespace Tests\Feature;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class OtpRateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('otp-send:verify.user@example.com|127.0.0.1');
        RateLimiter::clear('otp-verify-min:verify.user@example.com|127.0.0.1');
        RateLimiter::clear('otp-verify-hour:verify.user@example.com|127.0.0.1');
    }

    public function test_wrong_otp_can_be_retried_many_times_without_tripping_the_limiter(): void
    {
        $user = User::factory()->unverified()->create(['email' => 'verify.user@example.com']);
        Otp::query()->create(['user_id' => $user->id, 'code' => app(OtpService::class)->hash('123456'), 'expires_at' => now()->addMinutes(10)]);

        for ($attempt = 1; $attempt <= 9; $attempt++) {
            $this->post(route('auth.verify.submit'), [
                'email' => 'verify.user@example.com',
                'code' => '00000'.$attempt,
            ])->assertSessionHasErrors('code');
        }

        $this->post(route('auth.verify.submit'), [
            'email' => 'verify.user@example.com',
            'code' => '123456',
        ])->assertRedirect('/');

        $this->assertTrue($user->fresh()->is_verified);
    }

    public function test_verify_attempts_are_capped_per_minute(): void
    {
        User::factory()->unverified()->create(['email' => 'verify.user@example.com']);

        for ($attempt = 1; $attempt <= 10; $attempt++) {
            $this->post(route('auth.verify.submit'), [
                'email' => 'verify.user@example.com',
                'code' => '00000'.$attempt,
            ]);
        }

        $this->post(route('auth.verify.submit'), [
            'email' => 'verify.user@example.com',
            'code' => '000000',
        ])->assertStatus(429);
    }

    public function test_resending_otp_is_limited_but_does_not_block_verification(): void
    {
        $user = User::factory()->unverified()->create(['email' => 'verify.user@example.com']);
        Otp::query()->create(['user_id' => $user->id, 'code' => app(OtpService::class)->hash('654321'), 'expires_at' => now()->addMinutes(10)]);

        Mail::fake();
        $code = Mail::queued(OtpMail::class)->last()->code;

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $this->post(route('auth.resend'), ['email' => 'verify.user@example.com'])->assertRedirect();
        }

        $this->post(route('auth.resend'), ['email' => 'verify.user@example.com'])->assertStatus(429);

        $otp = Otp::query()->where('user_id', $user->id)->firstOrFail();

        $this->post(route('auth.verify.submit'), [
            'email' => 'verify.user@example.com',
            'code' => $otp->code,
        ])->assertRedirect('/');
    }
}
