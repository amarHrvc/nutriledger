<?php

use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// --- Basic creation ---

test('socioeconomic record can be created with minimal data', function (): void {
    $patient = Patient::factory()->create();

    $socio = $patient->socioeconomic()->create([]);

    expect($socio->id)->not->toBeNull()
        ->and($socio->patient_id)->toBe($patient->id);
});

test('socioeconomic record can be created with all fields', function (): void {
    $patient = Patient::factory()->create();

    $socio = $patient->socioeconomic()->create([
        'marital_status' => 'married',
        'number_of_dependents' => 2,
        'living_arrangement' => 'with_family',
        'employment_status' => 'employed_full_time',
        'occupation' => 'Engineer',
        'income_level' => 'middle',
        'has_health_insurance' => true,
        'education_level' => 'bachelor',
        'smoking_status' => 'never',
        'alcohol_consumption' => 'none',
        'physical_activity_level' => 'moderate',
        'has_family_support' => true,
        'has_caregiver' => false,
        'transportation_access' => 'own_vehicle',
        'food_security_status' => 'secure',
        'dietary_restrictions_cultural' => 'halal',
        'additional_notes' => 'Some notes here',
    ]);

    expect($socio->marital_status)->toBe('married')
        ->and($socio->number_of_dependents)->toBe(2)
        ->and($socio->occupation)->toBe('Engineer')
        ->and($socio->has_health_insurance)->toBeTrue()
        ->and($socio->has_family_support)->toBeTrue()
        ->and($socio->has_caregiver)->toBeFalse()
        ->and($socio->additional_notes)->toBe('Some notes here');
});

test('socioeconomic patient_id is required', function (): void {
    expect(fn () => PatientSocioeconomic::create([]))->toThrow(Exception::class);
});

// --- Fillable ---

test('all expected fields are mass assignable', function (): void {
    $patient = Patient::factory()->create();

    $socio = $patient->socioeconomic()->create([
        'marital_status' => 'single',
        'number_of_dependents' => 0,
        'living_arrangement' => 'alone',
        'employment_status' => 'unemployed',
        'occupation' => 'None',
        'income_level' => 'low',
        'has_health_insurance' => false,
        'education_level' => 'primary',
        'smoking_status' => 'former',
        'alcohol_consumption' => 'occasional',
        'physical_activity_level' => 'sedentary',
        'has_family_support' => false,
        'has_caregiver' => true,
        'transportation_access' => 'public_transport',
        'food_security_status' => 'insecure',
        'dietary_restrictions_cultural' => 'vegan',
        'additional_notes' => 'test note',
    ]);

    $fresh = $socio->fresh();

    expect($fresh->marital_status)->toBe('single')
        ->and($fresh->living_arrangement)->toBe('alone')
        ->and($fresh->employment_status)->toBe('unemployed')
        ->and($fresh->income_level)->toBe('low')
        ->and($fresh->education_level)->toBe('primary')
        ->and($fresh->transportation_access)->toBe('public_transport')
        ->and($fresh->food_security_status)->toBe('insecure')
        ->and($fresh->dietary_restrictions_cultural)->toBe('vegan')
        ->and($fresh->additional_notes)->toBe('test note');
});

// --- Casts ---

test('has_health_insurance is cast to boolean', function (): void {
    $patient = Patient::factory()->create();
    $socio = $patient->socioeconomic()->create(['has_health_insurance' => 1]);

    expect($socio->has_health_insurance)->toBeBool()->toBeTrue();
});

test('has_family_support is cast to boolean', function (): void {
    $patient = Patient::factory()->create();
    $socio = $patient->socioeconomic()->create(['has_family_support' => 0]);

    expect($socio->has_family_support)->toBeBool()->toBeFalse();
});

test('has_caregiver is cast to boolean', function (): void {
    $patient = Patient::factory()->create();
    $socio = $patient->socioeconomic()->create(['has_caregiver' => 1]);

    expect($socio->has_caregiver)->toBeBool()->toBeTrue();
});

test('number_of_dependents is cast to integer', function (): void {
    $patient = Patient::factory()->create();
    $socio = $patient->socioeconomic()->create(['number_of_dependents' => '3']);

    expect($socio->number_of_dependents)->toBeInt()->toBe(3);
});

// --- Relationships ---

test('socioeconomic data belongs to patient', function (): void {
    $patient = Patient::factory()->create();
    $socio = $patient->socioeconomic()->create([]);

    expect($socio->patient)->toBeInstanceOf(Patient::class)
        ->and($socio->patient->id)->toBe($patient->id);
});

test('patient has one socioeconomic record', function (): void {
    $patient = Patient::factory()->create();
    $patient->socioeconomic()->create(['marital_status' => 'single']);

    $patient->refresh();

    expect($patient->socioeconomic)->toBeInstanceOf(PatientSocioeconomic::class)
        ->and($patient->socioeconomic->marital_status)->toBe('single');
});

test('patient socioeconomic returns null when no record exists', function (): void {
    $patient = Patient::factory()->create();

    expect($patient->socioeconomic)->toBeNull();
});

// --- Unique constraint ---

test('patient can only have one socioeconomic record', function (): void {
    $patient = Patient::factory()->create();
    $patient->socioeconomic()->create([]);

    expect(fn () => $patient->socioeconomic()->create([]))->toThrow(Exception::class);
});

// --- Cascade delete ---

test('socioeconomic record is deleted when patient is force deleted', function (): void {
    $patient = Patient::factory()->create();
    $socio = $patient->socioeconomic()->create([]);

    $patient->forceDelete();

    expect(PatientSocioeconomic::find($socio->id))->toBeNull();
});

// --- Nullable fields ---

test('all nullable fields accept null values', function (): void {
    $patient = Patient::factory()->create();

    $socio = $patient->socioeconomic()->create([
        'marital_status' => null,
        'number_of_dependents' => null,
        'living_arrangement' => null,
        'employment_status' => null,
        'occupation' => null,
        'income_level' => null,
        'education_level' => null,
        'smoking_status' => null,
        'alcohol_consumption' => null,
        'physical_activity_level' => null,
        'transportation_access' => null,
        'food_security_status' => null,
        'dietary_restrictions_cultural' => null,
        'additional_notes' => null,
    ]);

    expect($socio->id)->not->toBeNull()
        ->and($socio->marital_status)->toBeNull()
        ->and($socio->employment_status)->toBeNull()
        ->and($socio->additional_notes)->toBeNull();
});
