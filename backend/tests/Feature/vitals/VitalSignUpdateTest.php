<?php

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Models\VitalSign;

test('admin updates any vitals', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    VitalSign::factory()->create(['visit_id' => $visit->id, 'weight' => 80.0, 'height' => 175.0]);

    $response = $this->actingAs($admin)->patchJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals",
        ['weight' => 78.0, 'height' => 175.0, 'systolic_bp' => 130, 'diastolic_bp' => 82]
    );

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'status',
            'data' => ['id', 'type', 'attributes' => ['bmi', 'weight', 'flags']],
        ]);
});

test('doctor updates own visit vitals', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    VitalSign::factory()->create(['visit_id' => $visit->id]);

    $response = $this->actingAs($doctor)->patchJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals",
        ['systolic_bp' => 125, 'diastolic_bp' => 80]
    );

    $response->assertOk();
});

test('doctor cannot update another doctors visit vitals', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $otherDoctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $otherDoctor->id]);
    VitalSign::factory()->create(['visit_id' => $visit->id]);

    $response = $this->actingAs($doctor)->patchJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals",
        ['systolic_bp' => 125]
    );

    $response->assertForbidden();
});

test('patient cannot update vitals', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    VitalSign::factory()->create(['visit_id' => $visit->id]);

    $response = $this->actingAs($patient->user)->patchJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals",
        ['systolic_bp' => 125]
    );

    $response->assertForbidden();
});

test('guest cannot update vitals', function () {
    $patient = Patient::factory()->create();
    $doctor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $response = $this->patchJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals",
        ['systolic_bp' => 125]
    );

    $response->assertUnauthorized();
});

test('visit belonging to different patient returns 404', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $otherPatient = Patient::factory()->create();
    $doctor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create(['patient_id' => $otherPatient->id, 'doctor_id' => $doctor->id]);
    VitalSign::factory()->create(['visit_id' => $visit->id]);

    $response = $this->actingAs($admin)->patchJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals",
        ['weight' => 78.0]
    );

    $response->assertNotFound();
});

test('updating only weight recomputes bmi using existing stored height', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    VitalSign::factory()->create(['visit_id' => $visit->id, 'weight' => 70.0, 'height' => 175.0, 'bmi' => 22.86]);

    $response = $this->actingAs($doctor)->patchJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals",
        ['weight' => 80.0]
    );

    $response->assertOk();

    // bmi = 80 / (1.75^2) = 26.12
    expect((float) $response->json('data.attributes.bmi'))->toBeGreaterThan(26.0);
});

test('no vitals on visit returns 404', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $response = $this->actingAs($doctor)->patchJson(
        "/api/patients/{$patient->id}/visits/{$visit->id}/vitals",
        ['systolic_bp' => 125]
    );

    $response->assertNotFound();
});
