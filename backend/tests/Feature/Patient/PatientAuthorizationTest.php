<?php

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// T037
it('allows doctor to list patients', function () {
    $doctor = User::factory()->doctor()->create();
    Patient::factory()->count(2)->create();

    $this->actingAs($doctor)
        ->getJson('/api/patients')
        ->assertOk();
});

it('allows doctor to view a patient', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = Patient::factory()->create();

    $this->actingAs($doctor)
        ->getJson("/api/patients/{$patient->id}")
        ->assertOk();
});

it('allows doctor to create a patient', function () {
    $doctor = User::factory()->doctor()->create();
    $patientUser = User::factory()->patient()->create();

    $this->actingAs($doctor)
        ->postJson('/api/patients', [
            'user_id' => $patientUser->id,
            'first_name' => 'Ana',
            'last_name' => 'Kovač',
            'date_of_birth' => '1990-05-15',
            'gender' => 'F',
            'phone' => '+387 61 100 200',
            'emergency_contact_name' => 'Marko Kovač',
            'emergency_contact_phone' => '+387 61 300 400',
        ])
        ->assertCreated();
});

it('allows doctor to update a patient', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = Patient::factory()->create();

    $this->actingAs($doctor)
        ->patchJson("/api/patients/{$patient->id}", [
            'first_name' => 'Updated',
        ])
        ->assertOk();
});

it('allows doctor to delete a patient', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = Patient::factory()->create();

    $this->actingAs($doctor)
        ->deleteJson("/api/patients/{$patient->id}")
        ->assertNoContent();
});
