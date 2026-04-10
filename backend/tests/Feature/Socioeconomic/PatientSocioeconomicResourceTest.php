<?php

use App\Http\Resources\Api\PatientSocioeconomicResource;
use App\Models\PatientSocioeconomic;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// === JSON:API shape ===

test('PatientSocioeconomicResource returns correct type and id', function (): void {
    $socio = PatientSocioeconomic::factory()->create();

    $data = (new PatientSocioeconomicResource($socio))->resolve();

    expect($data['type'])->toBe('patient_socioeconomic')
        ->and($data['id'])->toBe((string) $socio->id);
});

test('PatientSocioeconomicResource attributes contain all expected camelCase keys', function (): void {
    $socio = PatientSocioeconomic::factory()->create();

    $data = (new PatientSocioeconomicResource($socio))->resolve();

    expect($data['attributes'])->toHaveKeys([
        'maritalStatus',
        'numberOfDependents',
        'livingArrangement',
        'employmentStatus',
        'occupation',
        'incomeLevel',
        'hasHealthInsurance',
        'educationLevel',
        'smokingStatus',
        'alcoholConsumption',
        'physicalActivityLevel',
        'hasFamilySupport',
        'hasCaregiver',
        'transportationAccess',
        'foodSecurityStatus',
        'dietaryRestrictionsCultural',
        'additionalNotes',
        'createdAt',
        'updatedAt',
    ]);
});

// === Field mapping ===

test('PatientSocioeconomicResource maps snake_case to camelCase correctly', function (): void {
    $socio = PatientSocioeconomic::factory()->create([
        'marital_status' => 'married',
        'number_of_dependents' => 2,
        'living_arrangement' => 'with_family',
        'employment_status' => 'employed_full_time',
        'occupation' => 'Engineer',
        'income_level' => 'middle',
        'has_health_insurance' => true,
        'education_level' => 'bachelor',
        'smoking_status' => 'never',
        'alcohol_consumption' => 'occasional',
        'physical_activity_level' => 'moderate',
        'has_family_support' => true,
        'has_caregiver' => false,
        'transportation_access' => 'own_vehicle',
        'food_security_status' => 'secure',
        'dietary_restrictions_cultural' => 'halal',
        'additional_notes' => 'some note',
    ]);

    $data = (new PatientSocioeconomicResource($socio))->resolve();
    $attrs = $data['attributes'];

    expect($attrs['maritalStatus'])->toBe('married')
        ->and($attrs['numberOfDependents'])->toBe(2)
        ->and($attrs['livingArrangement'])->toBe('with_family')
        ->and($attrs['employmentStatus'])->toBe('employed_full_time')
        ->and($attrs['occupation'])->toBe('Engineer')
        ->and($attrs['incomeLevel'])->toBe('middle')
        ->and($attrs['hasHealthInsurance'])->toBeTrue()
        ->and($attrs['educationLevel'])->toBe('bachelor')
        ->and($attrs['smokingStatus'])->toBe('never')
        ->and($attrs['alcoholConsumption'])->toBe('occasional')
        ->and($attrs['physicalActivityLevel'])->toBe('moderate')
        ->and($attrs['hasFamilySupport'])->toBeTrue()
        ->and($attrs['hasCaregiver'])->toBeFalse()
        ->and($attrs['transportationAccess'])->toBe('own_vehicle')
        ->and($attrs['foodSecurityStatus'])->toBe('secure')
        ->and($attrs['dietaryRestrictionsCultural'])->toBe('halal')
        ->and($attrs['additionalNotes'])->toBe('some note');
});

// === Boolean casting ===

test('PatientSocioeconomicResource casts boolean fields to actual booleans', function (): void {
    $socio = PatientSocioeconomic::factory()->create([
        'has_health_insurance' => true,
        'has_family_support' => false,
        'has_caregiver' => true,
    ]);

    $attrs = (new PatientSocioeconomicResource($socio))->resolve()['attributes'];

    expect($attrs['hasHealthInsurance'])->toBeTrue()
        ->and($attrs['hasFamilySupport'])->toBeFalse()
        ->and($attrs['hasCaregiver'])->toBeTrue();
});

// === Nullable fields ===

test('PatientSocioeconomicResource outputs null for optional fields when not set', function (): void {
    $socio = PatientSocioeconomic::factory()->create([
        'marital_status' => null,
        'occupation' => null,
        'dietary_restrictions_cultural' => null,
        'additional_notes' => null,
    ]);

    $attrs = (new PatientSocioeconomicResource($socio))->resolve()['attributes'];

    expect($attrs['maritalStatus'])->toBeNull()
        ->and($attrs['occupation'])->toBeNull()
        ->and($attrs['dietaryRestrictionsCultural'])->toBeNull()
        ->and($attrs['additionalNotes'])->toBeNull();
});

// === Timestamps ===

test('PatientSocioeconomicResource timestamps are ISO 8601 strings', function (): void {
    $socio = PatientSocioeconomic::factory()->create();

    $attrs = (new PatientSocioeconomicResource($socio))->resolve()['attributes'];

    expect($attrs['createdAt'])->toBe($socio->created_at->toIso8601String())
        ->and($attrs['updatedAt'])->toBe($socio->updated_at->toIso8601String());
});

// === No unexpected keys ===

test('PatientSocioeconomicResource does not expose patient_id or raw column names', function (): void {
    $socio = PatientSocioeconomic::factory()->create();

    $data = (new PatientSocioeconomicResource($socio))->resolve();

    expect($data)->not->toHaveKey('patient_id');
    expect($data['attributes'])->not->toHaveKey('patient_id')
        ->and($data['attributes'])->not->toHaveKey('has_health_insurance')
        ->and($data['attributes'])->not->toHaveKey('marital_status');
});
