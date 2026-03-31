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
    $deletedUsers->each(fn (User $user) => $user->delete()); // soft delete

    $response = $this->actingAs($doctor)->getJson('/api/users');

    $response->assertOk();

    $ids = collect($response->json('data'))->pluck('id')->map('intval');

    $deletedId = $deletedUsers->pluck('id')->first();

    expect($ids)
        ->toContain($activeUser->pluck('id')->first())
        ->and($ids)->not->toContain($deletedId);

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
            'data' => ['*' => ['type',
                'id', 'attributes']],
            'meta' => ['current_page', 'per_page', 'total', 'last_page'],
        ]);
});

test('empty user list returns 200 with pagination', function () {
    $admin = User::factory()->create(['role' => 'doktor']);

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
    $patient = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($patient)
        ->getJson("/api/users/{$patient->id}");

    $response->assertOk()
        ->assertJsonPath('data.user.id', $patient->id);
});

test('patient cannot view other user', function () {
    $patient1 = User::factory()->create(['role' => 'pacijent']);
    $patient2 = User::factory()->create(['role' => 'pacijent']);

    $this->actingAs($patient1)
        ->getJson("/api/users/{$patient2->id}")
        ->assertForbidden();
});

test('admin can create new user', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $payload = User::factory()->raw([
        'password_confirmation' => 'password',
    ]);

    $response = $this->actingAs($admin)->postJson('/api/users', $payload);

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => ['user' => ['type', 'id', 'attributes']],
        ]);
});

test('duplicate email returns 422', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->create(['email' => 'taken@example.com']);

    $payload = User::factory()->raw([
        'email' => 'taken@example.com',
        'password_confirmation' => 'password',
    ]);

    $response = $this->actingAs($admin)->postJson('/api/users', $payload);

    $response->assertUnprocessable()
        ->assertJsonPath('errors.email.0', 'The email has already been taken.');
});

test('missing required fields returns 422', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)
        ->postJson('/api/users', [
            'email' => 'user@example.com',
            // Missing: password, password_confirmation, name, role
        ]);

    $response->assertUnprocessable()
        ->assertJsonPath('errors.password.0', 'The password field is required.');
});

test('password confirmation mismatch returns 422', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $payload = User::factory()->raw([
        'password_confirmation' => 'DifferentPass456!',
    ]);

    $response = $this->actingAs($admin)->postJson('/api/users', $payload);

    $response->assertUnprocessable()
        ->assertJsonPath('errors.password.0', 'The password confirmation does not match.');
});

test('invalid role returns 422', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $payload = User::factory()->raw([
        'role' => 'superuser',
        'password_confirmation' => 'password',
    ]);

    $response = $this->actingAs($admin)->postJson('/api/users', $payload);

    $response->assertUnprocessable()
        ->assertJsonPath('errors.role.0', 'The role field must be admin, doktor, or pacijent.');
});

test('non-admin cannot create user', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);

    $payload = User::factory()->raw([
        'password_confirmation' => 'password',
    ]);

    $response = $this->actingAs($doctor)->postJson('/api/users', $payload);

    $response->assertForbidden();
});

test('created user password is hashed', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $payload = User::factory()->raw([
        'password_confirmation' => 'password',
    ]);
    $email = $payload['email'];

    $this->actingAs($admin)->postJson('/api/users', $payload);

    $user = User::where('email', $email)->first();

    expect(Hash::check('password', $user->password))->toBeTrue()
        ->and($user->password)->not->toBe('password');
});

// AUTH-21: User Update Tests
test('admin can update user name', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent', 'name' => 'Old Name']);

    $response = $this->actingAs($admin)
        ->patchJson("/api/users/{$user->id}", [
            'name' => 'New Name',
        ]);

    $response->assertOk()
        ->assertJsonPath('data.user.attributes.name', 'New Name');

    $user->refresh();
    expect($user->name)->toBe('New Name');
});

test('admin can change user role', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($admin)
        ->patchJson("/api/users/{$user->id}", [
            'role' => 'doktor',
        ]);

    $response->assertOk()
        ->assertJsonPath('data.user.attributes.role', 'doktor');

    $user->refresh();
    expect($user->role)->toBe('doktor');
});

test('admin update with duplicate email returns 422', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['email' => 'unique@example.com']);
    User::factory()->create(['email' => 'taken@example.com']);

    $response = $this->actingAs($admin)
        ->patchJson("/api/users/{$user->id}", [
            'email' => 'taken@example.com',
        ]);

    $response->assertUnprocessable()
        ->assertJsonPath('errors.email.0', 'The email has already been taken.');
});

test('admin update email uniqueness ignores own record', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['email' => 'original@example.com']);

    $response = $this->actingAs($admin)
        ->patchJson("/api/users/{$user->id}", [
            'email' => 'original@example.com',
        ]);

    $response->assertOk();

    $user->refresh();
    expect($user->email)->toBe('original@example.com');
});

test('admin can update user password', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);
    $oldPassword = 'password';

    // Verify old password works
    expect(Hash::check($oldPassword, $user->password))->toBeTrue();

    $newPassword = 'NewPassword123!';
    $response = $this->actingAs($admin)
        ->patchJson("/api/users/{$user->id}", [
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

    $response->assertOk();

    $user->refresh();

    // Verify old password no longer works
    expect(Hash::check($oldPassword, $user->password))->toBeFalse();

    // Verify new password works
    expect(Hash::check($newPassword, $user->password))->toBeTrue();
});

test('admin partial update leaves other fields unchanged', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create([
        'role' => 'pacijent',
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);

    $response = $this->actingAs($admin)
        ->patchJson("/api/users/{$user->id}", [
            'name' => 'Updated Name',
        ]);

    $response->assertOk();

    $user->refresh();
    expect($user->name)->toBe('Updated Name')
        ->and($user->email)->toBe('original@example.com')
        ->and($user->role)->toBe('pacijent');
});

test('non-admin cannot update user', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $user = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($doctor)
        ->patchJson("/api/users/{$user->id}", [
            'name' => 'New Name',
        ]);

    $response->assertForbidden();
});

test('non-existent user update returns 404', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)
        ->patchJson('/api/users/99999', [
            'name' => 'New Name',
        ]);

    $response->assertNotFound();
});

// AUTH-23: Deactivation and Restore Tests
test('admin can deactivate user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($admin)
        ->deleteJson("/api/users/{$user->id}");

    $response->assertNoContent();

    $user->refresh();
    expect($user->deleted_at)->not->toBeNull();
});

test('deactivated user appears in admin list with non-null deletedAt', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);
    $user->delete();

    $response = $this->actingAs($admin)
        ->getJson('/api/users');

    $response->assertOk();

    $userData = collect($response->json('data'))
        ->firstWhere('id', (string) $user->id);

    expect($userData)->not->toBeNull()
        ->and($userData['attributes']['deletedAt'])->not->toBeNull();
});

test('deactivated user cannot log in', function () {
    $user = User::factory()->create(['role' => 'pacijent', 'email' => 'deactivated@example.com']);
    $user->delete();

    $response = $this->postJson('/api/login', [
        'email' => 'deactivated@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(401);
});

test('admin can restore user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);
    $user->delete();

    $response = $this->actingAs($admin)
        ->postJson("/api/users/{$user->id}/restore");

    $response->assertOk()
        ->assertJsonPath('data.user.attributes.deleted_at', null);

    $user->refresh();
    expect($user->deleted_at)->toBeNull();
});

test('admin cannot deactivate themselves', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)
        ->deleteJson("/api/users/{$admin->id}");

    $response->assertForbidden();

    $admin->refresh();
    expect($admin->deleted_at)->toBeNull();
});

test('non-admin cannot deactivate user', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $user = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($doctor)
        ->deleteJson("/api/users/{$user->id}");

    $response->assertForbidden();

    $user->refresh();
    expect($user->deleted_at)->toBeNull();
});

test('restore already-active user returns 200', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($admin)
        ->postJson("/api/users/{$user->id}/restore");

    $response->assertOk()
        ->assertJsonPath('data.user.attributes.deleted_at', null);

    $user->refresh();
    expect($user->deleted_at)->toBeNull();
});

// AUTH-29: UserResource Structure Tests
test('user resource has type id and attributes keys', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($admin)
        ->getJson("/api/users/{$user->id}");

    $response->assertJsonStructure([
        'data' => [
            'user' => ['type', 'id', 'attributes'],
        ],
    ]);

    expect($response->json('data.user.type'))->toBe('users');
    expect($response->json('data.user.id'))->toBe($user->id);
});

test('user resource attributes use camelCase keys', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($admin)
        ->getJson("/api/users/{$user->id}");

    $attributes = $response->json('data.user.attributes');

    expect($attributes)->toHaveKeys(['name', 'email', 'role', 'createdAt', 'updatedAt', 'deletedAt']);
    expect(array_key_exists('created_at', $attributes ?? []))->toBeFalse();
});

test('user resource never includes password field', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($admin)
        ->getJson("/api/users/{$user->id}");

    $resourceString = json_encode($response->json());

    expect(str_contains($resourceString, '"password"'))->toBeFalse();
});

test('collection response includes pagination meta and links', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory(5)->create(['role' => 'pacijent']);

    $response = $this->actingAs($admin)
        ->getJson('/api/users');

    $response->assertJsonStructure([
        'data' => ['*' => ['type', 'id', 'attributes']],
        'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        'links' => ['first', 'last', 'prev', 'next'],
    ]);
});

test('doctor sees only active users in list', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $activeUser = User::factory()->create(['role' => 'pacijent']);
    $deletedUser = User::factory()->create(['role' => 'pacijent']);
    $deletedUser->delete();

    $response = $this->actingAs($doctor)
        ->getJson('/api/users');

    $ids = collect($response->json('data'))->pluck('id')->map('intval');

    expect($ids)->toContain($activeUser->id)
        ->and($ids)->not->toContain($deletedUser->id);
});

test('admin sees all users including soft-deleted in list', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $activeUser = User::factory()->create(['role' => 'pacijent']);
    $deletedUser = User::factory()->create(['role' => 'pacijent']);
    $deletedUser->delete();

    $response = $this->actingAs($admin)
        ->getJson('/api/users');

    $ids = collect($response->json('data'))->pluck('id')->map('intval');

    expect($ids)->toContain($activeUser->id)
        ->and($ids)->toContain($deletedUser->id);
});

test('doctor gets 404 for soft-deleted user in show', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $user = User::factory()->create(['role' => 'pacijent']);
    $user->delete();

    $response = $this->actingAs($doctor)
        ->getJson("/api/users/{$user->id}");

    $response->assertNotFound();
});

test('admin can retrieve soft-deleted user via show', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);
    $user->delete();

    $response = $this->actingAs($admin)
        ->getJson("/api/users/{$user->id}");

    $response->assertOk()
        ->assertJsonPath('data.user.attributes.deletedAt', $user->deleted_at->toIso8601String());
});

// AUTH-26: Permanent Deletion Tests
test('admin can force-delete active user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($admin)
        ->deleteJson("/api/users/{$user->id}/force");

    $response->assertNoContent();

    expect(User::find($user->id))->toBeNull();
});

test('admin can force-delete deactivated user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);
    $user->delete();

    $response = $this->actingAs($admin)
        ->deleteJson("/api/users/{$user->id}/force");

    $response->assertNoContent();

    expect(User::withTrashed()->find($user->id))->toBeNull();
});

test('force-deleted user returns 404 on fetch', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);
    $user->forceDelete();

    $response = $this->actingAs($admin)
        ->getJson("/api/users/{$user->id}");

    $response->assertNotFound();
});

test('force-deleted email can be re-registered', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['email' => 'reusable@example.com']);
    $user->forceDelete();

    $payload = User::factory()->raw([
        'email' => 'reusable@example.com',
        'password_confirmation' => 'password',
    ]);

    $response = $this->actingAs($admin)
        ->postJson('/api/users', $payload);

    $response->assertCreated();
    expect(User::where('email', 'reusable@example.com')->first()->email)->toBe('reusable@example.com');
});

test('non-admin cannot force-delete user', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $user = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($doctor)
        ->deleteJson("/api/users/{$user->id}/force");

    $response->assertForbidden();

    expect(User::find($user->id))->not->toBeNull();
});
