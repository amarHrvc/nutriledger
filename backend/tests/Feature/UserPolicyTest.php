<?php

use App\Models\User;

// === viewAny() Authorization Tests ===

test('admin can view any users', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    expect($admin->can('viewAny', User::class))->toBeTrue();
});

test('doctor can view any users', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);

    expect($doktor->can('viewAny', User::class))->toBeTrue();
});

test('patient cannot view any users', function () {
    $pacijent = User::factory()->create(['role' => 'pacijent']);

    expect($pacijent->can('viewAny', User::class))->toBeFalse();
});

// === view() Authorization Tests ===

test('admin can view any user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);

    expect($admin->can('view', $user))->toBeTrue();
});

test('doctor can view any user', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $user = User::factory()->create(['role' => 'pacijent']);

    expect($doktor->can('view', $user))->toBeTrue();
});

test('patient cannot view other users', function () {
    $userA = User::factory()->create(['role' => 'pacijent']);
    $userB = User::factory()->create(['role' => 'pacijent']);

    expect($userA->can('view', $userB))->toBeFalse();
});

// === create() Authorization Tests ===

test('admin can create users', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    expect($admin->can('create', User::class))->toBeTrue();
});

test('doctor cannot create users', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);

    expect($doktor->can('create', User::class))->toBeFalse();
});

test('patient cannot create users', function () {
    $pacijent = User::factory()->create(['role' => 'pacijent']);

    expect($pacijent->can('create', User::class))->toBeFalse();
});

// === update() Authorization Tests ===

test('admin can update any user', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);

    expect($admin->can('update', $user))->toBeTrue();
});

test('doctor cannot update any user', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $user = User::factory()->create(['role' => 'pacijent']);

    expect($doktor->can('update', $user))->toBeFalse();
});

test('patient cannot update other users', function () {
    $userA = User::factory()->create(['role' => 'pacijent']);
    $userB = User::factory()->create(['role' => 'pacijent']);

    expect($userA->can('update', $userB))->toBeFalse();
});

// === delete() Authorization Tests ===

test('admin can delete other users', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);

    expect($admin->can('delete', $user))->toBeTrue();
});

test('admin cannot delete themselves', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    expect($admin->can('delete', $admin))->toBeFalse();
});

test('doctor cannot delete users', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $user = User::factory()->create(['role' => 'pacijent']);

    expect($doktor->can('delete', $user))->toBeFalse();
});

test('patient cannot delete themselves', function () {
    $pacijent = User::factory()->create(['role' => 'pacijent']);

    expect($pacijent->can('delete', $pacijent))->toBeFalse();
});

test('patient cannot delete other users', function () {
    $userA = User::factory()->create(['role' => 'pacijent']);
    $userB = User::factory()->create(['role' => 'pacijent']);

    expect($userA->can('delete', $userB))->toBeFalse();
});

// === restore() Authorization Tests ===

test('admin can restore soft deleted users', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);
    $user->delete(); // Soft delete

    expect($admin->can('restore', $user))->toBeTrue();
});

test('doctor cannot restore soft deleted users', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $user = User::factory()->create(['role' => 'pacijent']);
    $user->delete();

    expect($doktor->can('restore', $user))->toBeFalse();
});

test('patient cannot restore any users', function () {
    $pacijent = User::factory()->create(['role' => 'pacijent']);
    $user = User::factory()->create(['role' => 'pacijent']);
    $user->delete();

    expect($pacijent->can('restore', $user))->toBeFalse();
});

// === forceDelete() Authorization Tests ===

test('admin can permanently delete users', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'pacijent']);

    expect($admin->can('forceDelete', $user))->toBeTrue();
});

test('doctor cannot permanently delete users', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $user = User::factory()->create(['role' => 'pacijent']);

    expect($doktor->can('forceDelete', $user))->toBeFalse();
});

test('patient cannot permanently delete any users', function () {
    $pacijent = User::factory()->create(['role' => 'pacijent']);
    $user = User::factory()->create(['role' => 'pacijent']);

    expect($pacijent->can('forceDelete', $user))->toBeFalse();
});
