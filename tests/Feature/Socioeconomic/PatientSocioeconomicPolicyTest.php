<?php

use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// viewAny
test('admin can viewAny socioeconomic data', function (): void {
    $user = User::factory()->create(['role' => 'admin']);
    expect($user->can('viewAny', PatientSocioeconomic::class))->toBeTrue();
});

test('doktor can viewAny socioeconomic data', function (): void {
    $user = User::factory()->create(['role' => 'doktor']);
    expect($user->can('viewAny', PatientSocioeconomic::class))->toBeTrue();
});

test('pacijent cannot viewAny socioeconomic data', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);
    expect($user->can('viewAny', PatientSocioeconomic::class))->toBeFalse();
});

// view
test('admin can view any patient socioeconomic record', function (): void {
    $user = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $socio = $patient->socioeconomic()->create([]);
    expect($user->can('view', $socio))->toBeTrue();
});

test('doktor can view any patient socioeconomic record', function (): void {
    $user = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $socio = $patient->socioeconomic()->create([]);
    expect($user->can('view', $socio))->toBeTrue();
});

test('pacijent can view their own socioeconomic record', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $socio = $patient->socioeconomic()->create([]);
    expect($user->can('view', $socio))->toBeTrue();
});

test('pacijent cannot view another patients socioeconomic record', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);
    $other = Patient::factory()->create();
    $socio = $other->socioeconomic()->create([]);
    expect($user->can('view', $socio))->toBeFalse();
});

// create
test('admin can create socioeconomic data', function (): void {
    $user = User::factory()->create(['role' => 'admin']);
    expect($user->can('create', PatientSocioeconomic::class))->toBeTrue();
});

test('doktor can create socioeconomic data', function (): void {
    $user = User::factory()->create(['role' => 'doktor']);
    expect($user->can('create', PatientSocioeconomic::class))->toBeTrue();
});

test('pacijent cannot create socioeconomic data', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);
    expect($user->can('create', PatientSocioeconomic::class))->toBeFalse();
});

// update
test('admin can update socioeconomic data', function (): void {
    $user = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $socio = $patient->socioeconomic()->create([]);
    expect($user->can('update', $socio))->toBeTrue();
});

test('doktor can update socioeconomic data', function (): void {
    $user = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $socio = $patient->socioeconomic()->create([]);
    expect($user->can('update', $socio))->toBeTrue();
});

test('pacijent cannot update socioeconomic data', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create();
    $socio = $patient->socioeconomic()->create([]);
    expect($user->can('update', $socio))->toBeFalse();
});

// delete
test('admin can delete socioeconomic data', function (): void {
    $user = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $socio = $patient->socioeconomic()->create([]);
    expect($user->can('delete', $socio))->toBeTrue();
});

test('doktor can delete socioeconomic data', function (): void {
    $user = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $socio = $patient->socioeconomic()->create([]);
    expect($user->can('delete', $socio))->toBeTrue();
});

test('pacijent cannot delete socioeconomic data', function (): void {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create();
    $socio = $patient->socioeconomic()->create([]);
    expect($user->can('delete', $socio))->toBeFalse();
});
