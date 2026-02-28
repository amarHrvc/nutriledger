<?php

use App\Livewire\Patient\ViewPatient;
use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// --- Rendering: no socioeconomic data ---

test('patient profile shows Not recorded badge when no socioeconomic data', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(ViewPatient::class, ['patient' => $patient])
        ->assertSee('Not recorded');
});

test('patient profile shows Add Socioeconomic Data button for admin when no data', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(ViewPatient::class, ['patient' => $patient])
        ->assertSee('Add Socioeconomic Data');
});

test('patient profile shows Add Socioeconomic Data button for doktor when no data', function (): void {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($doktor)
        ->test(ViewPatient::class, ['patient' => $patient])
        ->assertSee('Add Socioeconomic Data');
});

test('pacijent does not see Add Socioeconomic Data button when viewing own profile', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(ViewPatient::class, ['patient' => $patient])
        ->assertDontSee('Add Socioeconomic Data');
});

test('patient profile shows no data message when no socioeconomic record exists', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(ViewPatient::class, ['patient' => $patient])
        ->assertSee('No socioeconomic data has been recorded');
});

// --- Rendering: with socioeconomic data ---

test('patient profile shows Recorded badge when socioeconomic data exists', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(ViewPatient::class, ['patient' => $patient])
        ->assertSee('Recorded');
});

test('patient profile shows View Details button when socioeconomic data exists', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(ViewPatient::class, ['patient' => $patient])
        ->assertSee('View Details');
});

test('patient profile shows Edit button for admin when socioeconomic data exists', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(ViewPatient::class, ['patient' => $patient])
        ->assertSeeHtml('socioeconomic/edit');
});

test('patient profile displays employment status from socioeconomic record', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create([
        'patient_id' => $patient->id,
        'employment_status' => 'retired',
    ]);

    Livewire::actingAs($admin)
        ->test(ViewPatient::class, ['patient' => $patient])
        ->assertSee('Retired');
});

test('patient profile displays income level from socioeconomic record', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create([
        'patient_id' => $patient->id,
        'income_level' => 'middle',
    ]);

    Livewire::actingAs($admin)
        ->test(ViewPatient::class, ['patient' => $patient])
        ->assertSee('Middle');
});

test('patient profile displays food security status from socioeconomic record', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create([
        'patient_id' => $patient->id,
        'food_security_status' => 'food_secure',
    ]);

    Livewire::actingAs($admin)
        ->test(ViewPatient::class, ['patient' => $patient])
        ->assertSee('Food Secure');
});

test('pacijent cannot see Edit socioeconomic button on their own profile', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($user)
        ->test(ViewPatient::class, ['patient' => $patient])
        ->assertDontSee('Add Socioeconomic Data');
});

test('patient profile shows socioeconomic section heading', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(ViewPatient::class, ['patient' => $patient])
        ->assertSee('Socioeconomic Data');
});

test('ViewPatient component loads socioeconomic relationship', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $socioeconomic = PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    $component = Livewire::actingAs($admin)
        ->test(ViewPatient::class, ['patient' => $patient]);

    expect($component->get('socioeconomic'))->not->toBeNull();
    expect($component->get('socioeconomic')->id)->toBe($socioeconomic->id);
});

test('ViewPatient component socioeconomic is null when no record', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    $component = Livewire::actingAs($admin)
        ->test(ViewPatient::class, ['patient' => $patient]);

    expect($component->get('socioeconomic'))->toBeNull();
});
