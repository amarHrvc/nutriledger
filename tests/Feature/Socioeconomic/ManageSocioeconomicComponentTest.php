<?php

use App\Livewire\Patient\Socioeconomic\ManageSocioeconomic;
use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// --- Route-level Authorization ---

test('admin can access create page for patient without socioeconomic data', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    $this->actingAs($admin)
        ->get(route('patients.socioeconomic.create', $patient))
        ->assertOk();
});

test('doktor can access create page for patient without socioeconomic data', function (): void {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    $this->actingAs($doktor)
        ->get(route('patients.socioeconomic.create', $patient))
        ->assertOk();
});

test('pacijent cannot access create page', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('patients.socioeconomic.create', $patient))
        ->assertForbidden();
});

test('guest is redirected to login for create page', function (): void {
    $patient = Patient::factory()->create();

    $this->get(route('patients.socioeconomic.create', $patient))
        ->assertRedirect(route('login'));
});

test('admin can access edit page for patient with existing socioeconomic data', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::create(['patient_id' => $patient->id]);

    $this->actingAs($admin)
        ->get(route('patients.socioeconomic.edit', $patient))
        ->assertOk();
});

test('doktor can access edit page for patient with existing socioeconomic data', function (): void {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::create(['patient_id' => $patient->id]);

    $this->actingAs($doktor)
        ->get(route('patients.socioeconomic.edit', $patient))
        ->assertOk();
});

test('pacijent cannot access edit page', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    PatientSocioeconomic::create(['patient_id' => $patient->id]);

    $this->actingAs($user)
        ->get(route('patients.socioeconomic.edit', $patient))
        ->assertForbidden();
});

// --- Component Rendering ---

test('component renders create form when no existing record', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(ManageSocioeconomic::class, ['patient' => $patient])
        ->assertSet('isEditing', false)
        ->assertOk();
});

test('component renders edit form with pre-filled values when record exists', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::create([
        'patient_id' => $patient->id,
        'employment_status' => 'employed_full_time',
        'income_level' => 'middle',
        'marital_status' => 'married',
    ]);

    Livewire::actingAs($admin)
        ->test(ManageSocioeconomic::class, ['patient' => $patient])
        ->assertSet('isEditing', true)
        ->assertSet('employment_status', 'employed_full_time')
        ->assertSet('income_level', 'middle')
        ->assertSet('marital_status', 'married')
        ->assertOk();
});

// --- Create / Update ---

test('admin can successfully create socioeconomic record', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(ManageSocioeconomic::class, ['patient' => $patient])
        ->set('marital_status', 'married')
        ->set('employment_status', 'employed_full_time')
        ->set('income_level', 'middle')
        ->set('number_of_dependents', 2)
        ->call('save')
        ->assertHasNoErrors();

    test()->assertDatabaseHas('patient_socioeconomic', [
        'patient_id' => $patient->id,
        'marital_status' => 'married',
        'employment_status' => 'employed_full_time',
        'income_level' => 'middle',
        'number_of_dependents' => 2,
    ]);
});

test('admin can successfully update existing socioeconomic record', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::create([
        'patient_id' => $patient->id,
        'employment_status' => 'unemployed',
        'income_level' => 'low',
        'marital_status' => 'single',
        'living_arrangement' => 'alone',
        'food_security_status' => 'food_secure',
    ]);

    Livewire::actingAs($admin)
        ->test(ManageSocioeconomic::class, ['patient' => $patient])
        ->set('employment_status', 'employed_full_time')
        ->set('income_level', 'middle')
        ->call('save')
        ->assertHasNoErrors();

    test()->assertDatabaseHas('patient_socioeconomic', [
        'patient_id' => $patient->id,
        'employment_status' => 'employed_full_time',
        'income_level' => 'middle',
    ]);
});

// --- Validation ---

test('validation fails when employment_status is invalid value', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(ManageSocioeconomic::class, ['patient' => $patient])
        ->set('employment_status', 'invalid_status')
        ->call('save')
        ->assertHasErrors(['employment_status']);
});

test('validation fails when income_level is invalid value', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(ManageSocioeconomic::class, ['patient' => $patient])
        ->set('income_level', 'very_rich')
        ->call('save')
        ->assertHasErrors(['income_level']);
});

test('validation fails when number_of_dependents is negative', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(ManageSocioeconomic::class, ['patient' => $patient])
        ->set('number_of_dependents', -1)
        ->call('save')
        ->assertHasErrors(['number_of_dependents']);
});

test('form shows validation errors', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(ManageSocioeconomic::class, ['patient' => $patient])
        ->set('marital_status', 'invalid_value')
        ->call('save')
        ->assertHasErrors(['marital_status']);
});

// --- Redirects ---

test('after successful create redirects to patients.socioeconomic.show', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(ManageSocioeconomic::class, ['patient' => $patient])
        ->set('employment_status', 'employed_full_time')
        ->call('save')
        ->assertRedirect(route('patients.socioeconomic.show', $patient));
});

test('after successful update redirects to patients.socioeconomic.show', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::create([
        'patient_id' => $patient->id,
        'employment_status' => 'unemployed',
        'marital_status' => 'single',
    ]);

    Livewire::actingAs($admin)
        ->test(ManageSocioeconomic::class, ['patient' => $patient])
        ->set('employment_status', 'retired')
        ->call('save')
        ->assertRedirect(route('patients.socioeconomic.show', $patient));
});

// --- Form Fields ---

test('component has all required form fields', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(ManageSocioeconomic::class, ['patient' => $patient])
        ->assertSeeHtml('wire:model="marital_status"')
        ->assertSeeHtml('wire:model="employment_status"')
        ->assertSeeHtml('wire:model="income_level"')
        ->assertSeeHtml('wire:model="number_of_dependents"')
        ->assertSeeHtml('wire:model="living_arrangement"')
        ->assertSeeHtml('wire:model="occupation"')
        ->assertSeeHtml('wire:model="has_health_insurance"')
        ->assertSeeHtml('wire:model="education_level"')
        ->assertSeeHtml('wire:model="smoking_status"')
        ->assertSeeHtml('wire:model="alcohol_consumption"')
        ->assertSeeHtml('wire:model="physical_activity_level"')
        ->assertSeeHtml('wire:model="has_family_support"')
        ->assertSeeHtml('wire:model="has_caregiver"')
        ->assertSeeHtml('wire:model="transportation_access"')
        ->assertSeeHtml('wire:model="food_security_status"')
        ->assertSeeHtml('wire:model="dietary_restrictions_cultural"')
        ->assertSeeHtml('wire:model="additional_notes"');
});
