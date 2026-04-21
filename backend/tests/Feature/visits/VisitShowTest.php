<?php

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;

test('guest cannot view visit', function () {
    $patient = Patient::factory()->create();
    $doctor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $response = $this->getJson("/api/patients/{$patient->id}/visits/{$visit->id}");

    $response->assertUnauthorized();
});

test('doctor can view any patient visit', function () {
    $patient = Patient::factory()->create();
    $doctor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $response = $this->actingAs($doctor)->getJson("/api/patients/{$patient->id}/visits/{$visit->id}");

    $response->assertOk();
    $response->assertJsonStructure([
        'message',
        'status',
        'data' => [
            'visit' => [
                'type',
                'id',
                'attributes' => [
                    'date',
                    'notes',
                    'doctorName',
                    'createdAt',
                    'updatedAt',
                ],
            ],
        ],
    ]);
});

test('admin can view any patient visit', function () {
    $patient = Patient::factory()->create();
    $doctor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->getJson("/api/patients/{$patient->id}/visits/{$visit->id}");

    $response->assertOk();
    $response->assertJsonStructure([
        'message',
        'status',
        'data' => [
            'visit' => [
                'type',
                'id',
                'attributes' => [
                    'date',
                    'notes',
                    'doctorName',
                    'createdAt',
                    'updatedAt',
                ],
            ],
        ],
    ]);
});

test('patient can view own visit', function () {
    $patient = Patient::factory()->create();
    $patientUser = $patient->user;
    $doctor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $response = $this->actingAs($patientUser)->getJson("/api/patients/{$patient->id}/visits/{$visit->id}");

    $response->assertOk();
    $response->assertJsonStructure([
        'message',
        'status',
        'data' => [
            'visit' => [
                'type',
                'id',
                'attributes' => [
                    'date',
                    'notes',
                    'doctorName',
                    'createdAt',
                    'updatedAt',
                ],
            ],
        ],
    ]);
});

test('patient cannot view other patient visit', function () {
    $patient = Patient::factory()->create();
    $otherPatient = Patient::factory()->create();
    $otherPatientUser = $otherPatient->user;
    $doctor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $response = $this->actingAs($otherPatientUser)->getJson("/api/patients/{$patient->id}/visits/{$visit->id}");

    $response->assertForbidden();
});

test('visit not found for a patient returns 404', function () {
    $patient = Patient::factory()->create();
    $doctor = User::factory()->create(['role' => 'doktor']);

    $response = $this->actingAs($doctor)->getJson("/api/patients/{$patient->id}/visits/999");

    $response->assertNotFound();
});

test('visit must belong to correct patient via route scoping', function () {
    $patient = Patient::factory()->create();
    $otherPatient = Patient::factory()->create();
    $doctor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create([
        'patient_id' => $otherPatient->id,
        'doctor_id' => $doctor->id,
    ]);

    $response = $this->actingAs($doctor)->getJson("/api/patients/{$patient->id}/visits/{$visit->id}");

    $response->assertNotFound();
});

test('response includes all visit details with doctor information', function () {
    $patient = Patient::factory()->create();
    $doctor = User::factory()->create(['role' => 'doktor', 'name' => 'Dr. Smith']);
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'date' => '2025-03-15',
        'notes' => 'Patient doing well',
    ]);

    $response = $this->actingAs($doctor)->getJson("/api/patients/{$patient->id}/visits/{$visit->id}");

    $response->assertOk();
    $response->assertJsonPath('data.visit.attributes.date', '2025-03-15');
    $response->assertJsonPath('data.visit.attributes.notes', 'Patient doing well');
    $response->assertJsonPath('data.visit.attributes.doctorName', 'Dr. Smith');
});

test('show visit with null notes', function () {
    $patient = Patient::factory()->create();
    $doctor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'notes' => null,
    ]);

    $response = $this->actingAs($doctor)->getJson("/api/patients/{$patient->id}/visits/{$visit->id}");

    $response->assertOk();
    $response->assertJsonPath('data.visit.attributes.notes', null);
});
