<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        Log::debug("RoleMiddleware role: " . json_encode($roles));

        $user = Auth::user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
            }
            return redirect()->route('login');
        }

        if (!in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
            }
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
