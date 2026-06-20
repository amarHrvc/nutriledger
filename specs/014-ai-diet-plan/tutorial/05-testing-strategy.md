# Tutorial 05: Testing Strategy

Testing AI features requires a different approach than testing regular CRUD endpoints. The challenge: AI responses are non-deterministic, slow, and cost money. You cannot use real API calls in tests.

This document explains how to test this feature correctly using the SDK's fake mechanism.

---

## The core rule: no real API calls in tests

Every test file for this feature must set up fakes before doing anything:

```php
use App\Ai\Agents\DietPlanAgent;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Queue::fake();
    DietPlanAgent::fake([json_encode([...])]);
});
```

`Queue::fake()` prevents jobs from actually running. `DietPlanAgent::fake([...])` intercepts agent calls and returns a controlled response.

Without these, your tests would:
- Make real HTTP requests to Anthropic's API
- Take 5–15 seconds per test
- Cost real money
- Fail in CI without internet access or a valid API key
- Produce different output each time (flaky tests)

---

## `Queue::fake()` — intercepting job dispatch

When `Queue::fake()` is active:
- `GenerateDietPlanJob::dispatch($plan)` silently no-ops — the job goes nowhere
- No queue worker is needed
- You can assert that the job WAS dispatched:

```php
test('store dispatches the generation job', function () {
    Queue::fake();
    
    $doctor = User::factory()->doctor()->create();
    $patient = Patient::factory()->create();
    
    $this->actingAs($doctor)
        ->postJson("/api/patients/{$patient->id}/diet-plans")
        ->assertStatus(202);
    
    Queue::assertPushed(GenerateDietPlanJob::class);
});
```

`Queue::assertPushed(GenerateDietPlanJob::class)` fails the test if the job was never dispatched. This is how you verify that the async machinery is wired up correctly without actually running the job.

You can also assert the job was pushed with specific data:

```php
Queue::assertPushed(GenerateDietPlanJob::class, function ($job) use ($plan) {
    return $job->plan->id === $plan->id;
});
```

---

## `DietPlanAgent::fake()` — intercepting agent calls

When you need to test code that calls the agent (like `GenerateDietPlanJob::handle()`), you need to fake the agent response:

```php
DietPlanAgent::fake([json_encode([
    'rationale'         => 'Balanced plan for a moderately active adult.',
    'daily_calories'    => 1800,
    'nutritional_goals' => ['protein_g' => 90, 'carbs_g' => 220, 'fat_g' => 60],
    'days'              => array_fill(0, 7, [
        'day'       => 'Monday',
        'breakfast' => 'Oatmeal with banana',
        'lunch'     => 'Lentil soup with bread',
        'dinner'    => 'Grilled chicken with vegetables',
        'snack'     => 'Apple',
    ]),
    'warnings' => [],
])]);
```

**Why `json_encode([...])`?**

For structured output agents, the SDK's fake mechanism expects the response to be the JSON-encoded string that the model would return. The inner array is your actual structured data. Wrap it with `json_encode()`.

**The `array_fill(0, 7, [...])` trick:**

`array_fill(0, 7, $item)` creates an array of 7 identical items. This is shorthand for writing out all 7 days in the fake response. For tests that don't care about the specific day names, this is fine. For tests that check specific day content, write out all 7 explicitly.

---

## Testing the happy path

```php
test('doctor can trigger diet plan generation and receives 202', function () {
    Queue::fake();
    
    $doctor = User::factory()->doctor()->create();
    $patient = Patient::factory()->create();
    
    $response = $this->actingAs($doctor)
        ->postJson("/api/patients/{$patient->id}/diet-plans")
        ->assertStatus(202)
        ->assertJsonStructure([
            'message',
            'status',
            'data' => [
                'diet_plan' => ['id', 'status', 'created_at'],
            ],
        ]);
    
    expect($response->json('data.diet_plan.status'))->toBe('pending');
    
    $this->assertDatabaseHas('patient_diet_plans', [
        'patient_id'   => $patient->id,
        'generated_by' => $doctor->id,
        'status'       => 'pending',
    ]);
    
    Queue::assertPushed(GenerateDietPlanJob::class);
});
```

This test verifies:
- The 202 status code
- The response structure matches the contract
- The DB record was created with `status=pending`
- The job was dispatched

What it does NOT verify: that the job actually generates a plan. That's covered by a separate job test.

---

## Testing the job directly

For the job, you bypass the queue and run the job synchronously using `dispatchSync()` or by calling `handle()` directly:

```php
test('job updates plan to completed when agent returns valid response', function () {
    DietPlanAgent::fake([json_encode([
        'rationale'         => 'Test rationale.',
        'daily_calories'    => 1800,
        'nutritional_goals' => ['protein_g' => 90, 'carbs_g' => 220, 'fat_g' => 60],
        'days'              => array_fill(0, 7, [
            'day'       => 'Monday', 'breakfast' => 'Oatmeal',
            'lunch'     => 'Salad', 'dinner' => 'Chicken', 'snack' => 'Apple',
        ]),
        'warnings' => [],
    ])]);
    
    $doctor = User::factory()->doctor()->create();
    $patient = Patient::factory()->has(
        PatientSocioeconomic::factory(), 'socioeconomic'
    )->create();
    $plan = PatientDietPlan::factory()->pending()->create([
        'patient_id'   => $patient->id,
        'generated_by' => $doctor->id,
    ]);
    
    GenerateDietPlanJob::dispatchSync($plan);
    
    $plan->refresh();
    expect($plan->status)->toBe('completed');
    expect($plan->daily_calories)->toBe(1800);
    expect($plan->days)->toHaveCount(7);
    expect($plan->rationale)->not->toBeNull();
});
```

`dispatchSync()` runs the job immediately in the current process, bypassing the queue. The fake agent response is returned when the job calls `(new DietPlanAgent($patient))->prompt(...)`.

---

## Testing the failure path

This is the most important path to test — it verifies that bad AI output doesn't crash the system.

**Failure from invalid structured output:**

```php
test('job marks plan as failed when agent returns invalid days count', function () {
    DietPlanAgent::fake([json_encode([
        'rationale'         => 'Bad plan.',
        'daily_calories'    => 1800,
        'nutritional_goals' => ['protein_g' => 90, 'carbs_g' => 220, 'fat_g' => 60],
        'days'              => array_fill(0, 3, [  // ONLY 3 DAYS — fails size:7 validation
            'day' => 'Monday', 'breakfast' => 'Toast',
            'lunch' => 'Soup', 'dinner' => 'Rice', 'snack' => 'Apple',
        ]),
        'warnings' => [],
    ])]);
    
    // ... setup plan ...
    
    GenerateDietPlanJob::dispatchSync($plan);
    
    $plan->refresh();
    expect($plan->status)->toBe('failed');
    expect($plan->failure_reason)->not->toBeNull();
    expect($plan->days)->toBeNull();  // never saved — plan stayed null
});
```

**Failure from exception:**

To simulate an exception from the agent, configure the fake to throw:

```php
DietPlanAgent::fake(function () {
    throw new \RuntimeException('Connection timeout');
});

// ... run job ...

$plan->refresh();
expect($plan->status)->toBe('failed');
expect($plan->failure_reason)->toBe('Connection timeout');
```

**Why test the failure path?**

You need confidence that when the AI misbehaves, the system degrades gracefully. Without this test, the failure handling code in `GenerateDietPlanJob` might have a bug that was never caught, and the first time it fails in production, it crashes rather than storing a clean failure status.

---

## Testing access control

Every endpoint must have 401 (unauthenticated) and 403 (wrong role) tests:

```php
test('unauthenticated request returns 401', function () {
    $patient = Patient::factory()->create();
    
    $this->postJson("/api/patients/{$patient->id}/diet-plans")
        ->assertUnauthorized();  // assertUnauthorized() checks for 401
});

test('patient role cannot trigger generation', function () {
    $patientUser = User::factory()->patient()->create();
    $patient = Patient::factory()->for($patientUser)->create();
    
    $this->actingAs($patientUser)
        ->postJson("/api/patients/{$patient->id}/diet-plans")
        ->assertForbidden();  // assertForbidden() checks for 403
});

test('admin can trigger generation', function () {
    Queue::fake();
    $admin = User::factory()->admin()->create();
    $patient = Patient::factory()->create();
    
    $this->actingAs($admin)
        ->postJson("/api/patients/{$patient->id}/diet-plans")
        ->assertStatus(202);
});
```

Use `assertUnauthorized()` and `assertForbidden()`, not `assertStatus(401)` or `assertStatus(403)`. The named methods are more expressive and project convention (see CLAUDE.md).

---

## Testing route scoping

The `show()` endpoint should return 404 if the `{dietPlan}` doesn't belong to the `{patient}`:

```php
test('show returns 404 when diet plan belongs to different patient', function () {
    $doctor = User::factory()->doctor()->create();
    $patient1 = Patient::factory()->create();
    $patient2 = Patient::factory()->create();
    
    // Plan belongs to patient1
    $plan = PatientDietPlan::factory()->completed()->create([
        'patient_id' => $patient1->id,
    ]);
    
    // But we request it under patient2's URL
    $this->actingAs($doctor)
        ->getJson("/api/patients/{$patient2->id}/diet-plans/{$plan->id}")
        ->assertNotFound();
});
```

This confirms the route scoping check (`if ($dietPlan->patient_id !== $patient->id) abort(404)`) is working. Without this test, you might remove that check during refactoring and never notice until production data leaks between patients.

---

## Testing the list endpoint structure

```php
test('list returns paginated history newest first', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = Patient::factory()->create();
    
    // Create 3 plans at different times
    $old = PatientDietPlan::factory()->completed()->create([
        'patient_id'   => $patient->id,
        'generated_by' => $doctor->id,
        'created_at'   => now()->subDays(3),
    ]);
    $new = PatientDietPlan::factory()->pending()->create([
        'patient_id'   => $patient->id,
        'generated_by' => $doctor->id,
        'created_at'   => now(),
    ]);
    
    $response = $this->actingAs($doctor)
        ->getJson("/api/patients/{$patient->id}/diet-plans")
        ->assertOk()
        ->assertJsonStructure([
            'data' => [['id', 'status', 'created_at']],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            'links' => ['first', 'last', 'prev', 'next'],
        ]);
    
    // Newest first
    expect($response->json('data.0.id'))->toBe($new->id);
    expect($response->json('data.1.id'))->toBe($old->id);
    
    // List items should NOT include days or rationale
    expect($response->json('data.0'))->not->toHaveKey('days');
    expect($response->json('data.0'))->not->toHaveKey('rationale');
});
```

The `not->toHaveKey('days')` assertion is important — it verifies that the summary resource correctly omits the heavy fields. If someone accidentally changes `DietPlanSummaryResource` to include `days`, this test catches it.

---

## Full test file structure reference

The project has three test files for this feature:

| File | What it tests |
|---|---|
| `tests/Feature/diet-plans/DietPlanGenerateTest.php` | `POST /api/patients/{patient}/diet-plans` — 202 response, job dispatched, DB record created, 401/403 |
| `tests/Feature/diet-plans/DietPlanListTest.php` | `GET /api/patients/{patient}/diet-plans` — pagination, field presence/absence, 401/403 |
| `tests/Feature/diet-plans/DietPlanShowTest.php` | `GET /api/patients/{patient}/diet-plans/{dietPlan}` — full plan fields, route scoping, 401/403 |
| `tests/Feature/diet-plans/DietPlanFailureTest.php` | Job failure paths — invalid output, exception, history preservation |

Job tests are in the failure test file because they specifically test the failure handling code. The happy path job behavior is implicitly tested through the full integration in other tests.

---

## Running tests

```bash
# Run all diet plan tests
cd backend && php artisan test tests/Feature/diet-plans/

# Run a single test file
cd backend && php artisan test tests/Feature/diet-plans/DietPlanGenerateTest.php

# Run a specific test by name filter
cd backend && php artisan test --filter="doctor can trigger"

# Run with output (shows each test name)
cd backend && php artisan test tests/Feature/diet-plans/ --verbose
```

Continue to: [Frontend Integration →](06-frontend-integration.md)
