<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

class AuthController extends ApiController
{

    //
    public function login(): JsonResponse
    {
        return $this->ok('Login successful');

    }

    public function register(): JsonResponse
    {
        return $this->ok('hello register');
    }

    public function logout(): JsonResponse
    {
        return $this->ok('hello logout');
    }

}
