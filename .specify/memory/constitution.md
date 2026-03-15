<!--
  SYNC IMPACT REPORT
  ==================
  Version change: (blank) → 1.0.0
  Type: MINOR (initial ratification from blank template)

  Modified principles: N/A (initial fill — all sections newly populated)

  Added sections:
  - Core Principles (I–V)
  - Shared Artifacts
  - Technology Stack
  - Governance

  Removed sections: N/A

  Template consistency review:
  - .specify/templates/plan-template.md   ✅ "Constitution Check" gate aligns with Principles II–IV
  - .specify/templates/spec-template.md   ✅ Authorization scenarios align with Principle II
  - .specify/templates/tasks-template.md  ✅ Test-first ordering aligns with Principle III; task format aligns with Principle V

  Follow-up TODOs: None — all placeholders resolved.
-->

# NutriLedger Constitution

## Core Principles

### I. Dual-Track Architecture

The repository hosts two parallel tracks that MUST coexist without interference:

- **SE track** — Laravel REST API (`api.php` routes, Eloquent Resources, Sanctum auth) + React SPA
  (separate frontend). Target: university SE project requirement of separated FE/BE.
- **SD track** — Laravel + Livewire/Volt monolith (`web.php` routes, Flux UI components).
  Target: software design coursework and production-style monolith practice.

Both tracks MUST share: `app/Models/`, `database/migrations/`, `database/factories/`,
`app/Policies/`, and `app/Http/Requests/`. Neither track may delete or rename the other
track's routes, views, Livewire components, API controllers, or resources. When a model or
migration changes, both tracks are affected — coordinate accordingly.

### II. Authorization at Every Layer (NON-NEGOTIABLE)

Every request that touches protected data MUST pass through all three authorization layers:

1. **Route middleware**: `auth:sanctum` (SE) or `auth` + `role:*` (SD) declared in the
   route definition.
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

BE (backend) and FE (frontend / Livewire) tasks MUST always be separate task items.
No single task may span both tracks or both concerns.

## Shared Artifacts

The following directories are owned jointly by both tracks. Changes MUST be backward
compatible with both, or coordinated with the maintainer of the other track:

| Artifact | Path | Notes |
|---|---|---|
| Eloquent models | `app/Models/` | Shared; both tracks read/write via these |
| Migrations | `database/migrations/` | Schema changes affect both tracks |
| Factories | `database/factories/` | Used by Pest tests in both tracks |
| Policies | `app/Policies/` | Enforced by both API controllers and Livewire |
| Form requests | `app/Http/Requests/` | May be reused across tracks where validation rules overlap |
| Pest feature tests | `tests/Feature/` | Cover shared domain logic; both tracks add tests here |

Track-exclusive artifacts (SD: `resources/views/livewire/`, `app/Livewire/`;
SE: `app/Http/Controllers/Api/`, `app/Http/Resources/`) MUST NOT be modified by the
other track.

## Technology Stack

| Concern | SD Track | SE Track |
|---|---|---|
| Framework | Laravel 12 + Livewire 3 | Laravel 12 (API-only) |
| Auth | Fortify (session) | Sanctum (token) |
| Frontend | Livewire/Volt + Flux UI v2 + Tailwind v4 | React SPA (separate repo/folder) |
| Routing | `routes/web.php` | `routes/api.php` |
| Responses | Blade views | Eloquent API Resources |
| Testing | Pest 4 + SQLite in-memory | Pest 4 + SQLite in-memory |
| Static analysis | Larastan level 5 | Larastan level 5 |
| Code style | Laravel Pint | Laravel Pint |

PHP version: 8.4. Do not introduce dependencies that require PHP < 8.4 or drop
support for either track's runtime.

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

**Compliance review**: every PR that touches models, routes, controllers, Livewire
components, or tests MUST include a Constitution Check confirming Principles II, III,
and IV are satisfied. Use the `plan.md` "Constitution Check" gate for feature work.

---

**Version**: 1.0.0 | **Ratified**: 2026-03-15 | **Last Amended**: 2026-03-15
