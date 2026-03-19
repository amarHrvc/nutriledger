<?php

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Support\Carbon;

test('visit can be created with required fields', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'date' => now(),
    ]);

    expect($visit)->toBeInstanceOf(Visit::class)
        ->and($visit->id)->toBeInt()
        ->and($visit->patient->id)->toBeInt()->and($visit->patient->id)->toEqual($patient->id)
        ->and($visit->date)->toBeInstanceOf(Carbon::class);
});

test('visit belongs to a patient', function () {
    $visit = Visit::factory()->create();

    expect($visit->patient)->toBeInstanceOf(Patient::class);
});

test('visit belongs to a doctor', function () {
    $visit = Visit::factory()->create();

    expect($visit->doctor)->toBeInstanceOf(User::class)
        ->and($visit->doctor->role)->toEqual('doktor');
});

test('visit can have notes', function () {
    $visit = Visit::factory()->create(['notes' => 'Patient is recovering well.']);

    expect($visit->notes)->toEqual('Patient is recovering well.');
});

test('visit can have date', function () {
    $visit = Visit::factory()->create(['date' => now()]);

    expect($visit->date)->toBeInstanceOf(Carbon::class)
        ->and($visit->date->isToday())->toBeTrue()
        ->and($visit->date->toDateString())->toEqual(now()->toDateString());

});

test('Patient can have many visits', function () {
    $patient = Patient::factory()->create();

    $visit = Visit::factory()->count(5)->create(['patient_id' => $patient->id]);

    expect($patient->visits)->toHaveCount(5)
        ->and($patient->visits->first())->toBeInstanceOf(Visit::class)
        ->and($patient->visits->first()->patient_id)->toEqual($patient->id);
});
