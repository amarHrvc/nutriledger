<?php

use App\Models\Patient;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->doktor = User::factory()->create(['role' => 'doktor']);
    $this->patientUser = User::factory()->create(['role' => 'pacijent']);
    $this->patient = Patient::factory()->create(['user_id' => $this->patientUser->id]);
});

test('admin can delete patient', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Patient\PatientList::class)
        ->call('deletePatient', $this->patient->id)
        ->assertHasNoErrors();

    expect($this->patient->fresh()->trashed())->toBeTrue();
});

test('doktor can delete patient', function () {
    Livewire::actingAs($this->doktor)
        ->test(\App\Livewire\Patient\PatientList::class)
        ->call('deletePatient', $this->patient->id)
        ->assertHasNoErrors();

    expect($this->patient->fresh()->trashed())->toBeTrue();
});

test('pacijent cannot delete patients', function () {
    try {
        Livewire::actingAs($this->patientUser)
            ->test(\App\Livewire\Patient\PatientList::class)
            ->call('deletePatient', $this->patient->id);
    } catch (\Exception $e) {
        // Expected - pacijent is not authorized to delete patients
    }

    expect($this->patient->fresh()->trashed())->toBeFalse();
});

test('patient is soft deleted not permanently deleted', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Patient\PatientList::class)
        ->call('deletePatient', $this->patient->id);

    $this->assertDatabaseHas('patients', ['id' => $this->patient->id]);
    expect($this->patient->fresh()->trashed())->toBeTrue();
});

test('shows success message after deletion', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Patient\PatientList::class)
        ->call('deletePatient', $this->patient->id)
        ->assertDispatched('patient-deleted');
});

test('deleted patient no longer appears in list', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Patient\PatientList::class)
        ->call('deletePatient', $this->patient->id)
        ->assertDontSee($this->patient->first_name);
});
