<?php

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Models\VitalSign;

test('admin deletes vitals and record is absent on subsequent get', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    VitalSign::factory()->create(['visit_id' => $visit->id]);

    $this->actingAs($admin)
        ->deleteJson("/api/patients/{$patient->id}/visits/{$visit->id}/vitals")
        ->assertNoContent();

    $this->actingAs($admin)
        ->getJson("/api/patients/{$patient->id}/visits/{$visit->id}/vitals")
        ->assertNotFound();
});

test('doctor cannot delete vitals', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    VitalSign::factory()->create(['visit_id' => $visit->id]);

    $this->actingAs($doctor)
        ->deleteJson("/api/patients/{$patient->id}/visits/{$visit->id}/vitals")
        ->assertForbidden();
});

test('patient cannot delete vitals', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    VitalSign::factory()->create(['visit_id' => $visit->id]);

    $this->actingAs($patient->user)
        ->deleteJson("/api/patients/{$patient->id}/visits/{$visit->id}/vitals")
        ->assertForbidden();
});

test('guest cannot delete vitals', function () {
    $patient = Patient::factory()->create();
    $doctor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $this->deleteJson("/api/patients/{$patient->id}/visits/{$visit->id}/vitals")
        ->assertUnauthorized();
});

test('visit belonging to different patient returns 404 on delete', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $otherPatient = Patient::factory()->create();
    $doctor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create(['patient_id' => $otherPatient->id, 'doctor_id' => $doctor->id]);
    VitalSign::factory()->create(['visit_id' => $visit->id]);

    $this->actingAs($admin)
        ->deleteJson("/api/patients/{$patient->id}/visits/{$visit->id}/vitals")
        ->assertNotFound();
});

test('deleting non-existent vitals returns 404', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    $this->actingAs($admin)
        ->deleteJson("/api/patients/{$patient->id}/visits/{$visit->id}/vitals")
        ->assertNotFound();
});
