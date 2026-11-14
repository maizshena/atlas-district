<?php

namespace App\Support;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

// app/Support/ApiResponse.php
// uniform success and error envelope for the api controllers
class ApiResponse
{
    public static function success(string $message, mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json(['status' => 'success', 'message' => $message, 'data' => $data], $status);
    }

    public static function error(string $message, mixed $data = null, int $status = 400): JsonResponse
    {
        return response()->json(['status' => 'error', 'message' => $message, 'data' => $data], $status);
    }

    /**
     * @return array{items: mixed, meta: array{current_page: int, last_page: int, per_page: int, total: int}}
     */
    public static function paginated(LengthAwarePaginator $paginator): array
    {
        return [
            'items' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }
}
