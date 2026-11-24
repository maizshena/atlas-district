<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

// app/Http/Controllers/Web/AuthController.php
// handwritten session auth for the web ui
class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request, AuthService $auth): RedirectResponse
    {
        try {
            $user = $auth->attempt($request->validated('email'), $request->validated('password'));
        } catch (AccessDeniedHttpException) {
            return redirect()->route('auth.verify', ['email' => $request->validated('email')])
                ->with('status', 'Akun belum diverifikasi. Masukkan kode OTP yang dikirim ke email Anda.');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route($user->homeRouteName()));    
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request, AuthService $auth): RedirectResponse
    {
        $user = $auth->register($request->validated());

        return redirect()->route('auth.verify', ['email' => $user->email])
            ->with('status', 'Kode verifikasi sudah dikirim ke email Anda.');
    }

    public function showVerify(Request $request): View
    {
        return view('auth.verify', ['email' => $request->old('email', $request->query('email'))]);
    }

    public function verify(VerifyOtpRequest $request, OtpService $otp): RedirectResponse
    {
        $user = User::query()->where('email', $request->validated('email'))->first();

        if ($user === null || ! $otp->verify($user, $request->validated('code'))) {
            return back()->withInput()->withErrors(['code' => 'Kode verifikasi salah atau sudah kedaluwarsa.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route($user->homeRouteName()))
            ->with('status', 'Akun berhasil diverifikasi. Selamat datang!');
    }

    public function resend(ResendOtpRequest $request, OtpService $otp): RedirectResponse
    {
        $user = User::query()->where('email', $request->validated('email'))->first();

        if ($user !== null && ! $user->is_verified) {
            $otp->issue($user);
        }

        return back()->withInput(['email' => $request->validated('email')])
            ->with('status', 'Jika akun memerlukan verifikasi, kode baru sudah dikirim.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('events.index');
    }
}
