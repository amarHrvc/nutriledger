<?php

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows doctor to create patient without socioeconomic data', function () {
    $doctor = User::factory()->doctor()->create();
    $patientUser = User::factory()->patient()->create();

    $response = $this->actingAs($doctor)
        ->postJson('/api/patients', [
            'userId' => $patientUser->id,
            'firstName' => 'Jane',
            'lastName' => 'Smith',
            'dateOfBirth' => '1985-05-20',
            'gender' => 'F',
            'phone' => '+387 62 111 222',
            'emergencyContactName' => 'John Smith',
            'emergencyContactPhone' => '+387 62 333 444',
        ]);

    $response->assertCreated();
    $this->assertDatabaseHas('patients', ['user_id' => $patientUser->id, 'first_name' => 'Jane']);
    $this->assertDatabaseMissing('patient_socioeconomic', ['patient_id' => Patient::latest()->first()->id]);
});
