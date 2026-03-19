---
description: "Task list for Auth & User Management API"
---

# Tasks: Auth & User Management API

**Input**: Design documents from `/specs/002-user-mgmt-api/`
**Branch**: `002-user-mgmt-api`
**Prerequisites**: plan.md ✅ spec.md ✅ research.md ✅ data-model.md ✅ contracts/ ✅

**Tests**: Included — spec SC-007 requires 25+ tests; constitution Principle III mandates TDD (tests written before implementation).

**Organization**: Tasks grouped by user story to enable independent implementation and testing.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (US1–US7 from spec.md)
- All paths relative to `backend/`

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Cross-cutting changes that ALL user stories depend on. Complete before any story work begins.

- [ ] T001 Update `ApiResponses` trait in `app/Traits/ApiResponses.php` — add optional `mixed $data = null` parameter to `ok()` and `created()`; when `$data` is not null, include a `"data"` key in the response JSON alongside `"message"` and `"status"`. Keep existing signatures backwards-compatible (no-data calls still return `{message, status}`).
- [ ] T002 [P] Update `config/sanctum.php` — change `expiration` from `null` to `env('SANCTUM_EXPIRATION', 1440)` so token lifetime is configurable per environment (default 24 h). Update `login()` in `AuthController` to derive the expiry argument as `Carbon::now()->addMinutes(config('sanctum.expiration'))` instead of the hardcoded `addDays(1)`.
- [ ] T003 [P] Register a named rate limiter `'login'` in `app/Providers/AppServiceProvider.php` `boot()` using `RateLimiter::for('login', fn($r) => Limit::perMinute(5)->by($r->ip()))`. Import `Illuminate\Cache\RateLimiting\Limit` and `Illuminate\Support\Facades\RateLimiter`.
- [ ] T004 [P] Fix `AuthController::logout()` in `app/Http/Controllers/Api/AuthController.php` — replace `$this->success('Logged out', Response::HTTP_NO_CONTENT)` with `$this->noContent()`. HTTP 204 MUST have no response body; `success()` produces a JSON body regardless of status code.

**Verification**: `vendor/bin/pint --dirty && composer run analyse`

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core components required before any user story endpoint can compile, route, or be tested.

**⚠️ CRITICAL**: No user story work begins until this phase is complete.

- [ ] T005 Refactor `app/Policies/UserPolicy.php` to admin-only for all 7 policy methods. Uncomment and implement `viewAny()`, `view()`, `restore()`, `forceDelete()`. Fix `create()` and `update()` to remove `doktor` access. Fix `delete()` to remove `session()->flash()` and the doctor-can-delete-patient branch — keep only the self-delete guard and admin check. All methods return `bool`; no session calls. See data-model.md authorization matrix and plan.md Key Design Decision #4.
- [ ] T006 [P] Create `app/Http/Resources/Api/UserResource.php`. JSON:API shape: `type`, `id`, `attributes` (camelCase keys: `name`, `email`, `role`, `deletedAt`, `createdAt`, `updatedAt`), `relationships.patient` via `$this->whenLoaded('patient', fn() => ['data' => $this->patient ? ['type' => 'patient', 'id' => $this->patient->id] : null])`, `links.self` via `route('users.show', $this->id)`. Never expose `password`, `remember_token`, `email_verified_at`. Extend `JsonResource`.
- [ ] T007 [P] Create `app/Http/Requests/Api/RegisterRequest.php`. `authorize()` returns `true` (public endpoint). Rules: `name` required string max:255, `email` required email unique:users,email, `password` required string min:8 confirmed, `role` sometimes in:admin,doktor,pacijent.
- [ ] T008 [P] Create `app/Http/Requests/Api/StoreUserRequest.php`. `authorize()` returns `$this->user()->isAdmin()`. Rules: `name` required string max:255, `email` required email unique:users,email, `password` required string min:8, `role` required in:admin,doktor,pacijent.
- [ ] T009 [P] Create `app/Http/Requests/Api/UpdateUserRequest.php`. `authorize()` returns `$this->user()->isAdmin()`. Rules: `name` sometimes string max:255, `email` sometimes email with `Rule::unique('users','email')->ignore($this->route('user'))`, `password` sometimes string min:8, `role` sometimes in:admin,doktor,pacijent.
- [ ] T010 Update `routes/api.php` — remove the manual `GET /api/user/{id}` route; add `GET /api/user` → `AuthController@me` (named `user.me`) inside the `auth:sanctum` group; add `Route::apiResource('users', UserController::class)` and `Route::post('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore')` and `Route::delete('users/{user}/force', [UserController::class, 'forceDelete'])->name('users.forceDelete')` inside the `auth:sanctum` + `role:admin` group. Apply `throttle:login` middleware to `POST /api/login`.

**Verification**: `php artisan route:list --path=api | grep users` confirms all 9 user routes; `php artisan route:list --path=api | grep login` confirms throttle middleware.

**Checkpoint**: Foundation ready — user story phases can now proceed.

---

## Phase 3: User Story 1 — Admin Authenticates and Views Own Profile (Priority: P1) 🎯 MVP

**Goal**: Login, logout, and view-own-profile work end-to-end with the correct response envelope and UserResource shape.

**Independent Test**: `php artisan test --filter=AuthTest`

- [ ] T011 [US1] Update `tests/Feature/Api/AuthTest.php` — rewrite existing `login should return valid token` assertion to expect `{message, status, data: {token, user: {type, id, attributes}}}` envelope. Add new tests: `login with deactivated account returns 401`, `logout returns 204 with no body`, `GET /api/user returns UserResource shape`, `GET /api/user without token returns 401`. Run tests to confirm they **fail** before T012/T013 are implemented (TDD red step).
- [ ] T012 [US1] Update `AuthController::login()` in `app/Http/Controllers/Api/AuthController.php` — replace `->setData([...])` with `$this->ok('Authenticated', ['token' => ..., 'user' => new UserResource(auth()->user())])`. Remove the redundant `$request->validate($request->rules())` call (LoginRequest already runs validation). Import `App\Http\Resources\Api\UserResource`.
- [ ] T013 [US1] Implement `AuthController::me()` in `app/Http/Controllers/Api/AuthController.php` — load the patient relationship (`$user->load('patient')`), return `$this->ok('OK', new UserResource($user))`. Method signature: `public function me(Request $request): JsonResponse`.

**Verification**: `php artisan test --filter=AuthTest` — all auth tests green.

---

## Phase 4: User Story 2 — Admin Lists and Views All Users (Priority: P1)

**Goal**: `GET /api/users` and `GET /api/users/{id}` work with correct authorization and UserResource structure.

**Independent Test**: `php artisan test --filter=UserManagement` (index and show tests only)

- [ ] T014 [US2] Create `tests/Feature/Api/UserManagementTest.php` with index and show test cases. Tests to include: `admin can list all users` (200, data.data array), `list includes soft-deleted users` (deletedAt not null appears), `non-admin cannot list users` (403), `unauthenticated list request returns 401`, `admin can view specific user` (200, type/id/attributes structure), `viewing non-existent user returns 404`, `non-admin cannot view user` (403). Run to confirm **fail** before T015/T016.
- [ ] T015 [P] [US2] Implement `UserController::index()` in `app/Http/Controllers/Api/UserController.php` — `$this->authorize('viewAny', User::class)`, `User::query()->withTrashed()->paginate()`, return `$this->ok('OK', UserResource::collection($users))`. Remove the existing stub `create()` and `edit()` methods from the controller entirely.
- [ ] T016 [P] [US2] Implement `UserController::show()` in `app/Http/Controllers/Api/UserController.php` — replace the broken string-ID implementation with `public function show(User $user): JsonResponse`, `$this->authorize('view', $user)`, `$user->load('patient')`, return `$this->ok('OK', new UserResource($user))`.

**Verification**: `php artisan test --filter=UserManagement` — index/show tests green; `php artisan test --filter=AuthTest` still green.

---

## Phase 5: User Story 3 — Admin Creates a New User Account (Priority: P1)

**Goal**: `POST /api/users` creates an account, validates input, and returns the new user as UserResource.

**Independent Test**: `php artisan test --filter=UserManagement` (store tests)

- [ ] T017 [US3] Add store tests to `tests/Feature/Api/UserManagementTest.php`: `admin can create new user` (201, data has type/id/attributes), `create with duplicate email returns 422`, `create with missing required fields returns 422`, `create validates role values`, `non-admin cannot create user` (403), `created user password is hashed` (verify `Hash::check()` on the stored record). Run to confirm **fail**.
- [ ] T018 [US3] Implement `UserController::store()` in `app/Http/Controllers/Api/UserController.php` — `public function store(StoreUserRequest $request): JsonResponse`, `$this->authorize('create', User::class)`, `User::create($request->validated())` (password auto-hashed via model cast), return `$this->created('User created successfully', new UserResource($user))`.

**Verification**: `php artisan test --filter=UserManagement` — store tests green.

---

## Phase 6: User Story 7 — New User Self-Registration (Priority: P2)

**Goal**: `POST /api/register` creates an account and returns a token (public endpoint, no auth required).

**Independent Test**: `php artisan test --filter=AuthTest` (register tests)

- [ ] T019 [US7] Add registration tests to `tests/Feature/Api/AuthTest.php`: `register creates account and returns 201 with token`, `register response data has token and UserResource user`, `register defaults role to pacijent when omitted`, `register with duplicate email returns 422`, `register with password confirmation mismatch returns 422`, `register with missing fields returns 422`. Run to confirm **fail**.
- [ ] T020 [US7] Implement `AuthController::register()` in `app/Http/Controllers/Api/AuthController.php` — inject `RegisterRequest $request`, create user with `User::create([...$request->validated(), 'role' => $request->input('role', 'pacijent')])`, create token with `Carbon::now()->addMinutes(config('sanctum.expiration'))` expiry, return `$this->created('Registered successfully', ['token' => $token, 'user' => new UserResource($user)])`.

**Verification**: `php artisan test --filter=AuthTest` — all auth tests (login + logout + me + register) green.

---

## Phase 7: User Story 4 — Admin Updates a User Account (Priority: P2)

**Goal**: `PUT/PATCH /api/users/{id}` updates only provided fields and validates correctly.

**Independent Test**: `php artisan test --filter=UserManagement` (update tests)

- [ ] T021 [US4] Add update tests to `tests/Feature/Api/UserManagementTest.php`: `admin can update user name` (only name changes), `admin can change user role`, `update with duplicate email returns 422`, `email uniqueness check ignores own user record` (can submit own current email), `admin can update password` (old password no longer authenticates), `partial update leaves other fields unchanged`, `non-admin cannot update user` (403), `update non-existent user returns 404`. Run to confirm **fail**.
- [ ] T022 [US4] Implement `UserController::update()` in `app/Http/Controllers/Api/UserController.php` — `public function update(UpdateUserRequest $request, User $user): JsonResponse`, `$this->authorize('update', $user)`, `$user->update($request->validated())` (password hash handled by model cast), return `$this->ok('User updated successfully', new UserResource($user))`.

**Verification**: `php artisan test --filter=UserManagement` — all CRUD tests green.

---

## Phase 8: User Story 5 — Admin Deactivates and Restores User Accounts (Priority: P2)

**Goal**: Soft-delete and restore work; deactivated users cannot authenticate; admin cannot deactivate themselves.

**Independent Test**: `php artisan test --filter=UserManagement` (deactivate/restore tests)

- [ ] T023 [US5] Add deactivate/restore tests to `tests/Feature/Api/UserManagementTest.php`: `admin can deactivate user` (204), `deactivated user appears in list with non-null deletedAt`, `deactivated user cannot log in` (401 — verifies SoftDeletes scope blocks auth), `admin can restore user` (200, deletedAt null in response), `admin cannot deactivate themselves` (403), `non-admin cannot deactivate user` (403), `restore already-active user returns 200 no-op`. Run to confirm **fail**.
- [ ] T024 [P] [US5] Implement `UserController::destroy()` in `app/Http/Controllers/Api/UserController.php` — `public function destroy(User $user): JsonResponse`, `$this->authorize('delete', $user)`, `$user->delete()`, return `$this->noContent()`. Log: `Log::warning('security.user_deactivated', ['by' => auth()->id(), 'target' => $user->id, 'ip' => request()->ip()])`.
- [ ] T025 [US5] Implement `UserController::restore()` in `app/Http/Controllers/Api/UserController.php` — `public function restore(int $id): JsonResponse`, resolve via `User::withTrashed()->findOrFail($id)`, `$this->authorize('restore', $user)`, `$user->restore()`, return `$this->ok('User restored successfully', new UserResource($user->fresh()))`.

**Verification**: `php artisan test --filter=UserManagement` — deactivate/restore tests green; confirm `php artisan test --filter=AuthTest` still green.

---

## Phase 9: User Story 6 — Admin Permanently Deletes a User Account (Priority: P3)

**Goal**: `DELETE /api/users/{id}/force` permanently removes the record and releases the email.

**Independent Test**: `php artisan test --filter=UserManagement` (force-delete tests)

- [ ] T026 [US6] Add force-delete tests to `tests/Feature/Api/UserManagementTest.php`: `admin can force-delete an active user` (204), `admin can force-delete a deactivated user` (204), `force-deleted user returns 404 on subsequent fetch`, `force-deleted email can be re-registered` (register succeeds with same email), `non-admin cannot force-delete` (403). Run to confirm **fail**.
- [ ] T027 [US6] Implement `UserController::forceDelete()` in `app/Http/Controllers/Api/UserController.php` — `public function forceDelete(int $id): JsonResponse`, resolve via `User::withTrashed()->findOrFail($id)`, `$this->authorize('forceDelete', $user)`, `$user->forceDelete()`, return `$this->noContent()`. Log: `Log::warning('security.user_force_deleted', ['by' => auth()->id(), 'target' => $id, 'ip' => request()->ip()])`.

**Verification**: `php artisan test --filter=UserManagement` — all 30+ tests green.

---

## Phase 10: Polish & Cross-Cutting Concerns

**Purpose**: Hardening, observability, and full-suite validation.

- [ ] T028 [P] Create `tests/Feature/Api/UserPolicyTest.php` — unit-test each of the 7 UserPolicy methods directly: `viewAny` admin allowed, doctor/patient denied; `view` admin allowed, others denied; `create` admin allowed, others denied; `update` admin allowed, others denied; `delete` admin allowed, self-delete denied, others denied; `restore` admin allowed, others denied; `forceDelete` admin allowed, others denied.
- [ ] T029 [P] Add UserResource structure tests to `tests/Feature/Api/UserManagementTest.php` — `user resource has type user id and attributes keys`, `user attributes use camelCase keys`, `user resource never includes password field`, `user collection response includes pagination meta and links`, `user resource includes patient relationship when loaded`.
- [ ] T030 Add security-event logging to `AuthController` in `app/Http/Controllers/Api/AuthController.php` — `Log::warning('security.login_failed', ['ip' => $request->ip()])` in the failed-auth branch of `login()`. Add `Log::warning('security.token_revoked', ['user_id' => $request->user()->id, 'ip' => $request->ip()])` to `logout()`. No PII (no email, name, or password) in any log entry. Import `Illuminate\Support\Facades\Log`.
- [ ] T031 Run full test suite `php artisan test` — confirm 25+ tests pass, all three quality gates green. Then run `vendor/bin/pint --dirty` and `composer run analyse` for final sign-off.

**Verification**: `php artisan test` — full suite green with 0 failures; Larastan level 5 passes; Pint reports no changes needed.

---

## Dependencies & Execution Order

### Phase Dependencies

- **Phase 1 (Setup)**: No dependencies — start immediately. T002, T003, T004 are parallel.
- **Phase 2 (Foundational)**: Depends on Phase 1. T006–T009 are parallel. T010 depends on T005 (policy must exist before routes are meaningful to test).
- **Phase 3–9 (User Stories)**: All depend on Phase 2 completion. Stories proceed in priority order (P1 → P2 → P3) since team is solo. P1 stories (3, 4, 5) can technically run in parallel if staffed.
- **Phase 10 (Polish)**: Depends on all story phases complete. T028, T029, T030 are parallel.

### User Story Dependencies

| Story | Depends On | Notes |
|-------|-----------|-------|
| US1 (P1) | Phase 2 complete | AuthController already exists |
| US2 (P1) | Phase 2 complete | First UserController methods |
| US3 (P1) | T015–T016 (US2 establishes controller structure) | Adds store() to same controller |
| US7 (P2) | US1 complete (shares AuthTest.php) | Registration builds on login patterns |
| US4 (P2) | US3 complete (creates users to update) | Adds update() |
| US5 (P2) | US2 complete (needs index to verify deactivated visibility) | Adds destroy() + restore() |
| US6 (P3) | US5 complete (force-delete is stronger form of deactivate) | Adds forceDelete() |

### Within Each Phase

- Test task first → confirms RED → implement → confirms GREEN
- Models/resources before services/controllers
- Controllers before integration tests that call them

### Parallel Opportunities

- Phase 1: T002 + T003 + T004 in parallel (different files)
- Phase 2: T006 + T007 + T008 + T009 in parallel (different files); T005 + T006 in parallel
- Phase 8: T024 (destroy) + T025 (restore) after T023 tests written
- Phase 10: T028 + T029 + T030 in parallel

---

## Parallel Execution Examples

```bash
# Phase 1 parallel setup:
Task: "T002 — Update sanctum.php expiration"
Task: "T003 — Register login rate limiter in AppServiceProvider"
Task: "T004 — Fix AuthController::logout to use noContent()"

# Phase 2 parallel form requests:
Task: "T006 — Create UserResource"
Task: "T007 — Create RegisterRequest"
Task: "T008 — Create StoreUserRequest"
Task: "T009 — Create UpdateUserRequest"

# Phase 10 parallel polish:
Task: "T028 — Write UserPolicyTest"
Task: "T029 — Add UserResource structure tests"
Task: "T030 — Add security logging to AuthController"
```

---

## Implementation Strategy

### MVP (User Stories 1–3 only — all P1)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational
3. Complete Phase 3: US1 (auth flow working end-to-end)
4. Complete Phase 4: US2 (admin can list and view users)
5. Complete Phase 5: US3 (admin can create users)
6. **STOP AND VALIDATE**: `php artisan test --filter=AuthTest` + `php artisan test --filter=UserManagement` — P1 stories independently functional
7. Deploy/demo — auth + user CRUD working

### Full Delivery (Incremental)

- After MVP: Add US7 (registration), US4 (update), US5 (deactivate/restore), US6 (force-delete)
- Each story adds value independently; all previous tests remain green

---

## Notes

- [P] = different files, no shared state, safe to parallelize
- [Story] label maps task to user story for traceability
- TDD: every implementation task has a preceding test task — run tests first, confirm RED, then implement GREEN
- Constitution gates (Pint + Larastan + Pest) required before each story is marked complete
- `UserController` rewrite (T015) removes the `create()` and `edit()` HTML-form stubs — these have no place in an API resource controller
- Do NOT use `session()` in any policy or controller — APIs are stateless
- `restore()` and `forceDelete()` use manual `User::withTrashed()->findOrFail($id)` — NOT implicit route model binding (which excludes soft-deleted records)
