<?php

use App\Models\Patient;
use App\Models\User;

// === Basic Model Tests ===

test('patient can be created with required fields', function () {

    $patient = Patient::factory()->create();
    $user = $patient->user;

    expect($patient)->toBeInstanceOf(Patient::class)
        ->and($patient->first_name)->toBe($user->name)
        ->and($patient->last_name)->toBeString()
        ->and($patient->user_id)->toBe($user->id)
        ->and($patient->gender)->toBeIn(["M", "F"])
        ->and($patient->blood_type)->toBeIn(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])
    ;
});

// TODO: Write test for patient belongs to user relationship

test('patient belongs to user relationship', function () {
    $patient = Patient::factory()->create();
    $user = $patient->user;

    expect($patient->user)->toBeInstanceOf(User::class)
        ->and($patient->user->id)->toBe($user->id);
});

// === Accessor Tests ===

test('patient full_name accessor returns combined name', function () {

    $patient = Patient::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
    ]);

    expect($patient->full_name)->toBe('Jane Smith');
});


// === Soft Delete Tests ===

test('patient can be soft deleted', function () {
    $patient = Patient::factory()->create();

    $patient->delete();

    expect($patient->trashed())->toBeTrue();
});

// TODO: Write test for patient can be restored after soft delete
test('patient can be restored after soft delete', function () {
    $patient = Patient::factory()->create();

    $patient->delete();
    expect($patient->trashed())->toBeTrue();

    $patient->restore();
    expect($patient->trashed())->toBeFalse();
});


//write test for patient date_of_birth is cast to date
test('patient date_of_birth is cast to date', function () {
    $patient = Patient::factory()->create([
        'date_of_birth' => '1990-05-15',
    ]);
    expect($patient->date_of_birth)->toBeInstanceOf(\Illuminate\Support\Carbon::class)
        ->and($patient->date_of_birth->format('Y-m-d'))->toBe('1990-05-15');
});

//test  that user  has patient relationship
test('user has patient relationship', function () {
    $patient = Patient::factory()->create();
    $user = $patient->user;


    expect($user->patient->id)->toBe($patient->id)
        ->and($user->patient)->toBeInstanceOf(Patient::class)
        ->and($user->patient->id)->toBe($patient->id);
});

// TODO: Write test for eager loading user with patient
