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
test('factory produces valid marital_status or null', function (): void {
    $valid = ['single', 'married', 'divorced', 'widowed', 'partnered'];
    $socio = PatientSocioeconomic::factory()->create();
    expect($socio->marital_status)->toBeIn([...$valid, null]);
});
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

test('factory produces valid employment_status or null', function (): void {
    $valid = ['employed_full_time', 'employed_part_time', 'self_employed', 'unemployed', 'retired', 'student', 'disabled'];
    $socio = PatientSocioeconomic::factory()->create();
    expect($socio->employment_status)->toBeIn([...$valid, null]);
});

test('factory produces valid income_level or null', function (): void {
    $valid = ['low', 'middle', 'high', 'prefer_not_to_say'];
    $socio = PatientSocioeconomic::factory()->create();
    expect($socio->income_level)->toBeIn([...$valid, null]);
});

test('factory produces valid education_level or null', function (): void {
    $valid = ['primary', 'secondary', 'vocational', 'bachelor', 'master', 'doctorate', 'other'];
    $socio = PatientSocioeconomic::factory()->create();
    expect($socio->education_level)->toBeIn([...$valid, null]);
});

test('factory produces valid smoking_status or null', function (): void {
    $valid = ['never', 'former', 'current'];
    $socio = PatientSocioeconomic::factory()->create();
    expect($socio->smoking_status)->toBeIn([...$valid, null]);
});

test('factory produces valid alcohol_consumption or null', function (): void {
    $valid = ['none', 'occasional', 'moderate', 'heavy'];
    $socio = PatientSocioeconomic::factory()->create();
    expect($socio->alcohol_consumption)->toBeIn([...$valid, null]);
});

test('factory produces valid physical_activity_level or null', function (): void {
    $valid = ['sedentary', 'light', 'moderate', 'active', 'very_active'];
    $socio = PatientSocioeconomic::factory()->create();
    expect($socio->physical_activity_level)->toBeIn([...$valid, null]);
});

test('factory produces valid transportation_access or null', function (): void {
    $valid = ['own_vehicle', 'public_transport', 'family', 'limited', 'none'];
    $socio = PatientSocioeconomic::factory()->create();
    expect($socio->transportation_access)->toBeIn([...$valid, null]);
});

test('factory produces valid food_security_status or null', function (): void {
    $valid = ['secure', 'at_risk', 'insecure'];
    $socio = PatientSocioeconomic::factory()->create();
    expect($socio->food_security_status)->toBeIn([...$valid, null]);
});

test('factory produces valid living_arrangement or null', function (): void {
    $valid = ['alone', 'with_family', 'with_partner', 'shared', 'institution'];
    $socio = PatientSocioeconomic::factory()->create();
    expect($socio->living_arrangement)->toBeIn([...$valid, null]);
});

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
