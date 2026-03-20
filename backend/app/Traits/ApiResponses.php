<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    protected function success(string $message, int $statusCode = 200, ?array $data = null): JsonResponse
    {
        $response = [
            'message' => $message,
            'status' => $statusCode,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    protected function ok(string $message, ?array $data = null): JsonResponse
    {
        return $this->success($message, 200, $data);
    }

    protected function created(string $message, ?array $data = null): JsonResponse
    {
        return $this->success($message, 201, $data);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }
    protected function notFound(): JsonResponse
    {
        return response()->json(null, 404);
    }

    public function error(string $message, int $statusCode): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'status' => $statusCode,
        ], $statusCode);
    }
}
