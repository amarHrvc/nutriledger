<?php

use App\Models\Patient;
use App\Models\Visit;

test('visit factory creates a valid visit', function () {
    $visit = Visit::factory()->create();

    expect($visit)->toBeInstanceOf(Visit::class)
        ->and($visit->patient_id)->not->toBeNull()
        ->and($visit->doctor_id)->not->toBeNull()
        ->and($visit->date)->not->toBeNull();
});

test('visit factory creates a doctor user', function () {
    $visit = Visit::factory()->create();

    expect($visit->doctor->role)->toBe('doktor');
});

test('visit factory creates a valid patient', function () {
    $visit = Visit::factory()->create();

    expect($visit->patient)->toBeInstanceOf(Patient::class);
});

test('visit factory accepts custom attributes', function () {
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    expect($visit->patient_id)->toBe($patient->id);
});
