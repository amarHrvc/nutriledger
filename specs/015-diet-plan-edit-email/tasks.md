# Tasks: Diet Plan Edit and Email Delivery (015)

**Input**: Design documents from `specs/015-diet-plan-edit-email/`
**Branch**: `015-diet-plan-edit-email`
**Stack**: Laravel 12 / PHP 8.4 (BE) · React + TypeScript (FE)
**TDD Discipline**: Constitution Principle III is NON-NEGOTIABLE — tests written FIRST (RED), then implementation (GREEN), then cleanup (REFACTOR). Each TDD phase is a separate atomic commit.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Parallelizable — different files, no shared dependency on an incomplete task
- **[USn]**: User story label — maps to spec.md user stories
- **Exact file paths** are included in every task description

---

## Phase 1: Setup (No new infrastructure required)

Feature 014 provides all queue, database, and SDK infrastructure. Proceed directly to Phase 2.

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Database schema, model extensions, and policy registration must exist before any user story can be implemented.

**⚠️ CRITICAL**: No user story work can begin until this phase is complete.

- [ ] T001 Create migration `backend/database/migrations/xxxx_add_edit_fields_to_patient_diet_plans_table.php` — add three columns to `patient_diet_plans`: `is_edited` (boolean, default `false`, NOT NULL), `edited_by` (unsignedBigInteger, nullable, FK → `users.id`, `nullOnDelete()`), `edited_at` (timestamp, nullable); run `php artisan migrate`

- [ ] T002 Create migration `backend/database/migrations/xxxx_create_diet_plan_deliveries_table.php` — columns: `id` (PK), `diet_plan_id` (unsignedBigInteger, FK → `patient_diet_plans.id`, cascade delete), `sent_by` (unsignedBigInteger, FK → `users.id`, restrict delete), `recipient_email` (varchar 255), `status` enum(`pending`,`sent`,`failed`) default `pending`, `failure_reason` varchar(500) nullable, `timestamps`; run `php artisan migrate`

- [ ] T003 [P] Create `backend/app/Models/DietPlanDelivery.php` — add `use HasFactory;` trait; `$fillable`: `['diet_plan_id', 'sent_by', 'recipient_email', 'status', 'failure_reason']`; two `BelongsTo` relations: `dietPlan(): BelongsTo` → `PatientDietPlan::class` (FK `diet_plan_id`), `sender(): BelongsTo` → `User::class` (FK `sent_by`); imports: `use App\Models\{PatientDietPlan, User}; use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Relations\BelongsTo;`

- [ ] T004 [P] Create `backend/database/factories/DietPlanDeliveryFactory.php` — default state: `status=pending`, `recipient_email=fake()->safeEmail()`; states: `sent()` (`status=sent`), `failed()` (`status=failed`, `failure_reason='SMTP timeout'`)

- [ ] T005 Update `backend/app/Models/PatientDietPlan.php` — (a) add `'is_edited', 'edited_by', 'edited_at'` to `$fillable`; (b) add to `casts()`: `'is_edited' => 'boolean', 'edited_at' => 'datetime'`; (c) add three new relations: `editor(): BelongsTo` → `User::class` (FK `edited_by`), `deliveries(): HasMany` → `DietPlanDelivery::class` (FK `diet_plan_id`), `latestDelivery(): HasOne` → `DietPlanDelivery::class` (FK `diet_plan_id`) → `->latestOfMany()`; add imports: `use App\Models\DietPlanDelivery; use Illuminate\Database\Eloquent\Relations\HasMany; use Illuminate\Database\Eloquent\Relations\HasOne;`

- [ ] T006 Update `backend/app/Providers/AppServiceProvider.php` — add `Gate::policy(PatientDietPlan::class, DietPlanPolicy::class);` inside `boot()`, following the existing `Gate::policy(VitalSign::class, VitalSignPolicy::class)` line; add imports: `use App\Models\PatientDietPlan; use App\Policies\DietPlanPolicy;` — **this was absent from 014; without it `authorize('update', $dietPlan)` silently falls back to deny-all, giving 403 to everyone**

- [ ] T007 [P] Create `backend/app/Policies/DietPlanPolicy.php` — **the file does NOT exist on disk** (014 git state `AD` = staged then deleted); create it unconditionally with all five methods:
  ```
  generate(User $user, Patient $patient): bool  → $user->isAdmin() || $user->isDoctor()
  viewAny(User $user, Patient $patient): bool   → $user->isAdmin() || $user->isDoctor()
  view(User $user, PatientDietPlan $plan): bool → $user->isAdmin() || $user->isDoctor()
  update(User $user, PatientDietPlan $plan): bool → $user->isAdmin() || $user->isDoctor()
  send(User $user, PatientDietPlan $plan): bool   → $user->isAdmin() || $user->isDoctor()
  ```
  imports: `use App\Models\{Patient, PatientDietPlan, User};`

**Checkpoint**: `php artisan migrate:status` shows both new migrations ran. `PatientDietPlan::factory()->make()->latestDelivery` is null. `Gate::getPolicyFor(PatientDietPlan::class)` returns `DietPlanPolicy`. `DietPlanDelivery::factory()->sent()->make()->status === 'sent'`.

---

## Phase 3: User Story 1 — Edit Diet Plan (Priority: P1) 🎯 MVP

**Goal**: Doctor PATCHes a completed plan; system validates, updates the record with edit metadata (`is_edited`, `edited_by`, `edited_at`), and returns the full updated plan. Access control (US3) 401/403 tests are embedded in RED.

**Independent Test**: `PATCH /api/patients/{patient}/diet-plans/{dietPlan}` as a doctor → 200, `isEdited: true`, `editedBy.name` populated; DB row has `is_edited=1`, `edited_by`, `edited_at` set.

### RED Phase — Write failing tests first, confirm they FAIL before GREEN

- [ ] T008 [US1] Create `backend/tests/Feature/diet-plans/DietPlanUpdateTest.php` using Pest `test()` syntax — cover: (a) doctor PATCHes completed plan with `['rationale' => 'Updated']` → 200, response has `data.diet_plan.isEdited === true`, `data.diet_plan.editedBy.name` equals the doctor's name; (b) admin PATCHes → 200; (c) guest → 401; (d) patient role → 403; (e) `dietPlan` belonging to a different patient → 404 (route scoping); (f) plan with `status=pending` → 422 with non-empty `message`; (g) plan with `status=failed` → 422 with non-empty `message`; (h) PATCH with `daily_calories=500` → 422; (i) PATCH with `days` containing 6 elements → 422; (j) DB assertions: after a valid PATCH, record has `is_edited=1`, `edited_by` = doctor ID, `edited_at` not null; run `php artisan test tests/Feature/diet-plans/DietPlanUpdateTest.php` and confirm ALL fail (RED)

### GREEN Phase — Implement to make T008 tests pass

- [ ] T009 [US1] Create `backend/app/Http/Requests/UpdateDietPlanRequest.php` — `authorize()`: `return $this->user()->can('update', $this->route('dietPlan'));` — **single model arg, not array form** (`[PatientDietPlan::class, $patient]` is the array form used only when the policy's extra arg is a *different* model, as in `generate`; here the policy arg IS the dietPlan); `rules()` uses `sometimes` on all field groups (partial update):
  ```php
  'rationale'                   => ['sometimes', 'required', 'string'],
  'daily_calories'              => ['sometimes', 'required', 'integer', 'between:1000,4000'],
  'nutritional_goals'           => ['sometimes', 'required', 'array'],
  'nutritional_goals.protein_g' => ['required_with:nutritional_goals', 'integer', 'min:0'],
  'nutritional_goals.carbs_g'   => ['required_with:nutritional_goals', 'integer', 'min:0'],
  'nutritional_goals.fat_g'     => ['required_with:nutritional_goals', 'integer', 'min:0'],
  'days'                        => ['sometimes', 'required', 'array', 'size:7'],
  'days.*.day'                  => ['required_with:days', 'string'],
  'days.*.breakfast'            => ['required_with:days', 'string'],
  'days.*.lunch'                => ['required_with:days', 'string'],
  'days.*.dinner'               => ['required_with:days', 'string'],
  'days.*.snack'                => ['required_with:days', 'string'],
  'warnings'                    => ['sometimes', 'nullable', 'array'],
  'warnings.*'                  => ['string'],
  ```
  add `messages()`: `['days.size' => 'A diet plan must contain exactly 7 days.']`

- [ ] T010 [US1] Implement `DietPlanController::update(UpdateDietPlanRequest $request, Patient $patient, PatientDietPlan $dietPlan): JsonResponse` in `backend/app/Http/Controllers/Api/DietPlanController.php` — steps in order: (1) `if ($dietPlan->patient_id !== $patient->id) abort(404);` — route scoping BEFORE authorize; (2) `if ($dietPlan->status !== 'completed') return $this->error('Only completed plans can be edited.', 422);`; (3) `$dietPlan->update(array_merge($request->validated(), ['is_edited' => true, 'edited_by' => $request->user()->id, 'edited_at' => now()]));`; (4) **load all three relations**: `$dietPlan->load(['doctor', 'editor', 'latestDelivery']);` — if `editor` is not loaded, `editedBy` will be null in the response even though `is_edited=true`; (5) `return $this->ok('Diet plan updated successfully.', ['diet_plan' => new DietPlanResource($dietPlan)]);`; add import: `use App\Http\Requests\UpdateDietPlanRequest;`

- [ ] T011 [US1] Register PATCH route in `backend/routes/api.php` inside the `auth:sanctum` group, beneath the existing diet-plan routes: `Route::patch('/patients/{patient}/diet-plans/{dietPlan}', [DietPlanController::class, 'update'])->name('patients.diet-plans.update');` — route must exist before T012 test run

- [ ] T012 [US1] Create `backend/app/Http/Resources/Api/DietPlanDeliveryResource.php` — `toArray()` returns: `'id' => $this->id`, `'status' => $this->status`, `'recipientEmail' => $this->recipient_email`, `'failureReason' => $this->when($this->status === 'failed', $this->failure_reason)`, `'createdAt' => $this->created_at->toDateTimeString()`

- [ ] T013 [US1] Update two resource files:

  **`backend/app/Http/Resources/Api/DietPlanResource.php`** — add to `toArray()`:
  ```php
  'isEdited'       => $this->is_edited,
  'editedAt'       => $this->when($this->is_edited, fn () => $this->edited_at?->toDateTimeString()),
  'editedBy'       => $this->when($this->is_edited, fn () => new UserResource($this->whenLoaded('editor'))),
  'latestDelivery' => $this->whenLoaded('latestDelivery', fn () => new DietPlanDeliveryResource($this->latestDelivery)),
  ```
  **⚠️ Do NOT write** `new DietPlanDeliveryResource($this->whenLoaded('latestDelivery'))` — passing `MissingValue` to a Resource constructor causes it to be serialized as an empty object. Use the callback form shown above. Add import: `use App\Http\Resources\Api\DietPlanDeliveryResource;`

  **`backend/app/Http/Resources/Api/DietPlanSummaryResource.php`** — add `'isEdited' => $this->is_edited` to `toArray()` (index list contract includes this field)

- [ ] T014 [US1] Run `php artisan test tests/Feature/diet-plans/DietPlanUpdateTest.php` — confirm ALL pass (GREEN); if test (a) fails on `editedBy`, check that `->load('editor')` is in `update()` before the return; if test (f) or (g) fails, check the `$this->error(...)` call returns the right status code

### REFACTOR Phase

- [ ] T015 [US1] Run `vendor/bin/pint --dirty` on all files modified in Phase 3 GREEN; run `composer run analyse`; run `php artisan test tests/Feature/diet-plans/DietPlanUpdateTest.php`; commit with message `"015 Pint + analyse (REFACTOR phase)\n\nCo-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>"`

**Checkpoint**: US1 complete. PATCH → 200, `isEdited: true`, `editedBy` populated. 401/403/404/422 enforced. Pint + Larastan green.

---

## Phase 4: User Story 2 — Send Diet Plan via Email (Priority: P2)

**Goal**: Doctor POSTs to `/send`; system creates a `DietPlanDelivery` (status `pending`), dispatches `SendDietPlanEmailJob`, returns 202. Job sends the email and updates delivery to `sent` or `failed`. Access control (US3) 401/403 tests are embedded in RED.

**Independent Test**: POST `/send` → 202; `Mail::fake()` asserts `DietPlanMailable` dispatched to the patient's email; `DietPlanDelivery` row exists with `status=pending`.

### RED Phase — Write failing tests first, confirm they FAIL before GREEN

- [ ] T016 [US2] Create `backend/tests/Feature/diet-plans/DietPlanSendTest.php` using Pest `test()` syntax — cover: (a) doctor sends completed plan → 202, response has `data.delivery.id` and `data.delivery.status === 'pending'`; (b) `Mail::fake()` + `Mail::assertQueued(DietPlanMailable::class, fn ($m) => $m->hasTo($patient->user->email))` — **use `Mail::fake()` not `Queue::fake()`** (Queue::fake stops jobs; Mail::fake intercepts Mailables directly); (c) `DietPlanDelivery` created in DB with `status=pending`, `recipient_email`, `sent_by=doctor->id`; (d) admin sends → 202; (e) guest → 401; (f) patient role → 403; (g) `status=pending` plan → 422 with non-empty `message`; (h) `status=failed` plan → 422; (i) dietPlan belonging to different patient → 404; (j) patient user has `email=null` → 422 with message about missing email; (k) two consecutive POSTs create two separate `DietPlanDelivery` rows; run `php artisan test tests/Feature/diet-plans/DietPlanSendTest.php` and confirm ALL fail (RED)

### GREEN Phase — Implement to make T016 tests pass

- [ ] T017 [P] [US2] Create `backend/app/Mail/DietPlanMailable.php` — `public function __construct(public PatientDietPlan $plan) {}`; `envelope(): Envelope` returns `new Envelope(subject: 'Your Personalised Diet Plan')`; `content(): Content` returns `new Content(view: 'emails.diet-plan')`; imports: `use Illuminate\Mail\Mailables\{Content, Envelope};`

- [ ] T018 [P] [US2] Create `backend/resources/views/emails/diet-plan.blade.php` — HTML email with **all styles inline** (`<style>` blocks are stripped by email clients); structure: (1) greeting `Dear {{ $plan->patient->full_name }}`; (2) rationale paragraph; (3) calorie + macro row (`daily_calories`, `nutritional_goals.protein_g`, `carbs_g`, `fat_g`); (4) `<table>` with one `<tr>` per day showing day name, breakfast, lunch, dinner, snack; (5) warnings list if `count($plan->warnings) > 0`; (6) footer `"This plan has been prepared by {{ $plan->doctor->name }}"` — the `$plan` variable is passed automatically because the Mailable sets `public PatientDietPlan $plan`

- [ ] T019 [US2] Create `backend/app/Jobs/SendDietPlanEmailJob.php` — implements `ShouldQueue`; `public int $tries = 1;` (prevents Laravel retrying 3× and sending 3 emails on failure); `public function __construct(public DietPlanDelivery $delivery) {}`; `handle()`:
  ```php
  public function handle(): void
  {
      try {
          $plan = $this->delivery->dietPlan()
              ->with(['patient', 'doctor'])   // patient.user NOT needed — email is already in delivery->recipient_email
              ->firstOrFail();

          Mail::to($this->delivery->recipient_email)->send(new DietPlanMailable($plan));

          $this->delivery->update(['status' => 'sent']);
      } catch (Throwable $e) {
          $this->delivery->update(['status' => 'failed', 'failure_reason' => $e->getMessage()]);
      }
  }
  ```
  imports: `use App\Mail\DietPlanMailable; use Illuminate\Support\Facades\Mail; use Throwable;`

- [ ] T020 [US2] Create `backend/app/Http/Requests/SendDietPlanRequest.php` — `authorize()`: `return $this->user()->can('send', $this->route('dietPlan'));`; `rules()`: returns `[]`

- [ ] T021 [US2] Implement `DietPlanController::send(SendDietPlanRequest $request, Patient $patient, PatientDietPlan $dietPlan): JsonResponse` — steps: (1) `if ($dietPlan->patient_id !== $patient->id) abort(404);`; (2) `if ($dietPlan->status !== 'completed') return $this->error('Only completed plans can be sent.', 422);`; (3) `$dietPlan->loadMissing('patient.user');` then `$email = $dietPlan->patient->user?->email;` then `if (! $email) return $this->error('Patient has no email address on file.', 422);`; (4) `$delivery = DietPlanDelivery::create(['diet_plan_id' => $dietPlan->id, 'sent_by' => $request->user()->id, 'recipient_email' => $email, 'status' => 'pending']);`; (5) `SendDietPlanEmailJob::dispatch($delivery);`; (6) `return $this->success('Diet plan is being sent to the patient.', 202, ['delivery' => new DietPlanDeliveryResource($delivery)]);`; add imports: `use App\Http\Requests\SendDietPlanRequest; use App\Jobs\SendDietPlanEmailJob; use App\Models\DietPlanDelivery;`

- [ ] T022 [US2] Register POST send route in `backend/routes/api.php` beneath the PATCH route: `Route::post('/patients/{patient}/diet-plans/{dietPlan}/send', [DietPlanController::class, 'send'])->name('patients.diet-plans.send');`

- [ ] T023 [US2] Update `DietPlanController::show()` in `backend/app/Http/Controllers/Api/DietPlanController.php` — change `$dietPlan->load('doctor')` to `$dietPlan->load(['doctor', 'editor', 'latestDelivery'])` so the show response includes edit metadata and last delivery status

- [ ] T024 [US2] Run `php artisan test tests/Feature/diet-plans/DietPlanSendTest.php` — confirm ALL pass (GREEN); if test (b) fails on `Mail::assertQueued`, confirm `Mail::fake()` is called in `beforeEach()` not `Queue::fake()`; if test (j) fails on the email guard, confirm `loadMissing('patient.user')` is called before the null check

### REFACTOR Phase

- [ ] T025 [US2] Run `vendor/bin/pint --dirty`; run `composer run analyse`; run `php artisan test tests/Feature/diet-plans/`; commit with message `"015 Pint + analyse + full suite (REFACTOR phase)\n\nCo-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>"`

**Checkpoint**: All BE endpoints work (index, show, update, send). Full Pest suite green. Pint + Larastan green.

---

## Phase 5: Frontend — Diet Plan Edit and Send UI (US1 + US2)

**Goal**: React UI on the patient profile: "Edit Plan" opens an inline form; saving calls PATCH; "Send to Patient" calls POST /send; edited badge and last-sent indicator appear in DietPlanCard.

**Note**: The diet plan FE components use raw `fetch()` calls — **not the orval-generated API client**. Running `pnpm run api:generate` is NOT required for this feature.

**Independent Test**: Patient profile with a completed plan renders Edit + Send buttons; Edit opens the form; Save calls PATCH and re-renders with "Edited" badge; Send fires POST and shows delivery badge.

### Types first (unblocks all FE tasks)

- [ ] T026 [P] [US1] Update `frontend/src/views/patients/diet-plans/types.ts` — add new interface and extend existing ones:
  ```ts
  export interface DeliveryRecord {
    id: string
    status: 'pending' | 'sent' | 'failed'
    recipientEmail: string
    failureReason: string | null
    createdAt: string
  }

  export interface EditedBy {
    id: string
    name: string
  }
  ```
  Extend `DietPlanSummary` with: `isEdited: boolean`
  Extend `DietPlan` (which extends `DietPlanSummary`) with:
  ```ts
  editedAt: string | null
  editedBy: EditedBy | null
  latestDelivery: DeliveryRecord | null
  ```

### Implementation

- [ ] T027 [P] [US2] Create `frontend/src/views/patients/diet-plans/DietPlanDeliveryBadge.tsx` — receives `latestDelivery: DeliveryRecord | null`; renders nothing if null; green MUI `<Chip>` "Sent [formatted date]" if `status=sent`; amber Chip "Sending…" if `status=pending`; red Chip "Send failed" with `<Tooltip title={latestDelivery.failureReason}>` if `status=failed`

- [ ] T028 [US1] Create `frontend/src/views/patients/diet-plans/DietPlanEditForm.tsx` — props: `plan: DietPlan`, `patientId: number`, `onSave: (updated: DietPlan) => void`, `onCancel: () => void`; state: local copy of all editable fields + `isDirty: boolean` + `saving: boolean` + `errors: Record<string, string>`; renders: `<TextField>` for rationale, number inputs for `dailyCalories` and each macro, 7-row grid of text inputs (breakfast/lunch/dinner/snack) per day, warnings tag list; **Cancel guard**: if `isDirty`, call `window.confirm('Unsaved changes will be lost. Continue?')` before invoking `onCancel`; **Submit**: `PATCH /api/patients/${patientId}/diet-plans/${plan.id}` with only the changed fields; on 200 call `onSave(json.data.diet_plan)`; on 422 parse `json.errors` and display inline under each field; disable Save while `saving=true`

- [ ] T029 [US1] Update `frontend/src/views/patients/diet-plans/DietPlanCard.tsx` — update `Props` interface: add `patientId: number`, `onUpdate: (updated: DietPlan) => void`; add `isEditing: boolean` state; when `isEditing=false`: show existing card, add "Edit Plan" `<Button>` (disabled unless `plan.status === 'completed'`), add "Send to Patient" `<Button>` (disabled if `plan.status !== 'completed'`), render `<Chip label="Edited" size="small">` if `plan.isEdited`, render `<DietPlanDeliveryBadge latestDelivery={plan.latestDelivery ?? null} />`; when `isEditing=true`: render `<DietPlanEditForm plan={plan} patientId={patientId} onSave={updated => { setIsEditing(false); onUpdate(updated); }} onCancel={() => setIsEditing(false)} />`; "Send to Patient" click: if `plan.latestDelivery !== null`, show `window.confirm('This plan was already sent on [date]. Send again?')`; then `POST /api/patients/${patientId}/diet-plans/${plan.id}/send`; show MUI `<Alert>` snackbar on success/failure; imports: `DietPlanEditForm`, `DietPlanDeliveryBadge`

- [ ] T030 [US1] Update `frontend/src/views/patients/diet-plans/DietPlanSection.tsx` — pass `patientId={patientId}` and `onUpdate={updated => setPlans(plans.map(p => p.id === updated.id ? updated : p))}` into `<DietPlanCard>`; after `onUpdate` fires, re-fetch the full plan via `GET /api/patients/${patientId}/diet-plans/${updated.id}` to refresh `latestDelivery` and edit metadata in state; run `pnpm run dev` and verify in browser: edit form opens, save updates card, Edited badge appears, send button shows delivery badge

**Checkpoint**: Patient profile renders Edit + Send. Edit form covers all 7 days. Cancel guards work. Save updates card with Edited badge. Send shows DeliveryBadge. No TypeScript errors.

---

## Phase 6: Polish & Cross-Cutting Concerns

- [ ] T031 [P] Run full backend quality gates: `vendor/bin/pint --dirty`, `composer run analyse`, `php artisan test` — all 014 and 015 tests green
- [ ] T032 [P] Run `pnpm run lint` and `pnpm run build` in `frontend/` — no TypeScript or lint errors; build succeeds
- [ ] T033 Update `specs/015-diet-plan-edit-email/quickstart.md` with corrections found during implementation — mail config, `Mail::fake()` vs `Mail::assertQueued()` note, any `latestDelivery` eager-loading gotchas

---

## Dependencies & Execution Order

### Phase Dependencies

- **Phase 2 (Foundational)**: No dependencies — start immediately; BLOCKS all user stories
- **Phase 3 (US1)**: Needs T005 (model), T006 (policy register), T007 (policy methods)
- **Phase 4 (US2)**: Needs Phase 2 — can run parallel to Phase 3 (different files)
- **Phase 5 (FE)**: Needs Phase 3 PATCH endpoint + Phase 4 POST /send endpoint; start with T026 (types) as it unblocks all other FE tasks
- **Phase 6 (Polish)**: Depends on all previous phases

### User Story Dependencies

- **US1 (P1 Edit)**: Unblocked after Phase 2
- **US2 (P2 Send)**: Unblocked after Phase 2; shares `DietPlanController` — adds new methods only, no conflicts with US1
- **US3 (access control)**: Embedded in T008 (RED) and T016 (RED)

### TDD Commit Sequence

```
US1:
  commit: "015 Write failing DietPlanUpdateTest (RED phase)
  
  Co-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>"    ← T008

  commit: "015 Implement update endpoint + resources (GREEN phase)

  Co-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>"   ← T009–T014

  commit: "015 Pint + analyse (REFACTOR phase)

  Co-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>"   ← T015

US2:
  commit: "015 Write failing DietPlanSendTest (RED phase)

  Co-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>"   ← T016

  commit: "015 Implement send endpoint + job + mailable (GREEN phase)

  Co-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>"   ← T017–T024

  commit: "015 Pint + analyse + full suite (REFACTOR phase)

  Co-authored-by: Copilot <223556219+Copilot@users.noreply.github.com>"   ← T025
```

### Parallel Opportunities

- Phase 2: T003+T004 ∥ T006+T007 (model/factory vs provider/policy — different files)
- Phase 4 GREEN: T017 ∥ T018 (Mailable class vs Blade template)
- Phase 5: T026 ∥ T027 (types vs DeliveryBadge, both read-only dependencies)
- Phase 6: T031 ∥ T032 (BE gates vs FE lint/build)

---

## Implementation Strategy

### MVP (US1 only — edit, no email)

1. Phase 2 (T001–T007)
2. Phase 3 (T008–T015)
3. **VALIDATE**: `PATCH /api/patients/1/diet-plans/1` as doctor → 200, DB has `is_edited=1`

### Full BE Delivery

1. MVP above
2. Phase 4 (T016–T025): send endpoint + mailable + job
3. All Pest tests pass, Pint + Larastan green

### Full Delivery (BE + FE)

1. Full BE delivery
2. Phase 5 (T026–T030): types → components → wiring
3. Phase 6 (T031–T033): lint, build, docs

---

## Notes & Common Pitfalls

- **`DietPlanPolicy` does not exist on disk** — T007 must create it, not update it
- **Policy registration was missing from 014** — T006 adds `Gate::policy(PatientDietPlan::class, DietPlanPolicy::class)` to `AppServiceProvider`; without it all `authorize()` calls return 403
- **`authorize()` in FormRequest — single vs array form**: `can('update', $this->route('dietPlan'))` (single model, routes to `DietPlanPolicy::update`) vs `can('generate', [PatientDietPlan::class, $patient])` (array form used when the policy arg is a *different* model). Use single form for `update` and `send`
- **`whenLoaded` wrapping**: `new SomeResource($this->whenLoaded('relation'))` is WRONG — use `$this->whenLoaded('relation', fn () => new SomeResource($this->relation))` callback form
- **`Mail::fake()` not `Queue::fake()`**: use `Mail::fake()` in `DietPlanSendTest`; `Queue::fake()` intercepts jobs but not Mailables sent via `Mail::to()->send()`
- **`$tries = 1` on `SendDietPlanEmailJob`**: prevents Laravel default of 3 retries (= 3 emails sent on transient failure)
- **Blade email — inline styles only**: email clients strip `<style>` tags; use `style=""` attributes on every element
- **`patient.user` in job NOT needed**: recipient email is already stored in `delivery->recipient_email`; only `patient` and `doctor` relations are needed for the email template
- **FE uses raw `fetch()`, not orval**: `pnpm run api:generate` is NOT required for this feature
- All BE tests use Pest `test()` function syntax (not PHPUnit class-based)
