<?php

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Models\VitalSign;

test('doctor views vitals on own patient visit', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    VitalSign::factory()->create(['visit_id' => $visit->id]);

    $response = $this->actingAs($doctor)->getJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals"
    );

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'status',
            'data' => [
                'id',
                'type',
                'attributes' => [
                    'systolicBp',
                    'diastolicBp',
                    'heartRate',
                    'temperature',
                    'weight',
                    'height',
                    'bmi',
                    'bmiCategory',
                    'flags',
                    'visitId',
                    'visitDate',
                    'patientId',
                    'patientName',
                    'doctorName',
                    'createdAt',
                    'updatedAt',
                ],
            ],
        ]);

    expect($response->json('data.attributes.flags'))->toBeArray();
});

test('admin views vitals on any visit', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    VitalSign::factory()->create(['visit_id' => $visit->id]);

    $response = $this->actingAs($admin)->getJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals"
    );

    $response->assertOk();
});

test('no vitals recorded for the visit returns 404', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $response = $this->actingAs($doctor)->getJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals"
    );

    $response->assertNotFound();
});

test('visit belonging to different patient returns 404', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $otherPatient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $otherPatient->id, 'doctor_id' => $doctor->id]);
    VitalSign::factory()->create(['visit_id' => $visit->id]);

    $response = $this->actingAs($doctor)->getJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals"
    );

    $response->assertNotFound();
});

test('guest cannot view vitals', function () {
    $patient = Patient::factory()->create();
    $doctor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $response = $this->getJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals"
    );

    $response->assertUnauthorized();
});

test('patient views own visit vitals', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    VitalSign::factory()->create(['visit_id' => $visit->id]);

    $response = $this->actingAs($patient->user)->getJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals"
    );

    $response->assertOk();
});

test('patient cannot view another patients visit vitals', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $otherPatient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $otherPatient->id, 'doctor_id' => $doctor->id]);
    VitalSign::factory()->create(['visit_id' => $visit->id]);

    $response = $this->actingAs($patient->user)->getJson(
        "/api/patients/{$otherPatient->id}/visits/{$visit->id}/vitals"
    );

    $response->assertForbidden();
});
