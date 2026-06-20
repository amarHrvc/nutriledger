<?php

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('GET /api/patients/{id} embeds socioeconomicData when patient has socioeconomic', function () {
    $admin = User::factory()->admin()->create();
    $patient = Patient::factory()->hasSocioeconomic(['marital_status' => 'married'])->create();

    $response = $this->actingAs($admin)->getJson("/api/patients/{$patient->id}");

    $response->assertOk()
        ->assertJsonPath('data.patient.attributes.socioeconomicData.type', 'patient_socioeconomic')
        ->assertJsonPath('data.patient.attributes.socioeconomicData.attributes.maritalStatus', 'married');
});

it('GET /api/patients/{id} returns null socioeconomicData when no socioeconomic record', function () {
    $admin = User::factory()->admin()->create();
    $patient = Patient::factory()->create();

    $response = $this->actingAs($admin)->getJson("/api/patients/{$patient->id}");

    $response->assertOk()
        ->assertJsonPath('data.patient.attributes.socioeconomicData', null);
});
