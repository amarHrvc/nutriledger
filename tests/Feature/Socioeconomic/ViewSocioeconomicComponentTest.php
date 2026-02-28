<?php

use App\Livewire\Patient\Socioeconomic\ViewSocioeconomic;
use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// --- Authorization ---

test('admin can view a patient socioeconomic page', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(ViewSocioeconomic::class, ['patient' => $patient])
        ->assertOk();
});

test('doktor can view a patient socioeconomic page', function (): void {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($doktor)
        ->test(ViewSocioeconomic::class, ['patient' => $patient])
        ->assertOk();
});

test('pacijent can view their own socioeconomic data page', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($user)
        ->test(ViewSocioeconomic::class, ['patient' => $patient])
        ->assertOk();
});

test('pacijent cannot view another patient socioeconomic data page', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);
    $otherPatient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $otherPatient->id]);

    Livewire::actingAs($user)
        ->test(ViewSocioeconomic::class, ['patient' => $otherPatient])
        ->assertForbidden();
});

test('guest is redirected to login when accessing socioeconomic page', function (): void {
    $patient = Patient::factory()->create();

    $this->get(route('patients.socioeconomic.show', $patient))
        ->assertRedirect(route('login'));
});

// --- Rendering: No Record ---

test('component shows no data message when no socioeconomic record exists', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(ViewSocioeconomic::class, ['patient' => $patient])
        ->assertSee('No socioeconomic data recorded yet');
});

test('admin sees Add Socioeconomic Data button when no record exists', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(ViewSocioeconomic::class, ['patient' => $patient])
        ->assertSee('Add Socioeconomic Data');
});

test('doktor sees Add Socioeconomic Data button when no record exists', function (): void {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($doktor)
        ->test(ViewSocioeconomic::class, ['patient' => $patient])
        ->assertSee('Add Socioeconomic Data');
});

test('pacijent does not see Add Socioeconomic Data button when no record exists', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(ViewSocioeconomic::class, ['patient' => $patient])
        ->assertDontSee('Add Socioeconomic Data');
});

// --- Rendering: Record Exists ---

test('component renders all field sections when socioeconomic data exists', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(ViewSocioeconomic::class, ['patient' => $patient])
        ->assertSee('Demographics')
        ->assertSee('Economic Status')
        ->assertSee('Lifestyle')
        ->assertSee('Support System')
        ->assertSee('Food Security');
});

test('admin sees Edit button when record exists', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(ViewSocioeconomic::class, ['patient' => $patient])
        ->assertSee('Edit');
});

test('doktor sees Edit button when record exists', function (): void {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($doktor)
        ->test(ViewSocioeconomic::class, ['patient' => $patient])
        ->assertSee('Edit');
});

test('pacijent does not see Edit button when record exists', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($user)
        ->test(ViewSocioeconomic::class, ['patient' => $patient])
        ->assertDontSee(route('patients.socioeconomic.edit', $patient));
});

test('component renders marital status correctly', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create([
        'patient_id' => $patient->id,
        'marital_status' => 'married',
    ]);

    Livewire::actingAs($admin)
        ->test(ViewSocioeconomic::class, ['patient' => $patient])
        ->assertSee('Married');
});

test('component renders employment status correctly', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create([
        'patient_id' => $patient->id,
        'employment_status' => 'employed_full_time',
    ]);

    Livewire::actingAs($admin)
        ->test(ViewSocioeconomic::class, ['patient' => $patient])
        ->assertSee('Employed Full Time');
});

test('component renders income level correctly', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create([
        'patient_id' => $patient->id,
        'income_level' => 'middle',
    ]);

    Livewire::actingAs($admin)
        ->test(ViewSocioeconomic::class, ['patient' => $patient])
        ->assertSee('Middle');
});

test('component renders boolean fields as Yes or No', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create([
        'patient_id'           => $patient->id,
        'has_health_insurance' => true,
        'has_family_support'   => false,
        'has_caregiver'        => true,
    ]);

    Livewire::actingAs($admin)
        ->test(ViewSocioeconomic::class, ['patient' => $patient])
        ->assertSeeInOrder(['Health Insurance', 'Yes', 'Family Support', 'No', 'Has Caregiver', 'Yes']);
});

test('component shows Back to Patient Profile link', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(ViewSocioeconomic::class, ['patient' => $patient])
        ->assertSee('Back to Patient Profile');
});
