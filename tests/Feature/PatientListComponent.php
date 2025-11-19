<?php

use App\Models\Patient;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->doktor = User::factory()->create(['role' => 'doktor']);
    $this->john = Patient::factory()->create(['first_name' => 'John']);
    $this->jane = Patient::factory()->create(['first_name' => 'Jane']);
    $this->liveWireTest = Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Patient\PatientList::class);
});

afterEach(function () {
    // Clean up if needed
    $this->admin->delete();
    $this->doktor->delete();
    $this->jane->delete();
    $this->john->delete();
});

test('admin can see patient list component', function () {

    $this->actingAs($this->admin)
        ->get('/patients')
        ->assertOk()
        ->assertSeeLivewire('patient.patient-list');
});

test('doctor can see patient list component', function () {

    $this->actingAs($this->doktor)
        ->get('/patients')
        ->assertOk()
        ->assertSeeLivewire('patient.patient-list');
});

test('patient list displays all patients', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patients = Patient::factory()->count(3)->create();

    $this->liveWireTest
        ->set('perPage', 10)
        ->assertSee($patients[0]->first_name)
        ->assertSee($patients[1]->first_name)
        ->assertSee($patients[2]->first_name);
});


test('pateint list can search by name', function () {
    $this->liveWireTest
        ->set('search', 'John')
        ->assertSee('John')
        ->assertDontSee("Jane");
});

test('patient pagination works', function () {
//    Patient::factory()->count(15)->create();
    $this->liveWireTest
        ->set('perPage', 1)
        ->assertSee('next')
        ->assertSee('John')
        ->assertDontSee('Jane')
        ->call('nextPage')
        ->assertSee('Jane')
        ->assertDontSee('John');
});

test('patient list show create button for admin', function () {
    $this->liveWireTest
        ->assertSee('Add Patient');
});

test('patients can not access patient list', function () {
    Livewire::actingAs($this->jane->user)
        ->test(\App\Livewire\Patient\PatientList::class)
        ->assertForbidden();

    $this->actingAs($this->jane->user)
        ->get('/patients')
        ->assertForbidden();
});

