# Implementation Plan: Auth & User Management API

**Branch**: `002-user-mgmt-api` | **Date**: 2026-03-19 | **Spec**: [spec.md](spec.md)
**Input**: Feature specification from `/specs/002-user-mgmt-api/spec.md`

## Summary

Complete the Auth & User Management REST API for NutriLedger SE track. Builds on existing Sanctum auth infrastructure by: refactoring `UserPolicy` to admin-only, implementing full `UserController` CRUD with soft-delete support, completing `AuthController` (`register`, `me`), creating `UserResource` (JSON:API structure), three form request classes, configuring token expiry and login rate limiting, adding minimal security-event logging, and writing 25+ Pest tests with full happy-path, validation, and authorization coverage.

## Technical Context

**Language/Version**: PHP 8.4
**Primary Dependencies**: Laravel 12, Sanctum (token auth), Pest 4, Larastan 3, Laravel Pint
**Storage**: MySQL (production), SQLite in-memory (tests)
**Testing**: Pest 4, `php artisan test`, SQLite in-memory via `RefreshDatabase`
**Target Platform**: Linux server, API-only backend
**Project Type**: web-service (REST API, unversioned)
**Performance Goals**: Standard web response times; no explicit latency targets for MVP scope
**Constraints**: Rate-limited login (5/min per IP), token absolute expiry via `config('sanctum.expiration')`, passwords never in any response payload
**Scale/Scope**: University MVP, SE Feature Groups 1-3, ~25+ automated tests

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

### Principle I — Dual-Track Architecture

- ✅ SE track work only; no FE tasks in this feature
- ✅ `app/Models/`, `app/Policies/`, `database/migrations/`, `database/factories/` are shared — no schema changes needed (User model + users table already exist)
- ✅ Livewire code (`app/Livewire/`, `resources/views/livewire/`) untouched
- ✅ No new dependencies introduced

### Principle II — Authorization at Every Layer (NON-NEGOTIABLE)

- ⚠️ **CURRENT VIOLATION (to remediate)**: `UserPolicy::create()` and `update()` allow `doktor` role. `viewAny()`, `view()`, `restore()`, `forceDelete()` are commented out — no policy method exists for these actions.
- ✅ **REMEDIATION IS THIS FEATURE**: Task 1 (UserPolicy refactor) fixes this violation before any other task proceeds.
- ✅ **Post-fix design**:
  - Layer 1 — Route middleware: `auth:sanctum` + `role:admin` on all `UserController` routes
  - Layer 2 — FormRequest `authorize()`: uses `$this->user()->isAdmin()` — not unconditional `true`
  - Layer 3 — Policy: dedicated method per action (`viewAny`, `view`, `create`, `update`, `delete`, `restore`, `forceDelete`), all admin-only

### Principle III — Test-First (NON-NEGOTIABLE)

- ✅ Tests written alongside or before implementation (TDD)
- ✅ 25+ Pest HTTP tests covering: happy path, validation failure (422), unauthenticated (401), wrong role (403)
- ✅ Deactivated-user login blocking verified by explicit test
- ✅ Verification command per task: `php artisan test --filter=...`

### Principle IV — Code Quality Gates (NON-NEGOTIABLE)

- ✅ `vendor/bin/pint --dirty` runs after every task
- ✅ `composer run analyse` (Larastan level 5) runs after every task
- ✅ Affected Pest tests pass before task is complete

### Principle V — Tasks Are Developer-Ready Specs

- ✅ All tasks (in tasks.md, Phase 2) will include: goal, inputs, outputs, ordered steps with code snippets, decision rationale, verification command
- ✅ BE and FE tasks are separate items (this feature is BE-only)

**Constitution Check Result**: ✅ PASS — one active violation identified and explicitly remediated as Task 1

## Project Structure

### Documentation (this feature)

```text
specs/002-user-mgmt-api/
├── plan.md              ← this file
├── research.md          ← Phase 0 output (complete)
├── data-model.md        ← Phase 1 output (complete)
├── quickstart.md        ← Phase 1 output (complete)
├── contracts/
│   └── api-endpoints.md ← Phase 1 output (complete)
└── tasks.md             ← Phase 2 output (/speckit.tasks — not yet created)
```

### Source Code

```text
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── ApiController.php            [EXISTS — no changes]
│   │   │   ├── AuthController.php           [MODIFY: register(), me()]
│   │   │   └── UserController.php           [REWRITE: remove create/edit stubs; implement index, store, show, update, destroy; add restore, forceDelete]
│   │   ├── Requests/Api/
│   │   │   ├── LoginRequest.php             [EXISTS — no changes]
│   │   │   ├── RegisterRequest.php          [CREATE]
│   │   │   ├── StoreUserRequest.php         [CREATE]
│   │   │   └── UpdateUserRequest.php        [CREATE]
│   │   └── Resources/Api/
│   │       └── UserResource.php             [CREATE]
│   ├── Policies/
│   │   └── UserPolicy.php                   [REFACTOR: admin-only, uncomment all methods]
│   ├── Providers/
│   │   └── AppServiceProvider.php           [MODIFY: register login rate limiter]
│   └── Traits/
│       └── ApiResponses.php                 [UPDATE: add $data param to ok(), created()]
├── config/
│   └── sanctum.php                          [UPDATE: set expiration from env]
├── routes/
│   └── api.php                              [UPDATE: replace manual route, add apiResource + restore/forceDelete/me]
└── tests/Feature/Api/
    ├── AuthTest.php                         [UPDATE: fix envelope assertions, fix logout to use noContent(), add new scenarios]
    ├── UserManagementTest.php               [CREATE: 20+ CRUD + auth + validation tests]
    └── UserPolicyTest.php                   [CREATE: policy unit tests]
```

**Structure Decision**: Single web-service project (Laravel BE only). No frontend work in this feature. All new source files follow existing `app/Http/Controllers/Api/`, `app/Http/Requests/Api/`, `app/Http/Resources/Api/` namespace conventions established in the project.

## Phase 0: Research Summary

All unknowns resolved. See [research.md](research.md) for full decisions and rationale.

| Unknown | Decision |
|---------|----------|
| Token expiry mechanism | Per-token `expires_at` via `createToken()` third arg; duration from `config('sanctum.expiration')` |
| Rate limiting on login | Named rate limiter `'login'` in `AppServiceProvider`; `throttle:login` on route; 5/min per IP |
| Route model binding for soft-deleted records | Explicit `User::withTrashed()->findOrFail($id)` in `restore()` and `forceDelete()` controllers |
| ApiResponses trait data envelope | Update `ok()` and `created()` to accept optional `$data` param; update existing auth tests |
| Deactivated user login blocking | No code needed — SoftDeletes global scope excludes them from `auth()->attempt()` query; verify via test |
| Security logging | `Log::warning()` calls in controllers for 4 events; no PII; written to `stack` channel |

## Phase 1: Design Summary

All design artifacts complete. See linked files for details.

| Artifact | File | Status |
|----------|------|--------|
| Entity definitions + validation rules + state machine | [data-model.md](data-model.md) | Complete |
| API endpoint contracts (all 11 operations) | [contracts/api-endpoints.md](contracts/api-endpoints.md) | Complete |
| Developer setup + verification commands | [quickstart.md](quickstart.md) | Complete |

### Key Design Decisions

1. **ApiResponses trait breaking change**: `ok()` and `created()` gain optional `$data` parameter. `AuthTest.php` must be updated — login response changes from `{token, user}` (flat) to `{message, status, data: {token, user}}`.

2. **UserController restore/forceDelete**: These methods do NOT use implicit route model binding. They call `User::withTrashed()->findOrFail($id)` explicitly to resolve soft-deleted records.

3. **UserResource relationships**: `patient` relationship is conditionally included using `whenLoaded()`. Controllers load it explicitly (`$user->load('patient')`) for `show()` and `me()`. Index results do not eager-load patient (performance).

4. **Self-deactivation prevention**: Enforced at policy level (`UserPolicy::delete()` returns false when `$user->id === $model->id`). Controller calls `$this->authorize('delete', $user)` which triggers the policy check.

5. **Register response**: Returns 201 with `{message, status, data: {token, user: UserResource}}`. Token is created immediately on registration.

6. **Sanctum expiration config**: `config/sanctum.php` `expiration` changes from `null` to `env('SANCTUM_EXPIRATION', 1440)`. Existing login code uses `Carbon::now()->addDays(1)` hardcoded — refactor to `Carbon::now()->addMinutes(config('sanctum.expiration'))`. Both the global `expiration` config and the per-token `expires_at` value are checked by Sanctum independently; keeping them consistent prevents subtle token lifetime drift.

7. **AuthController logout bug**: The current `logout()` calls `$this->success('Logged out', Response::HTTP_NO_CONTENT)`. `success()` returns a JSON-body response regardless of status code — a 204 response MUST have no body. Fix: replace with `$this->noContent()`. The existing `noContent()` method in `ApiResponses` already returns `response()->json(null, 204)` correctly.

## Constitution Check (Post-Design)

- ✅ Principle I: BE-only, SE track, shared artifacts untouched, no new dependencies
- ✅ Principle II: All 3 auth layers satisfied for every endpoint; UserPolicy violation remediated in Task 1
- ✅ Principle III: Test plan covers all mandatory scenarios; tests written first
- ✅ Principle IV: All 3 quality gates required per task
- ✅ Principle V: tasks.md will be generated by `/speckit.tasks`

**Post-design result**: ✅ PASS

## Next Step

Run `/speckit.tasks` to generate the ordered task list from this plan.
