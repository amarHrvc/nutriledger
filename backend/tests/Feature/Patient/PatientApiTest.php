<?php

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// T012
it('allows doctor to create patient without socioeconomic data', function () {
    $doctor = User::factory()->doctor()->create();
    $patientUser = User::factory()->patient()->create();

    $response = $this->actingAs($doctor)
        ->postJson('/api/patients', [
            'userId' => $patientUser->id,
            'firstName' => 'Jane',
            'lastName' => 'Smith',
            'dateOfBirth' => '1985-05-20',
            'gender' => 'F',
            'phone' => '+387 62 111 222',
            'emergencyContactName' => 'John Smith',
            'emergencyContactPhone' => '+387 62 333 444',
        ]);

    $response->assertCreated();
    $this->assertDatabaseHas('patients', ['user_id' => $patientUser->id, 'first_name' => 'Jane']);
    $this->assertDatabaseMissing('patient_socioeconomic', ['patient_id' => Patient::latest()->first()->id]);
});

// T011
it('allows admin to create patient with socioeconomic data', function () {
    $admin = User::factory()->admin()->create();
    $patientUser = User::factory()->patient()->create();

    $response = $this->actingAs($admin)
        ->postJson('/api/patients', [
            'userId' => $patientUser->id,
            'firstName' => 'John',
            'lastName' => 'Doe',
            'dateOfBirth' => '1990-01-15',
            'gender' => 'M',
            'phone' => '+387 61 234 567',
            'emergencyContactName' => 'Jane Doe',
            'emergencyContactPhone' => '+387 61 345 678',
            'socioeconomic' => [
                'maritalStatus' => 'married',
                'employmentStatus' => 'employed_full_time',
                'incomeLevel' => 'middle',
            ],
        ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => ['firstName', 'lastName'],
                'relationships' => ['user', 'socioeconomic'],
            ],
            'included',
        ]);

    $this->assertDatabaseHas('patients', ['user_id' => $patientUser->id, 'first_name' => 'John']);
    $this->assertDatabaseHas('patient_socioeconomic', ['marital_status' => 'married']);
});

// T013
it('allows admin to list patients with pagination', function () {
    $admin = User::factory()->admin()->create();
    Patient::factory()->count(5)->create();

    $response = $this->actingAs($admin)
        ->getJson('/api/patients');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [['type', 'id', 'attributes']],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            'links' => ['first', 'last'],
        ]);
});

// T014
it('allows doctor to list patients with relationships', function () {
    $doctor = User::factory()->doctor()->create();
    Patient::factory()->hasSocioeconomic()->create();

    $response = $this->actingAs($doctor)
        ->getJson('/api/patients');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [[
                'type',
                'id',
                'attributes',
                'relationships' => [
                    'user' => ['data'],
                    'socioeconomic' => ['data'],
                ],
            ]],
        ]);
});

// T015
it('allows admin to view single patient with socioeconomic', function () {
    $admin = User::factory()->admin()->create();
    $patient = Patient::factory()->hasSocioeconomic()->create();

    $response = $this->actingAs($admin)
        ->getJson("/api/patients/{$patient->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'firstName', 'lastName', 'fullName', 'dateOfBirth',
                    'gender', 'phone', 'bloodType', 'allergies', 'medicalNotes',
                ],
                'relationships' => [
                    'socioeconomic' => ['data'],
                    'user' => ['data'],
                ],
            ],
            'included',
        ])
        ->assertJsonPath('data.type', 'patient')
        ->assertJsonPath('data.id', (string) $patient->id);
});

// T016
it('allows admin to update patient fields', function () {
    $admin = User::factory()->admin()->create();
    $patient = Patient::factory()->create(['first_name' => 'Old']);

    $response = $this->actingAs($admin)
        ->patchJson("/api/patients/{$patient->id}", [
            'firstName' => 'Updated',
            'phone' => '+387 61 999 888',
        ]);

    $response->assertOk()
        ->assertJsonPath('data.attributes.firstName', 'Updated');

    $this->assertDatabaseHas('patients', [
        'id' => $patient->id,
        'first_name' => 'Updated',
        'phone' => '+387 61 999 888',
    ]);
});

// T017
it('allows admin to soft-delete patient and cascades to socioeconomic', function () {
    $admin = User::factory()->admin()->create();
    $patient = Patient::factory()->hasSocioeconomic()->create();
    $socioId = $patient->socioeconomic->id;

    $response = $this->actingAs($admin)
        ->deleteJson("/api/patients/{$patient->id}");

    $response->assertNoContent();

    $this->assertSoftDeleted('patients', ['id' => $patient->id]);
    $this->assertSoftDeleted('patient_socioeconomic', ['id' => $socioId]);
});

// T018
it('excludes soft-deleted patients from list', function () {
    $admin = User::factory()->admin()->create();
    $active = Patient::factory()->create(['first_name' => 'Active']);
    $deleted = Patient::factory()->create(['first_name' => 'Deleted']);
    $deleted->delete();

    $response = $this->actingAs($admin)
        ->getJson('/api/patients');

    $response->assertOk();

    $ids = collect($response->json('data'))->pluck('id')->map(fn ($id) => (int) $id)->all();
    expect($ids)->toContain($active->id)
        ->not->toContain($deleted->id);
});

// T019
it('returns 401 for unauthenticated requests', function (string $method, string $uri) {
    $response = $this->json($method, $uri);
    $response->assertUnauthorized();
})->with([
    ['GET', '/api/patients'],
    ['POST', '/api/patients'],
    ['GET', '/api/patients/1'],
    ['PATCH', '/api/patients/1'],
    ['DELETE', '/api/patients/1'],
]);

// T020
it('returns 422 when required fields are missing on create', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->postJson('/api/patients', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['firstName', 'lastName', 'dateOfBirth', 'gender']);
});

it('returns 422 for invalid gender value on create', function () {
    $admin = User::factory()->admin()->create();
    $patientUser = User::factory()->patient()->create();

    $response = $this->actingAs($admin)
        ->postJson('/api/patients', [
            'userId' => $patientUser->id,
            'firstName' => 'John',
            'lastName' => 'Doe',
            'dateOfBirth' => '1990-01-15',
            'gender' => 'X',
            'phone' => '+387 61 234 567',
            'emergencyContactName' => 'Jane',
            'emergencyContactPhone' => '+387 61 345 678',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['gender']);
});

it('returns 422 for invalid date format on create', function () {
    $admin = User::factory()->admin()->create();
    $patientUser = User::factory()->patient()->create();

    $response = $this->actingAs($admin)
        ->postJson('/api/patients', [
            'userId' => $patientUser->id,
            'firstName' => 'John',
            'lastName' => 'Doe',
            'dateOfBirth' => 'not-a-date',
            'gender' => 'M',
            'phone' => '+387 61 234 567',
            'emergencyContactName' => 'Jane',
            'emergencyContactPhone' => '+387 61 345 678',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['dateOfBirth']);
});

// T021
it('response conforms to JSON:API envelope', function () {
    $admin = User::factory()->admin()->create();
    $patient = Patient::factory()->create();

    $response = $this->actingAs($admin)
        ->getJson("/api/patients/{$patient->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes',
                'relationships',
            ],
        ])
        ->assertJsonPath('data.type', 'patient')
        ->assertJsonPath('data.id', (string) $patient->id);

    expect($response->json('data.attributes'))->toHaveKeys([
        'firstName', 'lastName', 'fullName', 'dateOfBirth',
        'gender', 'phone', 'createdAt', 'updatedAt',
    ]);
});
