<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

// app/Services/OtpService.php kode otp disimpan sebagai hash dan dibatasi jumlah percobaannya
class OtpService
{
    public function issue(User $user): Otp
    {
        $code = (string) random_int(100000, 999999);

        $otp = Otp::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'code' => $this->hash($code),
                'attempts' => 0,
                'expires_at' => now()->addMinutes(config('concert.otp_ttl_minutes')),
            ],
        );

        $this->send($user, $code, $otp);

        return $otp;
    }

    public function hash(string $code): string
    {
        return hash_hmac('sha256', $code, (string) config('app.key'));
    }

    public function verify(User $user, string $code): bool
    {
        return DB::transaction(function () use ($user, $code): bool {
            $otp = Otp::query()->where('user_id', $user->id)->lockForUpdate()->first();

            if ($otp === null || $otp->expires_at->isPast() || $otp->attempts >= config('concert.otp_max_attempts')) {
                return false;
            }

            if (! hash_equals($otp->code, $this->hash($code))) {
                $otp->increment('attempts');

                return false;
            }

            $user->forceFill(['is_verified' => true])->save();
            $otp->delete();

            return true;
        }, 3);
    }

    private function send(User $user, string $code, Otp $otp): void
    {
        try {
            Mail::to($user->email)->queue(new OtpMail($code, $otp->expires_at));
        } catch (Throwable $exception) {
            Log::error('Otp email could not be queued.', ['user_id' => $user->id, 'message' => $exception->getMessage()]);
        }
    }
}
