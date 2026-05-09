# Implementation Status — Git-Verified

**Date**: 2026-04-29
**Branch at time of assessment**: `develop`

---

## What's Committed and Where

| Feature Group | Scope | Branch / Status |
|---|---|---|
| **Group 1 — Bootstrap & Auth** | Sanctum, CORS, `routes/api.php`, AuthController (login/logout/me), UserController CRUD, UserResource, StoreUserRequest, UpdateUserRequest, LoginRequest, RoleMiddleware, UserPolicy | On `develop` — **COMPLETE** (register stub pending) |
| **Group 2 — Patient Management** | PatientController CRUD, PatientResource, PatientSummaryResource, PatientSocioeconomicResource, PatientService, PatientPolicy, StorePatientRequest, UpdatePatientRequest | On `develop` — **COMPLETE** |
| **Group 3 — Visits** | VisitController (index/show/store/update/destroy), VisitResource, VisitPolicy, full test suite | On `develop` — **COMPLETE** |
| **Groups 4–11** | Vitals, Medications, Labs, Recommendations, Reminders, Dashboards, etc. | Not started — deferred post Jun 7 2026 |

**Test count**: 36 test files in `backend/tests/` — all REST API tests covering Auth, User management, Patient CRUD, Socioeconomic, Visits (zero Livewire tests remain active).

---

## SE Track — Current State

### Domain Layer ✅

| Artifact | Status |
|---|---|
| `users` migration + `User` model + factory | ✅ develop |
| `patients` migration + `Patient` model + factory | ✅ develop |
| `patient_socioeconomic` migration + model + factory | ✅ develop |
| `visits` migration + `Visit` model + factory | ✅ develop |
| `PatientPolicy` | ✅ develop |
| `VisitPolicy` | ✅ develop |
| `StorePatientRequest` / `UpdatePatientRequest` | ✅ develop |
| `RoleMiddleware` | ✅ develop |

### API Layer — Group 1 (Auth & Users)

| Artifact | Status | Notes |
|---|---|---|
| Sanctum + `routes/api.php` | ✅ done | Registered in `bootstrap/app.php` |
| `AuthController::login()` | ✅ done | Rate limited via `throttle:login` |
| `AuthController::logout()` | ✅ done | |
| `AuthController::me()` | ✅ done | Returns authenticated user |
| `AuthController::register()` | ⚠️ stub | Returns `$this->ok('hello register')` — bd tasks `99s` / `wck` |
| `RegisterRequest` | ❌ missing | Needed for register implementation |
| `UserController` (index, show, store, update, destroy) | ✅ done | |
| `UserController::restore()` / `forceDelete()` | ✅ done | Admin-only routes |
| `UserResource` | ✅ done | `app/Http/Resources/Api/UserResource.php` |
| `StoreUserRequest` / `UpdateUserRequest` | ✅ done | |
| `LoginRequest` | ✅ done | |

### API Layer — Group 2 (Patients)

| Artifact | Status |
|---|---|
| `PatientController` (index, show, store, update, destroy) | ✅ done |
| `PatientResource` | ✅ done |
| `PatientSummaryResource` | ✅ done |
| `PatientSocioeconomicResource` | ✅ done |
| `PatientService` | ✅ done |

### API Layer — Group 3 (Visits)

| Artifact | Status |
|---|---|
| `VisitController` (index, show, store, update, destroy) | ✅ done |
| `VisitResource` | ✅ done |
| Route registration (nested under `patients.visits`) | ✅ done |

### What's Missing for SE (M2 / M3)

| Item | Required by | Status |
|---|---|---|
| `AuthController::register()` implementation | M2 | ⚠️ stub — bd `99s` → `wck` |
| `RegisterRequest` | M2 | ❌ missing |
| `UserService` | M3 patterns | ❌ missing (PatientService exists) |
| `VisitService` | M3 patterns | ❌ missing |
| `UserRepository`, `PatientRepository`, `VisitRepository` | M3 Repository pattern | ❌ missing |
| `PatientObserver` (created/deleted events) | M3 Observer pattern | ❌ missing |
| React SPA | M2/M3 frontend | ❌ nothing |
| 5 Pest HTTP tests (SE minimum) | M3 | ❌ (36 test files exist but SE min-5 not formally verified) |
| Deployment (Railway / Fly.io) | M3 | ❌ missing |

---

## Test Files in `backend/tests/`

```
Feature/Api/
  ApiRoutesTest.php
  AuthTest.php
  RegisterRequestTest.php       ← tests exist, register() not implemented
  ResponseContractTest.php
  RoleAccessTest.php
  ScrambleDocsTest.php
  UpdateUserRequestTest.php
  UserManagementTest.php
  UserResourceTest.php
Feature/Auth/
  LoginRateLimiterTest.php
  StoreUserRequestTest.php
Feature/Middleware/
  RoleMiddlewareTest.php
Feature/Patient/
  PatientApiTest.php
  PatientAuthorizationTest.php
  PatientSocioeconomicTest.php
Feature/
  PatientModelTest.php
  PatientPolicyTest.php
  PatientResourceTest.php
  PatientServiceTest.php
Feature/Socioeconomic/
  PatientSocioeconomicFactoryTest.php
  PatientSocioeconomicModelTest.php
  PatientSocioeconomicPolicyTest.php
  PatientSocioeconomicResourceTest.php
  SocioeconomicMigrationTest.php
Feature/
  UserPolicyTest.php
Feature/visits/
  VisitCreateTest.php
  VisitDeleteTest.php
  VisitListTest.php
  VisitPolicyTest.php
  VisitShowTest.php
  VisitUpdateTest.php
  VisitsFactoryTest.php
  VisitsMigrationTest.php
  VisitsModelTest.php
Unit/Traits/
  ApiResponsesTest.php
```

---

## SD Track

| Artifact | Status |
|---|---|
| M1 deliverables (30 user stories, 11 UML diagrams, Gantt, ER) | ✅ Complete |
| `SE_MVP_PLAN.md` — 30 user stories + milestone breakdown | ✅ |
| `M2_TASKS.md` + `M3_TASKS.md` — fully written, executable | ✅ |
| Architecture docs (`LARAVEL_API_RULES.md`, `DB_SCHEMA_FINAL.md`) | ✅ |
| SD implementation (Groups 4–11) | ❌ Nothing — starts post Jun 7 2026 |

---

## Livewire Monolith (Archived)

Preserved in `master` / `feature/patient_management` / `feature/3_visits` branches.
Not being extended. Reusable: DB schema, models, factories, policies, PatientPolicy, VisitPolicy — all ported to `develop`.

---

## Bottom Line

The SE API backend is **~95% complete** on `develop`. The only open backend task is `register()` (bd `99s` → `wck`). Remaining M3 work is patterns (UserService, VisitService, 3 repositories, PatientObserver) + React FE + deployment — none of these have bd tasks yet.
