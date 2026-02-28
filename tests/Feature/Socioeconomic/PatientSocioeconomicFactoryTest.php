<?php

use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// --- Basic factory ---
test('factory creates a valid socioeconomic record', function (): void {
    $socio = PatientSocioeconomic::factory()->create();
    expect($socio)->toBeInstanceOf(PatientSocioeconomic::class)
        ->and($socio->id)->not->toBeNull()
        ->and($socio->patient_id)->not->toBeNull();
});

test('factory automatically creates a patient if not provided', function (): void {
    $socio = PatientSocioeconomic::factory()->create();
    expect(Patient::find($socio->patient_id))->not->toBeNull();
});

test('factory creates record with associated patient', function (): void {
    $patient = Patient::factory()->create();
    $socio = PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);
    expect($socio->patient->id)->toBe($patient->id);
});

// --- Enum validations ---
dataset('enum_fields', [
    'marital_status'        => ['marital_status',        ['single', 'married', 'divorced', 'widowed', 'partnered']],
    'employment_status'     => ['employment_status',     ['employed_full_time', 'employed_part_time', 'self_employed', 'unemployed', 'retired', 'student', 'disabled']],
    'income_level'          => ['income_level',          ['low', 'middle', 'high', 'prefer_not_to_say']],
    'education_level'       => ['education_level',       ['primary', 'secondary', 'vocational', 'bachelor', 'master', 'doctorate', 'other']],
    'smoking_status'        => ['smoking_status',        ['never', 'former', 'current']],
    'alcohol_consumption'   => ['alcohol_consumption',   ['none', 'occasional', 'moderate', 'heavy']],
    'physical_activity_level' => ['physical_activity_level', ['sedentary', 'light', 'moderate', 'active', 'very_active']],
    'transportation_access' => ['transportation_access', ['own_vehicle', 'public_transport', 'family', 'limited', 'none']],
    'food_security_status'  => ['food_security_status',  ['secure', 'at_risk', 'insecure']],
    'living_arrangement'    => ['living_arrangement',    ['alone', 'with_family', 'with_partner', 'shared', 'institution']],
]);

test('factory produces valid enum value or null', function (string $field, array $validValues): void {
    $socio = PatientSocioeconomic::factory()->create();
    expect($socio->$field)->toBeIn([...$validValues, null]);
})->with('enum_fields');

// --- Boolean fields ---
test('factory produces boolean for has_health_insurance', function (): void {
    $socio = PatientSocioeconomic::factory()->create();
    expect($socio->has_health_insurance)->toBeIn([true, false]);
});

test('factory produces boolean for has_family_support', function (): void {
    $socio = PatientSocioeconomic::factory()->create();
    expect($socio->has_family_support)->toBeIn([true, false]);
});

test('factory produces boolean for has_caregiver', function (): void {
    $socio = PatientSocioeconomic::factory()->create();
    expect($socio->has_caregiver)->toBeIn([true, false]);
});

// --- Override ---
test('factory respects provided patient_id', function (): void {
    $patient = Patient::factory()->create();
    $socio = PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);
    expect($socio->patient_id)->toBe($patient->id);
});

test('factory respects provided attribute overrides', function (): void {
    $socio = PatientSocioeconomic::factory()->create(['marital_status' => 'married', 'income_level' => 'high']);
    expect($socio->marital_status)->toBe('married')
        ->and($socio->income_level)->toBe('high');
});

// --- Bulk ---
test('factory can create multiple records', function (): void {
    PatientSocioeconomic::factory()->count(3)->create();
    expect(PatientSocioeconomic::count())->toBe(3);
});
