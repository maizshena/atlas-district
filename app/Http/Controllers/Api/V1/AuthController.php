<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Services\OtpService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// app/Http/Controllers/Api/V1/AuthController.php
// token auth for the api, delegation only
class AuthController extends Controller
{
    public function register(RegisterRequest $request, AuthService $auth): JsonResponse
    {
        $user = $auth->register($request->validated());

        return ApiResponse::success('Pendaftaran berhasil. Kode verifikasi sudah dikirim ke email Anda.', ['email' => $user->email], 201);
    }

    public function verifyOtp(VerifyOtpRequest $request, OtpService $otp): JsonResponse
    {
        $user = User::query()->where('email', $request->validated('email'))->first();

        if ($user === null || ! $otp->verify($user, $request->validated('code'))) {
            return ApiResponse::error('Kode verifikasi salah atau sudah kedaluwarsa.', null, 422);
        }

        return ApiResponse::success('Akun berhasil diverifikasi. Silakan login.');
    }

    public function resendOtp(ResendOtpRequest $request, OtpService $otp): JsonResponse
    {
        $user = User::query()->where('email', $request->validated('email'))->first();

        if ($user !== null && ! $user->is_verified) {
            $otp->issue($user);
        }

        return ApiResponse::success('Jika akun memerlukan verifikasi, kode baru sudah dikirim.');
    }

    public function login(LoginRequest $request, AuthService $auth): JsonResponse
    {
        $user = $auth->attempt($request->validated('email'), $request->validated('password'));

        return ApiResponse::success('Login berhasil.', [
            'token' => $user->createToken('api')->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_verified' => $user->is_verified,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success('Data pengguna.', $request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return ApiResponse::success('Logout berhasil.');
    }
}
