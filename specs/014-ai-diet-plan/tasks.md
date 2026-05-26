# Tasks: AI Diet Plan Generator (014)

**Input**: Design documents from `specs/014-ai-diet-plan/`
**Branch**: `014-ai-diet-plan`
**Stack**: Laravel 12 / PHP 8.3 (BE) · React + JavaScript (FE)
**TDD Discipline**: Constitution Principle III is NON-NEGOTIABLE — tests written FIRST (RED), then implementation (GREEN), then cleanup (REFACTOR). Each TDD phase is a separate atomic commit.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Parallelizable — different files, no shared dependency on an incomplete task
- **[USn]**: User story label — maps to spec.md user stories
- **Exact file paths** are included in every task description

---

## Phase 1: Setup (Laravel AI SDK)

**Purpose**: Install and configure the Laravel AI SDK. Must complete before any feature code is written.

- [ ] T001 Install `laravel/ai` package: run `composer require laravel/ai` in `backend/`, then `php artisan vendor:publish --provider="Laravel\Ai\AiServiceProvider"`, then `php artisan migrate` — the SDK publishes `config/ai.php` and two migrations (`agent_conversations`, `agent_conversation_messages`)
- [ ] T002 Configure Anthropic provider in `backend/config/ai.php` — set `'default' => env('AI_DEFAULT_PROVIDER', 'anthropic')` and ensure the `anthropic` key has `'api_key' => env('ANTHROPIC_API_KEY')`; add `ANTHROPIC_API_KEY=` and `AI_DEFAULT_PROVIDER=anthropic` to `backend/.env.example`; add real key to `backend/.env`
- [ ] T003 Verify `jobs` table exists — run `php artisan migrate:status | grep jobs`; if absent run `php artisan queue:table && php artisan migrate`; confirm `QUEUE_CONNECTION=database` in `backend/.env`

**Checkpoint**: `php artisan tinker --execute="app(\Laravel\Ai\Contracts\Agent::class);"` does not throw. `php artisan migrate:status` shows `0001_01_01_000003_create_agent_conversations_table` as ran.

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Database layer, model, factory, and policy must exist before any user story can be implemented. US1, US2, US3, and US4 all depend on this phase.

**⚠️ CRITICAL**: No user story work can begin until this phase is complete.

- [ ] T004 Create migration `backend/database/migrations/xxxx_create_patient_diet_plans_table.php` — columns: `id`, `patient_id` (unsignedBigInteger, FK→patients.id, cascade delete), `generated_by` (unsignedBigInteger, FK→users.id, restrict delete), `status` enum(`pending`,`completed`,`failed`) default `pending`, `rationale` text nullable, `daily_calories` integer nullable, `nutritional_goals` json nullable, `days` json nullable, `warnings` json nullable, `failure_reason` varchar(500) nullable, `timestamps`; run `php artisan migrate`
- [ ] T005 [P] Create `backend/app/Models/PatientDietPlan.php` — `$fillable` covers all non-PK columns; `casts()` returns `['nutritional_goals' => 'array', 'days' => 'array', 'warnings' => 'array']`; define two explicit relationship methods: `public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }` and `public function doctor(): BelongsTo { return $this->belongsTo(User::class, 'generated_by'); }` — **the method name `doctor()` IS the accessor; do NOT use `->as()` which is a BelongsToMany pivot alias**; add `scopeCompleted($query)` (`where('status','completed')`) and `scopeLatestCompleted($query)` (`completed()->latest()`) scopes; add imports: `use Illuminate\Database\Eloquent\Relations\BelongsTo;`
- [ ] T006 [P] Create `backend/database/factories/PatientDietPlanFactory.php` — default state: `status=pending`; states: `pending()`, `completed()` (populate `rationale`, `daily_calories`, `nutritional_goals`, `days` as 7-element array, `warnings`), `failed()` (set `status=failed`, `failure_reason`)
- [ ] T007 Add `dietPlans(): HasMany` relationship to `backend/app/Models/Patient.php`: `public function dietPlans(): HasMany { return $this->hasMany(PatientDietPlan::class); }` — add imports: `use App\Models\PatientDietPlan; use Illuminate\Database\Eloquent\Relations\HasMany;`
- [ ] T008 [P] Create `backend/app/Policies/DietPlanPolicy.php` — three methods: `generate(User $user, Patient $patient): bool`, `viewAny(User $user, Patient $patient): bool`, `view(User $user, PatientDietPlan $dietPlan): bool` — all return `$user->isAdmin() || $user->isDoctor()`; add imports: `use App\Models\{Patient, PatientDietPlan, User};`
- [ ] T009 Register `DietPlanPolicy` in `backend/app/Providers/AppServiceProvider.php` — in the `boot()` method add `Gate::policy(PatientDietPlan::class, DietPlanPolicy::class);` matching the existing pattern (e.g. `Gate::policy(VitalSign::class, VitalSignPolicy::class)`); add imports: `use App\Models\PatientDietPlan; use App\Policies\DietPlanPolicy;`

**Checkpoint**: `php artisan migrate:status` shows `patient_diet_plans` as ran. `PatientDietPlan::factory()->completed()->make()` returns a valid model instance with a `doctor()` method. `php artisan tinker --execute="Gate::getPolicyFor(PatientDietPlan::class);"` returns `DietPlanPolicy`.

---

## Phase 3: User Story 1 — Generate Diet Plan (Priority: P1) 🎯 MVP

**Goal**: Doctor POSTs to trigger generation; system returns 202 immediately; `GenerateDietPlanJob` creates the `DietPlanAgent`, validates output, and updates the plan record to `completed` or `failed`.

**Independent Test**: `POST /api/patients/{patient}/diet-plans` as a doctor returns 202 with `status: pending`; queue is faked; plan record is created in DB.

**US4 coverage**: 401 (guest) and 403 (patient) assertions are included in the RED phase test for this story.

### RED Phase — Write failing tests first, confirm they FAIL before GREEN

- [ ] T010 [US1] Create `backend/tests/Feature/diet-plans/DietPlanGenerateTest.php` using Pest `test()` function syntax (NOT PHPUnit class-based) — cover: (a) doctor triggers generation → 202, `Queue::fake()` shows `GenerateDietPlanJob` dispatched, plan record created in DB with `status=pending`; (b) admin triggers generation → 202; (c) guest → 401; (d) patient role → 403; (e) non-existent patient → 404; (f) response JSON has `data.diet_plan.id` and `data.diet_plan.status === "pending"`; use `Queue::fake()` + `Agent::fake(DietPlanAgent::class, [...])` as setup in `beforeEach()`; run `php artisan test tests/Feature/diet-plans/DietPlanGenerateTest.php` and confirm ALL tests fail (RED — they should fail with 404/500 not 202)

### GREEN Phase — Implement to make T010 tests pass

- [ ] T011 [US1] Scaffold the agent with `php artisan make:agent DietPlanAgent --structured` in `backend/` (creates `app/Ai/Agents/DietPlanAgent.php`); then flesh it out: add PHP attributes `#[Provider(Lab::Anthropic)]`, `#[Model('claude-haiku-4-5-20251001')]`, `#[Temperature(0.3)]` — **there is NO `#[UseCheapestModel]` attribute in the SDK; the model is pinned explicitly**; add imports: `use Laravel\Ai\Attributes\{Model, Provider, Temperature}; use Laravel\Ai\Enums\Lab; use Illuminate\Contracts\JsonSchema\JsonSchema;`; constructor: `public function __construct(private Patient $patient)` (expects `socioeconomic` relation already loaded); `instructions()` returns a system prompt string containing computed age (`now()->diffInYears($this->patient->date_of_birth)`), gender, blood_type, allergies, medical_notes, dietary_restrictions, food_security_status, income_level, physical_activity_level, smoking_status, alcohol_consumption — include hard rules: "never include allergen items" and "meals must be affordable given food security and income level"; `schema(JsonSchema $schema): array` returns: `'rationale' => $schema->string()->required()`, `'daily_calories' => $schema->integer()->required()`, `'nutritional_goals' => $schema->object(['protein_g' => $schema->integer()->required(), 'carbs_g' => $schema->integer()->required(), 'fat_g' => $schema->integer()->required()])->required()`, `'days' => $schema->array($schema->object(['day' => ..., 'breakfast' => ..., 'lunch' => ..., 'dinner' => ..., 'snack' => ...]))->required()`, `'warnings' => $schema->array($schema->string())->required()`
- [ ] T012 [US1] Create `backend/app/Jobs/GenerateDietPlanJob.php` — implements `ShouldQueue`; add `public int $tries = 1;` property — **this is critical: without it, Laravel's default of 3 retries will re-run the entire job (including both AI attempts) 3 times for a total of 6 API calls**; constructor: `public function __construct(public PatientDietPlan $plan) {}`; `handle()` logic: (1) load `$patient = $this->plan->patient()->with('socioeconomic')->firstOrFail()`; (2) `$lastError = null;` then loop up to 2 attempts: call `$response = (new DietPlanAgent($patient))->prompt('Generate the plan.')`, run `$validator = Validator::make($response->toArray(), [...rules from data-model.md...])`, if `$validator->fails()` set `$lastError = $validator->errors()->first()` and `continue`, else update plan to `completed` with all fields and `return`; (3) after loop exits without return: update plan to `failed` with `failure_reason = $lastError`; (4) wrap entire body in `try/catch(Throwable $e)` → update plan to `failed` with `failure_reason = $e->getMessage()`; add imports: `use App\Ai\Agents\DietPlanAgent; use Illuminate\Support\Facades\Validator; use Throwable;`
- [ ] T013 [P] [US1] Create `backend/app/Http/Requests/StoreDietPlanRequest.php` — `authorize()`: `return $this->user()->can('generate', [PatientDietPlan::class, $this->route('patient')]);` — **must use array form `[ModelClass, $instance]` so Laravel resolves `DietPlanPolicy::generate()` not `PatientPolicy::generate()`**; `rules()` returns `[]` (no request body needed); add imports: `use App\Models\PatientDietPlan;`
- [ ] T014 [US1] Create `backend/app/Http/Controllers/Api/DietPlanController.php` extending `ApiController` — implement `store(StoreDietPlanRequest $request, Patient $patient): JsonResponse`: create plan with `PatientDietPlan::create(['patient_id' => $patient->id, 'generated_by' => $request->user()->id, 'status' => 'pending'])`, dispatch `GenerateDietPlanJob::dispatch($plan)`, return `$this->success('Diet plan generation started.', 202, ['diet_plan' => ['id' => $plan->id, 'status' => $plan->status, 'created_at' => $plan->created_at]])` — **use `$this->success('...', 202, [...])` not `$this->created()` — `created()` returns 201 but the spec requires 202 Accepted**; add imports: `use App\Http\Requests\StoreDietPlanRequest; use App\Jobs\GenerateDietPlanJob; use App\Models\{Patient, PatientDietPlan}; use Illuminate\Http\JsonResponse;`; also register `POST /api/patients/{patient}/diet-plans` route pointing to `[DietPlanController::class, 'store']` in `backend/routes/api.php` inside the `auth:sanctum` middleware group — **route registration is part of GREEN because the tests in T015 require the route to exist**
- [ ] T015 [US1] Run `php artisan test tests/Feature/diet-plans/DietPlanGenerateTest.php` — confirm ALL tests pass (GREEN); if any test fails, diagnose — common issues: missing import, wrong policy array syntax in T013, route not yet registered

### REFACTOR Phase

- [ ] T016 [US1] Run `vendor/bin/pint --dirty` on all new files from Phase 3; fix any formatting violations (REFACTOR)
- [ ] T017 [US1] Run `composer run analyse` (Larastan level 5) — fix any type errors; run `php artisan test tests/Feature/diet-plans/DietPlanGenerateTest.php` to confirm suite still green after formatting; commit REFACTOR phase (REFACTOR)

**Checkpoint**: US1 complete. Doctor POSTs → 202 returned immediately. `GenerateDietPlanJob` is on the queue. Access control enforced (401/403). Pint and Larastan green.

---

## Phase 4: User Story 2 — View Diet Plan History (Priority: P2)

**Goal**: Doctor GETs paginated history of all plans for a patient (newest first); GETs full detail of a specific plan. US4 coverage embedded.

**Independent Test**: `GET /api/patients/{patient}/diet-plans` returns paginated list with correct metadata; `GET /api/patients/{patient}/diet-plans/{id}` returns full plan including 7-day meals.

### RED Phase — Write failing tests first, confirm they FAIL before GREEN

- [ ] T018 [P] [US2] Create `backend/tests/Feature/diet-plans/DietPlanListTest.php` using Pest `test()` function syntax — cover: (a) doctor gets paginated list newest-first with `data` (array), `meta` (current_page, last_page, per_page, total), `links` (first, last, prev, next); (b) list items contain `id`, `status`, `generated_by.name`, `daily_calories`, `created_at` but NOT `days` or `rationale` (omitted from summary); (c) empty list returns `data: []` not 404; (d) guest → 401; (e) patient role → 403; (f) non-existent patient → 404 — confirm ALL fail (RED)
- [ ] T019 [P] [US2] Create `backend/tests/Feature/diet-plans/DietPlanShowTest.php` using Pest `test()` function syntax — cover: (a) doctor gets full plan detail including `rationale`, `days` (array of 7), `nutritional_goals`, `warnings`; (b) `dietPlan` belonging to a different patient → 404 (route scoping — plan exists but doesn't belong to this patient); (c) guest → 401; (d) patient role → 403; (e) completed plan returns all fields; (f) failed plan returns `failure_reason` and omits `days`/`rationale` (null) — confirm ALL fail (RED)

### GREEN Phase

- [ ] T020 [P] [US2] Create `backend/app/Http/Resources/Api/DietPlanSummaryResource.php` — returns: `id`, `status`, `generated_by` (nullable object with `id` and `name` from `$this->doctor`), `daily_calories` (when completed), `nutritional_goals` (when completed), `failure_reason` (when failed — use `$this->when($this->status === 'failed', $this->failure_reason)`), `created_at`; deliberately omits `rationale`, `days`, `warnings`
- [ ] T021 [P] [US2] Create `backend/app/Http/Resources/Api/DietPlanResource.php` — extends the summary fields and additionally includes `rationale`, `days`, `warnings`; use `$this->when()` for nullable fields
- [ ] T022 [US2] Implement `DietPlanController::index(Patient $patient): JsonResponse` — `$this->authorize('viewAny', [PatientDietPlan::class, $patient])` (array form required — policy receives Patient as extra arg); query `$plans = $patient->dietPlans()->with('doctor')->latest()->paginate(15)`; return `$this->paginated('Diet plans retrieved successfully.', DietPlanSummaryResource::collection($plans))`
- [ ] T023 [US2] Implement `DietPlanController::show(Patient $patient, PatientDietPlan $dietPlan): JsonResponse` — route scoping FIRST: `if ($dietPlan->patient_id !== $patient->id) abort(404);` — then `$this->authorize('view', $dietPlan)` — then `$dietPlan->load('doctor')` — return `$this->ok('Diet plan retrieved successfully.', ['diet_plan' => new DietPlanResource($dietPlan)])`; also register `GET /api/patients/{patient}/diet-plans` and `GET /api/patients/{patient}/diet-plans/{dietPlan}` routes in `backend/routes/api.php` — **register routes here in GREEN so T024 can pass**
- [ ] T024 [US2] Run `php artisan test tests/Feature/diet-plans/DietPlanListTest.php tests/Feature/diet-plans/DietPlanShowTest.php` — confirm ALL tests pass (GREEN)

### REFACTOR Phase

- [ ] T025 [US2] Run `vendor/bin/pint --dirty`; run `composer run analyse`; run full diet-plan test suite `php artisan test tests/Feature/diet-plans/` — all gates green; commit REFACTOR phase (REFACTOR)

**Checkpoint**: US1 + US2 complete. All three endpoints work. Doctors can generate plans and browse history. All gates pass.

---

## Phase 5: User Story 3 — Handle Generation Failure Gracefully (Priority: P3)

**Goal**: When AI output fails validation after two attempts, the plan record stores `status=failed` with a `failure_reason`. The polling endpoint returns this state so the UI can show a retry option.

**Independent Test**: Faking `Agent` to return invalid output (wrong day count) causes plan to reach `failed` status with a non-null `failure_reason` after job runs.

**Note**: `GenerateDietPlanJob` already implements this logic (designed in T012). This phase adds explicit test coverage for the failure paths and verifies the behaviour end-to-end.

### RED Phase

- [ ] T026 [US3] Create `backend/tests/Feature/diet-plans/DietPlanFailureTest.php` using Pest `test()` function syntax — cover: (a) job runs with `Agent::fake(DietPlanAgent::class, ['days' => array_fill(0, 3, [...])]) ` (only 3 days, fails `size:7` validation) → after running the job manually via `GenerateDietPlanJob::dispatchSync($plan)`, assert plan status is `failed` and `failure_reason` is not null; (b) job runs with `Agent::fake()` configured to throw an exception → plan status becomes `failed`, `failure_reason` contains the exception message; (c) `GET /api/patients/{patient}/diet-plans` returns the failed plan record with a `failure_reason` field in the response; (d) a second POST after failure creates a new `pending` plan record while preserving the failed one (history is not overwritten) — confirm ALL tests fail (RED)

### GREEN Phase

- [ ] T027 [US3] Run `php artisan test tests/Feature/diet-plans/DietPlanFailureTest.php` — these tests should pass because `GenerateDietPlanJob` was designed in T012 with both failure paths built in; if test (a) fails, verify the validation loop in `handle()` increments the attempt counter and falls through after 2 failures (check the `continue` vs `break` flow); if test (b) fails, verify the outer `try/catch(Throwable $e)` catches non-validation exceptions and calls the `failed`-status update; if test (c) fails, verify `DietPlanSummaryResource` includes `failure_reason` when status is `failed`; if test (d) fails, verify `store()` always creates a NEW record and does not upsert — confirm ALL pass (GREEN)

### REFACTOR Phase

- [ ] T028 [US3] Run `vendor/bin/pint --dirty`; run `composer run analyse`; run `php artisan test tests/Feature/diet-plans/` — all gates green; commit REFACTOR phase (REFACTOR)

**Checkpoint**: All BE user stories covered by tests. Full Pest suite passes. Pint and Larastan green.

---

## Phase 6: Frontend — Diet Plan Section (US1 + US2 + US3)

**Goal**: React UI on the patient profile page that shows the latest completed plan, polls while pending, handles failure, and lists history. BE must be complete before FE work begins.

**Independent Test**: Opening patient profile renders `DietPlanSection`; clicking "Generate Diet Plan" calls the POST endpoint; component shows "Generating..." while pending, renders the plan card on completion, and shows error message on failure.

**Note**: FE tasks are separated from BE per Constitution Principle V. These tasks consume the BE API defined in `contracts/api-endpoints.md`.

- [ ] T029 Create `frontend/src/pages/patients/components/DietPlanSection/DietPlanSection.jsx` — container component; fetches `GET /api/patients/:id/diet-plans` on mount using the orval-generated API client; polls every 3 seconds using `useEffect + setInterval` (or TanStack Query `refetchInterval`) while the latest plan has `status: 'pending'`; clears polling on `completed` or `failed` or on component unmount; renders `DietPlanCard` for completed plan, inline error + "Try Again" button for failed, loading skeleton for pending, and `DietPlanHistory` below; "Generate Diet Plan" button calls `POST /api/patients/:id/diet-plans` then immediately starts polling
- [ ] T030 [P] Create `frontend/src/pages/patients/components/DietPlanSection/DietPlanCard.jsx` — receives completed plan object; renders: rationale paragraph in italic, daily kcal displayed prominently, macro targets row (protein/carbs/fat in grams), 7-day meal grid with one card per day each showing breakfast/lunch/dinner/snack, warnings as amber chip badges if `warnings` array is non-empty; "Regenerate" button calls parent callback; "Export PDF" button renders as disabled with tooltip "Coming soon"
- [ ] T031 [P] Create `frontend/src/pages/patients/components/DietPlanSection/DietPlanHistory.jsx` — receives array of plan objects (all plans except currently displayed); renders list of past generations each showing: formatted `created_at` date, generating doctor's name (`generated_by.name`), status badge (green/red/amber); clicking a historical completed plan passes it up to parent to display in `DietPlanCard`; failed plans show `failure_reason` on hover/expand
- [ ] T032 Wire `DietPlanSection` into the patient detail page at `frontend/src/pages/patients/[id].jsx` (or equivalent) — import and render below the existing visit section; pass `patientId` as prop; run `pnpm run dev` and verify the section renders on patient profile

**Checkpoint**: Patient profile page renders `DietPlanSection`. Generate button fires POST and enters polling. Completed plan displays 7 days of meals with macros and warnings. History list shows previous plans with status badges.

---

## Phase 7: Polish & Cross-Cutting Concerns

- [ ] T033 [P] Run full backend quality gates: `vendor/bin/pint --dirty`, `composer run analyse`, `php artisan test` — resolve any remaining issues in `backend/`; all tests green
- [ ] T034 [P] Run `pnpm run lint` and `pnpm run build` in `frontend/` — resolve any type or lint errors in the new DietPlanSection components; build must succeed
- [ ] T035 Update `specs/014-ai-diet-plan/quickstart.md` with any corrections discovered during implementation — actual SDK install steps, any attribute import paths, any quirks found with `Agent::fake()` in Pest vs PHPUnit context
- [ ] T036 Update `_docs_uni/sections/08_ai_development_methodology.md` section 8.9 with implementation notes from Phase 4 once complete

---

## Dependencies & Execution Order

### Phase Dependencies

- **Phase 1 (Setup)**: No dependencies — start immediately
- **Phase 2 (Foundational)**: Depends on Phase 1 — BLOCKS all user stories
- **Phase 3 (US1)**: Depends on Phase 2
- **Phase 4 (US2)**: Depends on Phase 2; adds methods to the controller from Phase 3
- **Phase 5 (US3)**: Depends on Phase 3 (tests the job created in T012)
- **Phase 6 (FE)**: Depends on Phase 3 + Phase 4 (consumes all 3 endpoints)
- **Phase 7 (Polish)**: Depends on all previous phases

### User Story Dependencies

- **US1 (P1)**: Unblocked after Phase 2 — no story dependencies
- **US2 (P2)**: Unblocked after Phase 2 — shares `DietPlanController` with US1 (adds methods to the same class)
- **US3 (P3)**: Depends on US1 (verifies the job logic from T012)
- **US4 (access control)**: Embedded in US1 and US2 RED phase test files — no separate phase

### TDD Commit Sequence per Story

```
US1:
  commit: "014 Write failing DietPlanGenerateTest (RED phase)"          ← T010
  commit: "014 Implement agent + job + controller + route (GREEN phase)" ← T011–T015
  commit: "014 Pint + analyse (REFACTOR phase)"                         ← T016–T017

US2:
  commit: "014 Write failing DietPlanListTest + ShowTest (RED phase)"   ← T018–T019
  commit: "014 Implement resources + index + show + routes (GREEN phase)" ← T020–T024
  commit: "014 Pint + analyse (REFACTOR phase)"                         ← T025

US3:
  commit: "014 Write failing DietPlanFailureTest (RED phase)"           ← T026
  commit: "014 Verify failure paths in GenerateDietPlanJob (GREEN phase)" ← T027
  commit: "014 Pint + analyse + full suite (REFACTOR phase)"            ← T028
```

### Parallel Opportunities

Within Phase 2: T005, T006, T008 can run in parallel (different files, no mutual dependency)
Within Phase 3 GREEN: T011, T012, T013 can run in parallel before T014 needs them
Within Phase 4 RED: T018 and T019 can run in parallel
Within Phase 4 GREEN: T020 and T021 can run in parallel before T022/T023 need them
Within Phase 6: T030 and T031 can run in parallel
Within Phase 7: T033 and T034 can run in parallel

---

## Implementation Strategy

### MVP (US1 only — minimum to demo AI generation)

1. Complete Phase 1 (Setup)
2. Complete Phase 2 (Foundational)
3. Complete Phase 3 (US1 — T010–T017)
4. **STOP and VALIDATE**: run queue worker with `php artisan queue:work`, POST to `/api/patients/1/diet-plans` as doctor → 202, check `patient_diet_plans` table for `status=completed`
5. Confirm plan transitions to `completed` within 30 seconds

### Full BE Delivery

1. MVP above
2. Phase 4 (US2 — T018–T025): list + detail endpoints
3. Phase 5 (US3 — T026–T028): failure path verified
4. All Pest tests pass; Pint + Larastan green

### Full Delivery (BE + FE)

1. Full BE delivery above
2. Phase 6 (FE — T029–T032): React components on patient profile
3. Phase 7 (Polish — T033–T036): lint, build, docs

---

## Notes & Common Pitfalls

- **Route registration belongs in GREEN, not REFACTOR** — routes must exist before the test run that confirms GREEN; pint/analyse-only cleanup goes in REFACTOR
- **`#[UseCheapestModel]` does not exist in the SDK** — pin the model explicitly with `#[Provider(Lab::Anthropic)]` + `#[Model('claude-haiku-4-5-20251001')]`; this also appears incorrectly in `data-model.md` which will need correcting when implementing T011
- **`public int $tries = 1` is mandatory on `GenerateDietPlanJob`** — without it, Laravel retries the whole job 3 times on failure (default), resulting in 6 AI calls per request instead of 2
- **`->as('doctor')` is a BelongsToMany pivot alias** — for BelongsTo with a named accessor, the PHP method name IS the accessor: define `doctor()` returning `$this->belongsTo(User::class, 'generated_by')`
- **Use array form for policy when subject differs from policy target** — `can('generate', [PatientDietPlan::class, $patient])` routes to `DietPlanPolicy`; `can('generate', $patient)` routes to `PatientPolicy` (wrong)
- **`$this->success('...', 202, [...])` not `$this->created()`** — `created()` returns 201; the spec requires 202 Accepted; use the underlying `success()` helper directly
- All BE tests use Pest `test()` function syntax (not PHPUnit class-based) — see `tests/Feature/` examples
- All BE commits end with: `Co-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>`
- `Agent::fake()` and `Queue::fake()` are mandatory in all Pest tests — no real API calls permitted
- Route scoping check (`if ($dietPlan->patient_id !== $patient->id) abort(404)`) must come BEFORE `authorize()` in `show()`, matching `VisitController` pattern
- `DietPlanController` extends `ApiController` and uses the `ApiResponses` trait — never use raw `response()->json()`
- The `jobs` table must exist before dispatching `GenerateDietPlanJob` — verify in Phase 1 (T003)
