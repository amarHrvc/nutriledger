<?php

use App\Ai\Agents\DietPlanAgent;
use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use Illuminate\JsonSchema\JsonSchemaTypeFactory;

// --- instructions() ---

test('instructions contains patient age computed from date of birth', function () {
    $patient = Patient::factory()->create([
        'date_of_birth' => now()->subYears(35)->toDateString(),
    ]);

    $instructions = (new DietPlanAgent($patient))->instructions();

    expect((string) $instructions)->toContain('35 years');
});

test('instructions contains gender and blood type', function () {
    $patient = Patient::factory()->create([
        'gender'     => 'F',
        'blood_type' => 'O+',
    ]);

    $instructions = (new DietPlanAgent($patient))->instructions();

    expect((string) $instructions)
        ->toContain('F')
        ->toContain('O+');
});

test('instructions contains allergies when set', function () {
    $patient = Patient::factory()->create([
        'allergies' => 'Shellfish, tree nuts',
    ]);

    $instructions = (new DietPlanAgent($patient))->instructions();

    expect((string) $instructions)->toContain('Shellfish, tree nuts');
});

test('instructions shows None when allergies is null', function () {
    $patient = Patient::factory()->create(['allergies' => null]);

    $instructions = (new DietPlanAgent($patient))->instructions();

    expect((string) $instructions)->toContain('Allergies: None');
});

test('instructions shows None when medical notes is null', function () {
    $patient = Patient::factory()->create(['medical_notes' => null]);

    $instructions = (new DietPlanAgent($patient))->instructions();

    expect((string) $instructions)->toContain('Medical Notes: None');
});

test('instructions shows None for dietary restrictions as column does not exist on patients', function () {
    $patient = Patient::factory()->create();

    $instructions = (new DietPlanAgent($patient))->instructions();

    expect((string) $instructions)->toContain('Dietary Restrictions: None');
});

test('instructions shows Unknown for socioeconomic fields when no socioeconomic record exists', function () {
    $patient = Patient::factory()->create();

    $instructions = (new DietPlanAgent($patient))->instructions();

    expect((string) $instructions)
        ->toContain('Food Security Status: Unknown')
        ->toContain('Income Level: Unknown')
        ->toContain('Physical Activity Level: Unknown')
        ->toContain('Smoking: Unknown')
        ->toContain('Alcohol Consumption: Unknown');
});

test('instructions contains socioeconomic fields when record exists', function () {
    $patient = Patient::factory()
        ->has(PatientSocioeconomic::factory()->state([
            'food_security_status'    => 'insecure',
            'income_level'            => 'low',
            'physical_activity_level' => 'sedentary',
            'smoking_status'          => 'current',
            'alcohol_consumption'     => 'none',
        ]), 'socioeconomic')
        ->create();

    $instructions = (new DietPlanAgent($patient))->instructions();

    expect((string) $instructions)
        ->toContain('insecure')
        ->toContain('low')
        ->toContain('sedentary')
        ->toContain('current')
        ->toContain('Alcohol Consumption: none');
});

test('instructions contains allergen derived products rule', function () {
    $patient = Patient::factory()->create();

    $instructions = (new DietPlanAgent($patient))->instructions();

    expect((string) $instructions)->toContain('derived products');
});

test('instructions contains non-negotiable rules section', function () {
    $patient = Patient::factory()->create();

    $instructions = (new DietPlanAgent($patient))->instructions();

    expect((string) $instructions)
        ->toContain('NON-NEGOTIABLE')
        ->toContain('exactly 7 days')
        ->toContain('Monday through Sunday');
});

// --- schema() ---

test('schema returns all required top-level keys', function () {
    $patient = Patient::factory()->create();
    $schema  = (new DietPlanAgent($patient))->schema(new JsonSchemaTypeFactory);

    expect($schema)->toHaveKeys(['rationale', 'daily_calories', 'nutritional_goals', 'days', 'warnings']);
});

test('schema nutritional_goals has protein carbs and fat fields', function () {
    $patient    = Patient::factory()->create();
    $schema     = (new DietPlanAgent($patient))->schema(new JsonSchemaTypeFactory);
    $goalsArray = $schema['nutritional_goals']->toArray();

    expect($goalsArray['properties'])->toHaveKeys(['protein_g', 'carbs_g', 'fat_g']);
});
