<?php

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isAdmin()) {
            if ($request->is('api/*')) {
                return ApiResponse::error('Akses admin diperlukan.', null, 403);
            }

            abort(403, 'Akses admin diperlukan.');
        }

        return $next($request);
    }
}
