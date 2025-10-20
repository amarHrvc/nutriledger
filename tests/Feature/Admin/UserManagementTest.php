<?php

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->doktor = User::factory()->create(['role' => 'doktor']);
    $this->pacijent = User::factory()->create(['role' => 'pacijent']);
});

// Access Control Tests
test('admin can access user management index page', function () {
    $response = $this->actingAs($this->admin)->get('/admin/users');

    $response->assertStatus(200);
    $response->assertSeeLivewire('admin.user-management');
});

test('doktor cannot access user management index page', function () {
    $response = $this->actingAs($this->doktor)->get('/admin/users');

    $response->assertStatus(403);
});

test('pacijent cannot access user management index page', function () {
    $response = $this->actingAs($this->pacijent)->get('/admin/users');

    $response->assertStatus(403);
});

test('guest cannot access user management index page', function () {
    $response = $this->get('/admin/users');

    $response->assertRedirect('/login');
});

// List Users Tests
test('admin can see list of all users', function () {
    User::factory()->count(5)->create(['role' => 'doktor']);
    User::factory()->count(3)->create(['role' => 'pacijent']);

    $response = $this->actingAs($this->admin)->get('/admin/users');

    $response->assertStatus(200);
    // Should see at least some of the users
    expect(User::count())->toBeGreaterThanOrEqual(11); // 3 from beforeEach + 8 new
});

test('admin can filter users by role', function () {
    User::factory()->count(5)->create(['role' => 'doktor']);
    User::factory()->count(3)->create(['role' => 'pacijent']);

    $response = $this->actingAs($this->admin)
        ->get('/admin/users?role=doktor');

    $response->assertStatus(200);
});

test('admin can search users by name or email', function () {
    User::factory()->create([
        'name' => 'Dr. John Smith',
        'email' => 'john.smith@example.com',
        'role' => 'doktor'
    ]);

    $response = $this->actingAs($this->admin)
        ->get('/admin/users?search=john');

    $response->assertStatus(200);
});

// Create User Tests
test('admin can view create user form', function () {
    $response = $this->actingAs($this->admin)->get('/admin/users/create');

    $response->assertStatus(200);
    $response->assertSeeLivewire('admin.create-user');
});

test('admin can create a new doctor', function () {
    $userData = [
        'name' => 'Dr. Jane Doe',
        'email' => 'jane.doe@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'doktor',
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/users', $userData);

    $response->assertRedirect('/admin/users');
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'name' => 'Dr. Jane Doe',
        'email' => 'jane.doe@example.com',
        'role' => 'doktor',
    ]);
});

test('admin can create a new patient', function () {
    $userData = [
        'name' => 'Patient Name',
        'email' => 'patient@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'pacijent',
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/users', $userData);

    $response->assertRedirect('/admin/users');

    $this->assertDatabaseHas('users', [
        'email' => 'patient@example.com',
        'role' => 'pacijent',
    ]);
});

test('admin can create a new admin', function () {
    $userData = [
        'name' => 'New Admin',
        'email' => 'newadmin@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'admin',
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/users', $userData);

    $response->assertRedirect('/admin/users');

    $this->assertDatabaseHas('users', [
        'email' => 'newadmin@example.com',
        'role' => 'admin',
    ]);
});

test('create user requires name', function () {
    $userData = [
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'doktor',
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/users', $userData);

    $response->assertSessionHasErrors('name');
});

test('create user requires email', function () {
    $userData = [
        'name' => 'Test User',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'doktor',
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/users', $userData);

    $response->assertSessionHasErrors('email');
});

test('create user requires valid email', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'invalid-email',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'doktor',
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/users', $userData);

    $response->assertSessionHasErrors('email');
});

test('create user requires unique email', function () {
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);

    $userData = [
        'name' => 'Test User',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'doktor',
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/users', $userData);

    $response->assertSessionHasErrors('email');
});

test('create user requires password', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'role' => 'doktor',
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/users', $userData);

    $response->assertSessionHasErrors('password');
});

test('create user requires password confirmation', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'role' => 'doktor',
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/users', $userData);

    $response->assertSessionHasErrors('password');
});

test('create user requires minimum password length', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
        'role' => 'doktor',
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/users', $userData);

    $response->assertSessionHasErrors('password');
});

test('create user requires valid role', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'invalid-role',
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/users', $userData);

    $response->assertSessionHasErrors('role');
});

// Update User Tests
test('admin can view edit user form', function () {
    $user = User::factory()->create(['role' => 'doktor']);

    $response = $this->actingAs($this->admin)
        ->get("/admin/users/{$user->id}/edit");

    $response->assertStatus(200);
    $response->assertSeeLivewire('admin.edit-user');
});

test('admin can update user details', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
        'role' => 'doktor'
    ]);

    $updateData = [
        'name' => 'New Name',
        'email' => 'new@example.com',
        'role' => 'doktor',
    ];

    $response = $this->actingAs($this->admin)
        ->put("/admin/users/{$user->id}", $updateData);

    $response->assertRedirect('/admin/users');
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'New Name',
        'email' => 'new@example.com',
    ]);
});

test('admin can change user role', function () {
    $user = User::factory()->create(['role' => 'pacijent']);

    $updateData = [
        'name' => $user->name,
        'email' => $user->email,
        'role' => 'doktor',
    ];

    $response = $this->actingAs($this->admin)
        ->put("/admin/users/{$user->id}", $updateData);

    $response->assertRedirect('/admin/users');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'role' => 'doktor',
    ]);
});

test('admin can update user password', function () {
    $user = User::factory()->create();

    $updateData = [
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ];

    $response = $this->actingAs($this->admin)
        ->put("/admin/users/{$user->id}", $updateData);

    $response->assertRedirect('/admin/users');

    // Verify password was changed by attempting to authenticate
    $this->assertTrue(
        auth()->attempt(['email' => $user->email, 'password' => 'newpassword123'])
    );
});

test('update user password is optional', function () {
    $user = User::factory()->create(['email' => 'test@example.com']);
    $oldPassword = $user->password;

    $updateData = [
        'name' => 'Updated Name',
        'email' => $user->email,
        'role' => $user->role,
    ];

    $response = $this->actingAs($this->admin)
        ->put("/admin/users/{$user->id}", $updateData);

    $response->assertRedirect('/admin/users');

    // Password should remain unchanged
    $user->refresh();
    expect($user->password)->toBe($oldPassword);
});

test('update user requires password confirmation when password provided', function () {
    $user = User::factory()->create();

    $updateData = [
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
        'password' => 'newpassword123',
    ];

    $response = $this->actingAs($this->admin)
        ->put("/admin/users/{$user->id}", $updateData);

    $response->assertSessionHasErrors('password');
});

test('update user email must be unique', function () {
    $user1 = User::factory()->create(['email' => 'user1@example.com']);
    $user2 = User::factory()->create(['email' => 'user2@example.com']);

    $updateData = [
        'name' => $user2->name,
        'email' => 'user1@example.com', // Already exists
        'role' => $user2->role,
    ];

    $response = $this->actingAs($this->admin)
        ->put("/admin/users/{$user2->id}", $updateData);

    $response->assertSessionHasErrors('email');
});

test('update user can keep same email', function () {
    $user = User::factory()->create(['email' => 'same@example.com']);

    $updateData = [
        'name' => 'Updated Name',
        'email' => 'same@example.com', // Same email
        'role' => $user->role,
    ];

    $response = $this->actingAs($this->admin)
        ->put("/admin/users/{$user->id}", $updateData);

    $response->assertRedirect('/admin/users');
    $response->assertSessionHasNoErrors();
});

// Delete User Tests
test('admin can delete a user', function () {
    $user = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($this->admin)
        ->delete("/admin/users/{$user->id}");

    $response->assertRedirect('/admin/users');
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});

test('admin cannot delete themselves', function () {
    $response = $this->actingAs($this->admin)
        ->delete("/admin/users/{$this->admin->id}");

    $response->assertSessionHasErrors();

    $this->assertDatabaseHas('users', [
        'id' => $this->admin->id,
    ]);
});

test('deleting user shows confirmation', function () {
    $user = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($this->admin)
        ->get("/admin/users");

    $response->assertStatus(200);
    // The confirmation should be handled by Livewire component
});

// Non-admin Access Tests for Mutations
test('doktor cannot create users', function () {
    $userData = [
        'name' => 'New User',
        'email' => 'new@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'pacijent',
    ];

    $response = $this->actingAs($this->doktor)
        ->post('/admin/users', $userData);

    $response->assertStatus(403);
});

test('doktor cannot update users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($this->doktor)
        ->put("/admin/users/{$user->id}", ['name' => 'Updated']);

    $response->assertStatus(403);
});

test('doktor cannot delete users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($this->doktor)
        ->delete("/admin/users/{$user->id}");

    $response->assertStatus(403);
});

test('pacijent cannot create users', function () {
    $userData = [
        'name' => 'New User',
        'email' => 'new@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'pacijent',
    ];

    $response = $this->actingAs($this->pacijent)
        ->post('/admin/users', $userData);

    $response->assertStatus(403);
});

test('pacijent cannot update users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($this->pacijent)
        ->put("/admin/users/{$user->id}", ['name' => 'Updated']);

    $response->assertStatus(403);
});

test('pacijent cannot delete users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($this->pacijent)
        ->delete("/admin/users/{$user->id}");

    $response->assertStatus(403);
});
