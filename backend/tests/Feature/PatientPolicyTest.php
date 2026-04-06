<?php

use App\Models\Patient;
use App\Models\User;

// === viewAny() Authorization Tests ===

test('admin can view any patients', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    expect($admin->can('viewAny', Patient::class))->toBeTrue();
});
test('doctor can view any patients', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);

    expect($doktor->can('viewAny', Patient::class))->toBeTrue();
});

test('patient can not view other patients', function () {
    $user = User::factory()->create(['role' => 'pacijent']);

    expect($user->can('viewAny', Patient::class))->toBeFalse();
});

// === view() Authorization Tests ===

test('pacijent can view own patient profile', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    expect($user->can('view', $patient))->toBeTrue();
});

test('pacijent can not view other patient profile', function () {
    $patientA = Patient::factory()->create();
    $patientB = Patient::factory()->create();

    expect($patientB->user->can('view', $patientA))->toBeFalse();
});
test('admin can view any patient profile', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    expect($admin->can('view', $patient))->toBeTrue();
});
test('doktor can view any patient profile', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    expect($doktor->can('view', $patient))->toBeTrue();
});

// === create() Authorization Tests ===

test('admin can create patients', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    expect($admin->can('create', Patient::class))->toBeTrue();
});
test('doktor can create patients', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);

    expect($doktor->can('create', Patient::class))->toBeTrue();
});
test('pacijent cannot create patients', function () {
    $pacijent = User::factory()->create(['role' => 'pacijent']);

    expect($pacijent->can('create', Patient::class))->toBeFalse();
});

// === update() Authorization Tests ===

test('pacijent can update own profile', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    expect($user->can('update', $patient))->toBeTrue();
});
test('admin can update any patient', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    expect($admin->can('update', $patient))->toBeTrue();
});
test('doktor can update any patient', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    expect($doktor->can('update', $patient))->toBeTrue();
});
test('pacijent can not update other patient profile', function () {
    $patientA = Patient::factory()->create();
    $patientB = Patient::factory()->create();

    expect($patientB->user->can('update', $patientA))->toBeFalse();
});

// === delete() Authorization Tests ===

test('admin can delete patients', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    expect($admin->can('delete', $patient))->toBeTrue();
});
test('doktor can delete patients', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    expect($doktor->can('delete', $patient))->toBeTrue();
});
test('pacijent cannot delete own profile', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    expect($user->can('delete', $patient))->toBeFalse();
});
test('pacijent cannot delete other patients', function () {
    $patientA = Patient::factory()->create();
    $patientB = Patient::factory()->create();

    expect($patientB->user->can('delete', $patientA))->toBeFalse();
});

// === restore() Authorization Tests ===

test('admin can restore soft deleted patients', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $patient->delete(); // Soft delete

    expect($admin->can('restore', $patient))->toBeTrue();
});

test('doktor can restore soft deleted patients', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $patient->delete();

    expect($doktor->can('restore', $patient))->toBeTrue();
});

test('pacijent cannot restore own profile', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $patient->delete();

    expect($user->can('restore', $patient))->toBeFalse();
});

test('pacijent cannot restore other patients', function () {
    $patientA = Patient::factory()->create();
    $patientA->delete();
    $patientB = Patient::factory()->create();

    expect($patientB->user->can('restore', $patientA))->toBeFalse();
});

// === forceDelete() Authorization Tests ===

test('admin can permanently delete patients', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    expect($admin->can('forceDelete', $patient))->toBeTrue();
});

test('doktor cannot permanently delete patients', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    expect($doktor->can('forceDelete', $patient))->toBeFalse();
});

test('pacijent cannot permanently delete own profile', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    expect($user->can('forceDelete', $patient))->toBeFalse();
});

test('pacijent cannot permanently delete other patients', function () {
    $patientA = Patient::factory()->create();
    $patientB = Patient::factory()->create();

    expect($patientB->user->can('forceDelete', $patientA))->toBeFalse();
});
