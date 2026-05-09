# Tasks: Setup Scramble API Documentation

**Input**: Design documents from `/specs/004-scramble-api-docs/`
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/docs-endpoints.md

**Tests**: Included — Constitution Principle III requires Pest tests. SC-001 requires structural assertion on `/docs/api.json`.

**Organization**: Tasks grouped by user story. BE tasks and FE tasks are always separate (Constitution Principle V).

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (US1, US2, US3)

## Path Conventions

- Backend: `backend/`
- Frontend: `frontend/`

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Install packages required for all user stories.

- [ ] T001 Install `dedoc/scramble` package: run `composer require dedoc/scramble` in `backend/`
- [ ] T002 Publish Scramble config: run `php artisan vendor:publish --provider="Dedoc\Scramble\ScrambleServiceProvider" --tag=scramble-config` in `backend/` — produces `backend/config/scramble.php`

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core configuration that MUST be complete before any user story can be verified.

**⚠️ CRITICAL**: No user story work can begin until this phase is complete.

- [ ] T003 Add env gate + Sanctum Bearer auth config to `backend/app/Providers/AppServiceProvider.php`:
  - Import `Dedoc\Scramble\Scramble`, `Dedoc\Scramble\Support\Generator\OpenApi`, `Dedoc\Scramble\Support\Generator\SecurityScheme`, `Illuminate\Support\Facades\App`
  - In `boot()`, wrap all Scramble calls in `if (!App::isProduction()) { ... }`
  - Call `Scramble::extendOpenApi(fn (OpenApi $openApi) => $openApi->secure(SecurityScheme::http('bearer')))`
  - Leave route registration to T005 (done via config, not here)
- [ ] T004 Add `frontend/src/api/generated/` to `frontend/.gitignore` — generated output must never be committed

**Checkpoint**: Scramble is installed and gated; generated dir is ignored. User story work can begin.

---

## Phase 3: User Story 1 — Browse Live API Documentation (Priority: P1) 🎯 MVP

**Goal**: Serve a live OpenAPI 3.1 spec and interactive Swagger UI for all existing API endpoints, accessible in local/testing environments only.

**Independent Test**: Start backend → `GET http://localhost:8000/docs/api.json` returns 200 with JSON body containing paths for `/api/patients`, `/api/users`, `/api/login`.

### Test for User Story 1

> Write this test FIRST and confirm it FAILS before T006.

- [ ] T005 [US1] Write Pest feature test in `backend/tests/Feature/Api/ScrambleDocsTest.php`:
  - `it('exposes openapi spec endpoint')` — `GET /docs/api.json` → assertOk() + assertJsonStructure(['info', 'paths'])
  - `it('includes core api paths in spec')` — `GET /docs/api.json` → assert response JSON contains keys `paths./api/patients`, `paths./api/users`, `paths./api/login`
  - `it('exposes swagger ui')` — `GET /docs/api` → assertOk() + assertSee('swagger', false)
  - `it('documents bearer token security scheme')` — `GET /docs/api.json` → assertJsonPath('components.securitySchemes.bearerAuth.type', 'http') (covers SC-002)
  - `it('includes request body schema for patient endpoints')` — `GET /docs/api.json` → assert `paths./api/patients.post.requestBody` exists in JSON (covers FR-003)
  - `it('excludes docs routes from spec')` — `GET /docs/api.json` → assert `paths./docs/api` and `paths./docs/api.json` are absent from JSON (covers FR-009)

### Implementation for User Story 1

- [ ] T006 [US1] Configure `backend/config/scramble.php`:
  - Set `info.title` → `'NutriBase API'`
  - Set `info.version` → `'1.0.0'`
  - Set `api_path` → `'api'`
  - Set `routes` key to a closure that excludes routes whose name starts with `test`: `fn (\Illuminate\Routing\Route $route) => !str_starts_with($route->getName() ?? '', 'test')`
- [ ] T007 [US1] Run quality gates and confirm US1 test passes:
  - `vendor/bin/pint --dirty` (in `backend/`)
  - `composer run analyse` (in `backend/`)
  - `php artisan test --filter=ScrambleDocsTest` (in `backend/`)
  - All three must pass before proceeding

**Checkpoint**: `GET /docs/api` shows Swagger UI with all routes. `GET /docs/api.json` returns valid spec. Tests green.

---

## Phase 4: User Story 2 — Frontend Code Generation (Priority: P2)

**Goal**: Configure Orval to read the live spec URL and generate typed fetch wrappers + TypeScript interfaces into `frontend/src/api/generated/`.

**Independent Test**: With backend running, `pnpm run api:generate` in `frontend/` exits 0 and produces files in `frontend/src/api/generated/` including at least `patients.ts`, `users.ts`, `auth.ts`.

### Implementation for User Story 2

- [ ] T008 [P] [US2] Install Orval as dev dependency: run `pnpm add -D orval` in `frontend/`
- [ ] T009 [P] [US2] Create `frontend/orval.config.ts`:
  ```ts
  import { defineConfig } from 'orval';

  export default defineConfig({
    nutribase: {
      input: {
        target: 'http://localhost:8000/docs/api.json',
      },
      output: {
        target: './src/api/generated',
        client: 'fetch',
        mode: 'tags-split',
        mock: false,
      },
    },
  });
  ```
- [ ] T010 [US2] Add `"api:generate": "orval"` to the `scripts` section in `frontend/package.json` (depends on T008, T009)
- [ ] T011 [US2] Verify code generation end-to-end: start backend (`php artisan serve` in `backend/`), run `pnpm run api:generate` in `frontend/`, confirm `frontend/src/api/generated/` contains at least one resource file and a `model/` directory

**Checkpoint**: `pnpm run api:generate` runs cleanly. Generated TypeScript files exist and have no compile errors (`pnpm run build` in `frontend/` — Next.js build performs TypeScript checking).

---

## Phase 5: User Story 3 — Export Static Spec File (Priority: P3)

**Goal**: Produce a valid static OpenAPI JSON file on demand for offline use or tooling that cannot consume a live URL.

**Independent Test**: Run `php artisan scramble:export` in `backend/` → file exists at `backend/storage/app/api.json` and is valid JSON with an `openapi` key.

### Implementation for User Story 3

- [ ] T012 [US3] Verify static export command: run `php artisan scramble:export` in `backend/`, confirm `backend/storage/app/api.json` is produced and contains valid OpenAPI 3.1 structure (`openapi`, `info`, `paths` keys present)
- [ ] T013 [US3] Add `backend/storage/app/api.json` to `backend/.gitignore` — exported file is a build artifact, not source

**Checkpoint**: Static export works. File is git-ignored.

---

## Final Phase: Polish & Cross-Cutting Concerns

- [ ] T014 [P] Run full backend test suite to confirm no regressions: `php artisan test` in `backend/`
- [ ] T015 [P] Update `backend/README.md` with docs endpoint URLs (`/docs/api`, `/docs/api.json`) and `pnpm run api:generate` workflow
- [ ] T016 Manually execute each step in `specs/004-scramble-api-docs/quickstart.md` and confirm all steps succeed as documented

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies — start immediately
- **Foundational (Phase 2)**: Depends on Phase 1 completion (T001, T002 must be done)
- **US1 (Phase 3)**: Depends on Phase 2 — write test first (T005), then implement (T006, T007)
- **US2 (Phase 4)**: Depends on US1 being complete (needs live spec endpoint working)
- **US3 (Phase 5)**: Depends on Phase 2 only — can run in parallel with US2
- **Polish (Final)**: Depends on all desired stories complete

### User Story Dependencies

- **US1 (P1)**: Depends on Foundational only
- **US2 (P2)**: Depends on US1 — Orval needs a running spec endpoint
- **US3 (P3)**: Depends on Foundational only — independent of US1/US2

### Within Each User Story

- Test task written FIRST, confirmed failing, before implementation
- Config before quality gates
- Quality gates (Pint → Larastan → Pest) in that order, every time

### Parallel Opportunities

- T008 and T009 can run in parallel (different files, no shared dependency)
- T012 and T013 can run in parallel
- T014 and T015 can run in parallel in Polish phase

---

## Parallel Example: User Story 2

```bash
# T008 and T009 have no dependency on each other — launch together:
Task: "pnpm add -D orval in frontend/"
Task: "Create frontend/orval.config.ts"

# T010 depends on both — run after both complete:
Task: "Add api:generate script to frontend/package.json"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Install Scramble
2. Complete Phase 2: Env gate + gitignore
3. Complete Phase 3: Config + test
4. **STOP and VALIDATE**: Browse `http://localhost:8000/docs/api` — all routes visible
5. Docs are live and useful for manual API exploration immediately

### Incremental Delivery

1. Phase 1 + 2 → Scramble installed and gated
2. Phase 3 (US1) → Live Swagger UI + spec endpoint; tests green ← **demo-able**
3. Phase 4 (US2) → Frontend code gen works from live URL ← **full pipeline complete**
4. Phase 5 (US3) → Static export for offline use ← **optional convenience**

---

## Notes

- [P] tasks = different files, no shared state
- Constitution Principle III: tests written before implementation, must fail first
- Constitution Principle IV: Pint → Larastan → Pest in that order before each checkpoint
- Docs endpoints served by Scramble — NOT wrapped in `ApiResponses` trait (Constitution VII N/A)
- `frontend/src/api/generated/` is git-ignored — never commit generated output
- `backend/storage/app/api.json` is git-ignored — never commit exported spec
