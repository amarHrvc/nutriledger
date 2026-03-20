<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    //
    public function login(LoginRequest $request): JsonResponse
    {
        if (!auth()->attempt($request->only('email', 'password'))) {
            return $this->error('Invalid credentials', 401);
        }

        $user = auth()->user();

        // Check if user is deactivated
        if ($user->deactivated_at) {
            auth()->logout();
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
        $request->user()->currentAccessToken()->delete();
        return $this->noContent();
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'User profile',
            'status' => 200,
            'data' => new UserResource($request->user()),
        ]);
    }

}
