<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    // Define test routes with role middleware
    Route::middleware(['web', 'auth', 'role:admin'])->get('/admin-only', function () {
        return response()->json(['message' => 'Admin access granted']);
    });

    Route::middleware(['web', 'auth', 'role:doktor'])->get('/doktor-only', function () {
        return response()->json(['message' => 'Doktor access granted']);
    });

    Route::middleware(['web', 'auth', 'role:pacijent'])->get('/pacijent-only', function () {
        return response()->json(['message' => 'Pacijent access granted']);
    });

    Route::middleware(['web', 'auth', 'role:doktor,admin'])->get('/admin-or-doktor', function () {
        return response()->json(['message' => 'Admin or Doktor access granted']);
    });
});

test('admin can access admin-only routes', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/admin-only');

    $response->assertStatus(200);
    $response->assertJson(['message' => 'Admin access granted']);
});

test('doktor cannot access admin-only routes', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);

    $response = $this->actingAs($doktor)->get('/admin-only');

    $response->assertStatus(403);
});

test('pacijent cannot access admin-only routes', function () {
    $pacijent = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($pacijent)->get('/admin-only');

    $response->assertStatus(403);
});

test('doktor can access doktor-only routes', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);

    $response = $this->actingAs($doktor)->get('/doktor-only');

    $response->assertStatus(200);
    $response->assertJson(['message' => 'Doktor access granted']);
});

test('admin can access doktor-only routes', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/doktor-only');

    $response->assertOk();
});

test('pacijent can access pacijent-only routes', function () {
    $pacijent = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($pacijent)->get('/pacijent-only');

    $response->assertStatus(200);
    $response->assertJson(['message' => 'Pacijent access granted']);
});

test('admin can access routes allowing admin or doktor', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/admin-or-doktor');

    $response->assertStatus(200);
    $response->assertJson(['message' => 'Admin or Doktor access granted']);
});

test('doktor can access routes allowing admin or doktor', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);

    $response = $this->actingAs($doktor)->get('/admin-or-doktor');

    $response->assertStatus(200);
    $response->assertJson(['message' => 'Admin or Doktor access granted']);
});

test('pacijent cannot access routes allowing admin or doktor', function () {
    $pacijent = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($pacijent)->get('/admin-or-doktor');

    $response->assertStatus(403);
});

test('guest users cannot access role-protected routes', function () {
    $response = $this->getJson('/admin-only');

    $response->assertUnauthorized();
});

test('middleware returns json error for API requests', function () {
    $pacijent = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($pacijent)
        ->withHeader('Accept', 'application/json')
        ->get('/admin-only');

    $response->assertStatus(403);
    $response->assertJson(['message' => 'Unauthorized']);
});
