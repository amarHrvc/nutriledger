<?php

use App\Models\Patient;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->doktor = User::factory()->create(['role' => 'doktor']);
    $this->john = Patient::factory()->create(['first_name' => 'John']);
    $this->jane = Patient::factory()->create(['first_name' => 'Jane']);
    $this->liveWireTest = Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Patient\CreatePatient::class);
});

afterEach(function () {
    // Clean up if needed
    $this->admin->delete();
    $this->doktor->delete();
    $this->jane->delete();
    $this->john->delete();
});

test('only admin can see patient form', function () {

    $this->actingAs($this->admin)
        ->get('/patients/create')
        ->assertOk()
        ->assertSeeLivewire('patient.create-patient');
});


test('doctor can see patient form', function () {

    $this->actingAs($this->doktor)
        ->get('/patients/create')
        ->assertOk()
        ->assertSeeLivewire('patient.create-patient');
});

test('patient can see patient form', function () {

    $this->actingAs($this->jane->user)
        ->get('/patients/create')
        ->assertForbidden();
});

test('can create patient with valid data', function () {
    $this->liveWireTest
        ->set('email', 'alice@example.com')
        ->set('first_name', 'Alice')
        ->set('last_name', 'Smith')
        ->set('gender', 'F')
        ->set('date_of_birth', '1990-01-01')
        ->set('phone', '1234567890')
        ->set('blood_type', 'A+')
        ->call('createPatient')
        ->assertHasNoErrors()
        ->assertRedirect(route('patients.index'));

    $this->assertDatabaseHas('users', [
        'name' => 'Alice Smith',
        'email' => 'alice@example.com',
        'role' => 'pacijent'
    ]);

    $this->assertDatabaseHas('patients', [
        'first_name' => 'Alice',
        'last_name' => 'Smith',
        'gender' => 'F',
        'date_of_birth' => '1990-01-01 00:00:00',
        'phone' => '1234567890',
        'blood_type' => 'A+'

    ]);

});

test('email is required', function () {
    $this->liveWireTest
        ->set('email', '')
        ->call('createPatient')
        ->assertHasErrors(['email' => 'required']);
});

test('email must be unique', function () {
    User::factory(['email' => 'test@example.com'])->create();
    $this->liveWireTest
        ->set('email', 'test@example.com')
        ->call('createPatient')
        ->assertHasErrors(['email' => 'unique']);
});


test('first name required', function () {
    $this->liveWireTest
        ->set('first_name', '')
        ->call('createPatient')
        ->assertHasErrors(['first_name' => 'required']);
});

test('last_name required', function () {
    $this->liveWireTest
        ->set('last_name', '')
        ->call('createPatient')
        ->assertHasErrors(['last_name' => 'required']);
});

test('date_of_birth required', function () {

    $this->liveWireTest
        ->set('date_of_birth', '')
        ->call('createPatient')
        ->assertHasErrors(['date_of_birth' => 'required']);
});

test('gender required', function () {

    $this->liveWireTest
        ->set('gender', '')
        ->call('createPatient')
        ->assertHasErrors(['gender' => 'required']);
});
test('gender msut be valid', function () {

    $this->liveWireTest
        ->set('gender', 'X')
        ->call('createPatient')
        ->assertHasErrors(['gender' => 'in']);
});

