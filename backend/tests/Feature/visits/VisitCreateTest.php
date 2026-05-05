<?php

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;

test('guest cannot create visit', function () {
    $patient = Patient::factory()->create();

    $response = $this->postJson('/api/patients/'.$patient->id.'/visits', [
        'date' => '2024-01-15',
        'time' => '10:00',
        'notes' => 'Test notes',
    ]);

    $response->assertUnauthorized();
});

test('admin can create visit', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    $response = $this->actingAs($admin)->postJson('/api/patients/'.$patient->id.'/visits', [
        'date' => '2024-01-15',
        'time' => '10:00',
        'notes' => 'Test notes from admin',
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'message',
            'status',
            'data' => [
                'visit' => [
                    'type',
                    'id',
                    'attributes' => [
                        'date',
                        'time',
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
});

test('doctor can create visit', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    $response = $this->actingAs($doctor)->postJson('/api/patients/'.$patient->id.'/visits', [
        'date' => '2024-01-15',
        'time' => '10:00',
        'notes' => 'Test notes from doctor',
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'message',
            'status',
            'data' => [
                'visit' => [
                    'type',
                    'id',
                    'attributes' => [
                        'date',
                        'time',
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
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'date' => '2024-01-15',
        'time' => '10:00',
        'notes' => 'Test notes from doctor',
    ]);
});

test('patient cannot create visit', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $targetPatient = Patient::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/patients/'.$targetPatient->id.'/visits', [
        'date' => '2024-01-15',
        'time' => '10:00',
        'notes' => 'Test notes',
    ]);

    $response->assertForbidden();
});

test('doctor must provide date', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    $response = $this->actingAs($doctor)->postJson('/api/patients/'.$patient->id.'/visits', [
        'notes' => 'Test notes',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['date']);
});

test('notes can be null', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    $response = $this->actingAs($doctor)->postJson('/api/patients/'.$patient->id.'/visits', [
        'date' => '2024-01-15',
        'time' => '10:00',
    ]);

    $response->assertCreated();
});

test('time field is required', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    $response = $this->actingAs($doctor)->postJson('/api/patients/'.$patient->id.'/visits', [
        'date' => '2024-01-15',
        'notes' => 'Test notes',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['time']);
});

test('notes must not exceed 10000 characters', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $longNotes = str_repeat('a', 10001);

    $response = $this->actingAs($doctor)->postJson('/api/patients/'.$patient->id.'/visits', [
        'date' => '2024-01-15',
        'time' => '10:00',
        'notes' => $longNotes,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['notes']);
});

test('created visit belongs to correct patient and doctor', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    $response = $this->actingAs($doctor)->postJson('/api/patients/'.$patient->id.'/visits', [
        'date' => '2024-01-15',
        'time' => '10:00',
        'notes' => 'Correct patient and doctor test',
    ]);

    $response->assertCreated();

    $visit = Visit::latest()->first();

    expect($visit->patient_id)->toBe($patient->id);
    expect($visit->doctor_id)->toBe($doctor->id);
    expect($visit->date->toDateString())->toBe('2024-01-15');
    expect($visit->time)->toBe('10:00');
    expect($visit->notes)->toBe('Correct patient and doctor test');
});
