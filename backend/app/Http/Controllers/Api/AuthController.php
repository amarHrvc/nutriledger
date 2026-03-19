<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\LoginRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    //
    public function login(LoginRequest $request): JsonResponse
    {

        $request->validate($request->rules());

        return auth()->attempt($request->only('email', 'password'))
            ? $this->ok('Authenticated')->setData(
                ['token' => auth()->user()->createToken('api-token', ['*'], Carbon::now()->addMinutes(config('sanctum.expiration')))->plainTextToken, 'user' => auth()->user()->toSimpleData()]
            )
            : $this->error('Invalid credentials', 401);

    }

    public function register(): JsonResponse
    {
        return $this->ok('hello register');
    }

    public function logout(Request $request): JsonResponse
    {

        //      $request->user()->tokens()->where('id', $request->user()->currentAccessToken()->id)->delete();
        $request->user()->currentAccessToken()->delete();

        return $this->noContent();
    }
}
