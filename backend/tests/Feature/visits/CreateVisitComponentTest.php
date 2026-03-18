<?php

use App\Models\Patient;
use App\Livewire\Patient\Visit\CreateVisit;
use App\Models\User;
use App\Models\Visit;

test('admin can access create visit page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/visits/create")
        ->assertOk()
        ->assertSeeLivewire(CreateVisit::class);
});

test('pacijent cannot access create visit page', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get("/patients/{$patient->id}/visits/create")
        ->assertForbidden();
});

test('doktor can submit a new visit', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();


    Livewire::actingAs($doktor)
        ->test(CreateVisit::class, ['patient' => $patient])
        ->set('form.date', '2026-03-01')
        ->set('form.notes', 'Initial consultation.')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('visit.show', [$patient, Visit::latest()->first()]))
    ;

    expect(Visit::where('patient_id', $patient->id)->exists())->toBeTrue();
});

test('date field is required', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(CreateVisit::class, ['patient' => $patient])
        ->set('form.date', '')
        ->call('save')
        ->assertHasErrors(['form.date' => 'required']);
});

test('date field must be a valid date', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(CreateVisit::class, ['patient' => $patient])
        ->set('form.date', 'not-a-date')
        ->call('save')
        ->assertHasErrors(['form.date' => 'date']);
});

test('doctor id is set to current user when doktor submits', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($doktor)
        ->test(CreateVisit::class, ['patient' => $patient])
        ->set('form.date', '2026-03-01')
        ->call('save')
        ->assertHasNoErrors();

    $visit = Visit::where('patient_id', $patient->id)->latest()->first();
    expect($visit->doctor_id)->toBe($doktor->id);
});
