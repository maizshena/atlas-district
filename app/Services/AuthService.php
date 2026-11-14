<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

// app/Services/AuthService.php
// registration and credential checks shared by api and web controllers
class AuthService
{
    public function __construct(private OtpService $otp) {}

    public function register(array $attributes): User
    {
        $existing = User::query()->where('email', $attributes['email'])->first();

        if ($existing !== null && $existing->is_verified) {
            throw ValidationException::withMessages(['email' => ['Email sudah terdaftar.']]);
        }

        $user = DB::transaction(function () use ($existing, $attributes): User {
            $user = $existing ?? new User;
            $user->fill([
                'name' => $attributes['name'],
                'email' => $attributes['email'],
                'password' => $attributes['password'],
            ]);
            $user->forceFill(['role' => 'CUSTOMER', 'is_verified' => false])->save();

            return $user;
        }, 3);

        $this->otp->issue($user);

        return $user;
    }

    /**
     * Credential check shared by the api token flow and the web session flow.
     *
     * @throws AccessDeniedHttpException when the account is not verified
     * @throws AuthenticationException when the credentials do not match
     */
    public function attempt(string $email, string $password): User
    {
        $user = User::query()->where('email', $email)->first();

        if ($user === null || ! Hash::check($password, $user->password)) {
            throw new AuthenticationException('Kredensial tidak valid.');
        }

        if (! $user->is_verified) {
            throw new AccessDeniedHttpException('Akun belum diverifikasi. Selesaikan verifikasi OTP terlebih dahulu.');
        }

        return $user;
    }
}
