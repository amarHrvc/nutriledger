<?php

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;

test('admin can view any visit', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $visit = Visit::factory()->create();

    expect($admin->can('view', $visit))->toBeTrue();
});

test('doctor can view their own visit', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create(['doctor_id' => $doctor->id]);

    expect($doctor->can('view', $visit))->toBeTrue();
});

test('patient can view their own visit', function () {
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    expect($patient->user->can('view', $visit))->toBeTrue();

    //    dump($patient->toArray());
    //    dump($patient->user->toArray());
    //    dd($visit->toArray());

});

test('patient cannot view other patients visits', function () {
    $patient1 = Patient::factory()->create();
    $patient2 = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient2->id]);

    expect($patient1->user->can('view', $visit))->toBeFalse();
});

test('admin can create a visit', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    expect($admin->can('create', Visit::class))->toBeTrue();
});

test('doctor can create a visit', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);

    expect($doctor->can('create', Visit::class))->toBeTrue();
});

test('patient can not create a visit', function () {
    $patient = Patient::factory()->create();

    expect($patient->user->can('create', Visit::class))->toBeFalse();
});

// update
test('admin can update any visit', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $visit = Visit::factory()->create();
    expect($admin->can('update', $visit))->toBeTrue();
});

test('doktor can update own visits', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create(['doctor_id' => $doktor->id]);
    expect($doktor->can('update', $visit))->toBeTrue();
});

test('doktor cannot update visits by another doctor', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create(); // different doctor
    expect($doktor->can('update', $visit))->toBeFalse();
});

test('pacijent cannot update visits', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);
    expect($user->can('update', $visit))->toBeFalse();
});

// delete
test('admin can delete visits', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $visit = Visit::factory()->create();
    expect($admin->can('delete', $visit))->toBeTrue();
});

test('doktor cannot delete visits', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create(['doctor_id' => $doktor->id]);
    expect($doktor->can('delete', $visit))->toBeFalse();
});

test('pacijent cannot delete visits', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);
    expect($user->can('delete', $visit))->toBeFalse();
});
