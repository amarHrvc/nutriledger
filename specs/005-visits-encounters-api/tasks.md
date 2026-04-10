# Tasks: Visits & Encounters REST API

**Input**: Design documents from `/specs/005-visits-encounters-api/`
**Prerequisites**: plan.md ✅ | spec.md ✅ | research.md ✅ | data-model.md ✅ | contracts/api.md ✅ | quickstart.md ✅

**TDD**: Tests are written FIRST (RED phase) before each implementation task. A task is not complete if any test is red.

**Organization**: Tasks grouped by user story — each story is independently testable.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no incomplete-task dependencies)
- **[Story]**: User story this task belongs to (US1–US5 from spec.md)

## Path Conventions

All source paths are relative to `backend/`. All spec paths are relative to `specs/005-visits-encounters-api/`.

---

## Phase 1: Setup

**Purpose**: Confirm the domain layer is intact before making any changes.

- [ ] T001 Run existing domain tests and confirm all 4 files pass: `php artisan test tests/Feature/visits/`

**Checkpoint**: VisitPolicyTest, VisitsModelTest, VisitsFactoryTest, VisitsMigrationTest all green. If any fail, investigate before continuing.

---

## Phase 2: Foundational — Domain Corrections

**Purpose**: Fix three pre-existing bugs in the domain layer. All four tasks must be complete and green before any user story work begins.

**⚠️ CRITICAL**: No user story work can begin until this phase is complete.

- [ ] T002 Fix `date` cast in `app/Models/Visit.php` — in `casts()`, change `'date' => 'datetime'` to `'date' => 'date'` so API outputs `Y-m-d` strings instead of ISO-8601 datetimes

- [ ] T003 [P] Fix `VisitPolicy` in `app/Policies/VisitPolicy.php` — three corrections:

  **1. Update `viewAny` signature to accept patient context** (cross-patient list access is blocked here, not by route scoping — the `index` endpoint has no `{visit}` scoped binding):
  ```php
  use App\Models\Patient; // add import

  public function viewAny(User $user, Patient $patient): bool
  {
      if ($user->isPatient()) {
          return $user->patient?->id === $patient->id;
      }
      return $user->isAdmin() || $user->isDoctor();
  }
  ```

  **2. Fix `create` to doctor-only** (admins cannot create visits — clinical decision):
  ```php
  public function create(User $user): bool
  {
      return $user->isDoctor();
  }
  ```

  **3. Fix `view` typo** — change `$user->ispatient()` to `$user->isPatient()` (existing method call is case-insensitive but incorrect style; Pint will flag it):
  ```php
  if ($user->isPatient()) {
  ```

- [ ] T004 [P] Update `tests/Feature/visits/VisitPolicyTest.php` — two changes:

  **1. Fix the admin-can-create assertion** (admin can no longer create per corrected policy):
  ```php
  // Change this existing test:
  test('admin can create a visit', function () {
      $admin = User::factory()->create(['role' => 'admin']);
      expect($admin->can('create', Visit::class))->toBeFalse(); // was toBeTrue()
  });
  ```

  **2. Add four new `viewAny` tests** for the updated signature (append to the file):
  ```php
  test('admin can viewAny visits for any patient', function () {
      $admin = User::factory()->admin()->create();
      $patient = Patient::factory()->create();
      expect($admin->can('viewAny', [Visit::class, $patient]))->toBeTrue();
  });

  test('doctor can viewAny visits for any patient', function () {
      $doctor = User::factory()->doctor()->create();
      $patient = Patient::factory()->create();
      expect($doctor->can('viewAny', [Visit::class, $patient]))->toBeTrue();
  });

  test('patient can viewAny visits for their own patient record', function () {
      $patient = Patient::factory()->create();
      expect($patient->user->can('viewAny', [Visit::class, $patient]))->toBeTrue();
  });

  test('patient cannot viewAny visits for another patient', function () {
      $patient1 = Patient::factory()->create();
      $patient2 = Patient::factory()->create();
      expect($patient1->user->can('viewAny', [Visit::class, $patient2]))->toBeFalse();
  });
  ```

- [ ] T005 Fix `StoreVisitRequest` in `app/Http/Requests/StoreVisitRequest.php` — four corrections:
  1. Remove the `'doctor_id'` rule entirely — it is auto-assigned from auth, never accepted from request body
  2. Change `date` rule to `['required', 'date', 'before_or_equal:today']`
  3. Change `notes` rule to `['nullable', 'string', 'max:10000']`
  4. Add `messages()` method: `return ['date.before_or_equal' => 'Visit date cannot be in the future.'];`

**Verification**: `php artisan test tests/Feature/visits/` — all files (including the 4 new viewAny tests) green.

**Checkpoint**: Domain layer is correct and consistent with spec. API work can begin.

---

## Phase 3: User Story 1 — Doctor Records a Patient Visit (Priority: P1) 🎯 MVP

**Goal**: Doctors can submit a visit record (date + optional notes). The system auto-assigns the conducting doctor from the authenticated user. Returns 201 with a `VisitResource` that includes `doctorName`.

**Independent Test**: POST to `/api/patients/{id}/visits` as doctor → 201. Same POST as admin → 403. Response contains `data.visit.attributes.doctorName`.

- [ ] T006 [P] [US1] Create `VisitResource` scaffold then implement it:

  ```bash
  php artisan make:resource Api/VisitResource --no-interaction
  ```

  Fill `app/Http/Resources/Api/VisitResource.php` — follow `PatientResource` as the direct reference. Use these exact imports and structure:

  ```php
  namespace App\Http\Resources\Api;

  use App\Models\Patient;
  use App\Models\User;
  use Carbon\Carbon;
  use Illuminate\Http\Request;
  use Illuminate\Http\Resources\Json\JsonResource;

  /**
   * @property-read int $id
   * @property-read Carbon $date
   * @property-read string|null $notes
   * @property-read ?Carbon $created_at
   * @property-read ?Carbon $updated_at
   * @property-read Patient|null $patient
   * @property-read User|null $doctor
   */
  class VisitResource extends JsonResource
  {
      public function toArray(Request $request): array
      {
          return [
              'type' => 'visit',
              'id' => (string) $this->id,
              'attributes' => [
                  'date' => $this->date->toDateString(),
                  'notes' => $this->notes,
                  'doctorName' => $this->whenLoaded('doctor', fn () => $this->doctor->name),
                  'createdAt' => $this->created_at?->toIso8601String(),
                  'updatedAt' => $this->updated_at?->toIso8601String(),
              ],
              'relationships' => [
                  'patient' => [
                      'data' => $this->whenLoaded('patient',
                          fn () => ['type' => 'patient', 'id' => (string) $this->patient->id]),
                  ],
                  'doctor' => [
                      'data' => $this->whenLoaded('doctor',
                          fn () => ['type' => 'user', 'id' => (string) $this->doctor->id]),
                  ],
              ],
          ];
      }
  }
  ```

- [ ] T007 [P] [US1] Write failing tests — create the test file:

  ```bash
  php artisan make:test Visit/VisitCreateTest --pest --no-interaction
  ```

  Fill `tests/Feature/Visit/VisitCreateTest.php` with `uses(RefreshDatabase::class)` and these 10 tests. Use factory states (`->doctor()`, `->admin()`, `->patient()`). All single-resource responses are wrapped at `data.visit.*` (the controller returns `['visit' => new VisitResource(...)]`):

  1. `it('allows doctor to create visit for patient')` — POST valid payload as doctor → assertCreated, assertDatabaseHas('visits', ['patient_id' => $patient->id, 'doctor_id' => $doctor->id])
  2. `it('auto-assigns doctor_id from authenticated user ignoring request body')` — POST with an explicit `doctor_id` of a different user → assertCreated, assertJsonPath('data.visit.relationships.doctor.data.id', (string) $doctor->id) [confirm it matches the auth user, not the passed id]
  3. `it('prevents admin from creating visit')` — POST as admin → assertForbidden
  4. `it('prevents patient from creating visit')` — POST as patient → assertForbidden
  5. `it('returns 401 for unauthenticated create request')` — POST without auth → assertUnauthorized
  6. `it('requires date field on visit creation')` — POST without date → assertUnprocessable, assertJsonValidationErrors(['date'])
  7. `it('rejects future date on visit creation')` — POST with `date => now()->addDay()->toDateString()` → assertUnprocessable, assertJsonValidationErrors(['date'])
  8. `it('allows visit creation with null notes')` — POST with no notes field → assertCreated
  9. `it('rejects notes exceeding 10000 characters')` — POST with `notes => str_repeat('a', 10001)` → assertUnprocessable, assertJsonValidationErrors(['notes'])
  10. `it('returns 201 with correct VisitResource structure')` — POST as doctor → assertCreated, assertJsonStructure(['data' => ['visit' => ['type', 'id', 'attributes' => ['date', 'notes', 'doctorName', 'createdAt', 'updatedAt'], 'relationships' => ['patient', 'doctor']]]])

  Run and confirm all 10 FAIL with 404 (routes do not exist yet).

- [ ] T008 [US1] Register nested visit routes in `routes/api.php`:

  Add the import at the top with existing controller imports:
  ```php
  use App\Http\Controllers\Api\VisitController;
  ```

  Inside the existing `auth:sanctum` group, add:
  ```php
  Route::apiResource('patients.visits', VisitController::class)
      ->scoped(['visit' => 'patient']);
  ```

  This registers all 5 routes at once. The `->scoped(['visit' => 'patient'])` ensures `{visit}` is automatically scoped to the parent `{patient}` via the `Visit::patient()` relationship — any `{visit}` that does not belong to `{patient}` returns 404 before the controller method runs.

- [ ] T009 [US1] Create `VisitController` scaffold then implement `store()`:

  ```bash
  php artisan make:controller Api/VisitController --no-interaction
  ```

  Fill `app/Http/Controllers/Api/VisitController.php`. Required imports:
  ```php
  use App\Http\Controllers\Api\ApiController;
  use App\Http\Requests\StoreVisitRequest;
  use App\Http\Resources\Api\VisitResource;
  use App\Models\Patient;
  use App\Models\Visit;
  use Illuminate\Http\JsonResponse;
  ```

  Declare all 5 method stubs so the registered routes never throw a resolution error, then implement `store()` fully:

  ```php
  class VisitController extends ApiController
  {
      public function index(Patient $patient): JsonResponse
      {
          return $this->error('Not implemented.', 501);
      }

      public function store(StoreVisitRequest $request, Patient $patient): JsonResponse
      {
          $visit = $patient->visits()->create([
              'doctor_id' => $request->user()->id,
              ...$request->validated(),          // spreads only validated date + notes
          ]);

          return $this->created('Visit recorded successfully.', [
              'visit' => new VisitResource($visit->load('doctor')),
          ]);
      }

      public function show(Patient $patient, Visit $visit): JsonResponse
      {
          return $this->error('Not implemented.', 501);
      }

      public function update(Patient $patient, Visit $visit): JsonResponse
      {
          return $this->error('Not implemented.', 501);
      }

      public function destroy(Patient $patient, Visit $visit): JsonResponse
      {
          return $this->error('Not implemented.', 501);
      }
  }
  ```

  Note: `StoreVisitRequest::authorize()` already calls the `create` policy — no separate `$this->authorize()` call needed in `store()`.

**Verification**: `php artisan test tests/Feature/Visit/VisitCreateTest.php` — all 10 tests pass.

**Checkpoint**: US1 complete. `POST /api/patients/{patient}/visits` is functional and tested.

---

## Phase 4: User Story 2 — View Visit History for a Patient (Priority: P2)

**Goal**: Doctors and admins retrieve paginated, date-descending visit history for any patient. A patient requesting another patient's list gets 403 (the updated `viewAny` policy with patient context blocks it — the list endpoint has no `{visit}` scoped binding).

**Independent Test**: Create 3 visits with different dates for a patient, GET the list as doctor, verify dates descend and pagination meta is present.

- [ ] T010 [US2] Write failing tests:

  ```bash
  php artisan make:test Visit/VisitListTest --pest --no-interaction
  ```

  Fill `tests/Feature/Visit/VisitListTest.php` with `uses(RefreshDatabase::class)` and these 9 tests. Collection responses are at `data` (array), not `data.visit`. Use `$this->paginated()` format: `{ message, status, data: [...], meta: {...}, links: {...} }`:

  1. `it('allows admin to list visits for any patient')` — GET as admin → assertOk, assertJsonStructure(['data', 'meta', 'links'])
  2. `it('allows doctor to list visits for any patient')` — GET as doctor → assertOk
  3. `it('allows patient to list their own visits')` — create visit for patient, GET as that patient's user → assertOk
  4. `it('prevents patient from listing another patients visits')` — GET `/api/patients/{patient2->id}/visits` as patient1's user → **assertForbidden** (403 from viewAny policy)
  5. `it('returns 401 for unauthenticated list request')` — GET without auth → assertUnauthorized
  6. `it('returns visits ordered by date descending')` — create two visits: date '2026-01-01' and '2026-03-01'; GET as doctor → assertJsonPath('data.0.attributes.date', '2026-03-01')
  7. `it('returns pagination meta in visit list response')` — GET as doctor → assertJsonStructure(['meta' => ['current_page', 'last_page', 'per_page', 'total'], 'links' => ['first', 'last']])
  8. `it('includes doctorName in visit list attributes')` — GET as doctor → assertJsonPath('data.0.attributes.doctorName', $doctor->name)
  9. `it('returns empty data array when patient has no visits')` — GET for patient with no visits → assertOk, assertJsonPath('data', [])

  Run and confirm all 9 FAIL with 501.

- [ ] T011 [US2] Implement `index()` in `app/Http/Controllers/Api/VisitController.php` — replace the 501 stub:

  ```php
  public function index(Patient $patient): JsonResponse
  {
      $this->authorize('viewAny', [Visit::class, $patient]); // passes patient to updated policy

      $visits = $patient->visits()
          ->with('doctor')
          ->orderBy('date', 'desc')
          ->orderBy('created_at', 'desc')
          ->paginate();

      return $this->paginated('Visits retrieved successfully.', VisitResource::collection($visits));
  }
  ```

**Verification**: `php artisan test tests/Feature/Visit/VisitListTest.php` — all 9 tests pass.

**Checkpoint**: US1 + US2 independently functional.

---

## Phase 5: User Story 3 — View Individual Visit Details (Priority: P3)

**Goal**: Any authorized user retrieves full details of a single visit, including `doctorName`. A `{visit}` that does not belong to the URL `{patient}` returns 404 automatically via scoped route binding — no controller guard needed.

**Independent Test**: Create a visit, GET as doctor → 200 with full VisitResource. Create a second patient, attempt to GET the first patient's visit via the second patient's URL → 404.

- [ ] T012 [US3] Write failing tests:

  ```bash
  php artisan make:test Visit/VisitShowTest --pest --no-interaction
  ```

  Fill `tests/Feature/Visit/VisitShowTest.php` with `uses(RefreshDatabase::class)` and these 9 tests. Single-resource response is at `data.visit.*`:

  1. `it('allows admin to view any visit')` — GET as admin → assertOk
  2. `it('allows doctor to view any visit')` — GET as doctor → assertOk
  3. `it('allows patient to view their own visit')` — GET as the patient's user → assertOk
  4. `it('prevents patient from viewing another patients visit')` — visit belongs to patient2, request via patient2's URL but acting as patient1's user → assertForbidden (policy `view` denies cross-patient access)
  5. `it('returns 401 for unauthenticated show request')` — GET without auth → assertUnauthorized
  6. `it('returns 404 for non-existent visit id')` — GET `/api/patients/{patient}/visits/99999` → assertNotFound
  7. `it('includes doctor relationship data in show response')` — assertJsonPath('data.visit.relationships.doctor.data.type', 'user')
  8. `it('returns correct VisitResource structure on show')` — assertJsonStructure(['data' => ['visit' => ['type', 'id', 'attributes' => ['date', 'notes', 'doctorName', 'createdAt', 'updatedAt'], 'relationships' => ['patient', 'doctor']]]])
  9. `it('returns 404 when visit belongs to different patient than url')` — create visit for patient A; request via patient B's URL → assertNotFound (scoped binding)

  Run and confirm all 9 FAIL with 501.

- [ ] T013 [US3] Implement `show()` in `app/Http/Controllers/Api/VisitController.php` — replace the 501 stub:

  ```php
  public function show(Patient $patient, Visit $visit): JsonResponse
  {
      $this->authorize('view', $visit);

      return $this->ok('Visit retrieved successfully.', [
          'visit' => new VisitResource($visit->load('doctor', 'patient')),
      ]);
  }
  ```

  Scoped route binding has already returned 404 for patient mismatch before this method is reached — no manual check needed.

**Verification**: `php artisan test tests/Feature/Visit/VisitShowTest.php` — all 9 tests pass.

**Checkpoint**: US1 + US2 + US3 independently functional.

---

## Phase 6: User Story 4 — Doctor Edits Their Own Visit Notes (Priority: P4)

**Goal**: Doctors can update the date or notes of visits they personally conducted. Editing another doctor's visit returns 403. Admins can update any visit. Partial updates (notes-only or date-only) are supported.

**Independent Test**: PATCH own visit as doctor → 200. PATCH same visit as a different doctor → 403. PATCH with only `notes` field → 200, date unchanged in DB.

- [ ] T014 [P] [US4] Write failing tests:

  ```bash
  php artisan make:test Visit/VisitUpdateTest --pest --no-interaction
  ```

  Fill `tests/Feature/Visit/VisitUpdateTest.php` with `uses(RefreshDatabase::class)` and these 9 tests. Response at `data.visit.*`:

  1. `it('allows doctor to update their own visit')` — PATCH with `['notes' => 'Updated notes']` as the owning doctor → assertOk, assertJsonPath('data.visit.attributes.notes', 'Updated notes')
  2. `it('prevents doctor from updating another doctors visit')` — PATCH as a different doctor → assertForbidden
  3. `it('allows admin to update any visit')` — PATCH as admin → assertOk
  4. `it('prevents patient from updating any visit')` — PATCH as patient → assertForbidden
  5. `it('returns 401 for unauthenticated update request')` — PATCH without auth → assertUnauthorized
  6. `it('rejects future date on visit update')` — PATCH with `['date' => now()->addDay()->toDateString()]` → assertUnprocessable, assertJsonValidationErrors(['date'])
  7. `it('allows partial update with notes only leaving date unchanged')` — PATCH with `['notes' => 'new']`; assertDatabaseHas('visits', ['id' => $visit->id, 'date' => $originalDate])
  8. `it('allows partial update with date only leaving notes unchanged')` — PATCH with `['date' => '2026-01-01']`; assertDatabaseHas('visits', ['id' => $visit->id, 'notes' => $originalNotes])
  9. `it('returns 404 when visit belongs to different patient than url on update')` — assertNotFound (scoped binding)

  Run and confirm all 9 FAIL with 501.

- [ ] T015 [P] [US4] Create `UpdateVisitRequest`:

  ```bash
  php artisan make:request UpdateVisitRequest --no-interaction
  ```

  Fill `app/Http/Requests/UpdateVisitRequest.php`. Namespace is `App\Http\Requests` (not `Api\`) — matches `UpdatePatientRequest`:

  ```php
  namespace App\Http\Requests;

  use App\Models\Visit;
  use Illuminate\Contracts\Validation\ValidationRule;
  use Illuminate\Foundation\Http\FormRequest;

  class UpdateVisitRequest extends FormRequest
  {
      public function authorize(): bool
      {
          /** @var Visit $visit */
          $visit = $this->route('visit');

          return $this->user()->can('update', $visit);
      }

      /** @return array<string, ValidationRule|array<mixed>|string> */
      public function rules(): array
      {
          return [
              'date'  => ['sometimes', 'date', 'before_or_equal:today'],
              'notes' => ['nullable', 'string', 'max:10000'],
          ];
      }

      public function messages(): array
      {
          return [
              'date.before_or_equal' => 'Visit date cannot be in the future.',
          ];
      }
  }
  ```

  The `authorize()` method calls the `update` policy (Constitution Principle II). The controller must NOT call `$this->authorize()` separately.

- [ ] T016 [US4] Implement `update()` in `app/Http/Controllers/Api/VisitController.php` — replace the 501 stub. Add import: `use App\Http\Requests\UpdateVisitRequest;`. Do NOT call `$this->authorize()` — `UpdateVisitRequest::authorize()` already enforces the policy (same pattern as `PatientController::update()`):

  ```php
  public function update(UpdateVisitRequest $request, Patient $patient, Visit $visit): JsonResponse
  {
      $visit->update($request->validated());

      return $this->ok('Visit updated successfully.', [
          'visit' => new VisitResource($visit->load('doctor')),
      ]);
  }
  ```

**Verification**: `php artisan test tests/Feature/Visit/VisitUpdateTest.php` — all 9 tests pass.

**Checkpoint**: US1 + US2 + US3 + US4 independently functional.

---

## Phase 7: User Story 5 — Admin Permanently Deletes a Visit (Priority: P5)

**Goal**: Admins permanently remove a visit record (hard delete — no soft delete, no recovery). Doctors and patients cannot delete.

**Independent Test**: DELETE as admin → 204, `assertDatabaseMissing('visits', ['id' => $visit->id])`. DELETE same URL as doctor → 403.

- [ ] T017 [US5] Write failing tests:

  ```bash
  php artisan make:test Visit/VisitDeleteTest --pest --no-interaction
  ```

  Fill `tests/Feature/Visit/VisitDeleteTest.php` with `uses(RefreshDatabase::class)` and these 7 tests:

  1. `it('allows admin to delete any visit')` — DELETE as admin → assertNoContent
  2. `it('prevents doctor from deleting visits')` — DELETE as doctor → assertForbidden
  3. `it('prevents patient from deleting visits')` — DELETE as patient → assertForbidden
  4. `it('returns 401 for unauthenticated delete request')` — DELETE without auth → assertUnauthorized
  5. `it('hard deletes the visit record from the database')` — assertDatabaseMissing('visits', ['id' => $visit->id]) after DELETE
  6. `it('does not leave a soft-deleted record')` — after DELETE, `Visit::find($visit->id)` is null (Visit has no SoftDeletes; `withTrashed()` does not exist on this model and must not be used here)
  7. `it('returns 404 when visit belongs to different patient than url on delete')` — assertNotFound (scoped binding)

  Run and confirm all 7 FAIL with 501.

- [ ] T018 [US5] Implement `destroy()` in `app/Http/Controllers/Api/VisitController.php` — replace the 501 stub:

  ```php
  public function destroy(Patient $patient, Visit $visit): JsonResponse
  {
      $this->authorize('delete', $visit);

      $visit->delete();

      return $this->noContent();
  }
  ```

**Verification**: `php artisan test tests/Feature/Visit/` — all 44 tests across 5 files pass.

**Checkpoint**: All 5 user stories complete and independently functional.

---

## Phase 8: Polish & Quality Gates

**Purpose**: Enforce Constitution Principle IV — three non-negotiable gates before the branch is merge-ready.

- [ ] T019 [P] Run Pint and fix all style violations: `vendor/bin/pint --dirty` — re-run until output shows 0 files changed
- [ ] T020 [P] Run Larastan and fix all level-5 errors: `composer run analyse` — if an error cannot be resolved, suppress it with `// @phpstan-ignore-line` on the affected line and add an inline comment explaining why
- [ ] T021 Run full test suite to confirm no regressions: `php artisan test` — all tests (existing domain + new HTTP) must pass

**Final Checkpoint**: T019 + T020 + T021 all green → branch is merge-ready.

---

## Dependencies & Execution Order

### Phase Dependencies

```
Phase 1 (verify) → Phase 2 (domain fixes) → Phase 3 (US1, MVP)
                                                    ↓
                                       Phases 4–7 in any order
                                       (all depend on Phase 3 for
                                        VisitResource + routes)
                                                    ↓
                                            Phase 8 (gates)
```

### Key Dependency Notes

- **Phases 4–7 depend on Phase 3** because `VisitResource` (T006) and the route registration (T008) are created in Phase 3. The controller exists with all stubs after T009, so adding endpoint implementations in Phases 4–7 cannot cause route registration errors.
- **Phases 4–7 are independent of each other** — each adds to (does not rewrite) `VisitController` and writes to its own test file.
- **T003 and T004 are parallel** — different files, no shared state.
- **T006 and T007 are parallel** — VisitResource and the test file are independent; both must exist before T009.
- **T014 and T015 are parallel** — test file and UpdateVisitRequest are independent; both must exist before T016.

### Within-Phase Dependency Diagrams

**Phase 3 (US1)**:
```
T006 (VisitResource) ─┐
                       ├─→ T009 (VisitController.store + all stubs)
T007 (tests RED)   ───┘
T008 (routes)  ───────→ needed before T009 so imports are resolved
```

**Phase 6 (US4)**:
```
T014 (tests RED) ─┐
                   ├─→ T016 (VisitController.update)
T015 (Request)  ──┘
```

---

## Parallel Examples

**Phase 3 (US1)**:
```bash
# Batch 1 — run simultaneously (different files):
# T006: implement VisitResource.php
# T007: write VisitCreateTest.php — confirm 10 tests FAIL

# Sequential after batch 1:
# T008: add route to api.php
# T009: create VisitController, implement store()
php artisan test tests/Feature/Visit/VisitCreateTest.php
```

**Phase 6 (US4)**:
```bash
# Batch 1 — run simultaneously:
# T014: write VisitUpdateTest.php — confirm 9 tests FAIL
# T015: create UpdateVisitRequest.php

# Sequential after batch 1:
# T016: implement update() in VisitController
php artisan test tests/Feature/Visit/VisitUpdateTest.php
```

---

## Implementation Strategy

### MVP First (Phases 1–3, 9 tasks)

1. Phase 1: verify domain layer
2. Phase 2: fix 3 domain bugs
3. Phase 3: US1 — POST endpoint
4. **STOP**: `php artisan test tests/Feature/Visit/VisitCreateTest.php` — 10 tests green
5. `POST /api/patients/{patient}/visits` is production-ready

### Incremental Delivery

```
Phase 3 (US1) → POST working
Phase 4 (US2) → GET list working
Phase 5 (US3) → GET single working
Phase 6 (US4) → PATCH working
Phase 7 (US5) → DELETE working
Phase 8      → gates green, merge
```

---

## Task Summary

| Phase | Story | Tasks | HTTP Tests | Verification |
|---|---|---|---|---|
| 1 Setup | — | T001 | 0 | existing suite green |
| 2 Foundational | — | T002–T005 | +4 policy tests | `test tests/Feature/visits/` |
| 3 US1 (POST) | P1 | T006–T009 | 10 | `test Visit/VisitCreateTest.php` |
| 4 US2 (GET list) | P2 | T010–T011 | 9 | `test Visit/VisitListTest.php` |
| 5 US3 (GET single) | P3 | T012–T013 | 9 | `test Visit/VisitShowTest.php` |
| 6 US4 (PATCH) | P4 | T014–T016 | 9 | `test Visit/VisitUpdateTest.php` |
| 7 US5 (DELETE) | P5 | T017–T018 | 7 | `test Visit/VisitDeleteTest.php` |
| 8 Polish | — | T019–T021 | 0 | full suite + Pint + Larastan |
| **Total** | | **21 tasks** | **44 HTTP tests** | |

---

## Pitfall Reference (from quickstart.md)

- **`viewAny` takes `Patient`** — call is `$this->authorize('viewAny', [Visit::class, $patient])`, not `Visit::class` alone
- **`create` is doctor-only** — admin + patient get 403 on store
- **`update` policy is in FormRequest** — `VisitController::update()` must NOT call `$this->authorize()`
- **`doctor_id` never comes from request** — always `$request->user()->id` in store
- **Scoped binding covers `{visit}` only** — patient-level index access is controlled by `viewAny` policy, not by scoping
- **Single-resource JSON path** — `data.visit.attributes.*`, not `data.attributes.*`
- **Collection JSON path** — `data.0.attributes.*` (no `visit` key in collections)
- **Hard delete confirmation** — use `Visit::withTrashed()->find($id)` to confirm no soft-deleted record remains
- **Existing tests in `tests/Feature/visits/`** — must stay green throughout; do not delete or rename
