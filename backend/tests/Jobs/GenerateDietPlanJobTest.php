<?php

use App\Ai\Agents\DietPlanAgent;
use App\Jobs\GenerateDietPlanJob;
use App\Models\Patient;
use App\Models\PatientDietPlan;
use App\Models\PatientSocioeconomic;
use App\Models\User;

function validAgentResponse(): array
{
    return [
        'rationale'         => 'Balanced plan for a moderately active adult.',
        'daily_calories'    => 1800,
        'nutritional_goals' => ['protein_g' => 90, 'carbs_g' => 220, 'fat_g' => 60],
        'days'              => array_fill(0, 7, [
            'day'       => 'Monday',
            'breakfast' => 'Oatmeal with banana',
            'lunch'     => 'Lentil soup with bread',
            'dinner'    => 'Grilled chicken with roasted vegetables',
            'snack'     => 'Apple',
        ]),
        'warnings' => ['No allergens detected.'],
    ];
}

function makePlan(): PatientDietPlan
{
    $doctor  = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()
        ->has(PatientSocioeconomic::factory(), 'socioeconomic')
        ->create();

    return PatientDietPlan::factory()->pending()->create([
        'patient_id'   => $patient->id,
        'generated_by' => $doctor->id,
    ]);
}

test('job updates plan to completed when agent returns valid response', function () {
    DietPlanAgent::fake([validAgentResponse()]); // outer array = responses list, inner array = structured data

    $plan = makePlan();

    GenerateDietPlanJob::dispatchSync($plan);

    $plan->refresh();

    expect($plan->status)->toBe('completed');
    expect($plan->rationale)->not->toBeNull();
    expect($plan->daily_calories)->toBe(1800);
    expect($plan->nutritional_goals)->toBe(['protein_g' => 90, 'carbs_g' => 220, 'fat_g' => 60]);
    expect($plan->days)->toHaveCount(7);
    expect($plan->warnings)->toHaveCount(1);
    expect($plan->failure_reason)->toBeNull();
});

test('job marks plan as failed when agent returns wrong day count', function () {
    DietPlanAgent::fake([[
        'rationale'         => 'Bad plan.',
        'daily_calories'    => 1800,
        'nutritional_goals' => ['protein_g' => 90, 'carbs_g' => 220, 'fat_g' => 60],
        'days'              => array_fill(0, 3, [
            'day'       => 'Monday',
            'breakfast' => 'Toast',
            'lunch'     => 'Soup',
            'dinner'    => 'Rice',
            'snack'     => 'Apple',
        ]),
        'warnings' => [],
    ]]);

    $plan = makePlan();

    GenerateDietPlanJob::dispatchSync($plan);

    $plan->refresh();

    expect($plan->status)->toBe('failed');
    expect($plan->failure_reason)->not->toBeNull();
    expect($plan->days)->toBeNull();
});

test('job marks plan as failed when agent throws an exception', function () {
    DietPlanAgent::fake(fn () => throw new RuntimeException('Connection timeout'));

    $plan = makePlan();

    GenerateDietPlanJob::dispatchSync($plan);

    $plan->refresh();

    expect($plan->status)->toBe('failed');
    expect($plan->failure_reason)->toBe('Connection timeout');
});

test('job marks plan as failed when calories are out of valid range', function () {
    DietPlanAgent::fake([[
        'rationale'         => 'Extreme plan.',
        'daily_calories'    => 200,
        'nutritional_goals' => ['protein_g' => 10, 'carbs_g' => 20, 'fat_g' => 5],
        'days'              => array_fill(0, 7, [
            'day'       => 'Monday',
            'breakfast' => 'Nothing',
            'lunch'     => 'Nothing',
            'dinner'    => 'Nothing',
            'snack'     => 'Nothing',
        ]),
        'warnings' => [],
    ]]);

    $plan = makePlan();

    GenerateDietPlanJob::dispatchSync($plan);

    $plan->refresh();

    expect($plan->status)->toBe('failed');
    expect($plan->failure_reason)->not->toBeNull();
});

test('failed plan preserves history when a new plan is created', function () {
    DietPlanAgent::fake(fn () => throw new RuntimeException('API error'));

    $plan = makePlan();
    GenerateDietPlanJob::dispatchSync($plan);
    $plan->refresh();

    expect($plan->status)->toBe('failed');

    $doctor  = User::findOrFail($plan->generated_by);
    $patient = $plan->patient;

    $newPlan = PatientDietPlan::factory()->pending()->create([
        'patient_id'   => $patient->id,
        'generated_by' => $doctor->id,
    ]);

    expect(PatientDietPlan::where('patient_id', $patient->id)->count())->toBe(2);
    expect($plan->fresh()->status)->toBe('failed');
    expect($newPlan->fresh()->status)->toBe('pending');
});
