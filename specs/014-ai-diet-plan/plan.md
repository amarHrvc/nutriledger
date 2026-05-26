# Implementation Plan: AI Diet Plan Generator

**Branch**: `014-ai-diet-plan` | **Date**: 2026-05-17 | **Spec**: [spec.md](spec.md)

## Summary

A doctor triggers AI-powered 7-day meal plan generation for a patient. The system immediately creates a `patient_diet_plans` record (status: pending), dispatches a queued job, and returns 202. The job builds `DietPlanAgent` (Laravel AI SDK, Haiku, `HasStructuredOutput`), injects all patient clinical + socioeconomic data into the system prompt, calls `->prompt()`, validates the response, retries once on failure, and updates the record to `completed` or `failed`. Doctors poll `GET /api/patients/{patient}/diet-plans` for status. Full history of all generation attempts is preserved per patient.

---

## Technical Context

**Language/Version**: PHP 8.3 (backend), JavaScript/React (frontend)  
**Primary Dependencies**: Laravel 12, `laravel/ai` (new), Anthropic Claude Haiku  
**Storage**: MySQL — new `patient_diet_plans` table; JSON columns for `nutritional_goals`, `days`, `warnings`  
**Testing**: Pest 4 + `Agent::fake()` + `Queue::fake()` — no real API calls in tests  
**Target Platform**: Laravel REST API + React SPA  
**Project Type**: Web service (REST API) + React SPA  
**Performance Goals**: 202 response within 2 seconds; completed plan within 30 seconds  
**Constraints**: Haiku model only (cost); max 2 generation attempts per request; no WebSocket (polling)  
**Scale/Scope**: One plan generation per doctor action; history unlimited per patient

---

## Constitution Check

*GATE: Pre-design and post-design.*

### Principle II — Authorization at Every Layer ✅

All three layers applied:
1. **Route middleware**: `auth:sanctum` on all diet-plan routes
2. **FormRequest `authorize()`**: `StoreDietPlanRequest::authorize()` calls `$this->user()->can('generate', $patient)` via `DietPlanPolicy`
3. **Policy**: `DietPlanPolicy` with `generate`, `viewAny`, `view` — all return `$user->isAdmin() || $user->isDoctor()`. Patients explicitly excluded per FR-012.

Unauthenticated → 401, patient role → 403, cross-patient dietPlan → 404 (route scoping). All covered by test suite.

### Principle III — Test-First ✅

Tests required in `tests/Feature/diet-plans/`:
- `DietPlanGenerateTest` — happy path (doctor, admin), 401 guest, 403 patient, 202 response shape, job dispatched
- `DietPlanListTest` — returns paginated history ordered newest first, 401/403
- `DietPlanShowTest` — full plan detail, route scoping 404, 401/403

`Agent::fake()` and `Queue::fake()` used throughout — no real API calls.

### Principle IV — Code Quality Gates ✅

All changed files must pass:
1. `vendor/bin/pint --dirty`
2. `composer run analyse` (Larastan level 5)
3. `php artisan test --filter=DietPlan`

### Principle V — Developer-Ready Tasks ✅

BE and FE tasks are separated. Each task will include: goal, inputs, outputs, ordered steps, decision rationale, verification command.

**Complexity justified**: `laravel/ai` is a new dependency. Justified — it is the official Laravel SDK for this use case and the central learning objective of this SD-track feature.

---

## Project Structure

### Documentation (this feature)

```
specs/014-ai-diet-plan/
├── plan.md              ← this file
├── spec.md
├── research.md          ← Phase 0 ✅
├── data-model.md        ← Phase 1 ✅
├── quickstart.md        ← Phase 1 ✅
├── contracts/
│   └── api-endpoints.md ← Phase 1 ✅
└── tasks.md             ← Phase 2 (next: /speckit.tasks)
```

### Source Code

```
backend/
├── app/
│   ├── Ai/
│   │   └── Agents/
│   │       └── DietPlanAgent.php            ← new
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   └── DietPlanController.php       ← new (index, show, store)
│   │   ├── Requests/
│   │   │   └── StoreDietPlanRequest.php     ← new
│   │   └── Resources/Api/
│   │       ├── DietPlanResource.php         ← new (full detail)
│   │       └── DietPlanSummaryResource.php  ← new (list items)
│   ├── Jobs/
│   │   └── GenerateDietPlanJob.php          ← new
│   ├── Models/
│   │   └── PatientDietPlan.php              ← new
│   └── Policies/
│       └── DietPlanPolicy.php               ← new
├── database/migrations/
│   └── xxxx_create_patient_diet_plans_table.php ← new
├── config/
│   └── ai.php                               ← new (published by SDK)
└── tests/Feature/diet-plans/
    ├── DietPlanGenerateTest.php             ← new
    ├── DietPlanListTest.php                 ← new
    └── DietPlanShowTest.php                 ← new

frontend/
└── src/
    └── pages/patients/
        └── components/DietPlanSection/
            ├── DietPlanSection.jsx          ← new (container + polling)
            ├── DietPlanCard.jsx             ← new (completed plan view)
            └── DietPlanHistory.jsx          ← new (history list)
```

**Structure Decision**: Option 2 (Web application). Backend under `backend/`, frontend under `frontend/`. Agent code in `app/Ai/Agents/` following the SDK's recommended namespace (`App\Ai\Agents\`).

---

## Implementation Sequence

Tasks (to be detailed in `tasks.md` via `/speckit.tasks`):

| # | Layer | Description | Depends on |
|---|---|---|---|
| 1 | BE | Install `laravel/ai`, configure Anthropic provider | — |
| 2 | BE | Migration + `PatientDietPlan` model + factory | 1 |
| 3 | BE | `DietPlanAgent` with `HasStructuredOutput` | 1, 2 |
| 4 | BE | `GenerateDietPlanJob` (retry logic, status transitions) | 2, 3 |
| 5 | BE | `DietPlanPolicy` + register in `AuthServiceProvider` | 2 |
| 6 | BE | `DietPlanController` + `StoreDietPlanRequest` | 4, 5 |
| 7 | BE | `DietPlanResource` + `DietPlanSummaryResource` | 6 |
| 8 | BE | Routes (`api.php`) | 6, 7 |
| 9 | BE | Pest tests (`DietPlanGenerateTest`, `DietPlanListTest`, `DietPlanShowTest`) | 8 |
| 10 | FE | `DietPlanSection` component + polling hook | 8 |
| 11 | FE | `DietPlanCard` component (completed plan display) | 10 |
| 12 | FE | `DietPlanHistory` component (history list) | 10 |

---

## Key Risks & Mitigations

| Risk | Mitigation |
|---|---|
| SDK structured output fails validation silently | `Validator::make($response->toArray(), [...])` mandatory after every `->prompt()` — enforced in `GenerateDietPlanJob` |
| Agent returns wrong number of days | `'days' => ['required', 'array', 'size:7']` in validator |
| Allergen appears in plan despite prompt rule | Validator cannot catch this — it's a prompt engineering concern. Document as known limitation; future mitigation: post-generation allergen scan |
| `jobs` table missing | Documented in quickstart.md; `php artisan queue:table && migrate` if absent |
| Larastan complains about `$response->toArray()` return type | Add `@var array` annotation or cast explicitly |
