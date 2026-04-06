<?php

use App\Http\Middleware\RoleMiddleware;
use App\Traits\ApiResponses;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $apiResponses = new class
        {
            use ApiResponses;
        };

        $exceptions->render(fn (AuthenticationException $e, Request $request) => $request->is('api/*') ? $apiResponses->error($e->getMessage(), 401) : null
        );

        $exceptions->render(function (ValidationException $e, Request $request) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 422,
                'errors' => $e->errors(),
            ], 422);
        });

        $exceptions->render(function (Throwable $e, Request $request) use ($apiResponses) {
            if (! $request->is('api/*')) {
                return null;
            }

            $code = $e instanceof HttpExceptionInterface
                ? $e->getStatusCode()
                : ($e->getCode() ?: 500);

            return $apiResponses->error($e->getMessage(), $code);
        });
    })->create();
