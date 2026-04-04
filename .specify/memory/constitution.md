<!--
  SYNC IMPACT REPORT
  ==================
  Version change: 2.0.1 → 2.1.0
  Type: MINOR — New Principle VI added: API Input/Output Case Convention.

  Added principles:
  - VI. API Input/Output Case Convention (NON-NEGOTIABLE)
      Request bodies use snake_case; responses use camelCase (in Resources only).
      Eliminates Str::snake() conversion layers in controllers.
      Applies retroactively to all feature groups (Users, Patients, Visits, future).

  Propagation required:
  - specs/003-patient-management-api/spec.md   ✅ FR-006 updated; Key Entities corrected to snake_case.
  - specs/003-patient-management-api/tasks.md  → Review T006 (StorePatientRequest) — rule keys must be snake_case.
  - app/Http/Requests/StorePatientRequest.php  → MUST be updated to snake_case rule keys.
  - tests/Feature/Patient/PatientApiTest.php   → MUST send snake_case payloads.
  - CLAUDE.md                                  ✅ Updated.

  ---

  Version change: 1.0.0 → 2.0.1 (cumulative; 1.0.0 → 2.0.0 → 2.0.1)
  Type (2.0.1): PATCH — Technology Stack clarification: SE frontend locked to React + JavaScript.

  ---

  Version change: 1.0.0 → 2.0.0
  Type: MAJOR — backward-incompatible redefinition of Principle I.
    The Livewire monolith SD track is retired. Both SE and SD tracks now use the
    same BE+FE architecture (Laravel REST API + React SPA). Tracks are now
    differentiated by purpose/scope, not technology.

  Modified principles:
  - I. Dual-Track Architecture
      OLD: SE = Laravel REST API + React SPA; SD = Livewire/Volt monolith
      NEW: SE = MVP implementation (Users + Patients + Visits);
           SD = MVP design/proposal in same BE+FE architecture.
           Livewire monolith is archived — not deleted, not actively developed.

  Modified sections:
  - Shared Artifacts: Removed Livewire-exclusive SD paths; added legacy archive note.
  - Technology Stack: Collapsed dual-column table into unified stack; added scope row.
  - Principle V: Fixed stale "FE (frontend / Livewire)" wording.
  - Governance compliance review: Removed "Livewire components" from trigger list.

  Added sections: None.

  Removed sections: None.

  Template consistency review:
  - .specify/templates/plan-template.md   ✅ No Livewire-specific language; no changes needed.
  - .specify/templates/spec-template.md   ✅ Track-agnostic; no changes needed.
  - .specify/templates/tasks-template.md  ✅ Path conventions updated to match unified stack.

  Follow-up TODOs:
  - Livewire/Volt source files in app/Livewire/ and resources/views/livewire/ remain
    in the repo as archived code. A cleanup PR SHOULD be tracked separately.
  - Memory file MEMORY.md should reflect the track redefinition (Architecture Pivot note).
-->

# NutriLedger Constitution

## Core Principles

### I. Dual-Track Architecture

The repository hosts two tracks sharing one architecture. Both tracks use **Laravel REST API
+ React SPA** (Laravel 12 + Sanctum on the backend; React + TypeScript SPA on the frontend).

- **SE track** — Full MVP implementation up to and including the Visits feature
  (Feature Groups 1: Users, 2: Patients, 3: Visits). Deliverable: working, tested code.
  Target: university Software Engineering project milestone requirements.
- **SD track** — Initial MVP release proposal in the same BE+FE architecture. Deliverable:
  architecture documentation, design patterns, and the proposed MVP scope.
  Target: university Software Design coursework. The former Livewire/Volt monolith is
  **retired** — it is archived in the repository and MUST NOT be actively developed or
  extended. Existing Livewire code MUST NOT be deleted (preserves academic history).

Both tracks MUST share: `app/Models/`, `database/migrations/`, `database/factories/`,
`app/Policies/`, and `app/Http/Requests/`. Schema changes MUST be coordinated between tracks
since both consume the same database layer.

### II. Authorization at Every Layer (NON-NEGOTIABLE)

Every request that touches protected data MUST pass through all three authorization layers:

1. **Route middleware**: `auth:sanctum` declared in the route definition.
2. **FormRequest `authorize()`**: MUST call `$this->user()->can(...)` or an explicit policy
   check — never return `true` unconditionally on protected requests.
3. **Policy method**: A named Policy class (`PatientPolicy`, `UserPolicy`, etc.) with
   a dedicated method per action (view, create, update, delete).

Unauthenticated requests MUST return HTTP 401. Unauthorized requests MUST return HTTP 403.
Patients MUST only access their own records — cross-patient data access is a critical
security violation and MUST be caught by policy and tested explicitly.

### III. Test-First (NON-NEGOTIABLE)

No feature is considered done without passing Pest tests that cover:

- **Happy path**: the operation succeeds for an authorized user with valid input.
- **Validation failure**: invalid input returns the expected error structure.
- **Role/auth rejection**: unauthenticated → 401, wrong role → 403.

Tests MUST be written alongside or before implementation (TDD encouraged). The verification
command for every task MUST include a `php artisan test --filter=...` invocation. A task
may not be marked complete if any related test is failing or missing.

### IV. Code Quality Gates (NON-NEGOTIABLE)

Every changed file MUST pass all three gates before a task is complete:

1. `vendor/bin/pint --dirty` — enforces PSR-12 / project code style.
2. `composer run analyse` — Larastan level 5 static analysis; no ignored errors added
   without documented justification.
3. Affected Pest tests — `php artisan test --filter=<name>` (or full suite if scope
   warrants it).

No task is mergeable if any gate is red. Gates run in this order; fix style before
analysing, analyse before running tests.

### V. Tasks Are Developer-Ready Specs

Every task MUST be self-contained and assignable without verbal explanation. Required
fields per task:

- **Goal**: one sentence describing the outcome.
- **Inputs**: files, models, or prior tasks this task depends on.
- **Outputs**: exact files created or modified.
- **Ordered steps**: numbered, with full code snippets where non-trivial.
- **Decision rationale**: why this approach over the obvious alternative.
- **Verification command**: the exact shell command to confirm the task is done.

BE (backend) and FE (frontend / React) tasks MUST always be separate task items.
No single task may span both the API layer and the React SPA layer.

### VI. API Input/Output Case Convention (NON-NEGOTIABLE)

Request bodies (input) MUST use **snake_case** keys. This aligns with Laravel's native
conventions: `$fillable`, factory definitions, `$request->validated()`, and Eloquent all
use snake_case. No conversion layer between FormRequest and Eloquent is permitted.

Response bodies (output) MUST use **camelCase** attribute keys. The mapping from
snake_case model attributes to camelCase happens exclusively inside Eloquent API Resource
classes (`toArray()`). No other layer performs case conversion.

Concrete rule: `$request->validated()` MUST be passable directly to `Model::create()` or
`$model->update()` without any `Str::snake()` / `mapWithKeys()` transformation.
Tests MUST send snake_case payloads. FormRequest validation rules MUST use snake_case keys.

This rule governs all feature groups: Users, Patients, Visits, and any future groups.

## Shared Artifacts

The following directories are shared between SE and SD tracks. Changes MUST be backward
compatible with both, or explicitly coordinated:

| Artifact | Path | Notes |
|---|---|---|
| Eloquent models | `app/Models/` | Shared; both tracks depend on these |
| Migrations | `database/migrations/` | Schema changes affect both tracks |
| Factories | `database/factories/` | Used by Pest tests in both tracks |
| Policies | `app/Policies/` | Enforced by API middleware + FormRequests |
| Form requests | `app/Http/Requests/` | Shared validation rules |
| Pest feature tests | `tests/Feature/` | Cover shared domain logic |

**Archived (legacy) code**: `app/Livewire/`, `resources/views/livewire/`, `routes/web.php`
Livewire routes and components. These MUST NOT be modified or deleted. They are preserved
for academic history only. New features MUST NOT be added to this layer.

## Technology Stack

Both tracks share a single technology stack:

| Concern | Stack |
|---|---|
| Backend framework | Laravel 12 (API-only) |
| Auth | Sanctum (token-based) |
| Routing | `routes/api.php` |
| Responses | Eloquent API Resources |
| Frontend (SE) | React + JavaScript SPA (separate folder/repo) |
| Frontend (SD) | TBD — React (reuse SE) or Vue 3 (team-dependent); decided at SD frontend spec time |
| Testing | Pest 4 + SQLite in-memory |
| Static analysis | Larastan level 5 |
| Code style | Laravel Pint |

**Track scope differentiation**:

| Track | Scope | Deliverable |
|---|---|---|
| SE | Feature Groups 1–3 (Users, Patients, Visits) | Working implementation |
| SD | MVP proposal: same Feature Groups 1–3 | Design docs + proposed architecture |

PHP version: 8.4. Do not introduce dependencies that require PHP < 8.4.

## Governance

This constitution supersedes all other conventions and CLAUDE.md entries for architectural
decisions. When a conflict exists between this document and any other guideline, this
document wins. To override this document, amend it.

**Amendment procedure**:

1. Open a PR with the proposed change to `.specify/memory/constitution.md`.
2. Document the justification in the PR description (what problem does this solve?
   why can't it be solved within existing principles?).
3. Bump the version following semantic versioning:
   - MAJOR — backward-incompatible removal or redefinition of an existing principle.
   - MINOR — new principle or section added; materially expanded guidance.
   - PATCH — clarification, wording improvement, typo fix.
4. Run the consistency propagation checklist against all `.specify/templates/` files
   and update any that reference the changed principle.
5. Update `LAST_AMENDED_DATE` to the date of the amendment commit.

**Compliance review**: every PR that touches models, routes, controllers, or tests MUST
include a Constitution Check confirming Principles II, III, and IV are satisfied. Use
the `plan.md` "Constitution Check" gate for feature work.

---

**Version**: 2.1.0 | **Ratified**: 2026-03-15 | **Last Amended**: 2026-04-04
