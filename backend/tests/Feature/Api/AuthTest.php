<?php


use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('login should return valid token', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);
    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertOk()
        ->assertJsonStructure(['token', 'user'])
        ->assertJson([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
            ],
        ])
        ;
});


test('logout revokes token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withToken($token)->postJson('/api/logout');

    $response
        ->assertNoContent();

    $this->app['auth']->forgetGuards();

    $this->withToken($token)->getJson("/api/user/{$user->id}")
        ->assertUnauthorized();
});

test('invalid credentials return 401', function () {
    User::factory()->create(['email' => 'test@example.com']);

    $this->postJson('/api/login', [
        'email'    => 'test@example.com',
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
        ->assertJsonValidationErrors(['email', 'password'])
    ;
});


test('GET /api/user returns id name email role', function () {
    $user = User::factory()->create();

    $json = $this->actingAs($user)->getJson("/api/user/{$user->id}");
//    dd($json);
    $json
        ->assertOk()
        ->assertJsonStructure(['data']);
});

test('GET /api/user without token returns 401 not redirect', function () {
    $this->getJson('/api/user/1')->assertUnauthorized();
});
