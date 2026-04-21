<?php

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;

test('guest cannot delete visit', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $response = $this->deleteJson('/api/patients/'.$patient->id.'/visits/'.$visit->id);

    $response->assertUnauthorized();
});

test('admin can delete any visit', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $response = $this->actingAs($admin)->deleteJson('/api/patients/'.$patient->id.'/visits/'.$visit->id);

    $response->assertNoContent();
});

test('doctor cannot delete visit', function () {
    $doctor1 = User::factory()->create(['role' => 'doktor']);
    $doctor2 = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor1->id,
    ]);

    $response = $this->actingAs($doctor2)->deleteJson('/api/patients/'.$patient->id.'/visits/'.$visit->id);

    $response->assertForbidden();
});

test('patient cannot delete visit', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $doctor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $response = $this->actingAs($user)->deleteJson('/api/patients/'.$patient->id.'/visits/'.$visit->id);

    $response->assertForbidden();
});

test('admin delete hard deletes visit from database', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $response = $this->actingAs($admin)->deleteJson('/api/patients/'.$patient->id.'/visits/'.$visit->id);

    $response->assertNoContent();
    $this->assertDatabaseMissing('visits', ['id' => $visit->id]);
});

test('visit is completely removed with no soft delete recovery', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $this->actingAs($admin)->deleteJson('/api/patients/'.$patient->id.'/visits/'.$visit->id);

    $foundVisit = Visit::find($visit->id);
    expect($foundVisit)->toBeNull();
});

test('visit from different patient returns 404', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient1 = Patient::factory()->create();
    $patient2 = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient1->id,
        'doctor_id' => $doctor->id,
    ]);

    $response = $this->actingAs($admin)->deleteJson('/api/patients/'.$patient2->id.'/visits/'.$visit->id);

    $response->assertNotFound();
    // Verify visit was not deleted
    $this->assertDatabaseHas('visits', ['id' => $visit->id]);
});
