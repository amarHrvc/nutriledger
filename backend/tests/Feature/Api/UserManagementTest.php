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
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            'links' => ['first', 'last', 'prev', 'next'],
        ]);
});

test('list users includes soft-deleted users', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);
    $user->delete(); // soft delete

    $response = $this->actingAs($admin)->getJson('/api/users');

    $response->assertOk();

    $ids = collect($response->json('data'))->pluck('id')->map('intval');

    expect($ids)->toContain($user->id);
});

test('non-admin cannot list users', function () {
    $patient = User::factory()->create(['role' => 'pacijent']);

    $this->actingAs($patient)
        ->getJson('/api/users')
        ->assertForbidden();
});

test('unauthenticated request to list users returns 401', function () {
    $this->getJson('/api/users')->assertUnauthorized();
});

test('admin can view specific user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($admin)
        ->getJson("/api/users/{$user->id}");



    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => ['user' => ['type', 'id', 'attributes']],
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
        ->assertOk();
});

test('non-admin doctor can see list of all active users, not soft-deleted', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $activeUser = User::factory()->count(20)->create(['role' => 'pacijent']);
    $deletedUsers = User::factory()->count(10)->create(['role' => 'pacijent']);
    $deletedUsers->each(fn(User $user) => $user->delete()); // soft delete

    $response = $this->actingAs($doctor)->getJson('/api/users');

    $response->assertOk();

    $ids = collect($response->json('data'))->pluck('id')->map('intval');

    $deletedId = $deletedUsers->pluck('id')->first();

    expect($ids)
        ->toContain($activeUser->pluck('id')->first())
        ->and($ids)->not->toContain($deletedId)
    ;


    $response->assertOk()
        ->assertJsonPath('meta.total', 21); // Only admin exists

});

test('non-admin patient cannot list users', function () {
    $patient = User::factory()->create(['role' => 'pacijent']);

    $this->actingAs($patient)
        ->getJson('/api/users')
        ->assertForbidden();
});

test('users endpoint returns paginated results', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory(20)->create(['role' => 'pacijent']);

        $response = $this->actingAs($admin)->getJson('/api/users');


        $response->assertOk()
            ->assertJsonStructure([
                'data' =>  ['*' => ['type',
                    'id', 'attributes']],
                'meta' => ['current_page', 'per_page', 'total', 'last_page']
            ]);
    });

test('empty user list returns 200 with pagination', function () {
        $admin = User::factory()->create(['role' =>
            'doktor']);

        $response = $this->actingAs($admin)->getJson('/api/users');


        $response->assertOk()
            ->assertJsonPath('meta.total', 1); // Only admin exists
    });

test('admin can view soft-deleted user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);
    $user->delete(); // soft delete

    $response = $this->actingAs($admin)
        ->getJson("/api/users/{$user->id}");

    $response->assertOk()
        ->assertJsonPath('data.user.id', $user->id);
});

test('doctor can view other doctor', function () {
    $doctor1 = User::factory()->create(['role' => 'doktor']);
    $doctor2 = User::factory()->create(['role' => 'doktor']);

    $response = $this->actingAs($doctor1)
        ->getJson("/api/users/{$doctor2->id}");

    $response->assertOk()
        ->assertJsonPath('data.user.id', $doctor2->id);
});

test('doctor cannot view soft-deleted user', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $user = User::factory()->create(['role' => 'pacijent']);
    $user->delete(); // soft delete

    $this->actingAs($doctor)
        ->getJson("/api/users/{$user->id}")
        ->assertNotFound();
});

test('patient can view own profile', function () {
    $patient = User::factory()->create(['role' =>
        'pacijent']);

    $response = $this->actingAs($patient)
        ->getJson("/api/users/{$patient->id}");

    $response->assertOk()
        ->assertJsonPath('data.user.id', $patient->id);
});

test('patient cannot view other user', function () {
    $patient1 = User::factory()->create(['role' =>
        'pacijent']);
    $patient2 = User::factory()->create(['role' =>
        'pacijent']);

    $this->actingAs($patient1)
        ->getJson("/api/users/{$patient2->id}")
        ->assertForbidden();
});
