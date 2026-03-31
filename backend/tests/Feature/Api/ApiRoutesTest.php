<?php

use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;

// === API Routes Tests ===

test('login endpoint responds with 200', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);
    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertOk()
        ->assertJsonStructure(['data' => ['token', 'user']]);
});

it('created helper returns 201 with status in body', function () {
    // Call directly on a mock controller or via a test route
    $trait = new class
    {
        use ApiResponses;

        public function call(): JsonResponse
        {
            return $this->created('Resource created');
        }
    };

    $response = $trait->call();

    expect($response->getStatusCode())->toBe(201)
        ->and($response->getData(true))->toMatchArray([
            'message' => 'Resource created',
            'status' => 201,
        ]);
});

it('error helper returns correct status code', function () {
    $trait = new class
    {
        use ApiResponses;

        public function call(): JsonResponse
        {
            return $this->error('Not found', 404);
        }
    };

    $response = $trait->call();

    expect($response->getStatusCode())->toBe(404)
        ->and($response->getData(true)['status'])->toBe(404)
        ->and($response->getData(true)['message'])->toBe('Not found');

});

it('register endpoint responds', function () {
    $this->postJson('/api/register')
        ->assertOk();
});

it('logout endpoint requires authentication', function () {
    $this->postJson('/api/logout')
        ->assertUnauthorized();
});

it('logout endpoint responds when authenticated', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;
    $this->withToken($token)->postJson('/api/logout')
        ->assertNoContent();
});
