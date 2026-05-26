# Implementation Plan: Diet Plan Edit and Email Delivery

**Branch**: `015-diet-plan-edit-email` | **Date**: 2026-05-24 | **Spec**: [spec.md](spec.md)

## Summary

A doctor can select any completed diet plan from a patient's history, edit any field in-place (rationale, calories, macros, 7-day meals, warnings), save the changes, and send the plan to the patient's email address. Each save flags the plan as manually edited (`is_edited`, `edited_by`, `edited_at`). Each email send is recorded as a `DietPlanDelivery` row with a pending→sent/failed lifecycle driven by a queued job. The feature extends 014's `patient_diet_plans` table and `DietPlanController` with two new endpoints (PATCH update, POST send) and adds `diet_plan_deliveries` as a new table.

---

## Technical Context

**Language/Version**: PHP 8.4 (backend), TypeScript/React (frontend)
**Primary Dependencies**: Laravel 12, Sanctum, Laravel Mail (SMTP), existing Queue infrastructure from 014
**Storage**: MySQL — two migrations: `ALTER patient_diet_plans` (edit columns), `CREATE diet_plan_deliveries`
**Testing**: Pest 4 + `Mail::fake()` + `Queue::fake()` — no real SMTP calls in tests
**Target Platform**: Laravel REST API + React SPA
**Project Type**: Web application (BE + FE)
**Performance Goals**: PATCH responds within 500ms; POST /send returns 202 within 500ms; email delivered within 30 seconds
**Constraints**: Only `completed` plans are editable/sendable; patient must have a registered email; job retries handled by queue driver defaults
**Scale/Scope**: One edit and one send per doctor action; delivery history unlimited per plan

---

## Constitution Check

*GATE: Pre-design and post-design.*

### Principle I — Dual-Track Architecture ✅

This is an SD-track feature. It uses the shared Laravel REST API + React SPA architecture. No Livewire code is modified.

### Principle II — Authorization at Every Layer ✅

All three layers applied to both new endpoints:
1. **Route middleware**: `auth:sanctum` on all diet-plan routes (already in place)
2. **FormRequest `authorize()`**: `UpdateDietPlanRequest::authorize()` calls `$this->user()->can('update', $dietPlan)` via `DietPlanPolicy`; a new `SendDietPlanRequest::authorize()` calls `$this->user()->can('send', $dietPlan)`
3. **Policy**: `DietPlanPolicy::update()` and `DietPlanPolicy::send()` — both return `$user->isAdmin() || $user->isDoctor()`

Unauthenticated → 401, patient role → 403, cross-patient dietPlan → 404 (route scoping before authorize). All covered by test suite.

**Note**: `DietPlanPolicy` registration in `AppServiceProvider` was missing from 014 — this feature adds it explicitly.

### Principle III — Test-First ✅

Tests required in `tests/Feature/diet-plans/`:
- `DietPlanUpdateTest` — happy path (doctor, admin), 401 guest, 403 patient, 422 on non-completed plan, 422 on invalid fields, 404 route scoping, response shape, `is_edited` flag set
- `DietPlanSendTest` — happy path (doctor, admin), 401 guest, 403 patient, 422 on non-completed plan, 422 on missing patient email, 404 route scoping, delivery record created, `Mail::fake()` asserts mailable dispatched

`Mail::fake()` and `Queue::fake()` mandatory — no real SMTP or queue in tests.

### Principle IV — Code Quality Gates ✅

All changed files must pass:
1. `vendor/bin/pint --dirty`
2. `composer run analyse` (Larastan level 5)
3. `php artisan test tests/Feature/diet-plans/`

### Principle V — Developer-Ready Tasks ✅

BE and FE tasks are separated. Each task includes: goal, inputs, outputs, ordered steps, decision rationale, verification command.

---

## Project Structure

### Documentation (this feature)

```
specs/015-diet-plan-edit-email/
├── plan.md              ← this file
├── spec.md
├── research.md          ← Phase 0 ✅
├── data-model.md        ← Phase 1 ✅
├── quickstart.md        ← Phase 1 ✅
├── contracts/
│   └── api-endpoints.md ← Phase 1 ✅
├── checklists/
│   └── requirements.md
└── tasks.md             ← Phase 2 (next: /speckit.tasks)
```

### Source Code

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   └── DietPlanController.php          ← modified (update, send)
│   │   ├── Requests/
│   │   │   ├── UpdateDietPlanRequest.php        ← new
│   │   │   └── SendDietPlanRequest.php          ← new
│   │   └── Resources/Api/
│   │       ├── DietPlanResource.php             ← modified (edit fields + latestDelivery)
│   │       └── DietPlanDeliveryResource.php     ← new
│   ├── Jobs/
│   │   └── SendDietPlanEmailJob.php             ← new
│   ├── Mail/
│   │   └── DietPlanMailable.php                 ← new
│   ├── Models/
│   │   ├── PatientDietPlan.php                  ← modified (edit columns, deliveries relation)
│   │   └── DietPlanDelivery.php                 ← new
│   ├── Policies/
│   │   └── DietPlanPolicy.php                   ← modified (update, send methods)
│   └── Providers/
│       └── AppServiceProvider.php               ← modified (register DietPlanPolicy)
├── database/
│   ├── factories/
│   │   └── DietPlanDeliveryFactory.php          ← new
│   └── migrations/
│       ├── xxxx_add_edit_fields_to_patient_diet_plans.php ← new
│       └── xxxx_create_diet_plan_deliveries_table.php     ← new
├── resources/views/emails/
│   └── diet-plan.blade.php                      ← new
└── tests/Feature/diet-plans/
    ├── DietPlanUpdateTest.php                   ← new
    └── DietPlanSendTest.php                     ← new

frontend/
└── src/views/patients/diet-plans/
    ├── DietPlanCard.tsx                         ← modified (Edit + Send buttons, Edited badge, last sent)
    └── DietPlanEditForm.tsx                     ← new (inline edit form)
```

**Structure Decision**: Option 2 (Web application). Backend under `backend/`, frontend under `frontend/`. New models under `app/Models/`, new jobs under `app/Jobs/`, mail under `app/Mail/` following Laravel conventions.

---

## Implementation Sequence

| # | Layer | Description | Depends on |
|---|---|---|---|
| 1 | BE | Migrations: edit columns on `patient_diet_plans` + `diet_plan_deliveries` table | — |
| 2 | BE | `DietPlanDelivery` model + factory | 1 |
| 3 | BE | Update `PatientDietPlan` model (fillable, casts, relations) | 1, 2 |
| 4 | BE | Register `DietPlanPolicy` in `AppServiceProvider`; add `update` + `send` methods to policy | 3 |
| 5 | BE | `UpdateDietPlanRequest` + `SendDietPlanRequest` | 4 |
| 6 | BE | `DietPlanController::update()` + PATCH route | 3, 5 |
| 7 | BE | `DietPlanMailable` + `resources/views/emails/diet-plan.blade.php` | 3 |
| 8 | BE | `SendDietPlanEmailJob` | 2, 7 |
| 9 | BE | `DietPlanController::send()` + POST route | 5, 8 |
| 10 | BE | `DietPlanDeliveryResource` + update `DietPlanResource` (edit fields + latestDelivery) | 2, 6, 9 |
| 11 | BE | Pest tests: `DietPlanUpdateTest` + `DietPlanSendTest` | 6, 9, 10 |
| 12 | FE | `DietPlanEditForm.tsx` — inline edit form for all plan fields | 6 |
| 13 | FE | Update `DietPlanCard.tsx` — Edit button, Save/Cancel, Edited badge, Send button, last sent indicator | 12 |

---

## Key Risks & Mitigations

| Risk | Mitigation |
|---|---|
| `DietPlanPolicy` autodiscovery works without explicit registration | Verified against `AppServiceProvider` — registration is missing. Task 4 adds it explicitly. |
| Editing a plan while a send job is in-flight produces inconsistent email content | Acceptable — the email captures plan content at dispatch time inside the Mailable constructor. Race window is tiny. Document as known limitation. |
| Patient has no `user.email` | `SendDietPlanRequest::authorize()` (or controller pre-check) rejects with 422 before dispatching. |
| Queue worker down — delivery stays `pending` forever | Consistent with 014's pattern. Documented in quickstart. No automatic UI retry beyond the doctor re-clicking Send. |
| Larastan complains about `$this->when()` in resources | Use `@var` annotation or cast explicitly — same pattern as existing resources. |
