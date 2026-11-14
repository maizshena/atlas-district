<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

// app/Services/OtpService.php
// issues and checks the six digit email verification code
class OtpService
{
    public function issue(User $user): Otp
    {
        $otp = Otp::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'code' => (string) random_int(100000, 999999),
                'expires_at' => now()->addMinutes(config('concert.otp_ttl_minutes')),
            ],
        );

        $this->send($user, $otp);

        return $otp;
    }

    public function verify(User $user, string $code): bool
    {
        return DB::transaction(function () use ($user, $code): bool {
            $otp = Otp::query()->where('user_id', $user->id)->lockForUpdate()->first();

            if ($otp === null || $otp->expires_at->isPast() || ! hash_equals($otp->code, $code)) {
                return false;
            }

            $user->forceFill(['is_verified' => true])->save();
            $otp->delete();

            return true;
        }, 3);
    }

    /**
     * The code stays valid even if the mail is queued later, so delivery failures
     * are logged by the queue worker instead of failing the registration request.
     */
    private function send(User $user, Otp $otp): void
    {
        try {
            Mail::to($user->email)->queue(new OtpMail($otp->code, $otp->expires_at));
        } catch (Throwable $exception) {
            Log::error('Otp email could not be queued.', ['user_id' => $user->id, 'message' => $exception->getMessage()]);
        }
    }
}
