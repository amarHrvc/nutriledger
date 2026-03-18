<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use ApiResponses;
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
