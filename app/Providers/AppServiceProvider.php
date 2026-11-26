<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

// app/Providers/AppServiceProvider.php
// registers rate limiters shared by api and web routes
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('otp-send', fn (Request $request) => Limit::perMinute(3)->by('otp-send:'.strtolower((string) $request->input('email')).'|'.$request->ip()));
        RateLimiter::for('gate-scan', fn (Request $request) => Limit::perMinute(120)->by('gate-scan:'.$request->user()?->id.'|'.$request->ip()));

        RateLimiter::for('otp-verify', fn (Request $request) => [
            Limit::perMinute(10)->by('otp-verify-min:'.strtolower((string) $request->input('email')).'|'.$request->ip()),
            Limit::perHour(30)->by('otp-verify-hour:'.strtolower((string) $request->input('email')).'|'.$request->ip()),
        ]);

        RateLimiter::for('login', fn (Request $request) => [
            Limit::perMinute(10)->by('login-min:'.strtolower((string) $request->input('email')).'|'.$request->ip()),
            Limit::perHour(60)->by('login-hour:'.$request->ip()),
        ]);
    }
}
