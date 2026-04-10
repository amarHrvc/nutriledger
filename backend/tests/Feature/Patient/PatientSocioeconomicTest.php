<?php

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// T028
it('creates socioeconomic record via PATCH when patient has none', function () {
    $admin = User::factory()->admin()->create();
    $patient = Patient::factory()->create();

    $this->actingAs($admin)
        ->patchJson("/api/patients/{$patient->id}", [
            'socioeconomic' => [
                'marital_status' => 'single',
                'employment_status' => 'employed_full_time',
                'income_level' => 'middle',
            ],
        ])
        ->assertOk();

    $this->assertDatabaseHas('patient_socioeconomic', [
        'patient_id' => $patient->id,
        'marital_status' => 'single',
        'employment_status' => 'employed_full_time',
        'income_level' => 'middle',
    ]);
});

// T029
it('updates existing socioeconomic record via PATCH', function () {
    $admin = User::factory()->admin()->create();
    $patient = Patient::factory()->hasSocioeconomic(['marital_status' => 'single'])->create();

    $this->actingAs($admin)
        ->patchJson("/api/patients/{$patient->id}", [
            'socioeconomic' => [
                'marital_status' => 'married',
            ],
        ])
        ->assertOk();

    $this->assertDatabaseHas('patient_socioeconomic', [
        'patient_id' => $patient->id,
        'marital_status' => 'married',
    ]);
});

// T030
it('only updates provided socioeconomic fields leaving others unchanged', function () {
    $admin = User::factory()->admin()->create();
    $patient = Patient::factory()->create();
    $patient->socioeconomic()->create([
        'marital_status' => 'single',
        'income_level' => 'low',
        'smoking_status' => 'never',
    ]);

    $this->actingAs($admin)
        ->patchJson("/api/patients/{$patient->id}", [
            'socioeconomic' => [
                'marital_status' => 'married',
            ],
        ])
        ->assertOk();

    $this->assertDatabaseHas('patient_socioeconomic', [
        'patient_id' => $patient->id,
        'marital_status' => 'married',
        'income_level' => 'low',
        'smoking_status' => 'never',
    ]);
});

// T031
it('leaves patient core fields unchanged on socioeconomic-only PATCH', function () {
    $admin = User::factory()->admin()->create();
    $patient = Patient::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'phone' => '+387 61 111 000',
    ]);

    $this->actingAs($admin)
        ->patchJson("/api/patients/{$patient->id}", [
            'socioeconomic' => [
                'marital_status' => 'divorced',
            ],
        ])
        ->assertOk();

    $this->assertDatabaseHas('patients', [
        'id' => $patient->id,
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'phone' => '+387 61 111 000',
    ]);
});

// T032
it('returns 422 for invalid socioeconomic enum values', function (string $field, string $invalidValue) {
    $admin = User::factory()->admin()->create();
    $patient = Patient::factory()->create();

    $this->actingAs($admin)
        ->patchJson("/api/patients/{$patient->id}", [
            'socioeconomic' => [
                $field => $invalidValue,
            ],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(["socioeconomic.{$field}"]);
})->with([
    'marital_status' => ['marital_status', 'invalid_value'],
    'employment_status' => ['employment_status', 'invalid_value'],
    'income_level' => ['income_level', 'invalid_value'],
    'smoking_status' => ['smoking_status', 'invalid_value'],
    'alcohol_consumption' => ['alcohol_consumption', 'invalid_value'],
    'physical_activity_level' => ['physical_activity_level', 'invalid_value'],
    'food_security_status' => ['food_security_status', 'invalid_value'],
]);
