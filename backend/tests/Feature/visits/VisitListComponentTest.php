<?php

use App\Livewire\Patient\Visit\VisitList;

test('Admin can access the visit list component', function () {
    $admin = \App\Models\User::factory()->create(['role' => 'admin']);
    $patient = \App\Models\Patient::factory()->create();

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/visits")
        ->assertStatus(200)
        ->assertSeeLivewire(VisitList::class);
});

test('Doctor can access the visit list component for their patient', function () {
    $doctor = \App\Models\User::factory()->create(['role' => 'doktor']);
    $patient = \App\Models\Patient::factory()->create();

    $this->actingAs($doctor)
        ->get("/patients/{$patient->id}/visits")
        ->assertStatus(200)
        ->assertSeeLivewire(VisitList::class);
});

test('Pacijent cannot access the visit list component', function () {
    $user = \App\Models\User::factory()->create(['role' => 'pacijent']);
    $patient = \App\Models\Patient::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get("/patients/{$patient->id}/visits")
        ->assertForbidden();
});

test('Component shows visits for the patient', function () {
    $admin = \App\Models\User::factory()->create(['role' => 'admin']);
    $patient = \App\Models\Patient::factory()->create();
    $visit1 = \App\Models\Visit::factory()->create(['patient_id' => $patient->id]);
    $visit2 = \App\Models\Visit::factory()->create(['patient_id' => $patient->id]);

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/visits")
        ->assertStatus(200)
        ->assertSee($visit1->notes)
        ->assertSee($visit2->notes);
});

test('Comonent shows empty state when no visits', function () {
    $admin = \App\Models\User::factory()->create(['role' => 'admin']);
    $patient = \App\Models\Patient::factory()->create();

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/visits")
        ->assertStatus(200)
        ->assertSee('No visits found');
});

test('Component shows add visit button for admin and doctor', function () {
    $admin = \App\Models\User::factory()->create(['role' => 'admin']);
    $doctor = \App\Models\User::factory()->create(['role' => 'doktor']);
    $patient = \App\Models\Patient::factory()->create();

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/visits")
        ->assertStatus(200)
        ->assertSee('Add Visit');

    $this->actingAs($doctor)
        ->get("/patients/{$patient->id}/visits")
        ->assertStatus(200)
        ->assertSee('Add Visit');
});

test('Component does not shows visit of other patients', function () {
    $admin = \App\Models\User::factory()->create(['role' => 'admin']);
    $patient1 = \App\Models\Patient::factory()->create();
    $patient2 = \App\Models\Patient::factory()->create();
    $visit1 = \App\Models\Visit::factory()->create(['patient_id' => $patient1->id]);
    $visit2 = \App\Models\Visit::factory()->create(['patient_id' => $patient2->id]);

    $this->actingAs($admin)
        ->get("/patients/{$patient1->id}/visits")
        ->assertStatus(200)
        ->assertSee($visit1->notes)
        ->assertDontSee($visit2->notes);
});
