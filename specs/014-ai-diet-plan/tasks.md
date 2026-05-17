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

- [ ] T001 Install `laravel/ai` package: run `composer require laravel/ai`, then `php artisan vendor:publish --provider="Laravel\Ai\AiServiceProvider"`, then `php artisan migrate` in `backend/`
- [ ] T002 Configure Anthropic provider in `backend/config/ai.php` — set default provider to `anthropic`, add `ANTHROPIC_API_KEY` key; add `ANTHROPIC_API_KEY=` placeholder to `backend/.env.example`
- [ ] T003 Verify `jobs` table exists; if not, run `php artisan queue:table && php artisan migrate` in `backend/` — document result in `specs/014-ai-diet-plan/quickstart.md`

**Checkpoint**: `php artisan tinker` can instantiate `Laravel\Ai\Facades\AI` without error.

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Database layer, model, factory, and policy must exist before any user story can be implemented. US1, US2, US3, and US4 all depend on this phase.

**⚠️ CRITICAL**: No user story work can begin until this phase is complete.

- [ ] T004 Create migration `backend/database/migrations/xxxx_create_patient_diet_plans_table.php` — columns: `id`, `patient_id` (FK→patients, cascade), `generated_by` (FK→users, restrict), `status` enum(`pending`,`completed`,`failed`) default `pending`, `rationale` text nullable, `daily_calories` int nullable, `nutritional_goals` json nullable, `days` json nullable, `warnings` json nullable, `failure_reason` varchar(500) nullable, `timestamps`; run `php artisan migrate`
- [ ] T005 [P] Create `backend/app/Models/PatientDietPlan.php` — fillable all columns; `casts()` returns `nutritional_goals`, `days`, `warnings` as `array`; `belongsTo(Patient::class)` + `belongsTo(User::class, 'generated_by')->as('doctor')`; `scopeCompleted` + `scopeLatestCompleted` scopes
- [ ] T006 [P] Create `backend/database/factories/PatientDietPlanFactory.php` — states: `pending()`, `completed()` (with full 7-day `days` array), `failed()` (with `failure_reason`)
- [ ] T007 Add `dietPlans(): HasMany` relationship to `backend/app/Models/Patient.php` returning `$this->hasMany(PatientDietPlan::class)`
- [ ] T008 [P] Create `backend/app/Policies/DietPlanPolicy.php` — three methods: `generate(User $user, Patient $patient): bool`, `viewAny(User $user, Patient $patient): bool`, `view(User $user, PatientDietPlan $dietPlan): bool` — all return `$user->isAdmin() || $user->isDoctor()`
- [ ] T009 Register `DietPlanPolicy` in `backend/app/Providers/AppServiceProvider.php` (or wherever other policies are registered) — map `PatientDietPlan::class => DietPlanPolicy::class`

**Checkpoint**: `php artisan migrate:status` shows `patient_diet_plans` as ran. `PatientDietPlan::factory()->completed()->make()` returns a valid model instance.

---

## Phase 3: User Story 1 — Generate Diet Plan (Priority: P1) 🎯 MVP

**Goal**: Doctor POSTs to trigger generation; system returns 202 immediately; `GenerateDietPlanJob` creates the `DietPlanAgent`, validates output, and updates the plan record to `completed` or `failed`.

**Independent Test**: `POST /api/patients/{patient}/diet-plans` as a doctor returns 202 with `status: pending`; queue is faked; plan record is created in DB.

**US4 coverage**: 401 (guest) and 403 (patient) assertions are included in the RED phase test for this story.

### RED Phase — Write failing tests first, confirm they FAIL before T014

- [ ] T010 [US1] Create `backend/tests/Feature/diet-plans/DietPlanGenerateTest.php` — cover: (a) doctor triggers generation → 202, job dispatched, plan record created with `status=pending`; (b) admin triggers generation → 202; (c) guest → 401; (d) patient role → 403; (e) patient not found → 404; (f) response body contains `data.diet_plan.id` and `data.diet_plan.status`; use `Queue::fake()` + `Agent::fake()` throughout — run `php artisan test tests/Feature/diet-plans/DietPlanGenerateTest.php` and confirm ALL tests fail (RED)

### GREEN Phase — Implement to make T010 tests pass

- [ ] T011 [US1] Create `backend/app/Ai/Agents/DietPlanAgent.php` — `#[Temperature(0.3)]`, `#[UseCheapestModel]`; implements `Agent`, `HasStructuredOutput`; uses `Promptable`; constructor takes `Patient $patient` (expects `socioeconomic` relation loaded); `instructions()` builds system prompt from patient data including computed age; `schema(JsonSchema $schema): array` returns rationale, daily_calories, nutritional_goals (object), days (array of 7 objects with day/breakfast/lunch/dinner/snack), warnings (array of strings)
- [ ] T012 [US1] Create `backend/app/Jobs/GenerateDietPlanJob.php` — implements `ShouldQueue`; constructor takes `PatientDietPlan $plan`; `handle()`: loads `$plan->patient->load('socioeconomic')`; attempts up to 2 times: call `(new DietPlanAgent($patient))->prompt('Generate the plan.')`, then `Validator::make($response->toArray(), [...])` with rules from `data-model.md`; on success update plan to `completed` with all fields; after 2 failed validations update to `failed` with last validation error as `failure_reason`; catch any `Throwable` → update to `failed`
- [ ] T013 [P] [US1] Create `backend/app/Http/Requests/StoreDietPlanRequest.php` — `authorize()` calls `$this->user()->can('generate', $this->route('patient'))`; `rules()` returns `[]` (no body required)
- [ ] T014 [US1] Create `backend/app/Http/Controllers/Api/DietPlanController.php` with `store(StoreDietPlanRequest $request, Patient $patient): JsonResponse` — create plan record with `status=pending`, `generated_by=auth()->id()`; dispatch `GenerateDietPlanJob`; return `$this->created('Diet plan generation started.', ['diet_plan' => ['id' => $plan->id, 'status' => $plan->status, 'created_at' => $plan->created_at]])` — extend `ApiController`
- [ ] T015 [US1] Run `php artisan test tests/Feature/diet-plans/DietPlanGenerateTest.php` — confirm ALL tests pass (GREEN)

### REFACTOR Phase

- [ ] T016 [US1] Register `POST /api/patients/{patient}/diet-plans` route in `backend/routes/api.php` pointing to `DietPlanController@store` — inside `auth:sanctum` middleware group, after existing visit routes
- [ ] T017 [US1] Run `vendor/bin/pint --dirty` on all new files; run `composer run analyse`; run `php artisan test tests/Feature/diet-plans/DietPlanGenerateTest.php` — all gates green (REFACTOR)

**Checkpoint**: US1 complete. Doctor can trigger generation and receive 202. Job is dispatched to queue. Access control is enforced. All gates pass.

---

## Phase 4: User Story 2 — View Diet Plan History (Priority: P2)

**Goal**: Doctor GETs paginated history of all plans for a patient (newest first); GETs full detail of a specific plan. US4 coverage embedded.

**Independent Test**: `GET /api/patients/{patient}/diet-plans` returns paginated list with correct metadata; `GET /api/patients/{patient}/diet-plans/{id}` returns full plan including 7-day meals.

### RED Phase — Write failing tests first, confirm they FAIL before T022

- [ ] T018 [P] [US2] Create `backend/tests/Feature/diet-plans/DietPlanListTest.php` — cover: (a) doctor gets paginated list newest-first with `data`, `meta`, `links`; (b) list items contain `id`, `status`, `generated_by.name`, `daily_calories`, `created_at` but NOT `days`/`rationale`; (c) empty list returns empty `data` array; (d) guest → 401; (e) patient role → 403; (f) patient not found → 404 — confirm ALL fail (RED)
- [ ] T019 [P] [US2] Create `backend/tests/Feature/diet-plans/DietPlanShowTest.php` — cover: (a) doctor gets full plan detail including `rationale`, `days` (7 items), `nutritional_goals`, `warnings`; (b) plan belonging to different patient → 404 (route scoping); (c) guest → 401; (d) patient role → 403; (e) completed plan with all fields; (f) failed plan returns `failure_reason` — confirm ALL fail (RED)

### GREEN Phase

- [ ] T020 [P] [US2] Create `backend/app/Http/Resources/Api/DietPlanSummaryResource.php` — returns: `id`, `status`, `generated_by` (id + name from doctor relation, nullable), `daily_calories`, `nutritional_goals`, `failure_reason` (when failed), `created_at`; omits `rationale`, `days`, `warnings`
- [ ] T021 [P] [US2] Create `backend/app/Http/Resources/Api/DietPlanResource.php` — returns all fields including `rationale`, `days`, `warnings` in addition to summary fields
- [ ] T022 [US2] Implement `DietPlanController::index(Patient $patient): JsonResponse` — `authorize('viewAny', [PatientDietPlan::class, $patient])`; query `$patient->dietPlans()->with('doctor')->latest()->paginate()`; return `$this->paginated('Diet plans retrieved successfully.', DietPlanSummaryResource::collection($plans))`
- [ ] T023 [US2] Implement `DietPlanController::show(Patient $patient, PatientDietPlan $dietPlan): JsonResponse` — route scoping check `if ($dietPlan->patient_id !== $patient->id) abort(404)`; `$this->authorize('view', $dietPlan)`; load `$dietPlan->load('doctor')`; return `$this->ok('Diet plan retrieved successfully.', ['diet_plan' => new DietPlanResource($dietPlan)])`
- [ ] T024 [US2] Run `php artisan test tests/Feature/diet-plans/DietPlanListTest.php tests/Feature/diet-plans/DietPlanShowTest.php` — confirm ALL pass (GREEN)

### REFACTOR Phase

- [ ] T025 [US2] Register `GET /api/patients/{patient}/diet-plans` and `GET /api/patients/{patient}/diet-plans/{dietPlan}` routes in `backend/routes/api.php`; run `vendor/bin/pint --dirty`; run `composer run analyse`; run full diet-plan test suite (REFACTOR)

**Checkpoint**: US1 + US2 complete. Doctors can generate plans and browse history. All three endpoints work. All gates pass.

---

## Phase 5: User Story 3 — Handle Generation Failure Gracefully (Priority: P3)

**Goal**: When AI output fails validation after two attempts, the plan record stores `status=failed` with a `failure_reason`. The polling endpoint returns this state so the UI can show a retry option.

**Independent Test**: Faking `Agent` to return invalid output (wrong day count) causes plan to reach `failed` status with a non-null `failure_reason` after job runs.

**Note**: `GenerateDietPlanJob` already implements this logic (designed in T012). This phase adds explicit test coverage for the failure path and verifies the behaviour end-to-end.

### RED Phase

- [ ] T026 [US3] Create `backend/tests/Feature/diet-plans/DietPlanFailureTest.php` — cover: (a) job runs with `Agent::fake()` returning invalid output (e.g. `days` array with 3 items) → plan status becomes `failed`, `failure_reason` is non-null; (b) job runs with `Agent::fake()` throwing exception → plan status becomes `failed`; (c) `GET /api/patients/{patient}/diet-plans` returns the failed plan with `failure_reason` field; (d) a new generation can be triggered after a failure (previous failed record preserved, new pending record created) — confirm ALL fail (RED)

### GREEN Phase

- [ ] T027 [US3] Run `php artisan test tests/Feature/diet-plans/DietPlanFailureTest.php` — if any test fails due to missing logic in `GenerateDietPlanJob`, patch `backend/app/Jobs/GenerateDietPlanJob.php` accordingly; confirm ALL pass (GREEN)

### REFACTOR Phase

- [ ] T028 [US3] Run `vendor/bin/pint --dirty`; run `composer run analyse`; run full diet-plan test suite — all gates green; commit REFACTOR phase (REFACTOR)

**Checkpoint**: All four BE user stories covered by tests. Full suite passes. All gates green.

---

## Phase 6: Frontend — Diet Plan Section (US1 + US2 + US3)

**Goal**: React UI on the patient profile page that shows the latest completed plan, polls while pending, handles failure, and lists history. BE must be complete before FE work begins.

**Independent Test**: Opening patient profile renders `DietPlanSection`; clicking "Generate Diet Plan" calls the POST endpoint; component shows "Generating..." while pending, renders the plan card on completion, and shows error message on failure.

**Note**: FE tasks are separated from BE per Constitution Principle V. These tasks consume the BE API defined in `contracts/api-endpoints.md`.

- [ ] T029 Create `frontend/src/pages/patients/components/DietPlanSection/DietPlanSection.jsx` — container component; fetches `GET /api/patients/:id/diet-plans` on mount; polls every 3 seconds while latest plan has `status: pending`; stops polling on `completed` or `failed`; renders `DietPlanCard` for completed plan, inline error + retry button for failed, loading spinner for pending, and `DietPlanHistory` list below; exposes "Generate Diet Plan" button that calls `POST /api/patients/:id/diet-plans`
- [ ] T030 [P] Create `frontend/src/pages/patients/components/DietPlanSection/DietPlanCard.jsx` — receives a completed plan object; renders: rationale paragraph, daily kcal + macro targets (protein/carbs/fat), 7-day meal grid (one section per day with breakfast/lunch/dinner/snack), warnings as alert chips; "Regenerate" button calls parent callback; "Export PDF" button renders as disabled placeholder
- [ ] T031 [P] Create `frontend/src/pages/patients/components/DietPlanSection/DietPlanHistory.jsx` — receives array of plan objects; renders list of past plans (excluding the currently displayed latest) each showing generated date, doctor name, status badge; clicking a historical entry shows it in a modal or replaces the main card view
- [ ] T032 Wire `DietPlanSection` into `frontend/src/pages/patients/PatientProfile.jsx` (or equivalent patient detail page) — import and render below the existing visit section; pass `patientId` prop

**Checkpoint**: Patient profile page renders `DietPlanSection`. Generate button fires POST. Pending state shows spinner. Completed plan displays 7 days of meals. History list shows previous plans.

---

## Phase 7: Polish & Cross-Cutting Concerns

- [ ] T033 [P] Run full backend quality gates: `vendor/bin/pint --dirty`, `composer run analyse`, `php artisan test` — resolve any remaining issues in `backend/`
- [ ] T034 [P] Run `pnpm run lint` and `pnpm run build` in `frontend/` — resolve any type or lint errors in the new DietPlanSection components
- [ ] T035 Update `specs/014-ai-diet-plan/quickstart.md` with any corrections discovered during implementation (actual install steps, any SDK quirks found)
- [ ] T036 Update `_docs_uni/sections/08_ai_development_methodology.md` section 8.7 with implementation notes — what the tasks phase added, any decisions that changed during implementation

---

## Dependencies & Execution Order

### Phase Dependencies

- **Phase 1 (Setup)**: No dependencies — start immediately
- **Phase 2 (Foundational)**: Depends on Phase 1 — BLOCKS all user stories
- **Phase 3 (US1)**: Depends on Phase 2
- **Phase 4 (US2)**: Depends on Phase 2; integrates with Phase 3 controller (same class)
- **Phase 5 (US3)**: Depends on Phase 3 (tests the job from T012)
- **Phase 6 (FE)**: Depends on Phase 3 + Phase 4 being complete (consumes all 3 endpoints)
- **Phase 7 (Polish)**: Depends on all previous phases

### User Story Dependencies

- **US1 (P1)**: Unblocked after Phase 2 — no story dependencies
- **US2 (P2)**: Unblocked after Phase 2 — shares `DietPlanController` with US1 (sequential within same class)
- **US3 (P3)**: Depends on US1 (verifies job logic from T012)
- **US4 (access control)**: Embedded in US1 and US2 test files — no separate phase needed

### TDD Commit Sequence per Story

```
US1:
  commit: "014 Write failing DietPlanGenerateTest (RED phase)"    ← T010
  commit: "014 Implement DietPlanAgent + Job + Controller (GREEN phase)" ← T011-T015
  commit: "014 Register generate route, pint, analyse (REFACTOR phase)" ← T016-T017

US2:
  commit: "014 Write failing DietPlanListTest + ShowTest (RED phase)"    ← T018-T019
  commit: "014 Implement resources + index + show (GREEN phase)"          ← T020-T024
  commit: "014 Register list/show routes, pint, analyse (REFACTOR phase)"← T025

US3:
  commit: "014 Write failing DietPlanFailureTest (RED phase)"    ← T026
  commit: "014 Verify failure path in GenerateDietPlanJob (GREEN phase)" ← T027
  commit: "014 Pint, analyse, full suite (REFACTOR phase)"               ← T028
```

### Parallel Opportunities

Within Phase 2: T005, T006, T008 can run in parallel (different files)  
Within Phase 4 RED: T018 and T019 can run in parallel  
Within Phase 4 GREEN: T020 and T021 can run in parallel  
Within Phase 6: T030 and T031 can run in parallel  
Within Phase 7: T033 and T034 can run in parallel

---

## Implementation Strategy

### MVP (US1 only — minimum to demo AI generation)

1. Complete Phase 1 (Setup)
2. Complete Phase 2 (Foundational)
3. Complete Phase 3 (US1 — T010–T017)
4. **STOP and VALIDATE**: `POST /api/patients/1/diet-plans` as doctor → 202, job on queue, plan in DB
5. Run queue worker, verify plan transitions to `completed`

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

## Notes

- All BE commits end with: `Co-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>`
- `Agent::fake()` and `Queue::fake()` are mandatory in all Pest tests — no real API calls
- Route scoping check (`if ($dietPlan->patient_id !== $patient->id) abort(404)`) goes in `show()` before `authorize()`, matching `VisitController` pattern
- `DietPlanController` extends `ApiController` and uses the `ApiResponses` trait — never `response()->json()`
- `laravel/ai` publishes `config/ai.php` and two migrations — run `php artisan migrate` after publish or the SDK will error
- The `jobs` table must exist before dispatching `GenerateDietPlanJob` — verify in Phase 1 (T003)
