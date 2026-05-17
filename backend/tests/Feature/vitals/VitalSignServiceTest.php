<?php

use App\Models\VitalSign;
use App\Models\Visit;
use App\Services\VitalSignService;

// ─── computeBmi (tested via record) ──────────────────────────────────────────

test('computeBmi(80, 175) returns 26.12', function () {
    $visit = Visit::factory()->create();
    $service = new VitalSignService();

    $vital = $service->record($visit, ['weight' => 80, 'height' => 175]);

    expect((float) $vital->bmi)->toBe(26.12);
});

test('computeBmi with null weight returns null BMI', function () {
    $visit = Visit::factory()->create();
    $service = new VitalSignService();

    $vital = $service->record($visit, ['weight' => null, 'height' => 175]);

    expect($vital->bmi)->toBeNull();
});

test('computeBmi with null height returns null BMI', function () {
    $visit = Visit::factory()->create();
    $service = new VitalSignService();

    $vital = $service->record($visit, ['weight' => 80, 'height' => null]);

    expect($vital->bmi)->toBeNull();
});

test('computeBmi with height zero returns null BMI (division-by-zero guard)', function () {
    $visit = Visit::factory()->create();
    $service = new VitalSignService();

    $vital = $service->record($visit, ['weight' => 80, 'height' => 0]);

    expect($vital->bmi)->toBeNull();
});

// ─── update: BMI recomputation ────────────────────────────────────────────────

test('update with only weight recomputes BMI from merged weight and existing stored height', function () {
    $vital = VitalSign::factory()->create(['weight' => 70.00, 'height' => 175.00, 'bmi' => 22.86]);
    $service = new VitalSignService();

    $updated = $service->update($vital, ['weight' => 80]);

    // 80 / (1.75 ^ 2) = 26.12
    expect((float) $updated->bmi)->toBe(26.12);
});

test('update with height null stores null BMI', function () {
    $vital = VitalSign::factory()->create(['weight' => 70.00, 'height' => 175.00, 'bmi' => 22.86]);
    $service = new VitalSignService();

    $updated = $service->update($vital, ['height' => null]);

    expect($updated->bmi)->toBeNull();
});
