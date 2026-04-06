# Research: Auth & User Management API

**Branch**: `002-user-mgmt-api` | **Date**: 2026-03-19
**Phase**: 0 — Resolve unknowns before design

---

## Decision 1: Token Expiry Mechanism

**Decision**: Per-token absolute expiry set at creation time via `createToken()` third argument (`$expiresAt`). Duration driven by `config('sanctum.expiration')` in minutes. Current `config/sanctum.php` has `expiration => null` — must be set (e.g., `1440` = 24 hours).

**Rationale**: The current `AuthController::login()` already passes `Carbon::now()->addDays(1)` as the expiry argument to `createToken()`. This pattern is correct but hardcodes the duration. Moving duration to `config/sanctum.php` allows environment-specific tuning (shorter in production, longer in dev) with no code changes. Sanctum also respects `expires_at` on the token record for automatic rejection.

**Alternatives considered**:
- Global-only expiry via `sanctum.expiration` (simpler, but current code already uses per-token variant — keep consistency)
- Sliding idle-period expiry: deferred to future enhancement per spec clarification

**Implementation note**: Update `config/sanctum.php` `expiration` to `env('SANCTUM_EXPIRATION', 1440)`. Update `login()` and `register()` to derive expiry from config: `Carbon::now()->addMinutes(config('sanctum.expiration'))`.

---

## Decision 2: Rate Limiting on Login

**Decision**: Define a named rate limiter `'login'` using `RateLimiter::for()` in `AppServiceProvider::boot()`. Apply as `throttle:login` middleware on the `POST /api/login` route only.

**Rationale**: Laravel 12 named rate limiters are the idiomatic approach — they are descriptive, testable as a unit, and support per-user or per-IP keying. A threshold of 5 attempts per minute per IP (`by($request->ip())`) is a sensible default for a clinical API. Returns HTTP 429 automatically when throttled.

**Alternatives considered**:
- Inline `throttle:5,1`: works but is not named, harder to test, and not configurable without touching route definitions
- Per-user keying: not appropriate for pre-auth endpoints where there is no authenticated user

**Implementation note**:
```php
// AppServiceProvider::boot()
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip())->response(function () {
        return response()->json(['message' => 'Too many login attempts.', 'status' => 429], 429);
    });
});
```

---

## Decision 3: Route Model Binding for Soft-Deleted Records

**Decision**: For `restore` and `forceDelete` controller actions, resolve the User manually using `User::withTrashed()->findOrFail($id)` inside the controller, bypassing implicit route model binding. All other UserController actions use implicit binding (`User $user` parameter) — which correctly excludes soft-deleted records.

**Rationale**: Implicit route model binding applies the global `SoftDeletes` scope, making soft-deleted records invisible to `show()`, `update()`, and `destroy()`. This is correct behavior for those actions. `restore()` and `forceDelete()` intentionally target deactivated records, so they need explicit `withTrashed()` resolution. Manual resolution inside the controller keeps routes clean and makes the intent explicit at the call site.

**Alternatives considered**:
- `Route::bind('trashedUser', ...)` custom binding: clean but adds indirection; overkill for two endpoints
- `resolveRouteBinding()` override on the User model: affects all bindings globally — too broad

---

## Decision 4: ApiResponses Trait — Data Envelope

**Decision**: Update `ok()` and `created()` signatures to accept an optional `$data` parameter: `ok(string $message, mixed $data = null)`. When `$data` is provided, include `"data"` key in response. When `null`, omit (or include as `null`). Update `AuthController::login()` and `register()` to use the new signature instead of `->setData()`.

**Rationale**: FR-016 requires `{message, status, data}` envelope. The current trait returns only `{message, status}`. The current login uses `->setData()` which replaces the entire JSON body — losing `message` and `status`. The `ResponseContractTest` already tests `{message, status, data}` against `/api/ping` (manually constructed). All new UserResource responses must use the envelope. Updating the trait achieves consistency across the entire API surface.

**Breaking change**: `AuthTest` currently asserts `assertJsonStructure(['token', 'user'])` at root level. After this change, the structure becomes `{message, status, data: {token, user}}`. The test must be updated.

**Alternatives considered**:
- New separate trait methods (`okWithData()`): adds duplication, does not fix existing inconsistency
- Return `UserResource` directly (bypasses envelope): inconsistent with existing auth response pattern

---

## Decision 5: Deactivated User Login Blocking

**Decision**: No additional code required. Soft-deleted users are automatically excluded from `auth()->attempt()` queries because `SoftDeletes` applies a global scope (`deleted_at IS NULL`) to all User model queries, including the authentication provider's `retrieveByCredentials()`.

**Rationale**: Verified by inspecting the `User` model — it uses `SoftDeletes` trait. Laravel's `EloquentUserProvider::retrieveByCredentials()` queries the User model directly, which applies global scopes. A deactivated user will not be found, causing `attempt()` to return `false` → 401 Unauthorized. This satisfies FR-021 for free.

**Verification required**: The test `'deactivated user cannot log in'` must be included to explicitly confirm this behaviour in the test suite.

---

## Decision 6: Security Event Logging

**Decision**: Use `Log::warning()` calls at the controller action level for the four security-relevant events: failed login, account deactivation, token revocation (logout), and permanent deletion. Log entries include: event type (string), user ID (integer), request IP, and timestamp (auto-added by Laravel logger). No PII (name, email, password) in log entries.

**Rationale**: Minimal zero-dependency implementation. Laravel's `Log` facade writes to the configured channel (default: `stack` → `daily` log files). No package installation required. Satisfies FR-025 without introducing audit-log infrastructure that is explicitly deferred.

**Alternatives considered**:
- Model observer (`UserObserver`): cleaner separation of concerns, but requires registering an observer and adds a layer of indirection for what is currently a simple logging call
- Event/Listener pair: same as observer — appropriate when audit log becomes a full feature; defer to that future enhancement
- Dedicated audit package: out of scope per spec

**Log format example**:
```php
Log::warning('security.login_failed', ['ip' => $request->ip()]);
Log::warning('security.user_deactivated', ['by' => $request->user()->id, 'target' => $user->id, 'ip' => $request->ip()]);
Log::warning('security.token_revoked', ['user_id' => $request->user()->id, 'ip' => $request->ip()]);
Log::warning('security.user_force_deleted', ['by' => $request->user()->id, 'target' => $user->id, 'ip' => $request->ip()]);
```
