# Tasks: Vital Signs Recording & History (012)

**Input**: Design documents from `/specs/012-vital-signs-recording/`
**Branch**: `012-vital-signs-recording`
**Spec priorities**: VSR1 → VSR2 → VSR3 → VSR4

## Format: `[T#] [P?] Description`

- **[P]**: Can run in parallel (different files, no incomplete-task dependencies)
- Tasks are scoped to their section: `Foundation T3`, `VSR1 T5`, etc.

## ⚠️ Critical FE Dependency

**All frontend tasks are blocked by Foundation T8 (Orval client generation).**
Foundation T8 must run AFTER Foundation T7 (routes registered) with the backend server running so Scramble can introspect live routes. Only after `pnpm run api:generate` succeeds and TypeScript compiles clean can any FE task begin.

---

## Foundation (Blocking Prerequisites)

**Purpose**: Shared backend infrastructure that every user story depends on, plus the Orval client regeneration that gates all frontend work.

**⚠️ CRITICAL**: No VSR work can begin until Foundation T1–T8 are complete.

- [ ] T1 Create migration `create_vital_signs_table` in `backend/database/migrations/` — columns: `visit_id` (BIGINT UNSIGNED, unique FK → visits.id, cascadeOnDelete), `systolic_bp` (SMALLINT UNSIGNED nullable), `diastolic_bp` (SMALLINT UNSIGNED nullable), `heart_rate` (SMALLINT UNSIGNED nullable), `temperature` (DECIMAL 4,1 nullable), `weight` (DECIMAL 5,2 nullable), `height` (DECIMAL 5,2 nullable), `bmi` (DECIMAL 4,2 nullable), `timestamps`; run `php artisan migrate`
- [ ] T2 [P] Create `VitalSign` model in `backend/app/Models/VitalSign.php` — `$fillable` for all measurement columns + `visit_id`; `casts()` returning decimal casts per field; `belongsTo(Visit::class)` relationship; `getComputedFlagsAttribute(): array` accessor applying the five threshold rules from `data-model.md`; add `hasOne(VitalSign::class)` to `backend/app/Models/Visit.php`
- [ ] T3 [P] Create `VitalSignPolicy` in `backend/app/Policies/VitalSignPolicy.php` — methods: `view(User, VitalSign)`, `create(User, Visit)`, `update(User, VitalSign)`, `delete(User, VitalSign)`, `viewHistory(User, Patient)`; follow authorization matrix in `data-model.md`; register explicitly in `AppServiceProvider::boot()` via `Gate::policy(VitalSign::class, VitalSignPolicy::class)` — do not rely on auto-discovery
- [ ] T4 [P] Create `VitalSignService` in `backend/app/Services/VitalSignService.php` — `record(Visit $visit, array $data): VitalSign` computes BMI then creates record; `update(VitalSign $vitalSign, array $data): VitalSign` merges existing weight/height with incoming data before recomputing BMI, then updates; `delete(VitalSign $vitalSign): void`; `private computeBmi(mixed $weight, mixed $height): ?float` — use `mixed` (not `?float`) because request data arrives as strings; returns `null` if either argument is `null` or if height converts to zero
- [ ] T5 [P] Create `VitalSignResource` in `backend/app/Http/Resources/Api/VitalSignResource.php` — top-level `id` (cast to string) and `type` (`'vital_sign'`) fields; nested `attributes` array with camelCase keys: all measurements, `bmiCategory` (private helper returning `null | 'normal' | 'underweight' | 'overweight' | 'obese'`), `flags` (from `computed_flags` accessor); `visitId` is **always present** as `(string) $this->visit_id` (direct column, not `whenLoaded`); visit context loaded via `whenLoaded`: `visitDate`, `patientId`, `patientName`, `doctorName`; conditional `previousVisit` block (only when `$this->resource->previousVitals` is set) containing `visitDate`, `weight`, `bmi`, `systolicBp`, `diastolicBp`, `weightDelta`, `bmiDelta`; see `data-model.md` for full JSON shape
- [ ] T6 Create `VitalSignController` in `backend/app/Http/Controllers/Api/VitalSignController.php` — extends `ApiController`; constructor injects `VitalSignService` via readonly property; declare all five public method stubs (`show`, `store`, `update`, `destroy`, `history`) with `abort(501)` bodies; implement private helpers in full (not stubs): `scopeVisitToPatient(Visit $visit, Patient $patient): void` calls `abort(404)` if `$visit->patient_id !== $patient->id`; `loadPreviousVitals(Visit $currentVisit): ?VitalSign` queries `VitalSign::whereHas('visit', fn($q) => $q->where('patient_id', $currentVisit->patient_id)->where('date', '<', $currentVisit->date))` and orders using a **correlated subquery on the outer query** — `->orderByDesc(Visit::select('date')->whereColumn('visits.id', 'vital_signs.visit_id'))` — then `->with(['visit'])->first()`; do NOT put `orderByDesc` inside the `whereHas` closure (it has no effect there)
- [ ] T7 Register all 5 vitals routes in `backend/routes/api.php` inside the `auth:sanctum` group but outside role middleware — `GET`, `POST`, `PATCH`, `DELETE` on `patients/{patient}/visits/{visit}/vitals` and `GET` on `patients/{patient}/vitals`; verify with `php artisan route:list | grep vitals` (5 rows expected)
- [ ] T8 **[FE GATE]** With backend server running, regenerate Orval client from `frontend/`: `pnpm run api:generate` — verify generated types include `VitalSignResource`, `VitalSignResourceAttributes`, `StoreVitalSignRequest`, `UpdateVitalSignRequest`; verify generated functions include (expected names — confirm against generated file): `patientsVisitsVitalsShow`, `patientsVisitsVitalsStore`, `patientsVisitsVitalsUpdate`, `patientsVisitsVitalsDestroy`, `patientsVitals`; confirm `npx tsc --noEmit` exits with zero errors before starting any FE task

**Checkpoint**: Backend model/policy/service/resource/controller/routes exist. Orval client regenerated and TypeScript compiles clean.

---

## VSR1 — Doctor Records Vitals on a Visit 🎯 MVP

**Goal**: Doctor opens a visit, records a partial or full set of vitals; BMI is auto-computed and stored; abnormal values are flagged; the vitals card replaces the "Coming soon" placeholder on the Visit Detail page.

**Independent Test**: Log in as doctor → open own visit → click "Record vital signs" → enter weight 74.5 and height 175 only → submit → vitals card shows BMI ≈ 24.3 with "normal" chip and empty flags. Then edit → add systolic BP 155 → save → flag chip appears. Guest GET → 401. Visit belonging to a different patient → 404.

### Backend

- [ ] T1 [P] Write `VitalSignStoreTest` in `backend/tests/Feature/vitals/VitalSignStoreTest.php` (RED — all assertions must fail against the 501 stub): doctor records on own visit (201), admin records on any visit (201), partial record with weight+height only (201, `bmi` field present and computed), all six fields null (422 with `vitals` error key), doctor on another doctor's visit (403), patient role (403), guest (401), visit belonging to a different patient than the URL (404), second record on the same visit (409)
- [ ] T2 [P] Write `VitalSignShowTest` in `backend/tests/Feature/vitals/VitalSignShowTest.php` (RED — doctor/admin paths only; patient paths added in VSR3 T1): doctor views vitals on own-patient visit (200, `flags` array present), admin views vitals on any visit (200), no vitals recorded for the visit (404), visit belonging to a different patient (404), guest (401)
- [ ] T3 Create `StoreVitalSignRequest` in `backend/app/Http/Requests/StoreVitalSignRequest.php` — `authorize()` calls `$this->user()->can('create', [VitalSign::class, $this->route('visit')])`; `rules()` with all six measurement fields as nullable numeric with physiologically sane range limits; `withValidator()` adding an `after` hook that attaches a `vitals` error when all six fields are null; run `php artisan test --filter=VitalSignStore` and confirm failures shift from 501 to 403/422 shape errors
- [ ] T4 Implement `VitalSignController::store()` — call `scopeVisitToPatient()`; return 409 if `$visit->vitalSign` already exists; call `VitalSignService::record()`; eager-load `visit.patient.user` and `visit.doctor`; return `$this->created(new VitalSignResource($vitalSign), 'Vital signs recorded.')`; verify `php artisan test --filter=VitalSignStore` all green
- [ ] T5 Implement `VitalSignController::show()` — call `scopeVisitToPatient()`; return 404 if no vitals; call `$this->authorize('view', $vitalSign)`; eager-load relations; attach `$vitalSign->previousVitals` via `loadPreviousVitals()`; return `$this->ok(new VitalSignResource($vitalSign), 'Vital signs retrieved.')`; verify `php artisan test --filter=VitalSignShow` doctor/admin paths green

### Frontend

- [ ] T6 [P] Create BFF route `frontend/src/app/api/patients/[id]/visits/[visitId]/vitals/route.ts` — four handlers (`GET`, `POST`, `PATCH`, `DELETE`) each importing the corresponding Orval-generated function (names confirmed from Foundation T8 output); cast `id` and `visitId` with `Number()` before passing; `DELETE` returns `new Response(res.status === 204 ? null : JSON.stringify(res.data), { status: res.status })`
- [ ] T7 [P] Create `VitalsCard.tsx` in `frontend/src/views/visits/VitalsCard.tsx` — MUI `Card` with `CardHeader` ("Vital Signs") containing an Edit `IconButton` (visible when `canEdit` prop is true) and a Delete `IconButton` (visible when `canDelete` prop is true); `CardContent` with two-column MUI `Grid` showing each measurement with unit label (null values shown as "—"); MUI `Chip` for `bmiCategory` (colour-coded: normal=default, underweight/overweight=warning, obese=error); per-flag `Chip`; if `flags.length > 0` render a MUI `Alert severity="warning"` listing flags; if `previousVisit` is present show weight delta beneath the weight value; props: `vitals: VitalSignResource`, `patientId: string`, `visitId: string`, `canEdit: boolean`, `canDelete: boolean`, `onEdit: () => void`, `onDelete: () => void`
- [ ] T8 [P] Create `VitalsForm.tsx` in `frontend/src/views/visits/VitalsForm.tsx` — **create mode only** (POST); MUI `Dialog` with title "Record vital signs"; six `TextField` inputs (type `number`, all optional); live BMI preview: `weight && height ? (parseFloat(weight) / Math.pow(parseFloat(height) / 100, 2)).toFixed(1) : null` shown as helper text below height; 422 response maps error keys to inline field errors; 409 shows MUI `Alert` "Vital signs already recorded for this visit — use Edit to update"; props: `patientId: string`, `visitId: string`, `onSuccess: () => void`, `onClose: () => void`
- [ ] T9 Update `frontend/src/views/visits/VisitDetail.tsx` — remove the "Vital Signs — Coming soon" callout; add `vitals: VitalSignResource | null` and `vitalsLoading: boolean` state; fetch `GET /api/patients/${patientId}/visits/${visitId}/vitals` on mount (404 → set `vitals` to null without error); parse response as `data?.data ?? null` — the API envelope is `{ data: VitalSignResource, ... }` so the resource is at `response.data.data`, **not** `response.data.vitalSign`; render `CircularProgress` while loading; when `vitals` is non-null render `VitalsCard` with `canEdit={role === 'admin' || (role === 'doktor' && visit.doctorId === user.id)}` and `canDelete={role === 'admin'}`; when `vitals` is null and `canEdit` render an empty-state `Box` with a "Record vital signs" `Button` that opens `VitalsForm`; re-fetch vitals on `onSuccess` from both form and delete action

**Checkpoint**: Doctor records and views vitals on own visits. BMI auto-computes. Abnormal flags display. Visit Detail page shows the vitals card.

---

## VSR2 — Doctor Views Vitals History on Patient Profile

**Goal**: Doctor opens a patient's profile and navigates to a new Vitals tab showing all vitals across visits in date-descending order with BMI delta chips. An underweight/overweight/obese badge is visible on the patient overview card when applicable.

**Independent Test**: Create a patient with two visits having vitals (BMI 28.2 then 26.1). Open patient profile → Vitals tab → two rows, newest first; newest row shows "↓ −2.1" BMI delta chip. Patient overview card shows "Overweight" chip. Empty state renders correctly when no vitals exist.

### Backend

- [ ] T1 [P] Write `VitalSignHistoryTest` in `backend/tests/Feature/vitals/VitalSignHistoryTest.php` (RED — doctor/admin paths; patient paths added in VSR3 T1): doctor retrieves history for any patient (200, paginated `data` array), admin retrieves history for any patient (200), records ordered by visit date descending, `?from=YYYY-MM-DD` filter excludes earlier records, `?to=YYYY-MM-DD` filter excludes later records, guest (401)
- [ ] T2 Implement `VitalSignController::history()` — `$this->authorize('viewHistory', [VitalSign::class, $patient])`; query `VitalSign` via `whereHas('visit', fn($q) => $q->where('patient_id', $patient->id))`; eager-load `visit.patient.user` and `visit.doctor`; order by visit date descending using a **correlated subquery** `->orderByDesc(Visit::select('date')->whereColumn('visits.id', 'vital_signs.visit_id'))` — the `vital_signs` table has no `date` column so a plain `->orderByDesc('date')` would throw a SQL error; apply `from`/`to` query-param date filters against visit `date` when present; paginate with `$query->paginate(request()->integer('per_page', 15))` to honour the `per_page` query param; return `$this->paginated(VitalSignResource::collection(...), 'Vitals history retrieved.')`; verify `php artisan test --filter=VitalSignHistory` doctor/admin paths green

### Frontend

- [ ] T3 [P] Create BFF route `frontend/src/app/api/patients/[id]/vitals/route.ts` — `GET` handler using the Orval-generated history function (name confirmed from Foundation T8); forward `from`, `to`, `page`, and `per_page` query params from `req.url` search params
- [ ] T4 [P] Create `VitalsHistoryTab` in `frontend/src/views/patients/patient-right/vitals/index.tsx` — fetch `GET /api/patients/${patientId}/vitals`; MUI `Table` with columns: Visit Date, BP (systolic/diastolic mmHg), Heart Rate (bpm), Temperature (°C), Weight (kg), Height (cm), BMI, Category, Flags; compute BMI delta by comparing `bmi` of each row against the next item in the array (next-older visit): green `↓ −x.x` chip if improving, orange `↑ +x.x` if worsening, grey `→` if unchanged (within 0.1); no delta chip on the last row of the array; cross-page BMI delta is not computable from a single response — show a neutral `→` chip for the last row of each page; empty state with guidance text when no records; MUI `TablePagination` for page navigation
- [ ] T5 Wire Vitals tab into patient profile: add Vitals tab entry to `frontend/src/views/patients/patient-right/index.tsx` pointing to `VitalsHistoryTab`; add BMI status badge to the patient overview header card in `frontend/src/views/patients/patient-detail/PatientDetails.tsx` (or equivalent header component) — fetch `GET /api/patients/${patientId}/vitals?page=1&per_page=1` (works because VSR2 T2 honours `per_page` and VSR2 T3 forwards it); render a MUI `Chip` labelled with `bmiCategory` (warning colour for overweight/underweight, error for obese); hide the chip when `bmiCategory` is `null` or `'normal'`

**Checkpoint**: Patient profile Vitals tab renders history with BMI delta chips. BMI status badge appears on patient overview card.

---

## VSR3 — Patient Views Own Vitals

**Goal**: A logged-in patient can view their own vitals (single visit and history). No create, edit, or delete controls are visible. Any attempt to access another patient's vitals returns 403.

**Independent Test**: Log in as patient → open own visit detail → vitals card visible, no Edit or Delete buttons; navigate to own Vitals history tab → records visible. Directly request another patient's vitals URL → 403.

### Backend

- [ ] T1 [P] Extend `VitalSignShowTest` and `VitalSignHistoryTest` with patient-scoped cases (the policy in Foundation T3 already enforces these — confirm GREEN immediately): patient views own visit's vitals (200), patient requests vitals for a visit belonging to another patient (403), patient requests history for another patient (403); run `php artisan test tests/Feature/vitals/` to confirm all green with no new implementation

### Frontend

- [ ] T2 Audit role-gate visibility in `frontend/src/views/visits/VisitDetail.tsx` — confirm that `canEdit` and `canDelete` both evaluate to `false` for `pacijent` role so `VitalsCard` renders neither Edit nor Delete button, and the empty-state "Record vital signs" button is hidden; add any missing condition; verify manually by logging in as a patient account

**Checkpoint**: Patient sees read-only vitals for own visits only. 403 enforced at API level. UI shows no write controls for the patient role.

---

## VSR4 — Admin Manages Vitals

**Goal**: Admin can record vitals on any visit (covered by VSR1 since policy allows admin in `create`), edit any existing record (including BMI recomputation on partial update), and permanently delete any vitals record with a confirmation dialog.

**Independent Test**: Log in as admin → open any patient's visit → record vitals → edit (change weight only, verify BMI recomputes using stored height) → delete with confirmation → visit returns to empty state. Doctor PATCH on another doctor's visit vitals → 403. Doctor DELETE → 403.

### Backend

- [ ] T1 [P] Write `VitalSignUpdateTest` in `backend/tests/Feature/vitals/VitalSignUpdateTest.php` (RED): admin updates any vitals (200, `bmi` recomputed), doctor updates own visit's vitals (200), doctor cannot update another doctor's visit vitals (403), patient role (403), guest (401), visit belonging to a different patient than the URL (404), updating only weight recomputes BMI from merged weight + existing stored height (200), no vitals exist on the visit (404)
- [ ] T2 [P] Write `VitalSignDeleteTest` in `backend/tests/Feature/vitals/VitalSignDeleteTest.php` (RED): admin deletes vitals (204, record absent on subsequent GET), doctor cannot delete (403), patient cannot delete (403), guest (401), visit belonging to a different patient than the URL (404), deleting non-existent vitals (404)
- [ ] T3 [P] Write `VitalSignPolicyTest` in `backend/tests/Feature/vitals/VitalSignPolicyTest.php` — policy already implemented (Foundation T3) so all assertions should be GREEN immediately; cover every method × every role: `view` (admin ✅, doktor ✅, patient own ✅ / other ❌), `create` (admin ✅, doktor own visit ✅ / other ❌, patient ❌), `update` (admin ✅, doktor own ✅ / other ❌, patient ❌), `delete` (admin ✅, doktor ❌, patient ❌), `viewHistory` (admin ✅, doktor ✅, patient own ✅ / other ❌)
- [ ] T4 [P] Create `UpdateVitalSignRequest` in `backend/app/Http/Requests/UpdateVitalSignRequest.php` — `authorize()` retrieves the vital sign via `$this->route('visit')->vitalSign` (there is no `{vital}` route parameter — the URL is `PATCH /patients/{patient}/visits/{visit}/vitals`, so use the visit relationship, not `$this->route('vital')`); call `$this->user()->can('update', $vitalSign)`; all six measurement rules prefixed with `sometimes`; no at-least-one guard (PATCH partial updates are valid)
- [ ] T5 Implement `VitalSignController::update()` and `VitalSignController::destroy()` — `update()`: call `scopeVisitToPatient()`, return 404 if no vitals, delegate to `VitalSignService::update()` (authorization is handled by `UpdateVitalSignRequest::authorize()` — no explicit `$this->authorize()` call needed in the controller), eager-load relations, return `$this->ok()`; `destroy()`: call `scopeVisitToPatient()`, return 404 if no vitals, call `$this->authorize('delete', $vitalSign)`, delegate to `VitalSignService::delete()`, return `$this->noContent()`; verify `php artisan test tests/Feature/vitals/` all green

### Frontend

- [ ] T6 [P] Add edit mode to `frontend/src/views/visits/VitalsForm.tsx` — accept optional `existing: VitalSignResource` prop; when set: change Dialog title to "Edit vital signs", pre-fill all six fields from `existing.attributes` (convert decimal strings to numbers), submit fires `PATCH /api/patients/${patientId}/visits/${visitId}/vitals`; send all six fields in the payload (not just changed ones) — the backend's `sometimes` rules handle partial or full payloads correctly; live BMI preview initialises from pre-filled weight and height and updates as fields change; `onSuccess` and `onClose` callbacks unchanged
- [ ] T7 [P] Wire Delete confirmation flow in `frontend/src/views/visits/VisitDetail.tsx` — `onDelete` prop passed to `VitalsCard` opens a MUI `Dialog` with message "Delete vital signs? This action cannot be undone." and Confirm/Cancel buttons; on confirm call `DELETE /api/patients/${patientId}/visits/${visitId}/vitals` via the BFF; on 204 show a `react-toastify` success toast, set `vitals` state to `null`, close the dialog; on error show an error toast

**Checkpoint**: Admin has full create/edit/delete on any visit's vitals. Doctor cannot delete or edit another doctor's vitals. All policy rules covered by tests.

---

## Polish

**Purpose**: Unit tests for isolated logic, full code-quality gate pass, and end-to-end validation.

- [ ] T1 [P] Write `VitalSignResourceTest` in `backend/tests/Feature/vitals/VitalSignResourceTest.php` — `flags` array is empty when all values are within thresholds; each abnormal threshold produces the correct flag entry (field name, value, threshold string); `bmiCategory` returns the correct string for BMI values at and around each boundary (18.5, 25, 30); `previousVisit` block is present when `$model->previousVitals` is set; `previousVisit` is absent when not set; `weightDelta` and `bmiDelta` compute correctly
- [ ] T2 [P] Write `VitalSignServiceTest` in `backend/tests/Feature/vitals/VitalSignServiceTest.php` — placed in `tests/Feature/` (not `tests/Unit/`) because `update()` tests create real model instances requiring a database; `computeBmi(80, 175)` returns 26.12; `computeBmi(null, 175)` returns null; `computeBmi(80, null)` returns null; `computeBmi(80, 0)` returns null (division-by-zero guard); `update()` called with only `weight` in `$data` recomputes BMI from merged weight + existing stored height; `update()` called with `height => null` stores `null` BMI
- [ ] T3 Run `vendor/bin/pint --dirty` from `backend/` — resolve all style violations across new files: `VitalSign.php`, `VitalSignPolicy.php`, `VitalSignService.php`, `VitalSignResource.php`, `VitalSignController.php`, `StoreVitalSignRequest.php`, `UpdateVitalSignRequest.php`; commit style fixes separately from logic changes
- [ ] T4 Run `composer run analyse` from `backend/` — resolve all Larastan level-5 errors in new files; no `@phpstan-ignore` comment added without an inline justification comment explaining why
- [ ] T5 Run `php artisan test` from `backend/` — full suite must pass with zero failures; confirm no regressions in existing visit, patient, or user tests
- [ ] T6 Execute manual E2E validation per `specs/012-vital-signs-recording/quickstart.md` — complete all four flows: record vitals as doctor (partial + full), view history as doctor (verify BMI delta chip), read-only as patient (verify no write controls), admin delete (verify confirmation dialog and empty state); confirm BMI status badge renders on patient overview card with real data

---

## Dependencies & Execution Order

### Section Dependencies

- **Foundation**: No dependencies — start immediately
- **VSR1**: Requires Foundation T1–T8 complete
- **VSR2**: BE tasks (T1–T2) require Foundation T1–T7; FE tasks (T3–T5) require Foundation T8
- **VSR3**: T1 requires Foundation T3 (policy) + VSR1 T2 + VSR2 T1 test files; T2 requires VSR1 T9
- **VSR4**: BE tasks (T1–T5) require Foundation T1–T7; FE tasks (T6–T7) require VSR1 T7–T9
- **Polish**: Requires all VSR sections complete

### Within Each Section

- Test tasks (VSR1 T1/T2, VSR2 T1, VSR4 T1/T2) written and confirmed failing (RED) before their corresponding implementation tasks
- `StoreVitalSignRequest` (VSR1 T3) before `Controller::store()` (VSR1 T4)
- `UpdateVitalSignRequest` (VSR4 T4) before `Controller::update()` (VSR4 T5)
- FE tasks within a section can run in parallel once Foundation T8 is done

### Orval Client Generation (Foundation T8) — FE Blocker

```
Foundation T1 → T2–T5 (parallel) → T6 → T7
                                          ↓
                                Foundation T8: pnpm run api:generate
                                          ↓
         VSR1 T6  T7  T8      VSR2 T3  T4      VSR4 T6  T7
         (all FE tasks — none may start before Foundation T8)
```

### Parallel Opportunities

**Foundation — run in parallel after T1**:
```
T2 [model]   T3 [policy]   T4 [service]   T5 [resource]
```
Then sequentially: T6 → T7 → T8

**VSR1 — RED phase in parallel**:
```
T1 [StoreTest RED]    T2 [ShowTest RED]
```
Then sequentially: T3 → T4 → T5

**VSR1 — FE in parallel after Foundation T8**:
```
T6 [BFF route]    T7 [VitalsCard]    T8 [VitalsForm create mode]
```
Then T9 (depends on T7 + T8)

**VSR2 — in parallel after Foundation**:
```
T1 [HistoryTest RED]    T3 [BFF history route]    T4 [VitalsHistoryTab]
```
Then sequentially: T2 (BE), T5 (FE — depends on T4)

**VSR4 — RED phase + request in parallel**:
```
T1 [UpdateTest RED]   T2 [DeleteTest RED]   T3 [PolicyTest]   T4 [UpdateRequest]
```
Then T5 (BE)

**VSR4 — FE in parallel**:
```
T6 [VitalsForm edit mode]    T7 [VisitDetail delete flow]
```

**Polish — in parallel**:
```
T1 [ResourceTest]    T2 [ServiceTest]
```
Then sequentially: T3 → T4 → T5 → T6

---

## Implementation Strategy

### MVP First (VSR1 Only)

1. Complete Foundation (T1–T8) — 8 tasks
2. Complete VSR1 BE (T1–T5) — 5 tasks
3. Complete VSR1 FE (T6–T9) — 4 tasks
4. **STOP and VALIDATE**: Doctor records and views vitals on own visits
5. Demo: Visit Detail page shows real vitals data with BMI and flags

### Incremental Delivery

1. Foundation → VSR1 → validate → **MVP demo**
2. Add VSR2 (history tab + patient card badge) → validate
3. Add VSR3 (patient read-only access) → validate
4. Add VSR4 (admin edit + delete) → validate
5. Polish → full suite passes → branch ready for squash merge to develop

### Notes

- `[P]` = different files, no dependency on an incomplete sibling task
- TDD commit discipline: RED commit → GREEN commit → REFACTOR commit; each references a bd issue ID
- Foundation T8 is the single gate between all BE work and all FE work — `npx tsc --noEmit` must pass before any FE task starts
- Actual Orval function names (confirmed in Foundation T8) must be used verbatim in VSR1 T6 and VSR2 T3 — verify against generated file even if names match the expected list
- `UpdateVitalSignRequest::authorize()` uses `$this->route('visit')->vitalSign` — there is no `{vital}` route parameter in the URL; the vitals resource is singular and always accessed via the visit relationship
