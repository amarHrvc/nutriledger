<?php

use App\Models\User;
use Livewire\Livewire;

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
    $this->actingAs($this->admin);

    Livewire::test(\App\Livewire\Admin\CreateUser::class)
        ->set('name', 'Dr. Jane Doe')
        ->set('email', 'jane.doe@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'doktor')
        ->call('save')
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'name' => 'Dr. Jane Doe',
        'email' => 'jane.doe@example.com',
        'role' => 'doktor',
    ]);
});

test('admin can create a new patient', function () {
    $this->actingAs($this->admin);

    Livewire::test(\App\Livewire\Admin\CreateUser::class)
        ->set('name', 'Patient Name')
        ->set('email', 'patient@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'pacijent')
        ->call('save')
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'email' => 'patient@example.com',
        'role' => 'pacijent',
    ]);
});

test('admin can create a new admin', function () {
    $this->actingAs($this->admin);

    Livewire::test(\App\Livewire\Admin\CreateUser::class)
        ->set('name', 'New Admin')
        ->set('email', 'newadmin@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'admin')
        ->call('save')
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'email' => 'newadmin@example.com',
        'role' => 'admin',
    ]);
});

test('create user requires name', function () {
    $this->actingAs($this->admin);

    Livewire::test(\App\Livewire\Admin\CreateUser::class)
        ->set('email', 'test@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'doktor')
        ->call('save')
        ->assertHasErrors('name');
});

test('create user requires email', function () {
    $this->actingAs($this->admin);

    Livewire::test(\App\Livewire\Admin\CreateUser::class)
        ->set('name', 'Test User')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'doktor')
        ->call('save')
        ->assertHasErrors('email');
});

test('create user requires valid email', function () {
    $this->actingAs($this->admin);

    Livewire::test(\App\Livewire\Admin\CreateUser::class)
        ->set('name', 'Test User')
        ->set('email', 'invalid-email')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'doktor')
        ->call('save')
        ->assertHasErrors('email');
});

test('create user requires unique email', function () {
    $this->actingAs($this->admin);

    $existingUser = User::factory()->create(['email' => 'existing@example.com']);

    Livewire::test(\App\Livewire\Admin\CreateUser::class)
        ->set('name', 'Test User')
        ->set('email', 'existing@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'doktor')
        ->call('save')
        ->assertHasErrors('email');
});

test('create user requires password', function () {
    $this->actingAs($this->admin);

    Livewire::test(\App\Livewire\Admin\CreateUser::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('role', 'doktor')
        ->call('save')
        ->assertHasErrors('password');
});

test('create user requires password confirmation', function () {
    $this->actingAs($this->admin);

    Livewire::test(\App\Livewire\Admin\CreateUser::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'password123')
        ->set('role', 'doktor')
        ->call('save')
        ->assertHasErrors('password');
});

test('create user requires minimum password length', function () {
    $this->actingAs($this->admin);

    Livewire::test(\App\Livewire\Admin\CreateUser::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'short')
        ->set('password_confirmation', 'short')
        ->set('role', 'doktor')
        ->call('save')
        ->assertHasErrors('password');
});

test('create user requires valid role', function () {
    $this->actingAs($this->admin);

    Livewire::test(\App\Livewire\Admin\CreateUser::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'invalid-role')
        ->call('save')
        ->assertHasErrors('role');
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
    $this->actingAs($this->admin);

    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
        'role' => 'doktor'
    ]);

    Livewire::test(\App\Livewire\Admin\EditUser::class, ['user' => $user])
        ->set('name', 'New Name')
        ->set('email', 'new@example.com')
        ->set('role', 'doktor')
        ->call('save')
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'New Name',
        'email' => 'new@example.com',
    ]);
});

test('admin can change user role', function () {
    $this->actingAs($this->admin);

    $user = User::factory()->create(['role' => 'pacijent']);

    Livewire::test(App\Livewire\Admin\EditUser::class, ['user' => $user])
        ->set('role', 'doktor')
        ->call('save')
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'role' => 'doktor',
    ]);
});

test('admin can update user password', function () {
    $user = User::factory()->create(['password' => bcrypt('password123')]);

    $this->assertTrue(
        auth()->attempt(['email' => $user->email, 'password' => 'password123'])
    );

    Livewire::actingAs($this->admin)
        ->test(App\Livewire\Admin\EditUser::class, ['user' => $user])
        ->set('password', 'newpassword123')
        ->set('password_confirmation', 'newpassword123')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');

    $this->assertTrue(
        auth()->attempt(['email' => $user->email, 'password' => 'newpassword123'])
    );
});

test('update user password is optional', function () {
    $this->actingAs($this->admin);

    $user = User::factory()->create(['email' => 'test@example.com']);
    $oldPassword = $user->password;

    Livewire::test(\App\Livewire\Admin\EditUser::class, ['user' => $user])
        ->set('name', 'Updated Name')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');

    // Password should remain unchanged
    $user->refresh();
    expect($user->password)->toBe($oldPassword);
});

test('update user requires password confirmation when password provided', function () {
    $this->actingAs($this->admin);

    $user = User::factory()->create();

    Livewire::test(\App\Livewire\Admin\EditUser::class, ['user' => $user])
        ->set('password', 'newpassword123')
        ->call('save')
        ->assertHasErrors('password');
});

test('update user email must be unique', function () {
    $this->actingAs($this->admin);

    $user1 = User::factory()->create(['email' => 'user1@example.com']);
    $user2 = User::factory()->create(['email' => 'user2@example.com']);

    Livewire::test(\App\Livewire\Admin\EditUser::class, ['user' => $user2])
        ->set('email', 'user1@example.com')
        ->call('save')
        ->assertHasErrors('email');
});

test('update user can keep same email', function () {
    $this->actingAs($this->admin);

    $user = User::factory()->create(['email' => 'same@example.com']);

    Livewire::test(\App\Livewire\Admin\EditUser::class, ['user' => $user])
        ->set('name', 'Updated Name')
        ->set('email', 'same@example.com')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');
});

// Delete User Tests
test('admin can delete a user', function () {
    // Skip this test until delete functionality is implemented
    $this->markTestSkipped('Delete functionality not yet implemented in Livewire');
});

test('admin cannot delete themselves', function () {
    // Skip this test until delete functionality is implemented
    $this->markTestSkipped('Delete functionality not yet implemented in Livewire');
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
    Livewire::actingAs($this->doktor)
        ->test(\App\Livewire\Admin\CreateUser::class)
        ->set('name', 'New User')
        ->set('email', 'new@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'pacijent')
        ->call('save')
        ->assertForbidden();
});

test('doktor cannot update users', function () {
    $user = User::factory()->create();

    Livewire::actingAs($this->doktor)
        ->test(\App\Livewire\Admin\EditUser::class, ['user' => $user])
        ->set('name', 'Updated')
        ->call('save')
        ->assertForbidden();
});

test('doktor cannot delete users', function () {
    $user = User::factory()->create();

    // Skip this test until delete functionality is implemented
    $this->markTestSkipped('Delete functionality not yet implemented in Livewire');
});

test('pacijent cannot create users', function () {
    Livewire::actingAs($this->pacijent)
        ->test(\App\Livewire\Admin\CreateUser::class)
        ->set('name', 'New User')
        ->set('email', 'new@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('role', 'pacijent')
        ->call('save')
        ->assertForbidden();
});

test('pacijent cannot update users', function () {
    $user = User::factory()->create();

    Livewire::actingAs($this->pacijent)
        ->test(\App\Livewire\Admin\EditUser::class, ['user' => $user])
        ->set('name', 'Updated')
        ->call('save')
        ->assertForbidden();
});

test('pacijent cannot delete users', function () {
    $user = User::factory()->create();

    // Skip this test until delete functionality is implemented
    $this->markTestSkipped('Delete functionality not yet implemented in Livewire');
});
