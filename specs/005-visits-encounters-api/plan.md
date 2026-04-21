# Implementation Plan: Visits & Encounters REST API

**Branch**: `005-visits-encounters-api` | **Date**: 2026-04-10 | **Spec**: [spec.md](spec.md)
**Input**: Feature specification from `/specs/005-visits-encounters-api/spec.md`

## Summary

Build the REST API layer for clinical visit management on top of an existing domain layer (model, policy, factory, migration all present on branch). The API consists of five nested HTTP endpoints under `/api/patients/{patient}/visits`, enforcing a three-role authorization matrix (admin/doctor/patient) via Sanctum + VisitPolicy. Two corrections to existing domain files are required before new code is written. Minimum 30 Pest HTTP tests required; plan targets 44+.

## Technical Context

**Language/Version**: PHP 8.4 (Laravel 12)
**Primary Dependencies**: Laravel Sanctum (auth), Eloquent API Resources, Laravel Pest 4
**Storage**: MySQL (production), SQLite in-memory (tests)
**Testing**: Pest 4 with `RefreshDatabase`, HTTP-level feature tests via `actingAs()`
**Target Platform**: Linux server (API-only backend, no frontend in this feature)
**Project Type**: REST API (nested resource under patients)
**Performance Goals**: Standard web API expectations; pagination for large datasets (100+ visits)
**Constraints**: Sanctum token auth on all routes; no soft deletes; all routes behind `auth:sanctum`
**Scale/Scope**: Feature Groups 1–3 MVP; same DB shared by SE and SD tracks

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Status | Evidence |
|---|---|---|
| **II — Authorization at Every Layer** | PASS | Route: `auth:sanctum` middleware. FormRequest: `authorize()` calls policy for `create`. Controller: `$this->authorize(...)` for view/update/delete. Policy: `VisitPolicy` with per-action methods. Cross-patient access blocked by scoped route binding (404). |
| **III — Test-First** | PASS | 44+ Pest HTTP tests planned across 5 test files. Written alongside or before controller. Verification command per task included in quickstart.md. |
| **IV — Code Quality Gates** | PASS | All changed files run through `vendor/bin/pint --dirty` → `composer run analyse` (Larastan level 5) → `php artisan test` before task is complete. |
| **V — Tasks Are Developer-Ready** | PASS | Each task in tasks.md will include: goal, inputs, outputs, ordered steps with code, decision rationale, verification command. |

**Post-Design Re-check**: No design decisions introduced since initial check. Authorization layering confirmed at data-model.md level. No gate violations.

## Project Structure

### Documentation (this feature)

```text
specs/005-visits-encounters-api/
├── plan.md              # This file
├── research.md          # Phase 0 output — domain audit, decisions
├── data-model.md        # Phase 1 output — entity corrections + new files
├── quickstart.md        # Phase 1 output — task order, pitfalls, commands
├── contracts/
│   └── api.md           # Phase 1 output — endpoint contracts, response shapes
├── checklists/
│   └── requirements.md  # Spec quality checklist (all pass)
└── tasks.md             # Phase 2 output (NOT created by /speckit.plan — use /speckit.tasks)
```

### Source Code (backend/)

```text
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   └── VisitController.php          [CREATE] 5-method nested resource controller
│   │   ├── Requests/
│   │   │   ├── StoreVisitRequest.php        [UPDATE] fix date, notes max, remove doctor_id
│   │   │   └── Api/
│   │   └── UpdateVisitRequest.php           [CREATE] partial-update request (root, not Api/)
│   │   └── Resources/Api/
│   │       └── VisitResource.php            [CREATE] JSON:API-shaped resource
│   ├── Models/
│   │   └── Visit.php                        [UPDATE] fix date cast: datetime → date
│   └── Policies/
│       └── VisitPolicy.php                  [UPDATE] fix viewAny + create methods
├── routes/
│   └── api.php                              [UPDATE] add nested apiResource route
└── tests/Feature/
    ├── visits/                              [KEEP] existing domain-layer tests (do not delete)
    │   ├── VisitPolicyTest.php              [UPDATE] fix admin-can-create assertions
    │   ├── VisitsMigrationTest.php          (no changes)
    │   ├── VisitsModelTest.php              (no changes)
    │   └── VisitsFactoryTest.php            (no changes)
    └── Visit/                              [CREATE] new HTTP-level API tests
        ├── VisitListTest.php                9+ tests
        ├── VisitCreateTest.php              10+ tests
        ├── VisitShowTest.php                9+ tests
        ├── VisitUpdateTest.php              9+ tests
        └── VisitDeleteTest.php              7+ tests
```

**Structure Decision**: Web application layout. Backend API only (no frontend work in this feature). New test directory `tests/Feature/Visit/` (capital V) isolates HTTP tests from existing domain tests in `tests/Feature/visits/` (lowercase).

## Implementation Phases

### Phase 1: Domain Corrections (Prerequisites)

Must be done before any new API code is written to keep the test suite green.

**T01 — Fix Visit model date cast**
- File: `app/Models/Visit.php`
- Change: `'date' => 'datetime'` → `'date' => 'date'`
- Why: `datetime` cast produces ISO timestamp in output; spec and API contract require `Y-m-d` string

**T02 — Fix VisitPolicy**
- File: `app/Policies/VisitPolicy.php`
- Change `viewAny`: add `$user->isPatient()` to return true (cross-patient blocked by route scoping)
- Change `create`: return `$user->isDoctor()` only (remove admin)
- Also update: `tests/Feature/visits/VisitPolicyTest.php` — fix the `admin can create a visit` test to assert false

**T03 — Fix StoreVisitRequest**
- File: `app/Http/Requests/StoreVisitRequest.php`
- Remove `doctor_id` rule entirely
- Change `date` to: `['required', 'date', 'before_or_equal:today']`
- Change `notes` to: `['nullable', 'string', 'max:10000']`
- Also move file: consider moving to `app/Http/Requests/Api/` to match namespace convention (check existing path used by routes first)

### Phase 2: New Files

**T04 — Create UpdateVisitRequest**
- File: `app/Http/Requests/UpdateVisitRequest.php` (NOT in `Api/` — matches `UpdatePatientRequest` location)
- `authorize()`: `$visit = $this->route('visit'); return $this->user()->can('update', $visit);` (Constitution Principle II — never return true unconditionally)
- Controller `update()` must NOT call `$this->authorize()` — FormRequest already handles it (same as PatientController)
- Rules: `date` → `sometimes|date|before_or_equal:today`, `notes` → `nullable|string|max:10000`

**T05 — Create VisitResource**
- File: `app/Http/Resources/Api/VisitResource.php`
- Follow PatientResource pattern (type, id as string, camelCase attributes, relationships, whenLoaded)
- No `includes` block — not present in PatientResource or UserResource; use `doctorName` as a denormalized attribute instead
- Attributes: `date` (Y-m-d), `notes`, `createdAt`, `updatedAt`
- Relationships: `patient` (id only), `doctor` (id only)
- Includes: `doctor` details (name, email) via `whenLoaded('doctor')`

**T06 — Create VisitController**
- File: `app/Http/Controllers/Api/VisitController.php`
- Extends `ApiController` (gets `ApiResponses` + `AuthorizesRequests`)
- Constructor: no service layer, direct Eloquent
- `index($patient)`: authorize viewAny, eager load doctor, order date DESC, paginated()
- `store(StoreVisitRequest $request, $patient)`: auto-assign doctor_id, created()
- `show($patient, $visit)`: authorize view, load doctor+patient, ok()
- `update(UpdateVisitRequest $request, $patient, $visit)`: NO `$this->authorize()` call — FormRequest handles it; ok()
- `destroy($patient, $visit)`: authorize delete, hard delete, noContent()

**T07 — Register Routes**
- File: `routes/api.php`
- Inside existing `auth:sanctum` group: `Route::apiResource('patients.visits', VisitController::class)->scoped(['visit' => 'patient']);`

### Phase 3: HTTP Tests (44+ tests)

**T08 — VisitListTest** (9 cases)
- admin/doctor can list any patient's visits
- patient can list own visits; patient cannot list another patient's visits (404)
- unauthenticated returns 401
- list is ordered date DESC
- list includes pagination meta
- list eager-loads doctor (no N+1)
- empty history returns empty data array

**T09 — VisitCreateTest** (10 cases)
- doctor can create visit
- doctor_id auto-assigned from auth (not from request body)
- admin cannot create (403)
- patient cannot create (403)
- unauthenticated returns 401
- create requires date (422)
- date cannot be in future (422)
- notes can be null/omitted
- notes max 10000 validated (422 if exceeded)
- returns 201 with visit resource

**T10 — VisitShowTest** (9 cases)
- admin/doctor can view any visit
- patient can view own visit
- patient cannot view another patient's visit (404)
- unauthenticated returns 401
- non-existent visit returns 404
- response includes doctor relationship
- response includes patient relationship
- response matches VisitResource structure (type, id, attributes, relationships)
- visit from different patient returns 404 (scoped binding)

**T11 — VisitUpdateTest** (9 cases)
- doctor can update own visit
- doctor cannot update another doctor's visit (403)
- admin can update any visit
- patient cannot update (403)
- unauthenticated returns 401
- date cannot be future on update (422)
- partial update (notes only) works
- partial update (date only) works
- scoped binding: visit from different patient returns 404

**T12 — VisitDeleteTest** (7 cases)
- admin can delete any visit
- doctor cannot delete (403)
- patient cannot delete (403)
- unauthenticated returns 401
- delete returns 204
- record is fully removed from DB (`assertDatabaseMissing`)
- scoped binding: visit from different patient returns 404

## Complexity Tracking

No constitution violations. No complexity justification required.

All design decisions fit within existing project patterns:
- Controller pattern: matches PatientController
- Resource pattern: matches PatientResource
- Response format: matches ApiResponses trait
- Test pattern: matches PatientApiTest / PatientAuthorizationTest
