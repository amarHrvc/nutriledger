<?php

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Models\VitalSign;

// ── view ──────────────────────────────────────────────────────────────────────

test('admin can view any vitals', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    $vitalSign = VitalSign::factory()->create(['visit_id' => $visit->id]);
    $vitalSign->load('visit');

    expect($admin->can('view', $vitalSign))->toBeTrue();
});

test('doctor can view any vitals', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    $vitalSign = VitalSign::factory()->create(['visit_id' => $visit->id]);
    $vitalSign->load('visit');

    expect($doctor->can('view', $vitalSign))->toBeTrue();
});

test('patient can view own visit vitals', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    $vitalSign = VitalSign::factory()->create(['visit_id' => $visit->id]);
    $vitalSign->load('visit');

    expect($patient->user->can('view', $vitalSign))->toBeTrue();
});

test('patient cannot view another patients vitals', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $otherPatient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $otherPatient->id, 'doctor_id' => $doctor->id]);
    $vitalSign = VitalSign::factory()->create(['visit_id' => $visit->id]);
    $vitalSign->load('visit');

    expect($patient->user->can('view', $vitalSign))->toBeFalse();
});

// ── create ────────────────────────────────────────────────────────────────────

test('admin can create vitals on any visit', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    expect($admin->can('create', [VitalSign::class, $visit]))->toBeTrue();
});

test('doctor can create vitals on own visit', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    expect($doctor->can('create', [VitalSign::class, $visit]))->toBeTrue();
});

test('doctor cannot create vitals on another doctors visit', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $otherDoctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $otherDoctor->id]);

    expect($doctor->can('create', [VitalSign::class, $visit]))->toBeFalse();
});

test('patient cannot create vitals', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    expect($patient->user->can('create', [VitalSign::class, $visit]))->toBeFalse();
});

// ── update ────────────────────────────────────────────────────────────────────

test('admin can update any vitals', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    $vitalSign = VitalSign::factory()->create(['visit_id' => $visit->id]);
    $vitalSign->load('visit');

    expect($admin->can('update', $vitalSign))->toBeTrue();
});

test('doctor can update vitals on own visit', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    $vitalSign = VitalSign::factory()->create(['visit_id' => $visit->id]);
    $vitalSign->load('visit');

    expect($doctor->can('update', $vitalSign))->toBeTrue();
});

test('doctor cannot update vitals on another doctors visit', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $otherDoctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $otherDoctor->id]);
    $vitalSign = VitalSign::factory()->create(['visit_id' => $visit->id]);
    $vitalSign->load('visit');

    expect($doctor->can('update', $vitalSign))->toBeFalse();
});

test('patient cannot update vitals', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    $vitalSign = VitalSign::factory()->create(['visit_id' => $visit->id]);
    $vitalSign->load('visit');

    expect($patient->user->can('update', $vitalSign))->toBeFalse();
});

// ── delete ────────────────────────────────────────────────────────────────────

test('admin can delete vitals', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    $vitalSign = VitalSign::factory()->create(['visit_id' => $visit->id]);

    expect($admin->can('delete', $vitalSign))->toBeTrue();
});

test('doctor cannot delete vitals', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    $vitalSign = VitalSign::factory()->create(['visit_id' => $visit->id]);

    expect($doctor->can('delete', $vitalSign))->toBeFalse();
});

test('patient cannot delete vitals', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    $vitalSign = VitalSign::factory()->create(['visit_id' => $visit->id]);

    expect($patient->user->can('delete', $vitalSign))->toBeFalse();
});

// ── viewHistory ───────────────────────────────────────────────────────────────

test('admin can view history for any patient', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    expect($admin->can('viewHistory', [VitalSign::class, $patient]))->toBeTrue();
});

test('doctor can view history for any patient', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    expect($doctor->can('viewHistory', [VitalSign::class, $patient]))->toBeTrue();
});

test('patient can view own vitals history', function () {
    $patient = Patient::factory()->create();

    expect($patient->user->can('viewHistory', [VitalSign::class, $patient]))->toBeTrue();
});

test('patient cannot view another patients vitals history', function () {
    $patient = Patient::factory()->create();
    $otherPatient = Patient::factory()->create();

    expect($patient->user->can('viewHistory', [VitalSign::class, $otherPatient]))->toBeFalse();
});
