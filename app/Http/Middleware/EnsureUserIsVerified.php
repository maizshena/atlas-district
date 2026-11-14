<?php

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->is_verified) {
            if ($request->is('api/*')) {
                return ApiResponse::error('Akun belum diverifikasi.', null, 403);
            }

            return redirect()->route('auth.verify', ['email' => $request->user()->email]);
        }

        return $next($request);
    }
}
