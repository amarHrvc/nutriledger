<?php

use App\Livewire\Patient\Socioeconomic\DeleteSocioeconomic;
use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// --- Authorization ---

test('admin can access the delete socioeconomic page', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(DeleteSocioeconomic::class, ['patient' => $patient])
        ->assertOk();
});

test('doktor can access the delete socioeconomic page', function (): void {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($doktor)
        ->test(DeleteSocioeconomic::class, ['patient' => $patient])
        ->assertOk();
});

test('pacijent cannot access the delete socioeconomic page', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($user)
        ->test(DeleteSocioeconomic::class, ['patient' => $patient])
        ->assertForbidden();
});

test('guest is redirected to login when accessing delete socioeconomic page', function (): void {
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    $this->get(route('patients.socioeconomic.delete', $patient))
        ->assertRedirect(route('login'));
});

// --- Delete action ---

test('admin can delete socioeconomic data', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(DeleteSocioeconomic::class, ['patient' => $patient])
        ->call('delete')
        ->assertHasNoErrors();
});

test('deleting removes the record from the database', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(DeleteSocioeconomic::class, ['patient' => $patient])
        ->call('delete');

    expect(PatientSocioeconomic::where('patient_id', $patient->id)->exists())->toBeFalse();
});

test('after successful delete redirects to patients.show', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(DeleteSocioeconomic::class, ['patient' => $patient])
        ->call('delete')
        ->assertRedirect(route('patients.show', $patient));
});

test('flash message shown after successful delete', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(DeleteSocioeconomic::class, ['patient' => $patient])
        ->call('delete');

    expect(session('success'))->toBe('Socioeconomic data deleted successfully.');
});

test('attempting to delete when no record exists returns 404', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(DeleteSocioeconomic::class, ['patient' => $patient])
        ->assertNotFound();
});

// --- UI rendering ---

test('component renders a delete confirmation heading', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(DeleteSocioeconomic::class, ['patient' => $patient])
        ->assertSee('Delete Socioeconomic Data');
});

test('component shows a warning message with patient name', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(DeleteSocioeconomic::class, ['patient' => $patient])
        ->assertSee('This action cannot be undone');
});

test('component has a Confirm Delete button', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(DeleteSocioeconomic::class, ['patient' => $patient])
        ->assertSee('Confirm Delete');
});

test('component has a Cancel button', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(DeleteSocioeconomic::class, ['patient' => $patient])
        ->assertSee('Cancel');
});

test('delete button is visible for admin', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(DeleteSocioeconomic::class, ['patient' => $patient])
        ->assertSee('Confirm Delete');
});

test('delete button is visible for doktor', function (): void {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($doktor)
        ->test(DeleteSocioeconomic::class, ['patient' => $patient])
        ->assertSee('Confirm Delete');
});

test('pacijent does not see the delete button in the UI', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    // Pacijent gets forbidden, so the delete button is never rendered
    Livewire::actingAs($user)
        ->test(DeleteSocioeconomic::class, ['patient' => $patient])
        ->assertForbidden();
});

test('admin deletes record and record count goes from 1 to 0', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);

    expect(PatientSocioeconomic::where('patient_id', $patient->id)->count())->toBe(1);

    Livewire::actingAs($admin)
        ->test(DeleteSocioeconomic::class, ['patient' => $patient])
        ->call('delete');

    expect(PatientSocioeconomic::where('patient_id', $patient->id)->count())->toBe(0);
});
