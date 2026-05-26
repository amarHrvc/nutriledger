<?php

use App\Ai\Agents\DietPlanAgent;
use App\Jobs\GenerateDietPlanJob;
use App\Models\Patient;
use App\Models\PatientDietPlan;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

// --- store ---

test('guest cannot generate diet plan', function () {
    $patient = Patient::factory()->create();

    $this->postJson("/api/patients/{$patient->id}/diet-plans")
        ->assertUnauthorized();
});

test('patient cannot generate diet plan', function () {
    $user    = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->postJson("/api/patients/{$patient->id}/diet-plans")
        ->assertForbidden();
});

test('doctor can generate diet plan', function () {
    Queue::fake();

    $doctor  = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    $this->actingAs($doctor)
        ->postJson("/api/patients/{$patient->id}/diet-plans")
        ->assertStatus(202)
        ->assertJsonStructure(['message', 'status', 'data' => ['diet_plan' => ['id', 'status', 'created_at']]]);

    Queue::assertPushed(GenerateDietPlanJob::class);
});

test('admin can generate diet plan', function () {
    Queue::fake();

    $admin   = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    $this->actingAs($admin)
        ->postJson("/api/patients/{$patient->id}/diet-plans")
        ->assertStatus(202);

    Queue::assertPushed(GenerateDietPlanJob::class);
});

test('store creates a pending diet plan record', function () {
    Queue::fake();

    $doctor  = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    $this->actingAs($doctor)
        ->postJson("/api/patients/{$patient->id}/diet-plans");

    $this->assertDatabaseHas('patient_diet_plans', [
        'patient_id'   => $patient->id,
        'generated_by' => $doctor->id,
        'status'       => 'pending',
    ]);
});

// --- index ---

test('guest cannot list diet plans', function () {
    $patient = Patient::factory()->create();

    $this->getJson("/api/patients/{$patient->id}/diet-plans")
        ->assertUnauthorized();
});

test('patient cannot list diet plans', function () {
    $user    = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->getJson("/api/patients/{$patient->id}/diet-plans")
        ->assertForbidden();
});

test('doctor can list diet plans', function () {
    $doctor  = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    PatientDietPlan::factory()->completed()->create([
        'patient_id'   => $patient->id,
        'generated_by' => $doctor->id,
    ]);

    $this->actingAs($doctor)
        ->getJson("/api/patients/{$patient->id}/diet-plans")
        ->assertOk()
        ->assertJsonStructure(['message', 'status', 'data' => ['diet_plans']]);
});

test('index returns only diet plans for the given patient', function () {
    $doctor   = User::factory()->create(['role' => 'doktor']);
    $patient  = Patient::factory()->create();
    $other    = Patient::factory()->create();

    PatientDietPlan::factory()->completed()->create(['patient_id' => $patient->id, 'generated_by' => $doctor->id]);
    PatientDietPlan::factory()->completed()->create(['patient_id' => $other->id, 'generated_by' => $doctor->id]);

    $response = $this->actingAs($doctor)
        ->getJson("/api/patients/{$patient->id}/diet-plans")
        ->assertOk();

    expect($response->json('data.diet_plans'))->toHaveCount(1);
});

test('index returns empty array when patient has no diet plans', function () {
    $doctor  = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    $response = $this->actingAs($doctor)
        ->getJson("/api/patients/{$patient->id}/diet-plans")
        ->assertOk();

    expect($response->json('data.diet_plans'))->toBeEmpty();
});

test('index returns plans newest first', function () {
    $doctor  = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    $old = PatientDietPlan::factory()->completed()->create([
        'patient_id'   => $patient->id,
        'generated_by' => $doctor->id,
        'created_at'   => now()->subDays(3),
    ]);
    $new = PatientDietPlan::factory()->completed()->create([
        'patient_id'   => $patient->id,
        'generated_by' => $doctor->id,
        'created_at'   => now(),
    ]);

    $response = $this->actingAs($doctor)
        ->getJson("/api/patients/{$patient->id}/diet-plans")
        ->assertOk();

    expect($response->json('data.diet_plans.0.id'))->toBe($new->id);
    expect($response->json('data.diet_plans.1.id'))->toBe($old->id);
});

test('index items do not include days or rationale', function () {
    $doctor  = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    PatientDietPlan::factory()->completed()->create([
        'patient_id'   => $patient->id,
        'generated_by' => $doctor->id,
    ]);

    $response = $this->actingAs($doctor)
        ->getJson("/api/patients/{$patient->id}/diet-plans")
        ->assertOk();

    $item = $response->json('data.diet_plans.0');

    expect($item)->not->toHaveKey('days');
    expect($item)->not->toHaveKey('rationale');
});

// --- show ---

test('guest cannot view diet plan', function () {
    $patient  = Patient::factory()->create();
    $dietPlan = PatientDietPlan::factory()->completed()->create(['patient_id' => $patient->id]);

    $this->getJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}")
        ->assertUnauthorized();
});

test('patient cannot view diet plan', function () {
    $user     = User::factory()->create(['role' => 'pacijent']);
    $patient  = Patient::factory()->create(['user_id' => $user->id]);
    $dietPlan = PatientDietPlan::factory()->completed()->create(['patient_id' => $patient->id]);

    $this->actingAs($user)
        ->getJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}")
        ->assertForbidden();
});

test('doctor can view diet plan', function () {
    $doctor   = User::factory()->create(['role' => 'doktor']);
    $patient  = Patient::factory()->create();
    $dietPlan = PatientDietPlan::factory()->completed()->create([
        'patient_id'   => $patient->id,
        'generated_by' => $doctor->id,
    ]);

    $this->actingAs($doctor)
        ->getJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}")
        ->assertOk()
        ->assertJsonStructure([
            'message', 'status',
            'data' => [
                'diet_plan' => [
                    'id', 'status', 'rationale', 'dailyCalories',
                    'nutritionalGoals', 'days', 'warnings', 'createdAt',
                ],
            ],
        ]);
});

test('show returns 404 when diet plan belongs to different patient', function () {
    $doctor   = User::factory()->create(['role' => 'doktor']);
    $patient  = Patient::factory()->create();
    $other    = Patient::factory()->create();
    $dietPlan = PatientDietPlan::factory()->completed()->create(['patient_id' => $other->id]);

    $this->actingAs($doctor)
        ->getJson("/api/patients/{$patient->id}/diet-plans/{$dietPlan->id}")
        ->assertNotFound();
});
