<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends ApiController
{
    //
    public function login(LoginRequest $request): JsonResponse
    {
        if (! auth()->attempt($request->only('email', 'password'))) {
            // Check if the user exists but is soft-deleted or deactivated
            $user = User::withTrashed()->where('email', $request->email)->first();
            if ($user && ($user->trashed() || $user->deactivated_at)) {
                Log::warning('security.login_failed', [
                    'ip' => $request->ip(),
                ]);

                return $this->error('Account is deactivated', 401);
            }

            Log::warning('security.login_failed', [
                'ip' => $request->ip(),
            ]);

            return $this->error('Invalid credentials', 401);
        }

        $user = auth()->user();

        // Check if user is soft-deleted or deactivated (should not happen due to auth attempt)
        if ($user->trashed() || $user->deactivated_at) {
            auth()->logout();

            Log::warning('security.login_failed', [
                'ip' => $request->ip(),
            ]);

            return $this->error('Account is deactivated', 401);
        }

        return $this->ok('Authenticated', [
            'token' => $user->createToken('api-token', ['*'], Carbon::now()->addMinutes(config('sanctum.expiration')))->plainTextToken,
            'user' => new UserResource($user),
        ]);
    }

    public function register(): JsonResponse
    {
        return $this->ok('hello register');
    }

    public function logout(Request $request): JsonResponse
    {
        Log::warning('security.token_revoked', [
            'user_id' => $request->user()->id,
            'ip' => $request->ip(),
        ]);

        $request->user()->currentAccessToken()->delete();

        return $this->noContent();
    }

    public function me(Request $request): JsonResponse
    {

        $user = $request->user();
        $user->load('patient');

        return $this->ok(
            'Profile retrieved', ['user' => new UserResource($user)]
        );
    }
}
