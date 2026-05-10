<?php

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Models\VitalSign;

test('doctor retrieves vitals history for any patient', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    VitalSign::factory()->create(['visit_id' => $visit->id]);

    $response = $this->actingAs($doctor)->getJson("/api/patients/{$patient->id}/vitals");

    $response->assertOk()
        ->assertJsonStructure([
            'message',
            'status',
            'data' => [
                '*' => ['id', 'type', 'attributes' => ['bmi', 'flags', 'visitId', 'visitDate']],
            ],
            'meta',
            'links',
        ]);
});

test('admin retrieves vitals history for any patient', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    VitalSign::factory()->create(['visit_id' => $visit->id]);

    $response = $this->actingAs($admin)->getJson("/api/patients/{$patient->id}/vitals");

    $response->assertOk();
});

test('records are ordered by visit date descending', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    $older = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id, 'date' => '2025-01-10']);
    $newer = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id, 'date' => '2025-06-20']);

    VitalSign::factory()->create(['visit_id' => $older->id]);
    VitalSign::factory()->create(['visit_id' => $newer->id]);

    $response = $this->actingAs($doctor)->getJson("/api/patients/{$patient->id}/vitals");

    $response->assertOk();

    $dates = collect($response->json('data'))->pluck('attributes.visitDate');
    expect($dates->first())->toBe('2025-06-20');
    expect($dates->last())->toBe('2025-01-10');
});

test('from filter excludes earlier records', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    $early = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id, 'date' => '2025-01-01']);
    $late = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id, 'date' => '2025-06-01']);

    VitalSign::factory()->create(['visit_id' => $early->id]);
    VitalSign::factory()->create(['visit_id' => $late->id]);

    $response = $this->actingAs($doctor)->getJson("/api/patients/{$patient->id}/vitals?from=2025-03-01");

    $response->assertOk();

    $dates = collect($response->json('data'))->pluck('attributes.visitDate');
    expect($dates)->toContain('2025-06-01')
        ->and($dates)->not->toContain('2025-01-01');
});

test('to filter excludes later records', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    $early = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id, 'date' => '2025-01-01']);
    $late = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id, 'date' => '2025-06-01']);

    VitalSign::factory()->create(['visit_id' => $early->id]);
    VitalSign::factory()->create(['visit_id' => $late->id]);

    $response = $this->actingAs($doctor)->getJson("/api/patients/{$patient->id}/vitals?to=2025-03-01");

    $response->assertOk();

    $dates = collect($response->json('data'))->pluck('attributes.visitDate');
    expect($dates)->toContain('2025-01-01')
        ->and($dates)->not->toContain('2025-06-01');
});

test('guest cannot retrieve vitals history', function () {
    $patient = Patient::factory()->create();

    $response = $this->getJson("/api/patients/{$patient->id}/vitals");

    $response->assertUnauthorized();
});

test('patient views own vitals history', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
    VitalSign::factory()->create(['visit_id' => $visit->id]);

    $response = $this->actingAs($patient->user)->getJson("/api/patients/{$patient->id}/vitals");

    $response->assertOk();
});

test('patient cannot retrieve another patients vitals history', function () {
    $patient = Patient::factory()->create();
    $otherPatient = Patient::factory()->create();

    $response = $this->actingAs($patient->user)->getJson("/api/patients/{$otherPatient->id}/vitals");

    $response->assertForbidden();
});
