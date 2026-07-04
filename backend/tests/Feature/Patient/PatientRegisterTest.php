<?php

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function registerPatientPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Test Patient',
        'email' => 'test.patient@nutribase.test',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'first_name' => 'Test',
        'last_name' => 'Patient',
        'date_of_birth' => '1990-01-15',
        'gender' => 'F',
        'phone' => '+387 61 234 567',
        'emergency_contact_name' => 'Jane Doe',
        'emergency_contact_phone' => '+387 61 345 678',
    ], $overrides);
}

it('allows doctor to register a new patient with account', function () {
    $doctor = User::factory()->doctor()->create();

    $response = $this->actingAs($doctor)
        ->postJson('/api/patients/register', registerPatientPayload());

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => [
                'patient' => ['type', 'id', 'attributes', 'relationships'],
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'test.patient@nutribase.test',
        'role' => 'pacijent',
    ]);

    $user = User::where('email', 'test.patient@nutribase.test')->first();
    $this->assertDatabaseHas('patients', [
        'user_id' => $user->id,
        'first_name' => 'Test',
        'last_name' => 'Patient',
    ]);
});

it('allows admin to register a new patient with account', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->postJson('/api/patients/register', registerPatientPayload());

    $response->assertCreated();
});

it('allows registering a patient with socioeconomic data', function () {
    $doctor = User::factory()->doctor()->create();

    $response = $this->actingAs($doctor)
        ->postJson('/api/patients/register', registerPatientPayload([
            'socioeconomic' => [
                'marital_status' => 'married',
                'employment_status' => 'employed_full_time',
                'income_level' => 'middle',
            ],
        ]));

    $response->assertCreated();

    $patient = Patient::latest()->first();
    $this->assertDatabaseHas('patient_socioeconomic', [
        'patient_id' => $patient->id,
        'marital_status' => 'married',
    ]);
});

it('forbids patient from registering a new patient', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->postJson('/api/patients/register', registerPatientPayload())
        ->assertForbidden();
});

it('forbids guest from registering a new patient', function () {
    $this->postJson('/api/patients/register', registerPatientPayload())
        ->assertUnauthorized();
});

it('rejects registration with duplicate email', function () {
    $doctor = User::factory()->doctor()->create();
    $existing = User::factory()->patient()->create(['email' => 'taken@nutribase.test']);

    $this->actingAs($doctor)
        ->postJson('/api/patients/register', registerPatientPayload(['email' => 'taken@nutribase.test']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('rejects registration with mismatched password confirmation', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->postJson('/api/patients/register', registerPatientPayload(['password_confirmation' => 'different']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

it('rejects registration with missing required patient fields', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->postJson('/api/patients/register', registerPatientPayload(['first_name' => '']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['first_name']);

    $this->assertDatabaseMissing('users', ['email' => 'test.patient@nutribase.test']);
});

it('ignores a client-supplied role and always creates a pacijent account', function () {
    $doctor = User::factory()->doctor()->create();

    $this->actingAs($doctor)
        ->postJson('/api/patients/register', registerPatientPayload(['role' => 'admin']))
        ->assertCreated();

    $this->assertDatabaseHas('users', [
        'email' => 'test.patient@nutribase.test',
        'role' => 'pacijent',
    ]);
});
