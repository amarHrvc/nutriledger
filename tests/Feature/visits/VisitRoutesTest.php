<?php

use App\Models\Patient;
use App\Models\User;

test('visit list route requires authentication', function () {
    $patient = Patient::factory()->create();
    $this->get("/patients/{$patient->id}/visits")
        ->assertRedirect('/login');
});

test('admin can access visit list route for a patient', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/visits")
        ->assertStatus(200);
});

test('doctor can access visit list route for their patient', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create(['doctor_id' => $doctor->id]);
    $this->actingAs($doctor)
        ->get("/patients/{$patient->id}/visits")
        ->assertStatus(200);
});

test('pacijent cannot access visit list', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get("/patients/{$patient->id}/visits")
        ->assertForbidden();
});

test('create visit route requires admin or doktor', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create();

    $this->actingAs($user)
        ->get("/patients/{$patient->id}/visits/create")
        ->assertForbidden();
});

test('admin can access create visit route', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/visits/create")
        ->assertOk();
});

test('admin can access view visit route', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/visits/{$visit->id}")
        ->assertOk();
});

test('pacijent can view own visit detail', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    $this->actingAs($user)
        ->get("/patients/{$patient->id}/visits/{$visit->id}")
        ->assertOk();
});

test('pacijent cannot view another patients visit detail', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $otherPatient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $otherPatient->id]);

    $this->actingAs($user)
        ->get("/patients/{$otherPatient->id}/visits/{$visit->id}")
        ->assertForbidden();
});

test('edit visit route returns 404 for non-existent visit', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/visits/9999/edit")
        ->assertNotFound();
});




