<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    protected function success(string $message, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'status' => $statusCode,
        ], $statusCode);
    }

    protected function ok(string $message): JsonResponse
    {
        return $this->success($message);
    }

    protected function created(string $message): JsonResponse
    {
        return $this->success($message, 201);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    public function error(string $message, int $statusCode): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'status'  => $statusCode,
        ], $statusCode);
    }
}
