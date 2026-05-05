<?php

use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('doctor sees only own visits', function () {
    $doctor1 = User::factory()->create(['role' => 'doktor']);
    $doctor2 = User::factory()->create(['role' => 'doktor']);
    
    // Create a visit for doctor1
    $visit1 = Visit::factory()->create(['doctor_id' => $doctor1->id]);
    
    // Create a visit for doctor2
    $visit2 = Visit::factory()->create(['doctor_id' => $doctor2->id]);

    // Doctor1 should only see their own visit
    $response = $this->actingAs($doctor1)->getJson('/api/visits');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', (string) $visit1->id);
    
    // Verify doctor2's visit is not included
    $visitIds = collect($response->json('data'))->pluck('id')->all();
    $this->assertNotContains((string) $visit2->id, $visitIds);
});

test('doctor with no visits returns empty array', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    
    // Create a visit for another doctor
    $otherDoctor = User::factory()->create(['role' => 'doktor']);
    Visit::factory()->create(['doctor_id' => $otherDoctor->id]);

    $response = $this->actingAs($doctor)->getJson('/api/visits');

    $response->assertOk()
        ->assertJsonCount(0, 'data');
});

test('guest cannot access visits endpoint', function () {
    $response = $this->getJson('/api/visits');

    $response->assertUnauthorized();
});

test('patient cannot access global visits endpoint', function () {
    $patient = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($patient)->getJson('/api/visits');

    $response->assertForbidden();
});

test('admin sees all visits from all doctors', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $doctor1 = User::factory()->create(['role' => 'doktor']);
    $doctor2 = User::factory()->create(['role' => 'doktor']);

    // Create visits for doctor1 and doctor2
    $visit1 = Visit::factory()->create(['doctor_id' => $doctor1->id]);
    $visit2 = Visit::factory()->create(['doctor_id' => $doctor2->id]);

    // Admin should see all visits
    $response = $this->actingAs($admin)->getJson('/api/visits');

    $response->assertOk()
        ->assertJsonCount(2, 'data');

    // Verify both visits are included
    $visitIds = collect($response->json('data'))->pluck('id')->all();
    $this->assertContains((string) $visit1->id, $visitIds);
    $this->assertContains((string) $visit2->id, $visitIds);
});
