<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponses;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    use ApiResponses;

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        Log::debug('RoleMiddleware role: '.json_encode($roles));

        $user = Auth::user();

        if (! $user) {
            return $this->error('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        if (! in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return $this->error('Forbidden', Response::HTTP_FORBIDDEN);
            }
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
