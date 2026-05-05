# Implementation Plan: Patient Visits Feature

**Branch**: `010-patient-visits` | **Date**: 2026-05-05 | **Spec**: [spec.md](spec.md)  
**Input**: Feature specification from `/specs/010-patient-visits/spec.md`

---

## Summary

Build the full Visits feature end-to-end: a global Visits page (doctor-scoped / admin-global), visit creation and editing, the 1-day edit lock, upcoming-visit highlighting, and a working Visits tab on the patient profile. The backend already has the `Visit` model, policy, controller, and per-patient routes — this plan extends them rather than replacing them.

---

## Technical Context

**Language/Version**: PHP 8.4 (backend), TypeScript / React 19 (frontend)  
**Primary Dependencies**: Laravel 12, Sanctum, Eloquent Resources, MUI v7, Next.js 16, TanStack Table v8  
**Storage**: SQLite (dev/test), MySQL-compatible (production)  
**Testing**: Pest 4, SQLite in-memory (`RefreshDatabase`)  
**Target Platform**: Web (Next.js SPA + Laravel API)  
**Project Type**: Web application — separate BE + FE  
**Performance Goals**: Standard web response times; no special targets for this feature  
**Constraints**: No new npm packages. No new composer packages. `@mui/x-date-pickers` not installed — use `<TextField type='datetime-local'>` instead.  
**Scale/Scope**: Feature Groups 1–3 (SE/SD MVP scope per constitution)

---

## Constitution Check

| Principle | Status | Notes |
|---|---|---|
| **I. Dual-Track Architecture** | ✅ | Laravel REST API + React SPA. No Livewire changes. |
| **II. Authorization at Every Layer** | ✅ | Every route: `auth:sanctum`. Every FormRequest: delegates to policy. VisitPolicy covers all actions. 1-day lock enforced in policy. |
| **III. Test-First** | ✅ | Pest tests required for: `globalIndex` (doctor scope, admin scope, 401, 403), `store` (time field, doctor_id for admin), `update` (1-day lock), edit lock boundary. |
| **IV. Code Quality Gates** | ✅ | Pint + Larastan level 5 + affected tests must all pass before any task is closed. |
| **V. Tasks are Developer-Ready Specs** | ✅ | Each task below has Goal, Inputs, Outputs, Steps, Rationale, Verification. BE and FE tasks are always separate. |

---

## Project Structure

### Documentation (this feature)

```text
specs/010-patient-visits/
├── plan.md              ← this file
├── research.md          ← Phase 0 decisions
├── data-model.md        ← Phase 1 data design
├── quickstart.md        ← Phase 1 setup guide
├── contracts/
│   └── api-contracts.md ← Phase 1 API contracts
└── tasks.md             ← Phase 2 output (/speckit.tasks)
```

### Source Code

```text
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/VisitController.php   ← add globalIndex(); update store() for time + doctor_id
│   │   ├── Requests/
│   │   │   ├── StoreVisitRequest.php             ← add time, doctor_id; change date rule to future
│   │   │   └── UpdateVisitRequest.php            ← add time; remove before_or_equal (lock is in policy)
│   │   └── Resources/Api/VisitResource.php       ← add time, patientName, patientId, isEditable
│   ├── Models/Visit.php                          ← add time to fillable + casts
│   └── Policies/VisitPolicy.php                  ← add 1-day lock to update()
└── database/migrations/
    └── xxxx_add_time_to_visits_table.php          ← new: add time column

routes/api.php                                     ← add GET /api/visits

tests/Feature/visits/
├── VisitGlobalIndexTest.php                       ← new
├── VisitStoreTest.php                             ← update (time field, admin doctor_id)
└── VisitUpdateTest.php                            ← update (1-day lock tests)

frontend/
└── src/
    ├── api/generated/nutriBaseAPI.schemas.ts      ← add VisitResource* types
    ├── app/api/
    │   ├── visits/route.ts                        ← new BFF: GET /api/visits
    │   └── patients/[id]/visits/route.ts          ← rewrite: fix auth; add POST
    └── views/
        ├── visits/
        │   ├── index.tsx                          ← rewrite: full VisitList page
        │   ├── VisitForm.tsx                      ← new: create visit form
        │   └── VisitEditForm.tsx                  ← new: edit visit form
        └── patients/patient-right/visits/
            └── index.tsx                          ← rewrite: proper VisitsTab
```

**Structure Decision**: Option 2 (web application) — separate `backend/` and `frontend/` as established by the project.

---

## Implementation Phases

### Phase BE-1 — Migration: add `time` to visits

**Goal**: Add nullable `time` (TIME) column to `visits` table.

**Inputs**: `database/migrations/` (existing), `Visit` model.

**Outputs**:
- `database/migrations/xxxx_add_time_to_visits_table.php`
- `app/Models/Visit.php` (updated `$fillable` and `casts()`)

**Steps**:
1. Run `php artisan make:migration add_time_to_visits_table --table=visits --no-interaction`
2. In `up()`: `$table->time('time')->nullable()->after('date');`
3. In `down()`: `$table->dropColumn('time');`
4. In `Visit.php`: add `'time'` to `$fillable`; add `'time' => 'string'` to `casts()`

**Decision rationale**: Nullable so existing rows are not invalidated. New creates will always provide `time`.

**Verification**: `php artisan migrate && php artisan test --filter=Visit`

---

### Phase BE-2 — VisitResource: add time, patientName, patientId, isEditable

**Goal**: `VisitResource` exposes all fields the frontend needs.

**Inputs**: `VisitResource.php`, `Visit` model, `Patient` model (→ `user->name`).

**Outputs**: `app/Http/Resources/Api/VisitResource.php`

**Steps** — update `toArray()`:
```php
'attributes' => [
    'date'        => $this->date->toDateString(),
    'time'        => $this->time,
    'notes'       => $this->notes,
    'doctorName'  => $this->whenLoaded('doctor',  fn () => $this->doctor->name),
    'patientName' => $this->whenLoaded('patient', fn () => $this->patient->user->name ?? null),
    'patientId'   => $this->whenLoaded('patient', fn () => (string) $this->patient->id),
    'isEditable'  => $this->date->toDateString() >= now()->subDay()->toDateString(),
    'createdAt'   => $this->created_at?->toIso8601String(),
    'updatedAt'   => $this->updated_at?->toIso8601String(),
],
```

Eager-load `patient.user` wherever `VisitResource` is returned (controller calls).

**Verification**: `php artisan test --filter=VisitResource`

---

### Phase BE-3 — StoreVisitRequest: time + doctor_id + future date

**Goal**: Validate `time` (required), allow `doctor_id` (admin only, optional), change date to allow future.

**Inputs**: `StoreVisitRequest.php`.

**Outputs**: `StoreVisitRequest.php` (updated).

**Steps** — update `rules()`:
```php
return [
    'date'      => ['required', 'date'],
    'time'      => ['required', 'date_format:H:i'],
    'notes'     => ['nullable', 'string', 'max:10000'],
    'doctor_id' => ['nullable', 'integer', 'exists:users,id', Rule::exists('users', 'id')->where('role', 'doktor')],
];
```

Remove the `messages()` override for `before_or_equal` (no longer applies).

**Note**: `date_format:H:i` accepts `14:30` from `datetime-local` split.

**Verification**: `php artisan test --filter=VisitStore`

---

### Phase BE-4 — VisitController: globalIndex + store with time/doctor_id

**Goal**: Add `globalIndex()` for the Visits page; update `store()` to handle `time` and admin `doctor_id`.

**Inputs**: `VisitController.php`, `VisitPolicy`, `StoreVisitRequest`.

**Outputs**: `VisitController.php` (updated).

**Steps**:

`globalIndex()`:
```php
public function globalIndex(): JsonResponse
{
    $this->authorize('globalIndex', Visit::class);  // add to policy

    $query = Visit::query()->with(['doctor', 'patient.user']);

    if (auth()->user()->isDoctor()) {
        $query->where('doctor_id', auth()->id());
    }

    $visits = $query
        ->orderByRaw("CASE WHEN date >= CURDATE() THEN 0 ELSE 1 END")
        ->orderBy('date')
        ->orderBy('time')
        ->paginate();

    return $this->paginated(
        'Visits retrieved successfully.',
        VisitResource::collection($visits)
    );
}
```

`store()` update:
```php
$doctorId = (auth()->user()->isAdmin() && $request->filled('doctor_id'))
    ? $request->doctor_id
    : auth()->id();

$visit = $patient->visits()->create([
    'date'      => $request->date,
    'time'      => $request->time,
    'notes'     => $request->notes,
    'doctor_id' => $doctorId,
]);
```

Update `show()` and `update()` to also eager-load `patient.user`.

**Verification**: `php artisan test --filter=Visit`

---

### Phase BE-5 — VisitPolicy: globalIndex gate + 1-day lock

**Goal**: Add `globalIndex` gate (admin+doctor); add 1-day lock to `update`.

**Inputs**: `VisitPolicy.php`.

**Outputs**: `VisitPolicy.php` (updated).

**Steps**:
```php
public function globalIndex(User $user): bool
{
    return $this->adminOrDoctor($user);
}

public function update(User $user, Visit $visit): bool
{
    // 1-day edit lock
    if ($visit->date->toDateString() < now()->subDay()->toDateString()) {
        return false;
    }

    if ($user->isAdmin()) {
        return true;
    }

    return $user->isDoctor() && $user->id === $visit->doctor_id;
}
```

**Verification**: `php artisan test --filter=VisitPolicy`

---

### Phase BE-6 — UpdateVisitRequest: add time, remove stale date rule

**Goal**: Allow editing `time`; remove `before_or_equal:today` (lock is now in policy).

**Inputs**: `UpdateVisitRequest.php`.

**Outputs**: `UpdateVisitRequest.php` (updated).

**Steps** — update `rules()`:
```php
return [
    'date'  => ['sometimes', 'date'],
    'time'  => ['sometimes', 'date_format:H:i'],
    'notes' => ['nullable', 'string', 'max:10000'],
];
```

Remove `messages()` override.

**Verification**: `php artisan test --filter=VisitUpdate`

---

### Phase BE-7 — Routes: add GET /api/visits

**Goal**: Register the global visits listing route.

**Inputs**: `routes/api.php`.

**Outputs**: `routes/api.php` (updated).

**Steps** — inside the `auth:sanctum` group, alongside the `role:admin,doktor` group:
```php
Route::middleware(['role:admin,doktor'])->group(function () {
    Route::get('/visits', [VisitController::class, 'globalIndex'])->name('visits.index');
    // existing routes ...
});
```

**Verification**: `php artisan route:list | grep visits`

---

### Phase BE-8 — Pest Tests (RED → GREEN → REFACTOR)

**Goal**: Test coverage for all new/changed behaviour.

**New test files**:
- `tests/Feature/visits/VisitGlobalIndexTest.php`
  - Doctor sees only own visits (200 + scoped data)
  - Admin sees all visits (200 + unscoped data)
  - Guest → 401
  - Patient role → 403
- `tests/Feature/visits/VisitStoreTest.php` (update existing)
  - Doctor creates visit with time (201)
  - Doctor cannot set doctor_id (ignored)
  - Admin creates visit with doctor_id (201)
  - Missing time → 422
  - Date in past → no longer rejected (remove that assertion)
- `tests/Feature/visits/VisitUpdateTest.php` (update existing)
  - Visit from today: editable (200)
  - Visit from yesterday: editable (200)  ← boundary test
  - Visit from 2 days ago: locked (403)   ← boundary test
  - Wrong doctor → 403

**Verification**: `php artisan test --filter=Visit`

---

### Phase FE-1 — TypeScript: add VisitResource types to schemas

**Goal**: Typed `VisitResource`, `VisitResourceAttributes`, `VisitsGlobalIndex200`, `PatientVisitsIndex200` in the generated schema file.

**Inputs**: `frontend/src/api/generated/nutriBaseAPI.schemas.ts`, `data-model.md`.

**Outputs**: `nutriBaseAPI.schemas.ts` (appended).

**Steps**: Append the type definitions from `data-model.md` (TypeScript Types section) after the last `Patients*` type block. Reuse existing `PatientsIndex200Meta` and `PatientsIndex200Links`.

**Verification**: `cd frontend && npx tsc --noEmit`

---

### Phase FE-2 — BFF: global visits GET + patient visits fix

**Goal**: `GET /api/visits` BFF; rewrite `patients/[id]/visits/route.ts` with correct auth and POST.

**Inputs**: `frontend/src/app/api/patients/route.ts` (as pattern), `api-contracts.md`.

**Outputs**:
- `frontend/src/app/api/visits/route.ts` (new)
- `frontend/src/app/api/patients/[id]/visits/route.ts` (rewrite)

**Pattern** (from `patients/route.ts`): use the generated `patientsIndex()` / `patientsStore()` calls which use `customFetchMutator` (Bearer from cookie). For visits, use the raw `customFetchMutator` directly since visits aren't in the orval-generated client:

```ts
// app/api/visits/route.ts
import { customFetchMutator } from '@/api/generated/mutator/customFetchMutator'

export async function GET() {
  const res = await customFetchMutator<VisitsGlobalIndex200>({
    url: '/visits', method: 'GET'
  })
  return new Response(JSON.stringify(res), { status: 200 })
}
```

For the patient visits BFF, replicate the same pattern for GET and POST.

**Note**: `customFetchMutator` path must be confirmed from the project — check `frontend/src/api/generated/mutator/`.

**Verification**: Network tab in browser — requests carry `Authorization: Bearer ...` not `Cookie:`.

---

### Phase FE-3 — VisitForm.tsx (create visit)

**Goal**: Form component for creating a new visit. Used in both the Visits page and patient profile tab.

**Inputs**: `PatientForm.tsx` (form pattern), `api-contracts.md` POST contract, `VisitResource` TS types.

**Outputs**: `frontend/src/views/visits/VisitForm.tsx` (new)

**Props**:
```ts
interface Props {
  patientId?: string       // pre-filled from patient profile; omit on global Visits page
  onSuccess?: () => void
  onCancel?: () => void
}
```

**Fields**:
- Patient selector: `<Select>` populated from `/api/patients` (searchable via filter); hidden/disabled when `patientId` prop provided
- Doctor selector: shown only for admin role; `<Select>` from `/api/users?role=doktor`
- Date+time: `<TextField type='datetime-local' InputLabelProps={{ shrink: true }}>`
- Notes: `<TextField multiline rows={3}>`

**Submit logic**:
```ts
const [date, time] = datetimeLocal.split('T')  // '2026-06-10', '14:30'
fetch(`/api/patients/${resolvedPatientId}/visits`, {
  method: 'POST',
  body: JSON.stringify({ date, time, notes, doctor_id: adminSelectedDoctorId }),
})
```

**Error handling**: 422 → field errors (same pattern as PatientForm); non-422 → `formError` alert.

**Verification**: Manual — create a visit as doctor and as admin; verify in DB.

---

### Phase FE-4 — VisitEditForm.tsx (edit visit)

**Goal**: Edit form for an existing visit. Fields: date+time, notes. Read `isEditable` from `VisitResource` to guard.

**Inputs**: `PatientEditForm.tsx` (edit pattern), `VisitResource` TS types.

**Outputs**: `frontend/src/views/visits/VisitEditForm.tsx` (new)

**Props**:
```ts
interface Props {
  visit: VisitResource
  patientId: string
  onSuccess?: () => void
  onCancel?: () => void
}
```

**Fields**: `datetime-local` (pre-filled from `attributes.date + 'T' + attributes.time`), notes textarea.

**Submit**: `PATCH /api/patients/${patientId}/visits/${visit.id}` via BFF.

**Note**: The edit button is already disabled client-side when `!visit.attributes.isEditable`; the backend enforces this too (403 from policy).

**Verification**: Edit a visit from today (should save); edit a visit from 2 days ago (edit button hidden; direct PATCH → 403).

---

### Phase FE-5 — VisitsView rewrite (global Visits page)

**Goal**: Replace the stub `VisitsView` with a full implementation matching `PatientList.tsx` in structure.

**Inputs**: `PatientList.tsx` (list pattern), `VisitForm.tsx`, `VisitEditForm.tsx`, `VisitResource` TS types.

**Outputs**: `frontend/src/views/visits/index.tsx` (rewrite)

**Layout**:
```
Card
  CardHeader "Visits"
  Box (toolbar): [search — future enhancement] [Add Visit button]
  
  Section: "Upcoming" (chip/badge header)
    Table of upcoming visits (date >= today)
    Columns: Date, Time, Patient, Doctor, Notes, Actions (Edit)
  
  Section: "Past" (header)  
    Table of past visits
    Same columns; Edit disabled when !isEditable
  
  Dialog: VisitForm (create)
  Dialog: VisitEditForm (edit)
```

**Data fetch**: `GET /api/visits` BFF. Split response into `upcoming = visits.filter(v => v.attributes.date >= today)` and `past = the rest`. Upcoming sorted ascending by date (soonest first); past sorted descending.

**Upcoming highlight**: use MUI `Chip` label or a `Box` with `bgcolor='primary.50'` background on the section header row.

**Verification**: Visits page shows upcoming/past sections with correct data as doctor and admin.

---

### Phase FE-6 — VisitsTab rewrite (patient profile)

**Goal**: Replace the stub `patient-right/visits/index.tsx` with a proper implementation showing that patient's visits and an Add Visit button.

**Inputs**: Existing `VisitsTab`, `VisitForm.tsx`, `VisitEditForm.tsx`, `VisitResource` TS types.

**Outputs**: `frontend/src/views/patients/patient-right/visits/index.tsx` (rewrite)

**Layout**:
```
Box (toolbar): [Add Visit button → opens VisitForm with patientId pre-filled]

Visits list (no upcoming/past split — simpler for profile context)
  Each visit: date, time, doctor, notes; Edit action when isEditable
```

**Data fetch**: `GET /api/patients/${patient.id}/visits` BFF.

**Verification**: Visit tab on patient profile shows visits; Add Visit opens form with patient pre-filled.

---

## Complexity Tracking

No constitution violations. All changes are additive or replacements within existing patterns.
