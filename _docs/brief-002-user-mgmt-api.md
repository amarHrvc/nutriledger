# Developer Briefing — 002 User Management API
**Generated**: 2026-03-20 | **Branch**: `002-user-mgmt-api` | **Issues**: 18 open AUTH tasks

---

```
DEVELOPER BRIEFING
══════════════════════════════════════════════════════
6 work packages across 18 issues
(Note: AUTH-15 index() and AUTH-16 show() appear already implemented —
 tests pass. Consider closing those bd issues.)
══════════════════════════════════════════════════════

──────────────────────────────────────────────────────
[1] Auth endpoint polish — DONE 2026-03-21
──────────────────────────────────────────────────────
Goal:    Login and /me responses return a consistent UserResource envelope
         instead of ad-hoc data shapes.

Status:  COMPLETE — AUTH-12 and AUTH-13 closed. AuthTest 10/10 passing.
         login() returns UserResource envelope with token expiry from config.
         me() loads patient relationship and wraps in UserResource.

──────────────────────────────────────────────────────
[2] Admin user creation flow
──────────────────────────────────────────────────────
Goal:    Admin can POST /api/users and get a 201 with a UserResource;
         all validation and authorization is covered by tests.

Design:  StoreUserRequest handles both authorization (isAdmin() check) and
         validation (name, email unique, password min 8, role enum).
         UserController::store() delegates to the request, creates the user
         via User::create() — password is auto-hashed by the model's hashed
         cast, not manually. UserPolicy::create() guards the action at the
         policy layer on top of the request's authorize(). Three layers total.

Tasks:   nutri-ledger-6uk (AUTH-17) — TDD: write failing creation tests (RED)
         nutri-ledger-51d (AUTH-18) — Implement store() (GREEN)

Own it:  AUTH-17 — the "password is hashed" test is non-obvious: you call
         Hash::check() on the stored DB record, not the response. Worth
         writing yourself to understand how the test verifies the model cast.
Delegate: AUTH-18 — the implementation is mechanical given StoreUserRequest
         already exists.

──────────────────────────────────────────────────────
[3] Public self-registration
──────────────────────────────────────────────────────
Goal:    Anyone can POST /api/register, get a 201 with a token and
         UserResource, and immediately be authenticated.

Design:  Different from admin user creation — no auth required, role defaults
         to pacijent when omitted, RegisterRequest validates password
         confirmation. Token is created immediately on registration with
         expiry from config('sanctum.expiration'). Response shape mirrors
         login: {token, user: UserResource}.

Tasks:   nutri-ledger-99s (AUTH-19) — TDD: write failing registration tests (RED)
         nutri-ledger-wck (AUTH-20) — Implement register() (GREEN)

Own it:  AUTH-20 — the role defaulting logic and token expiry wiring are
         small but easy to get subtly wrong.
Delegate: AUTH-19 — test cases are listed explicitly in the issue description.

──────────────────────────────────────────────────────
[4] Admin user update
──────────────────────────────────────────────────────
Goal:    Admin can PATCH /api/users/{id} with partial data; email uniqueness
         ignores the user's own record; password changes work correctly.

Design:  UpdateUserRequest mirrors StoreUserRequest but all fields are
         optional. Email uniqueness rule must use Rule::unique('users')
         ->ignore($this->user) — this is the non-obvious part. Password is
         updated via the same hashed cast. UserPolicy::update() prevents
         non-admins. Partial updates must leave untouched fields unchanged.

Tasks:   nutri-ledger-a94 (AUTH-21) — TDD: write failing update tests (RED)
         nutri-ledger-3k9 (AUTH-22) — Implement update() (GREEN)

Own it:  AUTH-21 — "partial update leaves other fields unchanged" and "email
         uniqueness ignores own record" are the interesting test cases.
         AUTH-22 — the Rule::unique()->ignore() in UpdateUserRequest if you
         want to reason through the validation logic yourself.
Delegate: The happy-path test cases and boilerplate.

──────────────────────────────────────────────────────
[5] User lifecycle — deactivate, restore, force-delete
──────────────────────────────────────────────────────
Goal:    Admin can soft-delete (deactivate), restore, and permanently delete
         users; deactivated users cannot log in; self-deactivation is blocked.

Design:  Three things to understand: (1) soft-deleted users are excluded from
         Laravel's auth query automatically — no extra code needed for login
         blocking, just a test to verify it. (2) restore() and forceDelete()
         do NOT use implicit route model binding because soft-deleted records
         are excluded from the default scope — they call
         User::withTrashed()->findOrFail($id) explicitly. (3) Self-deactivation
         is blocked at policy level (UserPolicy::delete() returns false when
         $user->id === $model->id), not in the controller.

Tasks:   nutri-ledger-cod (AUTH-23) — TDD: deactivation + restore tests (RED)
         nutri-ledger-2uo (AUTH-24) — Implement destroy() with security log
         nutri-ledger-e6m (AUTH-25) — Implement restore() with withTrashed()
         nutri-ledger-uq6 (AUTH-26) — TDD: force-delete tests (RED)
         nutri-ledger-e9q (AUTH-27) — Implement forceDelete() with security log

Own it:  AUTH-23 — "deactivated user cannot log in" is the most interesting
         test; you're verifying emergent behavior from SoftDeletes, not code
         you wrote. AUTH-25 — the withTrashed() explicit resolution vs implicit
         binding is a Laravel subtlety worth understanding hands-on.
Delegate: AUTH-24 and AUTH-27 — both follow the same pattern: authorize,
         act, log, return noContent().

──────────────────────────────────────────────────────
[6] Quality hardening
──────────────────────────────────────────────────────
Goal:    Policy logic, resource structure, and security logging are all
         independently verified and the full suite is clean.

Design:  Three independent concerns grouped because none block features —
         they validate what's already built. UserPolicyTest tests all 7 policy
         methods directly (not via HTTP). UserResource structure tests assert
         camelCase keys, no password field, pagination meta on collections,
         conditional patient relationship. Security logging adds Log::warning()
         calls to AuthController for failed login and logout — no PII, no
         behavioral change, just observability.

Tasks:   nutri-ledger-qe9 (AUTH-28) — UserPolicyTest (7 methods, direct)
         nutri-ledger-38i (AUTH-29) — UserResource structure tests
         nutri-ledger-3er (AUTH-30) — Security-event logging in AuthController

Own it:  AUTH-28 — directly instantiating a policy and calling methods is a
         pattern worth knowing; different from HTTP tests.
Delegate: AUTH-29 and AUTH-30 — both have explicit field lists in their
         issue descriptions.

══════════════════════════════════════════════════════
Execution order: [1] → [2] → [3] → [4] → [5] → [6]
Packages 2–4 can overlap once AUTH-12/13 are done.
Package 6 can start after [4] and before [5] finishes.
══════════════════════════════════════════════════════
```
