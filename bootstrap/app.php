<?php

use App\Exceptions\ConflictException;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsVerified;
use App\Support\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api/v1',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'verified' => EnsureUserIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (Throwable $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            if ($exception instanceof ValidationException) {
                $message = collect($exception->errors())->flatten()->first() ?? 'Data tidak valid.';

                return ApiResponse::error($message, ['errors' => $exception->errors()], $exception->status);
            }

            if ($exception instanceof AuthenticationException) {
                return ApiResponse::error('Kredensial tidak valid atau sesi sudah berakhir.', null, 401);
            }

            if ($exception instanceof AccessDeniedHttpException) {
                return ApiResponse::error($exception->getMessage() ?: 'Akses ditolak.', null, 403);
            }

            if ($exception instanceof ConflictException) {
                return ApiResponse::error($exception->getMessage(), null, 409);
            }

            if ($exception instanceof TooManyRequestsHttpException) {
                return ApiResponse::error('Terlalu banyak percobaan. Coba lagi sebentar lagi.', null, 429);
            }

            if ($exception instanceof NotFoundHttpException) {
                $previous = $exception->getPrevious();
                if ($previous instanceof ModelNotFoundException) {
                    return ApiResponse::error('Data tidak ditemukan.', null, 404);
                }

                return ApiResponse::error('Endpoint atau data tidak ditemukan.', null, 404);
            }

            if ($exception instanceof HttpExceptionInterface) {
                return ApiResponse::error($exception->getMessage() ?: 'Permintaan gagal diproses.', null, $exception->getStatusCode());
            }
        });
    })->create();
