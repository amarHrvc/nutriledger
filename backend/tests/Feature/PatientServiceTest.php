<?php

use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use App\Models\User;
use App\Services\PatientService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// === createPatient() ===

test('createPatient persists patient record in database', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);

    $data = [
        'user_id' => $user->id,
        'first_name' => 'Ana',
        'last_name' => 'Kovac',
        'date_of_birth' => '1990-03-15',
        'gender' => 'F',
        'phone' => '061000000',
        'emergency_contact_name' => 'Kemal Kovac',
        'emergency_contact_phone' => '062000000',
    ];

    $patient = app(PatientService::class)->createPatient($data);

    expect($patient)->toBeInstanceOf(Patient::class)
        ->and($patient->first_name)->toBe('Ana')
        ->and($patient->last_name)->toBe('Kovac')
        ->and($patient->user_id)->toBe($user->id);

    $this->assertDatabaseHas('patients', ['first_name' => 'Ana', 'user_id' => $user->id]);
});

test('createPatient returns patient with user relation loaded', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);

    $patient = app(PatientService::class)->createPatient([
        'user_id' => $user->id,
        'first_name' => 'Marko',
        'last_name' => 'Petrovic',
        'date_of_birth' => '1980-01-01',
        'gender' => 'M',
        'phone' => '061111111',
        'emergency_contact_name' => 'Ana Petrovic',
        'emergency_contact_phone' => '062222222',
    ]);

    expect($patient->relationLoaded('user'))->toBeTrue()
        ->and($patient->user->id)->toBe($user->id);
});

test('createPatient without socioeconomic data creates no socioeconomic record', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);

    $patient = app(PatientService::class)->createPatient([
        'user_id' => $user->id,
        'first_name' => 'Lena',
        'last_name' => 'Basic',
        'date_of_birth' => '1995-06-10',
        'gender' => 'F',
        'phone' => '063333333',
        'emergency_contact_name' => 'Maja Basic',
        'emergency_contact_phone' => '064444444',
    ]);

    expect($patient->socioeconomic)->toBeNull();
    $this->assertDatabaseCount('patient_socioeconomic', 0);
});

test('createPatient with socioeconomic data creates linked record', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);

    $patient = app(PatientService::class)->createPatient(
        [
            'user_id' => $user->id,
            'first_name' => 'Sara',
            'last_name' => 'Mulic',
            'date_of_birth' => '1992-08-15',
            'gender' => 'F',
            'phone' => '065555555',
            'emergency_contact_name' => 'Amra Mulic',
            'emergency_contact_phone' => '066666666',
        ],
        ['marital_status' => 'single', 'income_level' => 'middle', 'has_health_insurance' => true]
    );

    expect($patient->relationLoaded('socioeconomic'))->toBeTrue()
        ->and($patient->socioeconomic)->toBeInstanceOf(PatientSocioeconomic::class)
        ->and($patient->socioeconomic->marital_status)->toBe('single')
        ->and($patient->socioeconomic->income_level)->toBe('middle');

    $this->assertDatabaseHas('patient_socioeconomic', [
        'patient_id' => $patient->id,
        'marital_status' => 'single',
    ]);
});

test('createPatient rolls back transaction when patient insert fails due to missing required field', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);

    // Omitting NOT NULL date_of_birth causes the patient insert to fail.
    try {
        app(PatientService::class)->createPatient([
            'user_id' => $user->id,
            'first_name' => 'Ivan',
            'last_name' => 'Test',
            // date_of_birth omitted — NOT NULL constraint violation
            'gender' => 'M',
            'phone' => '067777777',
            'emergency_contact_name' => 'Irena Test',
            'emergency_contact_phone' => '068888888',
        ], ['marital_status' => 'single']);
    } catch (Throwable) {
        // expected
    }

    $this->assertDatabaseCount('patients', 0);
    $this->assertDatabaseCount('patient_socioeconomic', 0);
});

// === updatePatient() ===

test('updatePatient persists changed patient fields', function (): void {
    $patient = Patient::factory()->create(['first_name' => 'Stara', 'last_name' => 'Vrijednost']);

    $updated = app(PatientService::class)->updatePatient($patient, [
        'first_name' => 'Nova',
        'last_name' => 'Vrijednost',
    ]);

    expect($updated->first_name)->toBe('Nova')
        ->and($updated->last_name)->toBe('Vrijednost');

    $this->assertDatabaseHas('patients', ['id' => $patient->id, 'first_name' => 'Nova']);
});

test('updatePatient returns patient with user and socioeconomic relations loaded', function (): void {
    $patient = Patient::factory()->create();

    $updated = app(PatientService::class)->updatePatient($patient, ['first_name' => 'Refreshed']);

    expect($updated->relationLoaded('user'))->toBeTrue()
        ->and($updated->relationLoaded('socioeconomic'))->toBeTrue();
});

test('updatePatient creates socioeconomic record when none exists', function (): void {
    $patient = Patient::factory()->create();

    $updated = app(PatientService::class)->updatePatient(
        $patient,
        ['first_name' => $patient->first_name],
        ['marital_status' => 'married', 'has_health_insurance' => false]
    );

    expect($updated->socioeconomic)->toBeInstanceOf(PatientSocioeconomic::class)
        ->and($updated->socioeconomic->marital_status)->toBe('married');

    $this->assertDatabaseHas('patient_socioeconomic', [
        'patient_id' => $patient->id,
        'marital_status' => 'married',
    ]);
});

test('updatePatient updates existing socioeconomic record', function (): void {
    $patient = Patient::factory()->hasSocioeconomic()->create();
    $socioId = $patient->socioeconomic->id;

    app(PatientService::class)->updatePatient(
        $patient,
        ['first_name' => $patient->first_name],
        ['marital_status' => 'widowed']
    );

    $this->assertDatabaseHas('patient_socioeconomic', [
        'id' => $socioId,
        'patient_id' => $patient->id,
        'marital_status' => 'widowed',
    ]);

    expect(PatientSocioeconomic::count())->toBe(1);
});

test('updatePatient without socioeconomic data does not touch socioeconomic table', function (): void {
    $patient = Patient::factory()->hasSocioeconomic()->create();
    $originalStatus = $patient->socioeconomic->marital_status;

    app(PatientService::class)->updatePatient($patient, ['first_name' => 'Changed']);

    $this->assertDatabaseHas('patient_socioeconomic', [
        'patient_id' => $patient->id,
        'marital_status' => $originalStatus,
    ]);
});
