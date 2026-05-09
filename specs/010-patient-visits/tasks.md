# Tasks: Patient Visits Management

**Input**: Design documents from `/specs/010-patient-visits/`  
**Prerequisites**: plan.md ✓, spec.md ✓, research.md ✓, data-model.md ✓, contracts/ ✓

**Tests**: Included (Pest — constitution Principle III).  
**TDD discipline**: RED commit (failing tests) → GREEN commit (implementation) → REFACTOR commit (cleanup). Within each story, RED tasks are listed before implementation tasks.

**BFF pattern**: BFF routes import `customFetchMutator` from `@/api/auth.mutator` and call it as  
`customFetchMutator<T>('http://localhost:8000/api/...', { method, headers?, body? })`.  
The mutator reads `auth_token` from cookies and attaches `Authorization: Bearer <token>`. See `frontend/src/api/auth.mutator.ts` and `frontend/src/app/api/patients/[id]/route.ts` for the exact pattern.

---

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel with other [P] tasks in the same phase (different files, no unmet dependency)
- **[Story]**: Maps to user story from spec.md

---

## Phase 1: Setup

**Purpose**: Create and apply the database migration before any code depends on the new column.

- [ ] T001 Create migration `backend/database/migrations/xxxx_add_time_to_visits_table.php` via `php artisan make:migration add_time_to_visits_table --table=visits --no-interaction`; in `up()`: `$table->time('time')->nullable()->after('date');`; in `down()`: `$table->dropColumn('time');`
- [ ] T002 Run `cd backend && php artisan migrate`; verify with `php artisan tinker --execute="echo implode(', ', Schema::getColumnListing('visits'));"` — output must include `time`

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Model, resource shape, policy rules, and FE types — all parallel after T002 completes.

**⚠️ CRITICAL**: No user story work can begin until this phase is complete.

- [ ] T003 [P] Update `backend/app/Models/Visit.php` — add `'time'` to `$fillable`; add `'time' => 'string'` to `casts()`  
  **Verify**: `php artisan test --filter=Visit` — all existing tests still pass

- [ ] T004 [P] Update `backend/app/Http/Resources/Api/VisitResource.php` — add to `attributes` array: `'time' => $this->time`, `'patientName' => $this->whenLoaded('patient', fn () => $this->patient->user?->name)`, `'patientId' => $this->whenLoaded('patient', fn () => (string) $this->patient->id)`, `'isEditable' => $this->date->toDateString() >= now()->subDay()->toDateString()`  
  **Verify**: `php artisan test --filter=VisitResource`

- [ ] T005 [P] Update `backend/app/Policies/VisitPolicy.php` — (a) add method `public function globalIndex(User $user): bool { return $this->adminOrDoctor($user); }`; (b) prepend 1-day lock to `update()`: `if ($visit->date->toDateString() < now()->subDay()->toDateString()) { return false; }`  
  **Verify**: `php artisan test --filter=VisitPolicy`

- [ ] T006 [P] Append to `frontend/src/api/generated/nutriBaseAPI.schemas.ts` after the last `Patients*` type block:
  ```ts
  export interface VisitResourceAttributes {
    date: string;
    time: string | null;
    notes: string | null;
    doctorName: string | null;
    patientName: string | null;
    patientId: string | null;
    isEditable: boolean;
    createdAt: string | null;
    updatedAt: string | null;
  }
  export interface VisitResource {
    type: 'visit';
    id: string;
    attributes: VisitResourceAttributes;
    relationships: {
      patient: { data?: { type: 'patient'; id: string } };
      doctor:  { data?: { type: 'user';    id: string } };
    };
  }
  export type VisitsGlobalIndex200 = {
    message: string; status: 200;
    data: VisitResource[];
    meta: PatientsIndex200Meta;
    links: PatientsIndex200Links;
  };
  export type PatientVisitsIndex200 = {
    message: string; status: 200;
    data: VisitResource[];
    meta: PatientsIndex200Meta;
    links: PatientsIndex200Links;
  };
  ```  
  **Verify**: `cd frontend && npx tsc --noEmit` — no new errors

**Checkpoint**: `php artisan test --filter=Visit` — all existing visit tests pass. TS compiles clean.

---

## Phase 3: User Story 1 — Doctor Views and Manages Visits (Priority: P1) 🎯 MVP

**Goal**: Doctor sees own visits on the Visits page (upcoming/past split), creates visits with date+time, edits within the 1-day window.

**Independent Test**: Log in as a doctor → `/dashboard/visits` → list shows only that doctor's visits with upcoming highlighted → create a visit with a future date → visit appears in upcoming section → edit a visit from today → saves → edit button absent on a visit from 2+ days ago.

### RED Phase — write failing tests first (commit before implementing)

- [ ] T007 [P] [US1] Write `tests/Feature/visits/VisitGlobalIndexTest.php` — cases: (1) doctor sees only own visits, 200 + assert other doctor's visits absent; (2) doctor with no visits → 200 + empty `data`; (3) guest → 401; (4) patient role → 403. All tests must **fail** before T012–T013. Commit: `[010] VisitGlobalIndexTest (RED phase)`

- [ ] T008 [P] [US1] Write/update `tests/Feature/visits/VisitStoreTest.php` — new cases: (1) doctor creates visit with `date` + `time` → 201; (2) missing `time` → 422; (3) future `date` → 201 (regression: old `before_or_equal:today` no longer blocks). Commit: `[010] VisitStoreTest time field (RED phase)`

- [ ] T009 [P] [US1] Write/update `tests/Feature/visits/VisitUpdateTest.php` — cases: (1) edit visit dated today → 200; (2) edit visit dated yesterday → 200 (boundary: still editable); (3) edit visit dated 2 days ago → 403 (locked); (4) wrong doctor → 403. Commit: `[010] VisitUpdateTest 1-day lock (RED phase)`

### GREEN Phase — backend implementation

- [ ] T010 [P] [US1] Update `backend/app/Http/Requests/StoreVisitRequest.php` — in `rules()`: change `date` to `['required', 'date']`; add `'time' => ['required', 'date_format:H:i']`; remove the `messages()` override for `before_or_equal`  
  **Verify**: `php artisan test --filter=VisitStore`

- [ ] T011 [P] [US1] Update `backend/app/Http/Requests/UpdateVisitRequest.php` — in `rules()`: change `date` to `['sometimes', 'date']`; add `'time' => ['sometimes', 'date_format:H:i']`; remove `messages()` override  
  **Verify**: `php artisan test --filter=VisitUpdate`

- [ ] T012 [US1] Update `backend/app/Http/Controllers/Api/VisitController.php` — (a) add `globalIndex()`:
  ```php
  public function globalIndex(): JsonResponse
  {
      $this->authorize('globalIndex', Visit::class);
      $today = now()->toDateString();
      $query = Visit::query()->with(['doctor', 'patient.user']);
      if (auth()->user()->isDoctor()) {
          $query->where('doctor_id', auth()->id());
      }
      $visits = $query
          ->orderByRaw("CASE WHEN date >= '{$today}' THEN 0 ELSE 1 END")
          ->orderBy('date')->orderBy('time')
          ->paginate();
      return $this->paginated('Visits retrieved successfully.', VisitResource::collection($visits));
  }
  ```
  (b) update `store()`: add `'time' => $request->time` to the `create()` array; (c) update `show()` and `update()` to eager-load `->load(['doctor', 'patient.user'])` before returning VisitResource  
  **Depends on**: T010, T011  
  **Verify**: `php artisan test --filter=VisitGlobalIndex`

- [ ] T013 [US1] Add route in `backend/routes/api.php` inside the `role:admin,doktor` middleware group: `Route::get('/visits', [VisitController::class, 'globalIndex'])->name('visits.index');`; add `use App\Http\Controllers\Api\VisitController;` if not already imported  
  **Depends on**: T012  
  **Verify**: `php artisan route:list --name=visits.index` shows the route; `php artisan test --filter=VisitGlobalIndex` — all tests green. Commit GREEN + REFACTOR.

### Frontend — US1 (parallel with BE after Phase 2)

- [ ] T014 [P] [US1] Create `frontend/src/app/api/visits/route.ts` — import `customFetchMutator` from `@/api/auth.mutator` and `VisitsGlobalIndex200` from `@/api/generated/nutriBaseAPI.schemas`; GET handler:
  ```ts
  export async function GET() {
    const res = await customFetchMutator<{ data: VisitsGlobalIndex200; status: number }>(
      'http://localhost:8000/api/visits', { method: 'GET' }
    )
    return new Response(JSON.stringify(res.data), { status: res.status })
  }
  ```  
  **Verify**: `npx tsc --noEmit` passes; in browser dev tools, `GET /api/visits` carries `Authorization: Bearer ...`

- [ ] T015 [P] [US1] Rewrite `frontend/src/app/api/patients/[id]/visits/route.ts` — replace Cookie-forwarding GET with `customFetchMutator` Bearer pattern; add POST handler:
  ```ts
  import { customFetchMutator } from '@/api/auth.mutator'
  export async function GET(_req: NextRequest, { params }: { params: Promise<{ id: string }> }) {
    const { id } = await params
    const res = await customFetchMutator<{ data: PatientVisitsIndex200; status: number }>(
      `http://localhost:8000/api/patients/${id}/visits`, { method: 'GET' }
    )
    return new Response(JSON.stringify(res.data), { status: res.status })
  }
  export async function POST(req: NextRequest, { params }: { params: Promise<{ id: string }> }) {
    const { id } = await params
    const body = await req.json()
    const res = await customFetchMutator<{ data: unknown; status: number }>(
      `http://localhost:8000/api/patients/${id}/visits`,
      { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) }
    )
    return new Response(JSON.stringify(res.data), { status: res.status })
  }
  ```  
  **Verify**: `npx tsc --noEmit`; GET and POST to `/api/patients/[id]/visits` carry Bearer token in network tab

- [ ] T016 [P] [US1] Create `frontend/src/app/api/patients/[id]/visits/[visitId]/route.ts` — PATCH handler using same `customFetchMutator` pattern: URL `` `http://localhost:8000/api/patients/${id}/visits/${visitId}` ``, method `PATCH`, JSON body forwarded from request  
  **Verify**: `npx tsc --noEmit`

- [ ] T017 [P] [US1] Create `frontend/src/views/visits/VisitForm.tsx` — props: `{ patientId?: string; onSuccess?: () => void; onCancel?: () => void }`. Fields: (a) patient `<Select>` — fetch `/api/patients` on mount, options built as `{ value: p.id, label: p.attributes.fullName }`; hide entire field when `patientId` prop is provided; (b) `<TextField type='datetime-local' label='Date & Time' InputLabelProps={{ shrink: true }} required />`; (c) notes `<TextField multiline rows={3} />`. Submit: split `datetimeValue.split('T')` → `[date, time]`; POST to `/api/patients/${resolvedPatientId}/visits` with `{ date, time, notes }`; 422 → set field errors; non-422 error → set `formError` alert; on success → `window.dispatchEvent(new CustomEvent('visits:changed'))` + `onSuccess?.()`. Follow `PatientForm.tsx` structure exactly (Stack spacing, Alert, CircularProgress submit button).  
  **Verify**: `npx tsc --noEmit`

- [ ] T018 [P] [US1] Create `frontend/src/views/visits/VisitEditForm.tsx` — props: `{ visit: VisitResource; patientId: string; onSuccess?: () => void; onCancel?: () => void }`. Pre-fill `datetime-local` as `` `${visit.attributes.date}T${visit.attributes.time?.slice(0, 5) ?? '00:00'}` ``; pre-fill notes. PATCH to `/api/patients/${patientId}/visits/${visit.id}`; handle 422 + generic `formError`. Follow `PatientEditForm.tsx` structure.  
  **Verify**: `npx tsc --noEmit`

- [ ] T019 [US1] Rewrite `frontend/src/views/visits/index.tsx` — fetch `GET /api/visits` on mount; split response: `const today = new Date().toISOString().split('T')[0]`; `upcoming = data.filter(v => v.attributes.date >= today)` sorted ASC by date then time; `past = data.filter(v => v.attributes.date < today)` sorted DESC. Render: toolbar with "Add Visit" button (opens `VisitForm` in Dialog); two sections — `<Chip label="Upcoming" color="primary" />` header above upcoming table, "Past Visits" Typography header above past table; columns: Date, Time, Patient, Doctor, Notes, Edit action; Edit button: `disabled={!v.attributes.isEditable}`, opens `VisitEditForm` dialog with `patientId={v.attributes.patientId}`; re-fetch on `visits:changed` event. Loading/empty/error states matching `PatientList.tsx` pattern.  
  **Depends on**: T014, T017, T018  
  **Verify**: Doctor login → Visits page renders own visits; create visit → appears in Upcoming; edit today's visit → saves; edit 2-day-old visit → button disabled

**Checkpoint — US1 complete**: `php artisan test --filter=Visit` all green. Doctor workflow end-to-end functional.

---

## Phase 4: User Story 2 — Admin Views and Manages All Visits (Priority: P2)

**Goal**: Admin sees all system visits; admin can create a visit and assign any doctor.

**Independent Test**: Admin login → Visits page shows visits from all doctors → create visit selecting Doctor A + Patient B → Doctor A's Visits page shows the new visit.

### RED Phase

- [ ] T020 [P] [US2] Add admin cases to `tests/Feature/visits/VisitGlobalIndexTest.php` — (1) admin sees all visits from all doctors (200 + count across multiple doctors); commit `[010] VisitGlobalIndexTest admin scope (RED phase)`

- [ ] T021 [P] [US2] Add admin cases to `tests/Feature/visits/VisitStoreTest.php` — (1) admin creates visit with valid `doctor_id` → 201, visit has correct `doctor_id`; (2) admin without `doctor_id` → 201, defaults to self; (3) admin with `doctor_id` pointing to non-doctor user → 422; commit `[010] VisitStoreTest admin doctor_id (RED phase)`

### GREEN Phase

- [ ] T022 [US2] Update `backend/app/Http/Requests/StoreVisitRequest.php` — add `'doctor_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', 'doktor')]`; add `use Illuminate\Validation\Rule;` at top  
  **Verify**: `php artisan test --filter=VisitStore`

- [ ] T023 [US2] Update `VisitController::store()` in `backend/app/Http/Controllers/Api/VisitController.php` — replace hardcoded `auth()->id()` with: `$doctorId = (auth()->user()->isAdmin() && $request->filled('doctor_id')) ? (int) $request->doctor_id : auth()->id();`  
  **Depends on**: T022  
  **Verify**: `php artisan test --filter=VisitStore` — all green. Commit GREEN + REFACTOR.

### Frontend — US2

- [ ] T024 [US2] Update `frontend/src/views/visits/VisitForm.tsx` — add doctor selector for admin: (a) call `useAuth()` to get `user.role`; (b) when `role === 'admin'`, fetch `/api/users` on mount and filter client-side to `role === 'doktor'` (the users BFF at `app/api/users/route.ts` already returns all users with `paginate:false`; filter the array in component); render `<Select label='Doctor' required>` with options `{ value: u.id, label: u.name }`; (c) include `doctor_id: selectedDoctorId` in POST body when admin. Doctor role never sees this selector.  
  **Verify**: Admin login → VisitForm shows doctor selector; doctor login → selector absent

**Checkpoint — US2 complete**: Admin visits page shows all visits; admin creates visit for any doctor+patient; `php artisan test --filter=Visit` all green.

---

## Phase 5: User Story 3 — View and Add Visits from Patient Profile (Priority: P3)

**Goal**: Patient profile Visits tab shows real data; Add Visit button pre-fills patient and creates from context.

**Independent Test**: Open patient profile → Visits tab → real visits listed → Add Visit opens form with patient pre-filled → submit → visit appears in profile tab and on global Visits page.

### Frontend — US3

- [ ] T025 [US3] Rewrite `frontend/src/views/patients/patient-right/visits/index.tsx` — fetch `GET /api/patients/${patient.id}/visits` on mount (uses BFF from T015); render visits list: columns date, time, doctorName, notes; Edit action opens `VisitEditForm` (uses PATCH BFF from T016) with `disabled={!v.attributes.isEditable}`; "Add Visit" button opens `VisitForm` with `patientId={patient.id}` pre-filled; loading/error/empty states; re-fetch on `visits:changed` event.  
  **Depends on**: T015 (GET + POST BFF), T016 (PATCH BFF), T017 (VisitForm), T018 (VisitEditForm)  
  **Verify**: Patient profile Visits tab renders visits; Add Visit opens pre-filled form; created visit appears in global Visits page

**Checkpoint — US3 complete**: Full profile-context visit workflow functional.

---

## Phase 6: Polish & Cross-Cutting Concerns

- [ ] T026 [P] Run `cd backend && vendor/bin/pint --dirty`; fix all style violations in changed files
- [ ] T027 [P] Run `cd backend && composer run analyse`; fix any Larastan level-5 type errors (check `globalIndex()` return type annotation, `VisitResource` new fields, `StoreVisitRequest` `doctor_id` cast)
- [ ] T028 Run `cd backend && php artisan test`; verify zero regressions across Patient, User, and Visit test suites
- [ ] T029 Run `cd frontend && npx tsc --noEmit`; verify no TypeScript errors across all changed files

---

## Dependencies & Execution Order

### Phase Dependencies

```
Phase 1 (T001–T002)  →  Phase 2 (T003–T006)  →  Phase 3 (T007–T019)
                                                  →  Phase 4 (T020–T024)
                                                  →  Phase 5 (T025, after T015–T018)
All phases complete  →  Phase 6 (T026–T029)
```

### Within Phase 3 — Parallel Groups

```
Group A (parallel, different files — start after Phase 2):
  T007  VisitGlobalIndexTest RED
  T008  VisitStoreTest RED
  T009  VisitUpdateTest RED
  T010  StoreVisitRequest
  T011  UpdateVisitRequest
  T014  BFF GET /api/visits
  T015  BFF patients/[id]/visits
  T016  BFF patients/[id]/visits/[visitId]
  T017  VisitForm.tsx
  T018  VisitEditForm.tsx

Group B (after Group A):
  T012  VisitController (depends on T010, T011)
  T013  Route registration (depends on T012)
  T019  VisitsView (depends on T014, T017, T018)
```

### Key Story Dependencies

| Story | Depends on |
|---|---|
| US1 (P1) | Phase 2 only |
| US2 (P2) | US1 BE complete (T010–T013) |
| US3 (P3) | T015, T016, T017, T018 from Phase 3 |

---

## Implementation Strategy

### MVP (US1 only) — 19 tasks

1. Phase 1 + 2 → T001–T006
2. Phase 3 → T007–T019
3. Validate: Doctor login → full visit workflow end-to-end
4. Run quality gates (T026–T029) before PR

### Incremental Delivery

- Phase 1+2+3 → Doctor workflow (MVP, demo-ready)
- + Phase 4 → Admin workflow
- + Phase 5 → Profile integration
- + Phase 6 → Quality gates → PR

### Parallel (2 developers)

After Phase 2:
- **Dev A**: T007–T013 (BE — TDD red then green)
- **Dev B**: T014–T019 (FE — BFF routes + components)

---

## Notes

- `customFetchMutator` lives at `frontend/src/api/auth.mutator.ts`; call signature: `(url: string, options: RequestInit)` — see `patient.ts` generated client for usage example
- `CURDATE()` is MySQL-only; `globalIndex()` uses PHP-injected `$today = now()->toDateString()` in `orderByRaw` — SQLite-safe
- `datetime-local` input submits `2026-06-10T14:30`; split on `'T'` to get `date` and `time` for the API
- Edit lock: `isEditable` comes from `VisitResource` backend — FE only reads the flag, never recomputes it
- Users BFF (`/api/users`) returns all users; filter to `role === 'doktor'` client-side in VisitForm for the admin doctor selector (no extra BFF call needed)
- TDD: commit RED before touching implementation files; this makes the failing tests visible in git history
