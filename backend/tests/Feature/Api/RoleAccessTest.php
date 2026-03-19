<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can access admin-only route', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->getJson('/api/test/admin-only')
        ->assertOk();
});

test('pacijent gets 403 JSON on admin-only route', function () {
    $pacijent = User::factory()->create(['role' => 'pacijent']);

    $this->actingAs($pacijent)->getJson('/api/test/admin-only')
        ->assertForbidden()
        ->assertJsonStructure(['message', 'status'])
        ->assertHeader('Content-Type', 'application/json');
});

test('unauthenticated request to role-protected route returns 401 not 403', function () {
    $this->getJson('/api/test/admin-only')
        ->assertUnauthorized();
});

test('doktor can access admin-doktor route', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);

    $this->actingAs($doktor)->getJson('/api/test/admin-doktor-only')
        ->assertOk();
});

test('pacijent gets 403 on admin-doktor route', function () {
    $pacijent = User::factory()->create(['role' => 'pacijent']);

    $this->actingAs($pacijent)->getJson('/api/test/admin-doktor-only')
        ->assertForbidden()
        ->assertJsonStructure(['message', 'status'])
        ->assertHeader('Content-Type', 'application/json');
});

test('admin can access admin-doktor route', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->getJson('/api/test/admin-doktor-only')
        ->assertOk();
});
