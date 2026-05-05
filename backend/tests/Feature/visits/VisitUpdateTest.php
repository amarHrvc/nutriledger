<?php

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;

test('guest cannot update visit', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $response = $this->putJson('/api/patients/'.$patient->id.'/visits/'.$visit->id, [
        'date' => now()->toDateString(),
        'notes' => 'Updated notes',
    ]);

    $response->assertUnauthorized();
});

test('admin can update any visit', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $response = $this->actingAs($admin)->putJson('/api/patients/'.$patient->id.'/visits/'.$visit->id, [
        'date' => now()->toDateString(),
        'notes' => 'Updated by admin',
    ]);

    $response->assertOk();
});

test('doctor can update own visit', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'date' => now()->toDateString(),
        'notes' => 'Original notes',
    ]);

    $response = $this->actingAs($doctor)->putJson('/api/patients/'.$patient->id.'/visits/'.$visit->id, [
        'date' => now()->toDateString(),
        'notes' => 'Updated notes',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
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
                    'relationships' => [
                        'patient',
                        'doctor',
                    ],
                ],
            ],
        ]);

    $this->assertDatabaseHas('visits', [
        'id' => $visit->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'date' => now()->toDateString(),
        'notes' => 'Updated notes',
    ]);
});

test('doctor cannot update another doctor\'s visit', function () {
    $doctor1 = User::factory()->create(['role' => 'doktor']);
    $doctor2 = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor1->id,
    ]);

    $response = $this->actingAs($doctor2)->putJson('/api/patients/'.$patient->id.'/visits/'.$visit->id, [
        'date' => now()->toDateString(),
        'notes' => 'Updated by another doctor',
    ]);

    $response->assertForbidden();
});

test('patient cannot update visit', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $doctor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $response = $this->actingAs($user)->putJson('/api/patients/'.$patient->id.'/visits/'.$visit->id, [
        'date' => now()->toDateString(),
        'notes' => 'Updated by patient',
    ]);

    $response->assertForbidden();
});

test('doctor can update only notes when updating', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'date' => now()->toDateString(),
        'notes' => 'Original notes',
    ]);

    $response = $this->actingAs($doctor)->putJson('/api/patients/'.$patient->id.'/visits/'.$visit->id, [
        'notes' => 'Updated notes only',
    ]);

    $response->assertOk();

    $this->assertDatabaseHas('visits', [
        'id' => $visit->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'date' => now()->toDateString(),
        'notes' => 'Updated notes only',
    ]);
});

test('date must be before or equal today when updating', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);
    $futureDate = now()->addDays(5)->toDateString();

    $response = $this->actingAs($doctor)->putJson('/api/patients/'.$patient->id.'/visits/'.$visit->id, [
        'date' => $futureDate,
        'notes' => 'Future date',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['date']);
});

test('doctor can update only date when updating', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'date' => now()->toDateString(),
        'notes' => 'Original notes',
    ]);
    $yesterday = now()->subDay()->toDateString();

    $response = $this->actingAs($doctor)->putJson('/api/patients/'.$patient->id.'/visits/'.$visit->id, [
        'date' => $yesterday,
    ]);

    $response->assertOk();

    $this->assertDatabaseHas('visits', [
        'id' => $visit->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'date' => $yesterday,
        'notes' => 'Original notes',
    ]);
});

test('notes must not exceed 10000 characters when updating', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);
    $longNotes = str_repeat('a', 10001);

    $response = $this->actingAs($doctor)->putJson('/api/patients/'.$patient->id.'/visits/'.$visit->id, [
        'date' => '2024-01-15',
        'notes' => $longNotes,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['notes']);
});

test('updated visit has correct values in response', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'date' => now()->toDateString(),
        'notes' => 'Original notes',
    ]);
    $yesterday = now()->subDay()->toDateString();

    $response = $this->actingAs($doctor)->putJson('/api/patients/'.$patient->id.'/visits/'.$visit->id, [
        'date' => $yesterday,
        'notes' => 'New updated notes',
    ]);

    $response->assertOk();

    $updatedVisit = Visit::find($visit->id);
    expect($updatedVisit->date->toDateString())->toBe($yesterday);
    expect($updatedVisit->notes)->toBe('New updated notes');
    expect($updatedVisit->doctor_id)->toBe($doctor->id);
});

test('visit from different patient returns 404', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient1 = Patient::factory()->create();
    $patient2 = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient1->id,
        'doctor_id' => $doctor->id,
    ]);

    // Try to update the visit through a different patient's endpoint
    $response = $this->actingAs($doctor)->putJson('/api/patients/'.$patient2->id.'/visits/'.$visit->id, [
        'date' => now()->toDateString(),
        'notes' => 'Updated notes',
    ]);

    $response->assertNotFound();
});

test('doctor can update visit dated today - boundary editable', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'date' => now()->toDateString(),
        'notes' => 'Original notes',
    ]);

    $response = $this->actingAs($doctor)->putJson('/api/patients/'.$patient->id.'/visits/'.$visit->id, [
        'date' => now()->toDateString(),
        'notes' => 'Updated today notes',
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('visits', [
        'id' => $visit->id,
        'date' => now()->toDateString(),
        'notes' => 'Updated today notes',
    ]);
});

test('doctor can update visit dated yesterday - 1-day lock boundary', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $yesterday = now()->subDay()->toDateString();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'date' => $yesterday,
        'notes' => 'Original notes',
    ]);

    $response = $this->actingAs($doctor)->putJson('/api/patients/'.$patient->id.'/visits/'.$visit->id, [
        'date' => $yesterday,
        'notes' => 'Updated yesterday notes',
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('visits', [
        'id' => $visit->id,
        'date' => $yesterday,
        'notes' => 'Updated yesterday notes',
    ]);
});

test('doctor cannot update visit dated 2 days ago - locked', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $twoDaysAgo = now()->subDays(2)->toDateString();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'date' => $twoDaysAgo,
        'notes' => 'Original notes',
    ]);

    $response = $this->actingAs($doctor)->putJson('/api/patients/'.$patient->id.'/visits/'.$visit->id, [
        'date' => $twoDaysAgo,
        'notes' => 'Trying to update locked visit',
    ]);

    $response->assertForbidden();
});

test('wrong doctor cannot update visit - authorization check', function () {
    $doctor1 = User::factory()->create(['role' => 'doktor']);
    $doctor2 = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor1->id,
        'date' => now()->toDateString(),
    ]);

    $response = $this->actingAs($doctor2)->putJson('/api/patients/'.$patient->id.'/visits/'.$visit->id, [
        'date' => now()->toDateString(),
        'notes' => 'Trying to update another doctor\'s visit',
    ]);

    $response->assertForbidden();
});
