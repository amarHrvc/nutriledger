<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can list all users', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory(3)->create(['role' => 'pacijent']);

    $this->actingAs($admin)
        ->getJson('/api/users')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['type', 'id', 'attributes'],
            ],
        ]);
});

test('list users includes soft-deleted users', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);
    $user->delete(); // soft delete

    $response = $this->actingAs($admin)->getJson('/api/users');

    $response->assertOk();
    // Verify soft-deleted user is in list
    $ids = collect($response->json('data.*.id'))->map('intval');
    expect($ids)->toContain($user->id);
});

test('non-admin cannot list users', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);

    $this->actingAs($doctor)
        ->getJson('/api/users')
        ->assertForbidden();
});

test('unauthenticated request to list users returns 401', function () {
    $this->getJson('/api/users')->assertUnauthorized();
});

test('admin can view specific user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);

    $this->actingAs($admin)
        ->getJson("/api/users/{$user->id}")
        ->assertOk()
        ->assertJsonStructure([
            'data' => ['type', 'id', 'attributes'],
        ]);
});

test('non-existent user returns 404', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->getJson('/api/users/99999')
        ->assertNotFound();
});

test('non-admin cannot view user', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $user = User::factory()->create(['role' => 'pacijent']);

    $this->actingAs($doctor)
        ->getJson("/api/users/{$user->id}")
        ->assertForbidden();
});
