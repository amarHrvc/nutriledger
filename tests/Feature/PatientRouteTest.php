<?php

use App\Models\Patient;
use App\Models\User;

test('patients index route exists and requires auth', function () {
    $response = $this->get('/patients');
    $response->assertRedirect('/login');
});

test('admin can access patients list', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get('/patients')
        ->assertOk();
});

test('doktor can access patients list', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);

    $this->actingAs($doktor)
        ->get('/patients')
        ->assertOk();
});
test('patient can not access patients list', function () {
    $patient = User::factory()->create(['role' => 'pacijent']);

    $this->actingAs($patient)
        ->get('/patients')
        ->assertForbidden();
});


test('create patient route exists', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get('/patients/create')
        ->assertOk();
});

test('view patient route exists', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}")
        ->assertOk();
});

test('edit patient route exists', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/edit")
        ->assertOk();
});

test('pacijent can view own profile', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get("/patients/{$patient->id}")
        ->assertOk();
});
