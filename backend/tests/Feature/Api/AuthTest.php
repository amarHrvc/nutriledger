<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('login should return valid token envelope', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);
    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertOk()
        ->assertJsonStructure([
            'message',
            'status',
            'data' => [
                'token',
                'user' => [
                    'type',
                    'id',
                    'attributes',
                ],
            ],
        ])
        ->assertJson([
            'status' => 200,
            'data' => [
                'user' => [
                    'type' => 'users',
                    'id' => (string) $user->id,
                ],
            ],
        ]);
});

test('logout revokes token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withToken($token)->postJson('/api/logout');

    $response
        ->assertNoContent();

    $this->app['auth']->forgetGuards();

    $this->withToken($token)->getJson('/api/user')
        ->assertUnauthorized();
});

test('logout returns 204 with empty response body', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withToken($token)->postJson('/api/logout');

    $response->assertNoContent();
    // Verify the response has no JSON body (204 should have null or empty string)
    $content = $response->getContent();
    expect($content)->toBeIn(['null', '', null]);
});

test('invalid credentials return 401', function () {
    User::factory()->create(['email' => 'test@example.com']);

    $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ])->assertUnauthorized();
});

test('missing password returns 422 with field errors', function () {
    $this->postJson('/api/login', ['email' => 'test@example.com'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});
test('missing email returns 422 with field errors', function () {
    $this->postJson('/api/login', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'password']);
});

test('GET /api/user returns UserResource shape', function () {
    $user = User::factory()->create();

    $json = $this->actingAs($user)->getJson('/api/user');

    dump($json->json());

    $json
        ->assertOk()
        ->assertJsonStructure([
            'data' => ['user' => [
                'type',
                'id',
                'attributes',
            ]],
        ]);
});

test('login with deactivated account returns 401', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
        'deactivated_at' => now(),
    ]);

    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertUnauthorized();
});

test('GET /api/user without token returns 401', function () {
    $this->getJson('/api/user')->assertUnauthorized();
});

test('login token expiration uses config sanctum.expiration', function () {
    $expirationMinutes = 2880;
    config(['sanctum.expiration' => $expirationMinutes]);

    $user = User::factory()->create(['password' => bcrypt('password')]);
    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertOk();

    // Verify token has expiration set from config
    $token = $user->tokens()->first();
    expect($token->expires_at)->not->toBeNull();

    // Check that expiration is approximately correct (within 1 minute)
    $expectedExpiration = Carbon::now()->addMinutes($expirationMinutes);
    expect($token->expires_at->diffInMinutes($expectedExpiration))->toBeLessThanOrEqual(1);
});
