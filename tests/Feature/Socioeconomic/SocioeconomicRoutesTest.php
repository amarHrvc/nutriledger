<?php

use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// --- Route existence ---

test('socioeconomic show route exists', function (): void {
    $patient = Patient::factory()->create();
    expect(route('patients.socioeconomic.show', $patient))->toContain('/patients/'.$patient->id.'/socioeconomic');
});

test('socioeconomic create route exists', function (): void {
    $patient = Patient::factory()->create();
    expect(route('patients.socioeconomic.create', $patient))->toContain('/patients/'.$patient->id.'/socioeconomic/create');
});

test('socioeconomic edit route exists', function (): void {
    $patient = Patient::factory()->create();
    expect(route('patients.socioeconomic.edit', $patient))->toContain('/patients/'.$patient->id.'/socioeconomic/edit');
});

// --- Auth guard ---

test('unauthenticated user is redirected from socioeconomic show', function (): void {
    $patient = Patient::factory()->create();
    $this->get(route('patients.socioeconomic.show', $patient))->assertRedirect();
});

test('unauthenticated user is redirected from socioeconomic create', function (): void {
    $patient = Patient::factory()->create();
    $this->get(route('patients.socioeconomic.create', $patient))->assertRedirect();
});

test('unauthenticated user is redirected from socioeconomic edit', function (): void {
    $patient = Patient::factory()->create();
    $this->get(route('patients.socioeconomic.edit', $patient))->assertRedirect();
});

// --- Admin access ---

test('admin can access socioeconomic show route', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::create(['patient_id' => $patient->id]);
    $this->actingAs($admin)->get(route('patients.socioeconomic.show', $patient))->assertOk();
});

test('admin can access socioeconomic create route', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $this->actingAs($admin)->get(route('patients.socioeconomic.create', $patient))->assertOk();
});

test('admin can access socioeconomic edit route', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::create(['patient_id' => $patient->id]);
    $this->actingAs($admin)->get(route('patients.socioeconomic.edit', $patient))->assertOk();
});

// --- Doktor access ---

test('doktor can access socioeconomic show route', function (): void {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::create(['patient_id' => $patient->id]);
    $this->actingAs($doktor)->get(route('patients.socioeconomic.show', $patient))->assertOk();
});

test('doktor can access socioeconomic create route', function (): void {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $this->actingAs($doktor)->get(route('patients.socioeconomic.create', $patient))->assertOk();
});

// --- Pacijent access ---

test('pacijent can view own socioeconomic show route', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    PatientSocioeconomic::create(['patient_id' => $patient->id]);
    $this->actingAs($user)->get(route('patients.socioeconomic.show', $patient))->assertOk();
});

test('pacijent cannot access socioeconomic create route', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user)->get(route('patients.socioeconomic.create', $patient))->assertForbidden();
});

test('pacijent cannot access another patients socioeconomic route', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);
    $otherPatient = Patient::factory()->create();
    PatientSocioeconomic::create(['patient_id' => $otherPatient->id]);
    $this->actingAs($user)->get(route('patients.socioeconomic.show', $otherPatient))->assertForbidden();
});
