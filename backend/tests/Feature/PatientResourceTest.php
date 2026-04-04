<?php

use App\Http\Resources\Api\PatientResource;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// === JSON:API shape ===

test('PatientResource returns correct JSON:API type and id', function (): void {
    $patient = Patient::factory()->create();

    $data = (new PatientResource($patient))->resolve();

    expect($data['type'])->toBe('patient')
        ->and($data['id'])->toBe((string) $patient->id);
});

test('PatientResource attributes contain all expected camelCase keys', function (): void {
    $patient = Patient::factory()->create();

    $data = (new PatientResource($patient))->resolve();

    expect($data['attributes'])->toHaveKeys([
        'firstName',
        'lastName',
        'fullName',
        'dateOfBirth',
        'gender',
        'phone',
        'address',
        'city',
        'postalCode',
        'emergencyContactName',
        'emergencyContactPhone',
        'bloodType',
        'allergies',
        'medicalNotes',
        'createdAt',
        'updatedAt',
    ]);
});

test('PatientResource maps snake_case fields to camelCase attributes correctly', function (): void {
    $patient = Patient::factory()->create([
        'first_name' => 'Hana',
        'last_name' => 'Hadzic',
        'date_of_birth' => '1985-07-20',
        'gender' => 'F',
        'postal_code' => '71000',
        'emergency_contact_name' => 'Kemal Hadzic',
        'emergency_contact_phone' => '+38761000000',
        'blood_type' => 'B+',
    ]);

    $data = (new PatientResource($patient))->resolve();

    expect($data['attributes']['firstName'])->toBe('Hana')
        ->and($data['attributes']['lastName'])->toBe('Hadzic')
        ->and($data['attributes']['fullName'])->toBe('Hana Hadzic')
        ->and($data['attributes']['dateOfBirth'])->toBe('1985-07-20')
        ->and($data['attributes']['gender'])->toBe('F')
        ->and($data['attributes']['postalCode'])->toBe('71000')
        ->and($data['attributes']['emergencyContactName'])->toBe('Kemal Hadzic')
        ->and($data['attributes']['emergencyContactPhone'])->toBe('+38761000000')
        ->and($data['attributes']['bloodType'])->toBe('B+');
});

test('PatientResource dateOfBirth is formatted as Y-m-d string', function (): void {
    $patient = Patient::factory()->create(['date_of_birth' => '2000-01-01']);

    $data = (new PatientResource($patient))->resolve();

    expect($data['attributes']['dateOfBirth'])->toBe('2000-01-01');
});

test('PatientResource timestamps are ISO 8601 strings', function (): void {
    $patient = Patient::factory()->create();

    $data = (new PatientResource($patient))->resolve();

    expect($data['attributes']['createdAt'])->toBe($patient->created_at->toIso8601String())
        ->and($data['attributes']['updatedAt'])->toBe($patient->updated_at->toIso8601String());
});

// === Relationships ===

test('PatientResource relationships key is present', function (): void {
    $patient = Patient::factory()->create();

    $data = (new PatientResource($patient))->resolve();

    expect($data)->toHaveKey('relationships');
});

test('PatientResource includes user relationship data when user is loaded', function (): void {
    $patient = Patient::factory()->create();
    $patient->load('user');

    $data = (new PatientResource($patient))->resolve();

    expect($data['relationships']['user']['data'])->toBe([
        'type' => 'user',
        'id' => (string) $patient->user->id,
    ]);
});

test('PatientResource omits user data when user relation is not loaded', function (): void {
    $patient = Patient::factory()->create();

    $data = (new PatientResource($patient))->resolve();

    expect($data['relationships']['user'])->not->toHaveKey('data');
});

test('PatientResource includes socioeconomic relationship data when loaded', function (): void {
    $patient = Patient::factory()->hasSocioeconomic()->create();
    $patient->load('socioeconomic');

    $data = (new PatientResource($patient))->resolve();

    expect($data['relationships']['socioeconomic']['data'])->toBe([
        'type' => 'patient_socioeconomic',
        'id' => (string) $patient->socioeconomic->id,
    ]);
});

test('PatientResource socioeconomic relationship data is null when patient has none', function (): void {
    $patient = Patient::factory()->create();
    $patient->load('socioeconomic');

    $data = (new PatientResource($patient))->resolve();

    expect($data['relationships']['socioeconomic']['data'])->toBeNull();
});

test('PatientResource omits socioeconomic data when relation is not loaded', function (): void {
    $patient = Patient::factory()->create();

    $data = (new PatientResource($patient))->resolve();

    expect($data['relationships']['socioeconomic'])->not->toHaveKey('data');
});

// === with() sideloaded included ===

test('PatientResource with() returns empty array when socioeconomic not loaded', function (): void {
    $patient = Patient::factory()->create();
    $resource = new PatientResource($patient);

    $with = $resource->with(request());

    expect($with)->toBe([]);
});

test('PatientResource with() returns empty array when patient has no socioeconomic', function (): void {
    $patient = Patient::factory()->create();
    $patient->load('socioeconomic');

    $with = (new PatientResource($patient))->with(request());

    expect($with)->toBe([]);
});
