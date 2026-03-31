<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('200 success response envelope', function () {
    $this->getJson('/api/ping')
        ->assertOk()
        ->assertJsonStructure(['message', 'status', 'data']);
});

test('422 validation error response envelope', function () {
    $this->postJson('/api/login', [])
        ->assertUnprocessable()
        ->assertJsonStructure(['message', 'errors'])
        ->assertJsonPath('errors.email.0', fn ($v) => is_string($v));
});

test('401 unauthenticated response envelope', function () {
    $this->getJson('/api/user')
        ->assertUnauthorized()
        ->assertJsonStructure(['message', 'status']);
});

test('403 wrong role response envelope', function () {
    $pacijent = User::factory()->create(['role' => 'pacijent']);

    $this->actingAs($pacijent)->getJson('/api/test/admin-only')
        ->assertForbidden()
        ->assertJsonStructure(['message', 'status'])
        ->assertHeader('Content-Type', 'application/json');
});

test('404 not found response envelope', function () {
    $this->getJson('/api/nonexistent-route')
        ->assertNotFound()
        ->assertJsonStructure(['message', 'status']);
});
