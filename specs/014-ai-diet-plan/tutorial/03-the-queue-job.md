# Tutorial 03: The Queue Job & Validation

The queue job is where the actual AI generation happens. It manages the full lifecycle: loading data, calling the agent, validating the response, retrying on failure, and writing the final result to the database. This document explains every decision.

---

## Why a queue job at all?

You might ask: why not call the agent directly in the controller?

```php
// DO NOT DO THIS
public function store(Request $request, Patient $patient): JsonResponse
{
    $response = (new DietPlanAgent($patient))->prompt('Generate the plan.');
    // ...
    return $this->ok('Done!', [...]);
}
```

This fails for two reasons:

**1. Timeouts.** Claude Haiku typically responds in 5–15 seconds. PHP-FPM (the process running your Laravel app) has a request timeout, typically 30 seconds. Nginx or your load balancer has its own timeout. In production, slow AI responses will time out intermittently, leaving the user with a broken error page for a request that may have partially succeeded.

**2. User experience.** Even if the request didn't time out, making the user wait 10 seconds on a spinner is bad UX. The accepted pattern for operations that take more than 200ms is: accept the request immediately, process it asynchronously, let the client poll or receive a push notification.

The queue job solves both problems: the controller returns 202 in under 200ms, and the actual generation happens in the background.

---

## The 202 Accepted pattern

HTTP status 202 means "I received your request and it is being processed, but I don't have the result yet." It is the correct status for asynchronous operations.

```
POST /api/patients/1/diet-plans
→ 202 Accepted
{ "data": { "diet_plan": { "id": 42, "status": "pending" } } }
```

The client uses the returned `id` to poll the list endpoint until `status` changes. This is called "polling" and it's the simplest reliable approach for async status — no WebSockets, no push notifications, no long-polling required.

---

## Why not use the SDK's built-in queue API?

The SDK offers a fluent queue API:

```php
// SDK's built-in queuing
(new DietPlanAgent($patient))
    ->queue('Generate the plan.')
    ->then(function ($response) {
        $this->plan->update([...]);
    })
    ->catch(function ($e) {
        $this->plan->update(['status' => 'failed']);
    });
```

We chose NOT to use this. The reason is documented in `research.md` as D2:

**We need a manual retry loop.** The spec requires:
1. Call the agent
2. Validate the response
3. If validation fails, retry once more
4. After 2 failures, mark as `failed`

The SDK's `->then()`/`->catch()` callbacks are invoked once each — they don't support "retry once on validation failure then give up". To implement this logic cleanly, we need imperative control flow (`for` loop with `continue`/`return`), which doesn't fit the callback model.

The SDK's queuing is great for simple "fire and handle result" cases. For conditional retry logic with status transitions, a custom job is cleaner.

---

## Building the job

Create the file at `backend/app/Jobs/GenerateDietPlanJob.php`:

```php
<?php

namespace App\Jobs;

use App\Ai\Agents\DietPlanAgent;
use App\Models\PatientDietPlan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Validator;
use Throwable;

class GenerateDietPlanJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(public PatientDietPlan $plan) {}

    public function handle(): void
    {
        try {
            $patient = $this->plan->patient()->with('socioeconomic')->firstOrFail();

            $lastError = null;

            for ($attempt = 1; $attempt <= 2; $attempt++) {
                $response = (new DietPlanAgent($patient))->prompt('Generate the plan.');

                $validator = Validator::make($response->toArray(), [
                    'rationale'                   => ['required', 'string'],
                    'daily_calories'              => ['required', 'integer', 'between:1000,4000'],
                    'nutritional_goals'           => ['required', 'array'],
                    'nutritional_goals.protein_g' => ['required', 'integer', 'min:0'],
                    'nutritional_goals.carbs_g'   => ['required', 'integer', 'min:0'],
                    'nutritional_goals.fat_g'     => ['required', 'integer', 'min:0'],
                    'days'                        => ['required', 'array', 'size:7'],
                    'days.*.day'                  => ['required', 'string'],
                    'days.*.breakfast'            => ['required', 'string'],
                    'days.*.lunch'                => ['required', 'string'],
                    'days.*.dinner'               => ['required', 'string'],
                    'days.*.snack'                => ['required', 'string'],
                    'warnings'                    => ['required', 'array'],
                    'warnings.*'                  => ['string'],
                ]);

                if ($validator->fails()) {
                    $lastError = $validator->errors()->first();
                    continue;  // try again
                }

                $validated = $validator->validated();

                $this->plan->update([
                    'status'             => 'completed',
                    'rationale'          => $validated['rationale'],
                    'daily_calories'     => $validated['daily_calories'],
                    'nutritional_goals'  => $validated['nutritional_goals'],
                    'days'               => $validated['days'],
                    'warnings'           => $validated['warnings'],
                ]);

                return;  // success — exit the loop and the method
            }

            // Both attempts failed validation
            $this->plan->update([
                'status'         => 'failed',
                'failure_reason' => "AI response failed validation after 2 attempts: {$lastError}",
            ]);

        } catch (Throwable $e) {
            $this->plan->update([
                'status'         => 'failed',
                'failure_reason' => $e->getMessage(),
            ]);
        }
    }
}
```

---

## The `$tries = 1` property — why it is critical

Laravel jobs have built-in retry behavior: if a job throws an exception and is not caught inside `handle()`, Laravel requeues it automatically. The default `$tries` is usually unlimited (or 3, depending on the queue driver configuration).

**Our job catches all exceptions itself** — including all failure paths. It never lets an exception bubble up to Laravel's retry mechanism. We update the plan to `failed` and return cleanly.

BUT: `$tries = 1` is still mandatory. Here's why.

If somehow an exception leaks out of our `try/catch` (a database connection error during the `plan->update()` itself, for example), Laravel would retry the whole job. With `$tries = 1`, it retries zero times and marks the job as failed. Without it, the job might run multiple times, each time making 2 AI API calls, burning credits and producing duplicate records.

Setting `public int $tries = 1` is a safety net. Our inner try/catch is the primary defense; `$tries = 1` is insurance.

---

## Understanding the retry loop

```php
for ($attempt = 1; $attempt <= 2; $attempt++) {
    // ... call agent, validate
    
    if ($validator->fails()) {
        $lastError = $validator->errors()->first();
        continue;  // goes back to the top of the loop
    }
    
    // validation passed — save and exit
    $this->plan->update([...]);
    return;  // exits handle() entirely
}

// If we reach here, both attempts failed
$this->plan->update(['status' => 'failed', ...]);
```

There are two exits from this loop:
1. `return` inside the loop — validation passed, plan saved, job done
2. Fall through the loop — both attempts failed, plan marked failed

The `continue` keyword is important here. It skips the `$this->plan->update([status: completed...])` code and jumps to the next iteration. Without it, a failed validation would fall through to the success update.

**Why only 2 attempts and not 3 or 5?**

If the model returns invalid output, it's usually a systematic problem with the prompt or schema, not a transient error. Retrying 5 times would:
- Spend 5× the API credits
- Delay marking the plan as failed by up to 50 seconds
- Give the false impression that more retries fix structural prompt issues (they usually don't)

2 attempts is a reasonable balance: one try for the real generation, one retry to rule out an unlucky transient glitch.

---

## Why `Throwable` not `Exception`

```php
} catch (Throwable $e) {
```

PHP has two error hierarchies:
- `Exception` — thrown by application code, `throw new Exception(...)`
- `Error` — thrown by the PHP engine for fatal conditions (type errors, out-of-memory, etc.)

Both `Exception` and `Error` implement the `Throwable` interface.

The agent could fail because of a network error (an `Exception` subclass) or because of a type error in the SDK's internal code (an `Error`). We want to catch both and mark the plan as failed gracefully, rather than crashing the queue worker process. Using `Throwable` ensures we catch everything.

---

## Status transition diagram

The `status` column follows a strict state machine:

```
          POST /api/patients/{patient}/diet-plans
                        ↓
                    [pending]
                        ↓
            GenerateDietPlanJob runs
            ↙                    ↘
      [completed]             [failed]
    (plan stored)         (failure_reason set)
```

Once a plan reaches `completed` or `failed`, it never changes again. A new generation attempt creates a NEW row — the old one is preserved as history.

This is why the controller creates a new record on every POST request:

```php
$plan = PatientDietPlan::create([
    'patient_id'   => $patient->id,
    'generated_by' => $request->user()->id,
    'status'       => 'pending',
]);
GenerateDietPlanJob::dispatch($plan);
```

It never updates an existing record. History is preserved.

---

## The validation rules explained

```php
'daily_calories' => ['required', 'integer', 'between:1000,4000'],
```
1000 kcal is below medically supervised very-low-calorie diet thresholds. 4000 kcal is above typical athletic maintenance. Values outside this range are almost certainly model errors.

```php
'days' => ['required', 'array', 'size:7'],
```
The spec requires exactly 7 days. `size:7` on an array checks the count. This catches the most common structured output failure: the model returning fewer days than requested.

```php
'days.*.day' => ['required', 'string'],
'days.*.breakfast' => ['required', 'string'],
```
The `*` wildcard validates every element of the `days` array. This catches the model omitting a field from one specific day, which would otherwise be hard to detect.

```php
'warnings.*' => ['string'],
```
Note: `warnings` itself is `['required', 'array']`, but individual warnings are just `['string']` (no `required`). The array can be empty — that's valid. This is intentional: some patients have no constraints that need warning notes.

---

## Running the queue worker

In development, queue jobs don't run automatically. You need a worker:

```bash
# In a separate terminal
cd backend
php artisan queue:work
```

To test the full flow manually:
1. Start `php artisan serve` in terminal 1
2. Start `php artisan queue:work` in terminal 2
3. POST to the endpoint
4. Watch terminal 2 for the job running
5. Check the `patient_diet_plans` table for the result

For automated tests, you use `Queue::fake()` instead — see [Tutorial 05: Testing Strategy](05-testing-strategy.md).

---

## Dispatching the job from the controller

The controller creates the plan record and dispatches the job in one action:

```php
public function store(StoreDietPlanRequest $request, Patient $patient): JsonResponse
{
    $plan = PatientDietPlan::create([
        'patient_id'   => $patient->id,
        'generated_by' => $request->user()->id,
        'status'       => 'pending',
    ]);

    GenerateDietPlanJob::dispatch($plan);

    return $this->success('Diet plan generation started.', 202, [
        'diet_plan' => [
            'id'         => $plan->id,
            'status'     => $plan->status,
            'created_at' => $plan->created_at,
        ],
    ]);
}
```

`GenerateDietPlanJob::dispatch($plan)` pushes the job onto the queue. It does not run the job immediately. The queue worker picks it up and runs it asynchronously.

The `PatientDietPlan` model is serialized into the job payload (Eloquent models implement `Serializable`). When the worker runs the job, it deserializes the model, which re-fetches it from the database. This means the `$this->plan` inside `handle()` is always a fresh DB record.

Continue to: [The API Layer →](04-the-api-layer.md)
