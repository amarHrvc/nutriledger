# Implementation Status — Git-Verified

**Date**: 2026-03-15
**Branch at time of assessment**: `feature/SPecKit_Added`

---

## What's Committed and Where

| Feature Group | Scope | Branch / Status |
|---|---|---|
| **Group 1 — Bootstrap & Auth** | User migrations, RBAC, RoleMiddleware, Fortify auth, User Management (CRUD + soft delete) | Merged to `master` — **COMPLETE** |
| **Group 2 — Patient Management** | Patient + PatientSocioeconomic: migration, model, factory, policy, routes, List/Create/View/Edit/Delete components, integrated into patient profile | Merged to `master` — **COMPLETE** |
| **Group 3 — Visits** | Migration, model, factory, policy, routes, VisitList, CreateVisit, ViewVisit (Tasks 21–27) | On `feature/3_visits`, **not merged** — EditVisit + DeleteVisit (Tasks 28–29) missing |
| **Groups 4–11** | Vitals, Medications, Labs, Recommendations, Reminders, Dashboards, etc. | Not started |

**Test count**: ~160 tests across Auth, UserManagement, PatientPolicy, Patient CRUD, Socioeconomic, Visit — all Livewire-era, zero REST API tests.

---

## SE / SD Cross-Section

### Domain Layer (shared — already done)

| Artifact | Status | Usable for SE? |
|---|---|---|
| `users` migration + `User` model + factory | ✅ master | Yes — shared as-is |
| `patients` migration + `Patient` model + factory | ✅ master | Yes |
| `patient_socioeconomic` migration + model + factory | ✅ master | Yes |
| `visits` migration + `Visit` model + factory | ✅ feature/3_visits | Yes — needs merge |
| `PatientPolicy` | ✅ master | Yes |
| `VisitPolicy` | ✅ feature/3_visits | Yes — needs merge |
| `StorePatientRequest` / `UpdatePatientRequest` | ✅ master | Yes |
| `RoleMiddleware` | ✅ master | Yes |

### SE Track — What's Missing (zero implementation)

| Layer | Needed | Status |
|---|---|---|
| Sanctum install + config | `php artisan install:api` | ❌ |
| `routes/api.php` | Auth, User, Patient, Visit endpoints | ❌ |
| `AuthController` | login / logout / me | ❌ |
| `UserController` (API) | CRUD | ❌ |
| `PatientController` (API) | CRUD | ❌ |
| `VisitController` (API) | CRUD | ❌ |
| Eloquent Resources | UserResource, PatientResource, PatientSocioeconomicResource, VisitResource | ❌ |
| Service layer | UserService, PatientService, VisitService | ❌ |
| Pest HTTP tests | 5+ required by SE M3 | ❌ |
| React SPA | All pages | ❌ |

### SD Track — What Exists

| Artifact | Status |
|---|---|
| M1 deliverables (30 user stories, 11 UML diagrams, Gantt, ER) | ✅ Complete |
| `SE_MVP_PLAN.md` — 30 user stories + milestone breakdown | ✅ |
| `M2_TASKS.md` + `M3_TASKS.md` — fully written, executable | ✅ |
| Architecture docs (`LARAVEL_API_RULES.md`, `DB_SCHEMA_FINAL.md`) | ✅ |
| SD implementation | ❌ Nothing |

---

## Bottom Line

The **domain layer is the biggest asset** — all 4 tables, models, factories, and policies are
done and directly reusable by the SE track with no changes. The Livewire UI layer (Groups 1–3)
is archived going forward.

The SE track's entire M2 starts from `routes/api.php` outward — the backend domain is ready,
nothing above it exists.

**Immediate prerequisite**: merge `feature/3_visits` into master before SE work starts, to
bring the Visit model + VisitPolicy into the shared domain layer.
