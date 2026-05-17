<?php

use App\Http\Resources\Api\VitalSignResource;
use App\Models\VitalSign;
use Illuminate\Http\Request;

// ─── flags: all within thresholds ────────────────────────────────────────────

test('flags array is empty when all values are within thresholds', function () {
    $vital = VitalSign::factory()->create([
        'systolic_bp' => 120,
        'diastolic_bp' => 80,
        'heart_rate' => 70,
        'temperature' => 36.5,
        'weight' => 70.00,
        'height' => 175.00,
        'bmi' => 22.86,
    ]);

    $attrs = (new VitalSignResource($vital))->toArray(new Request)['attributes'];

    expect($attrs['flags'])->toBeEmpty();
});

// ─── flags: systolic BP ───────────────────────────────────────────────────────

test('high systolic BP produces flag with correct field, value, and threshold', function () {
    $vital = VitalSign::factory()->create(['systolic_bp' => 155, 'bmi' => 22.00]);

    $flags = (new VitalSignResource($vital))->toArray(new Request)['attributes']['flags'];
    $flag = collect($flags)->firstWhere('field', 'systolicBp');

    expect($flag)->not->toBeNull()
        ->and($flag['value'])->toBe(155)
        ->and($flag['threshold'])->toBe('90–140 mmHg');
});

test('low systolic BP produces flag with correct field, value, and threshold', function () {
    $vital = VitalSign::factory()->create(['systolic_bp' => 80, 'bmi' => 22.00]);

    $flags = (new VitalSignResource($vital))->toArray(new Request)['attributes']['flags'];
    $flag = collect($flags)->firstWhere('field', 'systolicBp');

    expect($flag)->not->toBeNull()
        ->and($flag['value'])->toBe(80)
        ->and($flag['threshold'])->toBe('90–140 mmHg');
});

// ─── flags: diastolic BP ─────────────────────────────────────────────────────

test('high diastolic BP produces flag with correct field, value, and threshold', function () {
    $vital = VitalSign::factory()->create(['diastolic_bp' => 95, 'bmi' => 22.00]);

    $flags = (new VitalSignResource($vital))->toArray(new Request)['attributes']['flags'];
    $flag = collect($flags)->firstWhere('field', 'diastolicBp');

    expect($flag)->not->toBeNull()
        ->and($flag['value'])->toBe(95)
        ->and($flag['threshold'])->toBe('60–90 mmHg');
});

test('low diastolic BP produces flag with correct field, value, and threshold', function () {
    $vital = VitalSign::factory()->create(['diastolic_bp' => 55, 'bmi' => 22.00]);

    $flags = (new VitalSignResource($vital))->toArray(new Request)['attributes']['flags'];
    $flag = collect($flags)->firstWhere('field', 'diastolicBp');

    expect($flag)->not->toBeNull()
        ->and($flag['value'])->toBe(55)
        ->and($flag['threshold'])->toBe('60–90 mmHg');
});

// ─── flags: heart rate ────────────────────────────────────────────────────────

test('high heart rate produces flag with correct field, value, and threshold', function () {
    $vital = VitalSign::factory()->create(['heart_rate' => 110, 'bmi' => 22.00]);

    $flags = (new VitalSignResource($vital))->toArray(new Request)['attributes']['flags'];
    $flag = collect($flags)->firstWhere('field', 'heartRate');

    expect($flag)->not->toBeNull()
        ->and($flag['value'])->toBe(110)
        ->and($flag['threshold'])->toBe('50–100 bpm');
});

test('low heart rate produces flag with correct field, value, and threshold', function () {
    $vital = VitalSign::factory()->create(['heart_rate' => 45, 'bmi' => 22.00]);

    $flags = (new VitalSignResource($vital))->toArray(new Request)['attributes']['flags'];
    $flag = collect($flags)->firstWhere('field', 'heartRate');

    expect($flag)->not->toBeNull()
        ->and($flag['value'])->toBe(45)
        ->and($flag['threshold'])->toBe('50–100 bpm');
});

// ─── flags: temperature ───────────────────────────────────────────────────────

test('high temperature produces flag with correct field, value, and threshold', function () {
    $vital = VitalSign::factory()->create(['temperature' => 38.0, 'bmi' => 22.00]);

    $flags = (new VitalSignResource($vital))->toArray(new Request)['attributes']['flags'];
    $flag = collect($flags)->firstWhere('field', 'temperature');

    expect($flag)->not->toBeNull()
        ->and((float) $flag['value'])->toBe(38.0)
        ->and($flag['threshold'])->toBe('36.0–37.5 °C');
});

test('low temperature produces flag with correct field, value, and threshold', function () {
    $vital = VitalSign::factory()->create(['temperature' => 35.5, 'bmi' => 22.00]);

    $flags = (new VitalSignResource($vital))->toArray(new Request)['attributes']['flags'];
    $flag = collect($flags)->firstWhere('field', 'temperature');

    expect($flag)->not->toBeNull()
        ->and((float) $flag['value'])->toBe(35.5)
        ->and($flag['threshold'])->toBe('36.0–37.5 °C');
});

// ─── flags: BMI ───────────────────────────────────────────────────────────────

test('underweight BMI produces bmi flag with underweight category', function () {
    $vital = VitalSign::factory()->create(['bmi' => 17.00, 'weight' => null, 'height' => null]);

    $flags = (new VitalSignResource($vital))->toArray(new Request)['attributes']['flags'];
    $flag = collect($flags)->firstWhere('field', 'bmi');

    expect($flag)->not->toBeNull()
        ->and($flag['category'])->toBe('underweight');
});

test('overweight BMI produces bmi flag with overweight category', function () {
    $vital = VitalSign::factory()->create(['bmi' => 27.00, 'weight' => null, 'height' => null]);

    $flags = (new VitalSignResource($vital))->toArray(new Request)['attributes']['flags'];
    $flag = collect($flags)->firstWhere('field', 'bmi');

    expect($flag)->not->toBeNull()
        ->and($flag['category'])->toBe('overweight');
});

test('obese BMI produces bmi flag with obese category', function () {
    $vital = VitalSign::factory()->create(['bmi' => 32.00, 'weight' => null, 'height' => null]);

    $flags = (new VitalSignResource($vital))->toArray(new Request)['attributes']['flags'];
    $flag = collect($flags)->firstWhere('field', 'bmi');

    expect($flag)->not->toBeNull()
        ->and($flag['category'])->toBe('obese');
});

// ─── bmiCategory boundaries ───────────────────────────────────────────────────

test('bmiCategory returns correct string at BMI boundaries', function (float $bmi, string $expected) {
    $vital = VitalSign::factory()->create(['bmi' => $bmi]);

    $attrs = (new VitalSignResource($vital))->toArray(new Request)['attributes'];

    expect($attrs['bmiCategory'])->toBe($expected);
})->with([
    'below 18.5 → underweight' => [18.4, 'underweight'],
    'at 18.5 → normal' => [18.5, 'normal'],
    'mid normal' => [22.0, 'normal'],
    'at 25.0 → overweight' => [25.0, 'overweight'],
    'mid overweight' => [27.5, 'overweight'],
    'at 30.0 → obese' => [30.0, 'obese'],
]);

test('bmiCategory is null when bmi is null', function () {
    $vital = VitalSign::factory()->create(['bmi' => null, 'weight' => null, 'height' => null]);

    $attrs = (new VitalSignResource($vital))->toArray(new Request)['attributes'];

    expect($attrs['bmiCategory'])->toBeNull();
});

// ─── previousVisit block ──────────────────────────────────────────────────────

test('previousVisit block is absent when previousVitals is not set', function () {
    $vital = VitalSign::factory()->create();

    $attrs = (new VitalSignResource($vital))->toArray(new Request)['attributes'];

    expect($attrs)->not->toHaveKey('previousVisit');
});

test('previousVisit block is present when previousVitals is set', function () {
    $prevVital = VitalSign::factory()->create();
    $currVital = VitalSign::factory()->create();

    $prevVital->load('visit');
    $currVital->previousVitals = $prevVital;

    $attrs = (new VitalSignResource($currVital))->toArray(new Request)['attributes'];

    expect($attrs)->toHaveKey('previousVisit')
        ->and($attrs['previousVisit'])->not->toBeNull();
});

// ─── weightDelta and bmiDelta ─────────────────────────────────────────────────

test('weightDelta computes correctly as current minus previous weight', function () {
    $prevVital = VitalSign::factory()->create(['weight' => 72.00, 'bmi' => 23.51]);
    $currVital = VitalSign::factory()->create(['weight' => 75.00, 'bmi' => 24.49]);

    $prevVital->load('visit');
    $currVital->previousVitals = $prevVital;

    $attrs = (new VitalSignResource($currVital))->toArray(new Request)['attributes'];

    expect($attrs['previousVisit']['weightDelta'])->toBe(3.0);
});

test('bmiDelta computes correctly as current minus previous BMI', function () {
    $prevVital = VitalSign::factory()->create(['bmi' => 23.00]);
    $currVital = VitalSign::factory()->create(['bmi' => 25.00]);

    $prevVital->load('visit');
    $currVital->previousVitals = $prevVital;

    $attrs = (new VitalSignResource($currVital))->toArray(new Request)['attributes'];

    expect($attrs['previousVisit']['bmiDelta'])->toBe(2.0);
});

test('weightDelta is null when previous weight is null', function () {
    $prevVital = VitalSign::factory()->create(['weight' => null, 'height' => null, 'bmi' => null]);
    $currVital = VitalSign::factory()->create(['weight' => 75.00]);

    $prevVital->load('visit');
    $currVital->previousVitals = $prevVital;

    $attrs = (new VitalSignResource($currVital))->toArray(new Request)['attributes'];

    expect($attrs['previousVisit']['weightDelta'])->toBeNull();
});
