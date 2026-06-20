<?php

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Models\VitalSign;

test('doctor records vitals on own visit', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $response = $this->actingAs($doctor)->postJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals",
        ['weight' => 74.5, 'height' => 175.0, 'systolic_bp' => 120, 'diastolic_bp' => 80]
    );

    $response->assertCreated()
        ->assertJsonStructure([
            'message',
            'status',
            'data' => ['id', 'type', 'attributes' => ['bmi', 'flags', 'visitId']],
        ]);
});

test('admin records vitals on any visit', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $response = $this->actingAs($admin)->postJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals",
        ['weight' => 80.0, 'height' => 180.0]
    );

    $response->assertCreated();
});

test('partial record with weight and height computes bmi', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $response = $this->actingAs($doctor)->postJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals",
        ['weight' => 80.0, 'height' => 175.0]
    );

    $response->assertCreated()
        ->assertJsonPath('data.attributes.weight', '80.00')
        ->assertJsonPath('data.attributes.height', '175.00');

    expect($response->json('data.attributes.bmi'))->not->toBeNull();
});

test('all six fields null returns 422 with vitals error key', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $response = $this->actingAs($doctor)->postJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals",
        [
            'systolic_bp' => null,
            'diastolic_bp' => null,
            'heart_rate' => null,
            'temperature' => null,
            'weight' => null,
            'height' => null,
        ]
    );

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['vitals']);
});

test('doctor cannot record vitals on another doctors visit', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $otherDoctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $otherDoctor->id]);

    $response = $this->actingAs($doctor)->postJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals",
        ['weight' => 74.5, 'height' => 175.0]
    );

    $response->assertForbidden();
});

test('patient role cannot record vitals', function () {
    $patient = Patient::factory()->create();
    $patientUser = $patient->user;
    $doctor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $response = $this->actingAs($patientUser)->postJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals",
        ['weight' => 74.5, 'height' => 175.0]
    );

    $response->assertForbidden();
});

test('guest cannot record vitals', function () {
    $patient = Patient::factory()->create();
    $doctor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $response = $this->postJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals",
        ['weight' => 74.5, 'height' => 175.0]
    );

    $response->assertUnauthorized();
});

test('visit belonging to different patient returns 404', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $otherPatient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $otherPatient->id, 'doctor_id' => $doctor->id]);

    $response = $this->actingAs($doctor)->postJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals",
        ['weight' => 74.5, 'height' => 175.0]
    );

    $response->assertNotFound();
});

test('second record on the same visit returns 409', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    VitalSign::factory()->create(['visit_id' => $visit->id]);

    $response = $this->actingAs($doctor)->postJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals",
        ['weight' => 74.5, 'height' => 175.0]
    );

    $response->assertStatus(409);
});
