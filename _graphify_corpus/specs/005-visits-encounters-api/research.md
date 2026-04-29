# Research: Visits & Encounters REST API

**Branch**: `005-visits-encounters-api` | **Date**: 2026-04-10

## Domain Layer Audit

The domain layer exists on the current branch (`005-visits-encounters-api`, branched from `develop`).
All files are present — no branch merge needed.

| Artifact | Status | Issues Found |
|---|---|---|
| `app/Models/Visit.php` | EXISTS | `date` cast as `datetime` — should be `date` to match `Y-m-d` output |
| `app/Policies/VisitPolicy.php` | EXISTS | `viewAny` excludes patients; `create` allows admins — both contradict spec |
| `app/Http/Requests/StoreVisitRequest.php` | EXISTS | notes max:500 (spec says 10000); `doctor_id` accepted in body (security); missing `before_or_equal:today` |
| `database/migrations/2026_03_01_023721_create_visits_table.php` | EXISTS | Correct — date, notes nullable, cascade deletes, no soft deletes |
| `database/factories/VisitFactory.php` | EXISTS | Assumed correct (used by passing tests) |
| `tests/Feature/visits/` | EXISTS | 4 test files: policy, model, factory, migration — pre-existing, must not be deleted |

### Policy Corrections Required

**`viewAny`** — current implementation excludes patients and takes no `Patient` argument. Two problems: (1) patients cannot list their own visits; (2) there is no patient-context check for cross-patient access on the index endpoint.

Decision: update signature to `viewAny(User $user, Patient $patient)`. Patients are allowed only if the patient record matches their own. Admin and doctor always allowed. Controller calls `$this->authorize('viewAny', [Visit::class, $patient])`.

Why this matters: the `->scoped(['visit' => 'patient'])` route binding only applies to the `{visit}` parameter — it scopes individual visit lookups. The `index` endpoint has no `{visit}` in its URL (`GET /api/patients/{patient}/visits`) so scoping never fires. Cross-patient list access is blocked entirely by the `viewAny` policy check.

Alternative considered: return `true` for all roles and filter in the controller — rejected because it violates Constitution Principle II (authorization must go through policy, not controller logic) and would return an empty list instead of 403 for cross-patient access.

**`create`** — current implementation allows admins. The spec explicitly states only doctors can record clinical encounters.
Decision: update `create` to return `$user->isDoctor()` only.

### StoreVisitRequest Corrections Required

- Remove `doctor_id` from rules — it must never come from the request body; always auto-assigned from `$request->user()->id`
- Update `notes` max from 500 → 10000
- Add `before_or_equal:today` to date rule
- The `authorize()` call must use `Visit::class` policy (already does)

### Visit Model Correction

- Change `'date' => 'datetime'` cast to `'date' => 'date'` to produce `Y-m-d` string output (not ISO datetime)

---

## Response Format Decision

**Decision**: Follow the project's existing `ApiResponses` trait pattern, not a strict JSON:API outer envelope.

**Rationale**: PatientController uses `$this->ok()`, `$this->created()`, `$this->paginated()` — these produce `{message, status, data}`. The inner resource (PatientResource) uses JSON:API structure (`type`, `id`, `attributes`, `relationships`). VisitResource will match this convention.

**Single resource wrapper**:
```json
{ "message": "...", "status": 200, "data": { "visit": { "type": "visit", "id": "...", ... } } }
```

**Collection wrapper** (via `paginated()`):
```json
{ "message": "...", "status": 200, "data": [...], "meta": {...}, "links": {...} }
```

**Alternatives considered**: Return raw VisitResource::collection — rejected because inconsistent with PatientController which wraps in `$this->paginated()`. Strict JSON:API outer envelope — rejected, no existing precedent in the project.

---

## Nested Route Scoping Decision

**Decision**: Use `Route::apiResource('patients.visits', VisitController::class)->scoped(['visit' => 'patient'])`.

**Rationale**: The `->scoped()` call tells Laravel to add a WHERE constraint when resolving `{visit}`, ensuring the visit belongs to the given patient. This returns 404 automatically if the visit's `patient_id` doesn't match the URL `{patient}`. This eliminates the need for manual `if ($visit->patient_id !== $patient->id) abort(404)` guards in every controller method.

**Alternatives considered**: Manual guard in each controller method — rejected as verbose and error-prone (a missed check = data leakage). Separate flat `/api/visits` resource — rejected as it contradicts the nested route requirement in the spec.

---

## Controller Complexity Decision

**Decision**: No service layer for visits. Direct Eloquent queries in the controller.

**Rationale**: Visit CRUD has no multi-table transactions, no complex domain logic beyond eager loading. PatientController uses PatientService only because of the patient + socioeconomic joint creation pattern. Visit creation is a single-table insert. Adding a service layer would be speculative abstraction.

**Alternatives considered**: VisitService following PatientService pattern — rejected, no problem it would solve.

---

## Test Structure Decision

**Decision**: Group visit API tests in `tests/Feature/Visit/` (capital V), separate from existing `tests/Feature/visits/` (lowercase, domain-layer tests).

**Rationale**: Existing tests in `tests/Feature/visits/` cover policy, model, factory, and migration — unit/integration tests for the domain layer. New tests will be HTTP-level feature tests (actingAs + HTTP verbs). Separating them keeps domain tests and API tests distinct.

Files:
- `tests/Feature/Visit/VisitListTest.php` — index endpoint (9+ cases)
- `tests/Feature/Visit/VisitCreateTest.php` — store endpoint (10+ cases)  
- `tests/Feature/Visit/VisitShowTest.php` — show endpoint (9+ cases)
- `tests/Feature/Visit/VisitUpdateTest.php` — update endpoint (9+ cases)
- `tests/Feature/Visit/VisitDeleteTest.php` — destroy endpoint (7+ cases)

Total: 44+ tests (exceeds 30-test minimum).

---

## Authorization Matrix (Verified Against Spec)

| Action | Admin | Doctor (own) | Doctor (other's) | Patient (own) | Patient (other's) |
|---|---|---|---|---|---|
| List visits | Allow | Allow | Allow | Allow | Deny (403 via viewAny policy) |
| Create visit | Deny | Allow | Allow | Deny | Deny |
| View single | Allow | Allow | Allow | Allow | Deny (404 via route) |
| Update notes | Allow | Allow | Deny (403) | Deny | Deny |
| Delete visit | Allow | Deny | Deny | Deny | Deny |
