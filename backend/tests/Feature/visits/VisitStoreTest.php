<?php

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;

test('doctor creates visit with date and time', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    $response = $this->actingAs($doctor)->postJson('/api/patients/'.$patient->id.'/visits', [
        'date' => '2024-01-15',
        'time' => '14:30',
        'notes' => 'Visit with time field',
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
        'time' => '14:30',
        'notes' => 'Visit with time field',
    ]);
});

test('visit without time field returns 422', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    $response = $this->actingAs($doctor)->postJson('/api/patients/'.$patient->id.'/visits', [
        'date' => '2024-01-15',
        'notes' => 'Visit without time',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['time']);
});

test('visit with future date is allowed', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $futureDate = now()->addDays(5)->toDateString();

    $response = $this->actingAs($doctor)->postJson('/api/patients/'.$patient->id.'/visits', [
        'date' => $futureDate,
        'time' => '10:00',
        'notes' => 'Future visit',
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
                ],
            ],
        ]);

    $this->assertDatabaseHas('visits', [
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'date' => $futureDate,
        'time' => '10:00',
    ]);
});
