<?php

use App\Models\Patient;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->doktor = User::factory()->create(['role' => 'doktor']);
    $this->patientUser = User::factory()->create(['role' => 'pacijent']);
    $this->patient = Patient::factory()->create([
        'user_id' => $this->patientUser->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
});

test('admin can access edit form', function () {
    $this->actingAs($this->admin)
        ->get("/patients/{$this->patient->id}/edit")
        ->assertOk()
        ->assertSeeLivewire('patient.edit-patient');
});

test('doktor can access edit form', function () {
    $this->actingAs($this->doktor)
        ->get("/patients/{$this->patient->id}/edit")
        ->assertOk()
        ->assertSeeLivewire('patient.edit-patient');
});

test('pacijent can edit own profile', function () {
    $this->actingAs($this->patientUser)
        ->get("/patients/{$this->patient->id}/edit")
        ->assertOk()
        ->assertSeeLivewire('patient.edit-patient');
});

test('pacijent cannot edit other profiles', function () {
    $other = Patient::factory()->create();

    $this->actingAs($this->patientUser)
        ->get("/patients/{$other->id}/edit")
        ->assertForbidden();
});

test('form is pre-filled with patient data', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Patient\EditPatient::class, ['patient' => $this->patient])
        ->assertSet('first_name', 'John')
        ->assertSet('last_name', 'Doe');
});

test('can update patient with valid data', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Patient\EditPatient::class, ['patient' => $this->patient])
        ->set('first_name', 'Jane')
        ->set('last_name', 'Smith')
        ->call('updatePatient')
        ->assertHasNoErrors()
        ->assertRedirect(route('patients.show', $this->patient));

    $this->patient->refresh();
    expect($this->patient->first_name)->toBe('Jane')
        ->and($this->patient->last_name)->toBe('Smith');
});

test('first name is required', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Patient\EditPatient::class, ['patient' => $this->patient])
        ->set('first_name', '')
        ->call('updatePatient')
        ->assertHasErrors(['first_name' => 'required']);
});

test('last name is required', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Patient\EditPatient::class, ['patient' => $this->patient])
        ->set('last_name', '')
        ->call('updatePatient')
        ->assertHasErrors(['last_name' => 'required']);
});

test('date of birth is required', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Patient\EditPatient::class, ['patient' => $this->patient])
        ->set('date_of_birth', '')
        ->call('updatePatient')
        ->assertHasErrors(['date_of_birth' => 'required']);
});

test('gender must be valid', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Patient\EditPatient::class, ['patient' => $this->patient])
        ->set('gender', 'X')
        ->call('updatePatient')
        ->assertHasErrors(['gender']);
});

test('shows flash message after update', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Patient\EditPatient::class, ['patient' => $this->patient])
        ->call('updatePatient')
        ->assertHasNoErrors();

    expect(session('success'))->toBe('Patient updated successfully.');
});
