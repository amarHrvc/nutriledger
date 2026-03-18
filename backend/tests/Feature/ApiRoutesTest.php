<?php

use App\Models\User;

// === API Routes Tests ===

test('login endpoint responds with 200', function () {
    $this->postJson('/api/login')
        ->assertOk()
        ->assertJsonStructure(['message', 'status'])
        ->assertJson([
            'message' => 'Login successful',
            'status' => 200,
        ]);
});


it('created helper returns 201 with status in body', function () {
    // Call directly on a mock controller or via a test route
    $trait = new class {
        use \App\Traits\ApiResponses;
        public function call(): \Illuminate\Http\JsonResponse
        {
            return $this->created('Resource created');
        }
    };

    $response = $trait->call();

    expect($response->getStatusCode())->toBe(201)
        ->and($response->getData(true))->toMatchArray([
            'message' => 'Resource created',
            'status'  => 201,
        ]);
});

it('error helper returns correct status code', function () {
    $trait = new class {
        use \App\Traits\ApiResponses;
        public function call(): \Illuminate\Http\JsonResponse
        {
            return $this->error('Not found', 404);
        }
    };

    $response = $trait->call();

    expect($response->getStatusCode())->toBe(404)
        ->and($response->getData(true)['status'])->toBe(404)
        ->and($response->getData(true)['message'])->toBe('Not found')
    ;

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
    $this->actingAs($user)->postJson('/api/logout')
        ->assertOk();
});
