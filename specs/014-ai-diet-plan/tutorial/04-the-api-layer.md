# Tutorial 04: The API Layer

This document covers the HTTP layer: the controller, form request, policy, resources, and routes. By the time you finish the queue job (Tutorial 03), the AI logic is done. The API layer is standard Laravel plumbing — but there are several project-specific patterns to follow carefully.

---

## The authorization model

Before writing any code, understand how authorization works for this feature:

**Three layers of authorization**, each enforcing access at a different level:

1. **Route middleware** — `auth:sanctum` ensures the user is authenticated. Unauthenticated requests get 401 before they reach the controller.

2. **FormRequest `authorize()`** — checks that the authenticated user has permission to perform the action. Patient role gets 403.

3. **Route scoping** — for the `show()` endpoint, verifies the `{dietPlan}` belongs to the `{patient}` in the URL. Mismatched plans get 404 (not 403 — the resource appears to not exist from the client's perspective).

All three must be in place. Missing any one of them creates a security gap.

---

## The policy

Create `backend/app/Policies/DietPlanPolicy.php`:

```php
<?php

namespace App\Policies;

use App\Models\{Patient, PatientDietPlan, User};

class DietPlanPolicy
{
    public function generate(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }

    public function viewAny(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }

    public function view(User $user, PatientDietPlan $dietPlan): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }
}
```

**Why a separate `DietPlanPolicy` instead of adding methods to `PatientPolicy`?**

`PatientPolicy` already handles `view`, `update`, `delete` for the patient record. Adding `generate`, `viewAny`, and `view` for diet plans to that same class creates confusion — methods like `view` would be ambiguous (view the patient? view the plan?). Separate policies for separate resources is cleaner and follows the single-responsibility principle.

**Why do all three methods return the same expression?**

Patients cannot access diet plan data. Only doctors and admins can. This is a business rule from the spec (FR-012): "AI-generated content is a clinical tool intended for practitioner review, not direct patient consumption."

If this rule changes in the future, you update one policy file, not scattered `if` checks across the codebase.

**Register the policy in `AppServiceProvider`:**

```php
// backend/app/Providers/AppServiceProvider.php
use App\Models\PatientDietPlan;
use App\Policies\DietPlanPolicy;
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    // ... existing policy registrations
    Gate::policy(PatientDietPlan::class, DietPlanPolicy::class);
}
```

Match the existing pattern (look for `Gate::policy(VitalSign::class, ...)` in the file).

---

## The form request

Create `backend/app/Http/Requests/StoreDietPlanRequest.php`:

```php
<?php

namespace App\Http\Requests;

use App\Models\PatientDietPlan;
use Illuminate\Foundation\Http\FormRequest;

class StoreDietPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('generate', [PatientDietPlan::class, $this->route('patient')]);
    }

    public function rules(): array
    {
        return [];
    }
}
```

**The critical detail: array form for `can()`.**

```php
// CORRECT
$this->user()->can('generate', [PatientDietPlan::class, $this->route('patient')])

// WRONG — routes to PatientPolicy::generate(), not DietPlanPolicy::generate()
$this->user()->can('generate', $this->route('patient'))
```

When you call `$user->can('action', $model)`, Laravel finds the policy by looking at the model's class. Passing a `Patient` instance sends you to `PatientPolicy`. But we want `DietPlanPolicy`.

The array form `[ModelClass, $extraArg]` says: "find the policy for `PatientDietPlan`, and pass `$patient` as the extra argument to the policy method." This is how you call a policy when the action is on a related model, not the model itself.

**Why are `rules()` empty?**

The POST endpoint takes no request body. Generation is triggered by the act of POSTing — there's no data to submit. Empty rules is correct, not a mistake.

---

## The controller

Create `backend/app/Http/Controllers/Api/DietPlanController.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreDietPlanRequest;
use App\Http\Resources\Api\{DietPlanResource, DietPlanSummaryResource};
use App\Jobs\GenerateDietPlanJob;
use App\Models\{Patient, PatientDietPlan};
use Illuminate\Http\JsonResponse;

class DietPlanController extends ApiController
{
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

    public function index(Patient $patient): JsonResponse
    {
        $this->authorize('viewAny', [PatientDietPlan::class, $patient]);

        $plans = $patient->dietPlans()
            ->with('doctor')
            ->latest()
            ->paginate(15);

        return $this->paginated(
            'Diet plans retrieved successfully.',
            DietPlanSummaryResource::collection($plans)
        );
    }

    public function show(Patient $patient, PatientDietPlan $dietPlan): JsonResponse
    {
        if ($dietPlan->patient_id !== $patient->id) {
            abort(404);
        }

        $this->authorize('view', $dietPlan);

        $dietPlan->load('doctor');

        return $this->ok('Diet plan retrieved successfully.', [
            'diet_plan' => new DietPlanResource($dietPlan),
        ]);
    }
}
```

**`store()` — why `$this->success(..., 202, ...)`?**

The project's `ApiResponses` trait has:
- `$this->created()` → 201 Created
- `$this->ok()` → 200 OK

But the spec requires 202 Accepted. There is no `$this->accepted()` helper. Use `$this->success('message', 202, $data)` directly — it's the underlying method that all the other helpers call.

**`index()` — why `->latest()` for ordering?**

The spec says "newest first". `->latest()` is shorthand for `->orderBy('created_at', 'desc')`. The doctor sees the most recent generation attempt at the top of the list.

**`show()` — route scoping before authorization.**

```php
if ($dietPlan->patient_id !== $patient->id) {
    abort(404);
}
$this->authorize('view', $dietPlan);
```

The route scoping check comes FIRST. This is the `VisitController` pattern from the project. Why 404 instead of 403?

If the route is `GET /patients/1/diet-plans/99` and plan 99 belongs to patient 2, the correct response is 404 (the resource doesn't exist at this URL, from the caller's perspective). Returning 403 would confirm the existence of plan 99, which is an information disclosure concern.

The authorization check comes after scoping. This ordering means: first confirm the resource exists in this context, then confirm the user has permission to access it.

---

## The resources

Resources control what fields appear in the API response. This feature uses two:

`DietPlanSummaryResource` — for list responses (omits heavy fields like `days` and `rationale`)
`DietPlanResource` — for detail responses (includes everything)

**Why two resources?**

The paginated list endpoint might return 20–50 plans. Each plan's `days` field is a JSON array of 7 objects, each with 5 string fields. Transmitting all of that on every list response is wasteful — the doctor is scanning a list to find a plan, not reading all 7 days for each entry. The summary resource omits the heavy fields. The detail resource includes them. This is a deliberate performance optimization documented in `contracts/api-endpoints.md`.

**`DietPlanSummaryResource`:**

```php
<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DietPlanSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'status'     => $this->status,
            'generated_by' => $this->when(
                $this->doctor !== null,
                fn () => ['id' => $this->doctor->id, 'name' => $this->doctor->name]
            ),
            'daily_calories'    => $this->daily_calories,
            'nutritional_goals' => $this->nutritional_goals,
            'failure_reason'    => $this->when($this->status === 'failed', $this->failure_reason),
            'created_at'        => $this->created_at,
        ];
    }
}
```

**`DietPlanResource`** (extends with the full data fields):

```php
<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DietPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'status'     => $this->status,
            'generated_by' => $this->when(
                $this->doctor !== null,
                fn () => ['id' => $this->doctor->id, 'name' => $this->doctor->name]
            ),
            'rationale'         => $this->rationale,
            'daily_calories'    => $this->daily_calories,
            'nutritional_goals' => $this->nutritional_goals,
            'days'              => $this->days,
            'warnings'          => $this->warnings,
            'failure_reason'    => $this->when($this->status === 'failed', $this->failure_reason),
            'created_at'        => $this->created_at,
        ];
    }
}
```

**`$this->when($condition, $value)` in resources:**

This method conditionally includes a field. `$this->when($this->status === 'failed', $this->failure_reason)` means: include `failure_reason` in the JSON only when the plan's status is `failed`. For completed or pending plans, the field is omitted entirely from the response.

This keeps the response clean — the frontend doesn't have to handle `"failure_reason": null` on successful plans.

---

## The routes

Add to `backend/routes/api.php` inside the `auth:sanctum` middleware group:

```php
Route::prefix('patients/{patient}')->group(function () {
    // ... existing visit routes

    Route::post('diet-plans', [DietPlanController::class, 'store']);
    Route::get('diet-plans', [DietPlanController::class, 'index']);
    Route::get('diet-plans/{dietPlan}', [DietPlanController::class, 'show']);
});
```

Or if the project uses nested `apiResource`:

```php
Route::apiResource('patients.diet-plans', DietPlanController::class)
    ->only(['store', 'index', 'show']);
```

Check `routes/api.php` to see which pattern the existing visit routes use, and match it.

**Why are diet plan routes inside the `auth:sanctum` group?**

Because `auth:sanctum` is middleware that runs for every request in the group, rejecting unauthenticated requests with 401 before they reach the controller. Without it, someone could hit these endpoints without being logged in.

**Why NOT inside the role middleware (`role:admin,doktor`)?**

Looking at how visit routes are structured in this project, some routes are placed outside role middleware to allow patient access (with policy-level role checks). Diet plan routes are inside `auth:sanctum` only — the role check happens in the policy (`DietPlanPolicy` rejects patient role with 403). This is consistent with the project's approach of policy-level role enforcement.

---

## Request lifecycle summary

Here is the full request lifecycle for `POST /api/patients/1/diet-plans`:

```
1. Request arrives at Laravel
2. auth:sanctum middleware → not logged in? 401. Logged in? continue.
3. Route model binding resolves {patient} → Patient::findOrFail(1) → not found? 404
4. StoreDietPlanRequest::authorize() → $user->can('generate', [PatientDietPlan::class, $patient])
   → DietPlanPolicy::generate($user, $patient) → patient role? 403. Admin/doctor? continue.
5. StoreDietPlanRequest::rules() → validated (empty rules, always passes)
6. DietPlanController::store() runs:
   a. Creates PatientDietPlan record (status: pending)
   b. Dispatches GenerateDietPlanJob to queue
   c. Returns 202 with { diet_plan: { id, status, created_at } }
7. Response returns to client
8. (background) Queue worker picks up GenerateDietPlanJob
   a. Calls DietPlanAgent, validates response
   b. Updates PatientDietPlan to completed or failed
```

Steps 1–7 happen in under 200ms. Step 8 happens in 5–30 seconds in the background.

Continue to: [Testing Strategy →](05-testing-strategy.md)
