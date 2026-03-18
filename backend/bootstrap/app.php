<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $apiResponses = new class { use \App\Traits\ApiResponses; };

        $exceptions->render(fn(\Illuminate\Auth\AuthenticationException $e, Request $request) =>
            $request->is('api/*') ? $apiResponses->error($e->getMessage(), 401) : null
        );

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, Request $request) {
            return response()->json([
                'message' => $e->getMessage(),
                'status'  => 422,
                'errors'  => $e->errors(),
            ], 422);
        });

        $exceptions->render(function (Throwable $e, Request $request) use ($apiResponses) {
            if (! $request->is('api/*')) {
                return null;
            }

            $code = $e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface
                ? $e->getStatusCode()
                : ($e->getCode() ?: 500);
            return $apiResponses->error($e->getMessage(), $code);
        });
    })->create();
