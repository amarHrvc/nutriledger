# Tasks: SE API Foundation

**Input**: Design documents from `specs/001-se-api-foundation/`
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/auth.md, quickstart.md
**Learning Mode**: Tasks follow `_docs/LEARNING_MODE.md` — concept-first, TDD, specification-not-code

**Organization**: Tasks grouped by user story; BE and FE tasks are always separate phases.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story ([US1]–[US4])
- **BE** / **FE** labels in phase headings — never mix in same task

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Install and configure the BE infrastructure before any feature work begins.

- [ ] T001 Run `php artisan install:api` and verify migration — `config/sanctum.php` + `database/migrations/*_create_personal_access_tokens_table.php`
- [ ] T002 [P] Configure CORS for localhost:5173 in `config/cors.php`
- [ ] T003 [P] Run `php artisan migrate` and verify `personal_access_tokens` table exists

---

### T001 — Install Sanctum via `php artisan install:api`

**Goal**: Install Laravel Sanctum's API support, which generates the token storage migration and publishes the Sanctum config.

**Key Concepts**:
- `install:api` is a Laravel 12 artisan command — it installs Sanctum, publishes `config/sanctum.php`, and creates the `personal_access_tokens` migration in one step
- Sanctum uses a database table (not a cache or JWT) to store hashed tokens — revocation is immediate because you delete the row
- `HasApiTokens` trait must be on the `User` model — check if it's already there before adding

**Steps**:

1. Run the command:
   ```bash
   php artisan install:api --no-interaction
   ```
2. Verify these files were created/modified:
   - `config/sanctum.php` — Sanctum configuration
   - A new migration file: `database/migrations/*_create_personal_access_tokens_table.php`
3. Open `app/Models/User.php` — confirm `HasApiTokens` is in the `use` statement. If missing, add it alongside the existing traits.
4. Check `bootstrap/app.php` — `install:api` may add `api` route file registration. Verify `routes/api.php` is referenced.

**Why this approach**: `install:api` is the official zero-config path. Doing it manually (copying migrations, editing config by hand) is error-prone and deviates from Laravel conventions.

**Verification**:
```bash
php artisan migrate --pretend | grep personal_access_tokens
# Should show CREATE TABLE personal_access_tokens
```

---

### T002 — Configure CORS in `config/cors.php`

**Goal**: Allow the React SPA (running on `localhost:5173`) to make requests to the Laravel API (`localhost:8000`) without browser CORS errors.

**Key Concepts**:
- Browsers enforce the Same-Origin Policy — a page on port 5173 cannot fetch from port 8000 unless the server explicitly permits it via CORS headers
- Laravel ships a `HandleCors` middleware registered globally — it reads `config/cors.php` at runtime
- `allowed_origins` must be an exact match (including scheme and port) — wildcards work but are less precise
- `supports_credentials` controls whether cookies/auth headers can be sent cross-origin — for Bearer token auth, this is `false`

**Steps**:

1. Open `config/cors.php` — read the current values before changing anything
2. Locate the `allowed_origins` key. Add `http://localhost:5173`:
   ```php
   'allowed_origins' => ['http://localhost:5173'],
   ```
3. Verify `allowed_methods` includes `['*']` or explicitly lists `GET, POST, PUT, PATCH, DELETE, OPTIONS`
4. Verify `allowed_headers` includes `Authorization` (needed for Bearer token)

**Why this approach**: Laravel's built-in CORS handling (via `fruitcake/laravel-cors` merged into core) handles preflight OPTIONS automatically. No additional packages needed.

**Verification**:
```bash
curl -i -X OPTIONS http://localhost:8000/api/ping \
  -H "Origin: http://localhost:5173" \
  -H "Access-Control-Request-Method: GET"
# Response must include: Access-Control-Allow-Origin: http://localhost:5173
```

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Shared infrastructure that all user stories depend on — `ApiResponses` trait, base controller, global exception handling, and the `routes/api.php` skeleton.

**⚠️ CRITICAL**: No user story implementation can begin until T004–T007 are complete.

- [ ] T004 Create `ApiResponses` trait in `app/Traits/ApiResponses.php`
- [ ] T005 [P] Create `ApiController` base in `app/Http/Controllers/Api/ApiController.php`
- [ ] T006 Configure global exception handling in `bootstrap/app.php`
- [ ] T007 [P] Create `routes/api.php` with public ping route + auth group + role group skeletons

**Checkpoint**: Foundation ready — all user story phases can now begin.

---

### T004 — Create `ApiResponses` Trait

**Goal**: Build the single source of truth for all API response shapes. Every controller will use this trait — direct `response()->json()` calls are forbidden after this task.

**Key Concepts**:
- A PHP trait is a reusable method group — controllers `use ApiResponses` to inherit the helper methods
- The envelope format is `{ message, status, data }` for success and `{ message, errors }` for validation failures (per `LARAVEL_API_RULES.md §2`)
- HTTP status codes go in both the HTTP response header AND in the `status` body field — the body field helps clients that parse JSON directly without inspecting headers

**Specification** (implement this, not a copy-paste):

**File**: `app/Traits/ApiResponses.php`
**Command**: `php artisan make:class Traits/ApiResponses --no-interaction` then convert to trait

**Methods to implement**:

| Method | Signature | HTTP Status | Body shape |
|---|---|---|---|
| `ok` | `ok(string $message, mixed $data = null)` | 200 | `{ message, status: 200, data }` |
| `created` | `created(string $message, mixed $data = null)` | 201 | `{ message, status: 201, data }` |
| `noContent` | `noContent()` | 204 | empty body |
| `error` | `error(string $message, int $status = 400, mixed $data = null)` | `$status` | `{ message, status, data }` |

**TDD — Step 1: Write the test first (RED)**

File: `tests/Feature/Api/ApiResponsesTest.php`

```php
// Example: test ok() returns correct structure
it('ok response has correct envelope', function () {
    // Use a test controller or call the trait directly via a closure route
    $response = $this->getJson('/api/ping');
    $response->assertOk()
             ->assertJsonStructure(['message', 'status', 'data'])
             ->assertJsonPath('status', 200);
});

// TODO: Write test — created() returns 201 with correct envelope
// TODO: Write test — error() returns the passed status code
// TODO: Write test — noContent() returns 204 with empty body
```

**TDD — Step 2: Implement (GREEN)**: Write the trait methods per the specification above.

**TDD — Step 3: Verify (REFACTOR)**:
```bash
php artisan test tests/Feature/Api/ApiResponsesTest.php
vendor/bin/pint --dirty
composer run analyse
```

**Why this approach**: Centralising response shapes means the client-side developer can write error handling once. If we ever change the envelope, one file changes — not every controller.

---

### T005 — Create `ApiController` Base

**Goal**: Create a minimal base controller that all API controllers extend. It uses the `ApiResponses` trait so subclasses inherit all response helpers.

**Key Concepts**:
- Inheritance chain: `ApiController extends Controller` → `AuthController extends ApiController`
- The base controller carries the trait so each child doesn't need to declare `use ApiResponses` itself
- Keep the base controller empty except for the trait — no logic, no methods

**Specification**:

**File**: `app/Http/Controllers/Api/ApiController.php`
**Command**: `php artisan make:controller Api/ApiController --no-interaction`

Modify the generated class:
- Extend `App\Http\Controllers\Controller`
- Add `use App\Traits\ApiResponses;`
- No constructor, no methods — the class body is just the trait declaration

**Verification**:
```bash
php artisan route:list --path=api
# No errors = class is loadable
vendor/bin/pint --dirty
```

---

### T006 — Global Exception Handling in `bootstrap/app.php`

**Goal**: Ensure that Laravel's built-in exception handling returns JSON (not HTML redirects) for two critical cases: unauthenticated requests and validation failures.

**Key Concepts**:
- Laravel's `AuthenticationException` is thrown when `auth:sanctum` middleware rejects a request — by default it redirects to `/login` (a Fortify web route). We must intercept it for API routes and return 401 JSON instead
- Laravel's `ValidationException` is thrown when a FormRequest fails — we control the shape here to match our envelope
- `bootstrap/app.php` `withExceptions()` closure is the Laravel 12 way to register exception handlers (no `app/Exceptions/Handler.php` in Laravel 12's streamlined structure)
- The intercept must be selective — only API routes get JSON; web routes keep their redirect behaviour

**Key Concepts — detecting API requests**:
- Check `$request->expectsJson()` or `$request->is('api/*')` to distinguish API from web requests

**Specification** — add inside the `withExceptions()` closure:

1. **AuthenticationException handler**:
   - Condition: `$request->is('api/*')` or `$request->expectsJson()`
   - Return: `response()->json(['message' => 'Unauthenticated.', 'status' => 401, 'data' => null], 401)`

2. **ValidationException handler**:
   - Return: `response()->json(['message' => $e->getMessage(), 'errors' => $e->errors()], 422)`

**TDD — Write test first (RED)**:

File: `tests/Feature/Api/AuthTest.php` (add to the test file you write in T009)

```php
// This test verifies the exception handler (no controller needed)
it('returns 401 json for unauthenticated api request', function () {
    $response = $this->getJson('/api/user');
    $response->assertUnauthorized()
             ->assertJsonStructure(['message', 'status', 'data'])
             ->assertHeader('Content-Type', 'application/json');
    // Verify it is NOT an HTML redirect
    expect($response->headers->get('Content-Type'))->toContain('application/json');
});
```

**Why this approach**: Without this handler, Fortify's redirect fires for API routes — the React SPA receives a 302 HTML response it cannot parse, which is silent and confusing to debug.

**Verification**:
```bash
php artisan test --filter="unauthenticated api request"
```

---

### T007 — Create `routes/api.php`

**Goal**: Define the full route structure: public ping, authenticated auth routes, and empty role group skeletons. Skeleton groups are intentionally empty — they will be populated in 002-patients and 003-visits.

**Key Concepts**:
- `routes/api.php` is auto-loaded by Laravel 12 when registered in `bootstrap/app.php` via `->withRouting(api: __DIR__.'/../routes/api.php')`
- `auth:sanctum` middleware validates the Bearer token from the `Authorization` header — requests without a valid token hit the `AuthenticationException` handler from T006
- `RoleMiddleware` is already registered as `role` alias in `bootstrap/app.php` — you call it as `middleware('role:admin')`
- The ping route MUST NOT have any middleware — it is the CORS smoke test endpoint

**Specification** — structure for `routes/api.php`:

```
Public routes (no middleware):
  GET  /api/ping  → closure returning ok envelope with { status: "ok" }

Auth routes (auth:sanctum middleware):
  POST /api/login   → AuthController@login
  POST /api/logout  → AuthController@logout
  GET  /api/user    → AuthController@me

Role group skeletons (auth:sanctum + RoleMiddleware):
  middleware('role:admin') group   → empty, comment: "User management — 002-users BE"
  middleware('role:admin,doktor') group → empty, comment: "Patients + Visits — 002-patients, 003-visits BE"
```

**Note on `/api/login`**: Login does NOT need `auth:sanctum` — the user is not authenticated yet.

**Verification**:
```bash
php artisan route:list --path=api
# Expected: ping, login, logout, user all listed
```

---

## Phase 3 (BE): User Story 1 — Token Authentication (Priority: P1) 🎯 MVP

**Goal**: Implement `POST /api/login`, `POST /api/logout`, `GET /api/user` with Pest tests covering the full token lifecycle.

**Independent Test**: Run `php artisan test tests/Feature/Api/AuthTest.php` — must pass with no other features implemented.

### Tests for US1 — Write First (RED Phase)

- [ ] T008 [US1] Write Pest tests covering login/logout/me lifecycle in `tests/Feature/Api/AuthTest.php`

### Implementation for US1

- [ ] T009 [P] [US1] Create `LoginRequest` FormRequest in `app/Http/Requests/Api/LoginRequest.php`
- [ ] T010 [US1] Create `AuthController` with `login`, `logout`, `me` methods in `app/Http/Controllers/Api/AuthController.php`

**Checkpoint**: `php artisan test tests/Feature/Api/AuthTest.php` — all tests green.

---

### T008 — Write Pest Tests for Token Authentication (RED)

**Goal**: Write the complete test suite for US1 BEFORE implementing any controller code. Tests must fail (RED) until T010 is complete.

**Key Concepts**:
- `RefreshDatabase` — each test runs on a fresh SQLite in-memory database; factories create users on the fly
- `actingAs($user)` in HTTP tests sets the authenticated user — but for API token tests, you issue a real token via `$user->createToken('test')->plainTextToken` and pass it as a Bearer header
- `assertJsonStructure` validates keys exist; `assertJsonPath` validates values

**TDD — Step 1: Write tests (RED)**

File: `tests/Feature/Api/AuthTest.php`
Command: `php artisan make:test Api/AuthTest --pest --no-interaction`

```php
uses(RefreshDatabase::class);

// ✅ Happy path — valid credentials
it('returns token on valid login', function () {
    $user = User::factory()->create(['password' => bcrypt('secret123')]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'secret123',
    ]);

    $response->assertOk()
             ->assertJsonStructure(['data' => ['token', 'user']])
             ->assertJsonPath('data.user.id', $user->id);
});

// ✅ Happy path — logout revokes token
it('logout revokes the token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;

    $this->withToken($token)->postJson('/api/logout')->assertNoContent();

    // Token must be rejected after logout
    $this->withToken($token)->getJson('/api/user')->assertUnauthorized();
});

// TODO: Write test — invalid credentials return 401 (not 422)
// TODO: Write test — missing email field returns 422 with field-level errors
// TODO: Write test — missing password field returns 422 with field-level errors
// TODO: Write test — GET /api/user returns id, name, email, role
// TODO: Write test — GET /api/user without token returns 401 JSON (not redirect)
```

**Why this structure**:
- Two examples show the two most critical scenarios: issue token, revoke token
- TODO comments guide you through the remaining acceptance criteria from spec.md
- The `withToken()` helper (Pest + Laravel) sets `Authorization: Bearer {token}` automatically

**Step 2: Run tests — confirm RED**:
```bash
php artisan test tests/Feature/Api/AuthTest.php
# Expected: failures (AuthController does not exist yet)
```

---

### T009 — Create `LoginRequest` FormRequest

**Goal**: Validate the login payload before it reaches the controller. `email` and `password` are required; email must be a valid format.

**Key Concepts**:
- FormRequest `authorize()` method — for public endpoints (no domain data involved), it should return `true`
- FormRequest `rules()` — where validation rules live; keep them minimal: required + email format
- The `ValidationException` thrown on failure hits our T006 handler → 422 with `{ message, errors }`

**Specification**:

**File**: `app/Http/Requests/Api/LoginRequest.php`
**Command**: `php artisan make:request Api/LoginRequest --no-interaction`

| Method | Return | Logic |
|---|---|---|
| `authorize()` | `bool` | Always `true` — public endpoint, no domain data |
| `rules()` | `array` | `email`: `required\|email`, `password`: `required\|string` |

**Verification**:
```bash
php artisan test tests/Feature/Api/AuthTest.php --filter="missing email"
# This should now pass (422 returned) once T010 uses LoginRequest
vendor/bin/pint --dirty
composer run analyse
```

---

### T010 — Create `AuthController`

**Goal**: Implement the three auth actions: `login` (issue token), `logout` (revoke token), `me` (return user identity).

**Key Concepts**:
- `Auth::attempt(['email' => ..., 'password' => ...])` — validates credentials against the `users` table; returns `false` if they don't match
- `$request->user()->createToken('auth_token', ['*'], Carbon::now()->addMonth())` — third argument sets expiry (LARAVEL_API_RULES §5: always set expiry)
- `$request->user()->currentAccessToken()->delete()` — deletes the specific token used in this request; does NOT affect other tokens the user may have
- `logout` returns 204 No Content — there is nothing to return after revocation

**Specification**:

**File**: `app/Http/Controllers/Api/AuthController.php`
**Command**: `php artisan make:controller Api/AuthController --no-interaction`

Modify: extend `ApiController` (not the base Laravel `Controller` directly).

| Method | Signature | Logic |
|---|---|---|
| `login` | `login(LoginRequest $request): JsonResponse` | `Auth::attempt()` → if fails, return `$this->error('Invalid credentials.', 401)` → if succeeds, create token with 1-month expiry → return `$this->ok('Login successful.', ['token' => ..., 'user' => [...]])` |
| `logout` | `logout(Request $request): JsonResponse` | `$request->user()->currentAccessToken()->delete()` → return `$this->noContent()` |
| `me` | `me(Request $request): JsonResponse` | return `$this->ok('OK', ['id' => ..., 'name' => ..., 'email' => ..., 'role' => ...])` |

**`me` — return only these fields**: `id`, `name`, `email`, `role` (FR-004 — no other User fields exposed)

**TDD — Step 3: Run tests (GREEN)**:
```bash
php artisan test tests/Feature/Api/AuthTest.php
# All written tests must now pass
```

**Quality gates**:
```bash
vendor/bin/pint --dirty
composer run analyse
php artisan test tests/Feature/Api/AuthTest.php
```

---

## Phase 4 (BE): User Story 2 — Role-Gated Access Control (Priority: P2)

**Goal**: Verify that `RoleMiddleware` returns JSON 403 (not redirect/HTML) for wrong-role requests, and wire the role group skeletons in `routes/api.php`.

**Independent Test**: Run `php artisan test tests/Feature/Api/RoleAccessTest.php` with a seeded admin, doktor, and pacijent user.

### Tests for US2 — Write First (RED Phase)

- [ ] T011 [US2] Write role access Pest tests in `tests/Feature/Api/RoleAccessTest.php`

### Implementation for US2

- [ ] T012 [US2] Verify `RoleMiddleware` returns JSON 403 and register role routes in `routes/api.php`

**Checkpoint**: `php artisan test tests/Feature/Api/RoleAccessTest.php` — all tests green.

---

### T011 — Write Pest Tests for Role Access (RED)

**Goal**: Confirm that role enforcement produces the exact JSON shape the spec requires — no HTML, no redirects.

**Key Concepts**:
- `RoleMiddleware` is already implemented — your job is to verify its behaviour via HTTP tests, not rewrite it
- A test route is needed inside the role group — add a temporary stub route in `routes/api.php` for testing purposes only (or use a route that will exist in 002-patients)
- `assertForbidden()` checks HTTP 403; `assertJsonStructure` confirms JSON shape

**TDD — Step 1: Write tests (RED)**

File: `tests/Feature/Api/RoleAccessTest.php`
Command: `php artisan make:test Api/RoleAccessTest --pest --no-interaction`

```php
uses(RefreshDatabase::class);

// ✅ Correct role — admin accessing admin-only route
it('admin can access admin-only route', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('test')->plainTextToken;

    // Add a test stub route inside role:admin group in routes/api.php
    $this->withToken($token)
         ->getJson('/api/test/admin-only')
         ->assertOk();
});

// ✅ Wrong role — pacijent accessing admin route gets 403 JSON
it('pacijent gets 403 json on admin-only route', function () {
    $pacijent = User::factory()->create(['role' => 'pacijent']);
    $token = $pacijent->createToken('test')->plainTextToken;

    $this->withToken($token)
         ->getJson('/api/test/admin-only')
         ->assertForbidden()
         ->assertJsonStructure(['message', 'status', 'data'])
         ->assertHeader('Content-Type', 'application/json');
});

// TODO: Write test — unauthenticated request to role-protected route returns 401 (not 403)
// TODO: Write test — doktor can access admin,doktor route
// TODO: Write test — pacijent cannot access admin,doktor route (403)
// TODO: Write test — admin can access admin,doktor route
```

**Why the last TODO is important**: The spec explicitly distinguishes 401 (unauthenticated) from 403 (wrong role). Tests must prove both cases.

---

### T012 — Verify `RoleMiddleware` JSON Response + Wire Routes

**Goal**: Confirm `RoleMiddleware` returns the `ApiResponses` envelope on 403 (not a plain string). Add test stub routes and verify the role group skeletons are wired correctly.

**Key Concepts**:
- Open `app/Http/Middleware/RoleMiddleware.php` and read the current 403 response shape — does it match `{ message, status, data }` from `ApiResponses`?
- If it returns `response()->json(['message' => 'Forbidden'], 403)` — that's close but missing `status` and `data` keys — update to use the full envelope
- Do NOT rewrite the middleware logic — only adjust the response shape to match the `ApiResponses` format

**Steps**:

1. Read `app/Http/Middleware/RoleMiddleware.php` before making any changes
2. Compare its 403 response shape to the `error()` method in your `ApiResponses` trait
3. If shapes differ, update the middleware's 403 response to match
4. In `routes/api.php`, add a temporary test stub inside the `role:admin` group:
   ```php
   Route::get('/test/admin-only', fn () => response()->json(['message' => 'ok', 'status' => 200, 'data' => null]));
   ```
   *(Remove or keep commented after testing — real admin routes come in 002-patients)*

**Verification**:
```bash
php artisan test tests/Feature/Api/RoleAccessTest.php
vendor/bin/pint --dirty
composer run analyse
```

---

## Phase 5 (BE): User Story 3 — Consistent Response Contracts (Priority: P3)

**Goal**: Validate that every response type (success, 422, 401, 403, 404) uses the `{ message, status, data }` / `{ message, errors }` envelope with no exceptions.

**Independent Test**: Trigger each response type via HTTP test and assert structure.

- [ ] T013 [US3] Write response contract tests covering all envelope shapes in `tests/Feature/Api/ResponseContractTest.php`

**Checkpoint**: `php artisan test tests/Feature/Api/ResponseContractTest.php` — all tests green.

---

### T013 — Write Response Contract Tests

**Goal**: One test per response type, asserting the exact envelope shape. These are integration tests — they hit real routes.

**Key Concepts**:
- `assertJsonStructure` checks that keys exist, not their values
- `assertJsonPath('status', 200)` checks a specific value at a JSON path
- A 404 test requires requesting a route that does not exist — add a 404 handler in `bootstrap/app.php` if needed (check if Laravel's default 404 returns JSON for API routes)

**TDD — Write tests (RED → GREEN)**

File: `tests/Feature/Api/ResponseContractTest.php`
Command: `php artisan make:test Api/ResponseContractTest --pest --no-interaction`

```php
uses(RefreshDatabase::class);

// ✅ Success envelope
it('successful response has message status data keys', function () {
    $response = $this->getJson('/api/ping');
    $response->assertOk()
             ->assertJsonStructure(['message', 'status', 'data']);
});

// ✅ Validation error envelope
it('validation error response has message errors keys', function () {
    $response = $this->postJson('/api/login', []); // missing email + password
    $response->assertUnprocessable()
             ->assertJsonStructure(['message', 'errors'])
             ->assertJsonPath('errors.email.0', fn ($v) => is_string($v));
});

// TODO: Write test — 401 response has message, status, data keys
// TODO: Write test — 403 response has message, status, data keys
// TODO: Write test — 404 response has message, status, data keys (add 404 handler in bootstrap/app.php if needed)
```

**Verification**:
```bash
php artisan test tests/Feature/Api/ResponseContractTest.php
vendor/bin/pint --dirty
composer run analyse
```

---

## Phase 6 (FE): User Story 4 — Cross-Origin SPA Connectivity (Priority: P4)

**Goal**: Scaffold a minimal React SPA in `/frontend`, configure Axios to point to `VITE_API_URL`, and build a smoke-test component that calls `GET /api/ping` from a real browser to confirm CORS works.

**Independent Test**: Open `http://localhost:5173` in a browser with DevTools open — no CORS errors in console, ping response logged.

- [ ] T014 [FE] Scaffold Vite + React project in `/frontend` directory
- [ ] T015 [P] [FE] [US4] Create Axios client instance in `frontend/src/api/client.js`
- [ ] T016 [P] [FE] [US4] Create smoke-test `App.jsx` that calls `GET /api/ping` on mount
- [ ] T017 [FE] [US4] Configure `.env` and `.env.example` with `VITE_API_URL=http://localhost:8000`

**Checkpoint**: `npm run dev` starts without errors; browser console shows ping response at `http://localhost:5173`.

---

### T014 — Scaffold Vite + React in `/frontend`

**Goal**: Create the React SPA project structure using the official Vite scaffolding tool.

**Key Concepts**:
- `npm create vite@latest` generates the minimum project structure — `index.html`, `src/App.jsx`, `vite.config.js`, `package.json`
- `--template react` picks JavaScript React (not TypeScript) — constitution v2.0.1 SE track uses JS
- The scaffold lives at `/frontend` (repo root, alongside Laravel root) — not inside `resources/`

**Steps**:

1. From the repo root, run:
   ```bash
   npm create vite@latest frontend -- --template react
   cd frontend
   npm install
   npm install axios
   ```
2. Verify the project starts:
   ```bash
   npm run dev
   # Should print: VITE vX.X.X ready on http://localhost:5173
   ```
3. Open `http://localhost:5173` in a browser — the default Vite+React page should appear.

**Why `npm create vite@latest` not CRA**: Create React App is deprecated. Vite is the official recommendation.

---

### T015 — Create Axios Client in `frontend/src/api/client.js`

**Goal**: Build a pre-configured Axios instance that all API calls will use. Includes `baseURL` from environment and a Bearer token interceptor stub.

**Key Concepts**:
- `axios.create({ baseURL })` returns a new Axios instance — all calls on this instance automatically prepend the base URL
- Request interceptor — runs before every request; reads `localStorage.getItem('token')` and adds `Authorization: Bearer {token}` header if a token exists
- This is a **stub** — there is no auth state yet (that belongs to 002-users FE). The interceptor is wired but will add nothing until a token is stored

**Specification**:

**File**: `frontend/src/api/client.js`

Implement an Axios instance with:
1. `baseURL`: `import.meta.env.VITE_API_URL` (Vite's way of reading `.env` variables)
2. `headers`: `{ 'Content-Type': 'application/json', 'Accept': 'application/json' }`
3. Request interceptor: read `localStorage.getItem('token')`; if it exists, set `config.headers.Authorization = 'Bearer ' + token`; always return `config`

Export the instance as the default export.

**Why the interceptor stub now**: 002-users FE will store the token in `localStorage` after login — this interceptor picks it up automatically without needing to modify `client.js` again.

---

### T016 — Create Smoke-Test `App.jsx`

**Goal**: Replace the default Vite placeholder with a minimal component that calls `GET /api/ping` on mount and logs the response. This confirms CORS is working from a real browser.

**Key Concepts**:
- `useEffect(() => { ... }, [])` — runs once after the component mounts; the right place for a one-time API call
- `console.log` is intentional here — this is a smoke test, not a UI feature
- If the CORS request fails, the browser console shows a CORS error — not a network error. Watch for: `Access to XMLHttpRequest at 'http://localhost:8000/api/ping' from origin 'http://localhost:5173' has been blocked by CORS policy`

**Specification**:

**File**: `frontend/src/App.jsx`

Implement a functional component that:
1. Imports the Axios `client` from `./api/client`
2. In `useEffect` (on mount): calls `client.get('/api/ping')` — logs `response.data` to the console on success, logs the error on failure (do NOT crash the page)
3. Renders a minimal `<div>` — e.g. "NutriLedger API" heading. No styling required.

**Verification**:
1. Ensure `php artisan serve` is running on port 8000
2. Ensure `npm run dev` is running on port 5173
3. Open `http://localhost:5173` in browser
4. Open DevTools → Console
5. Expected: `{ message: "ok", status: 200, data: { status: "ok" } }`
6. Expected: No red CORS error messages in Console or Network tab

---

### T017 — Configure `.env` and `.env.example`

**Goal**: Set up environment variable configuration so any developer can clone the repo and point the SPA to the correct API URL without hardcoding it.

**Key Concepts**:
- Vite reads `.env` at build time and injects variables prefixed with `VITE_` into the bundle
- `.env` is gitignored — `.env.example` is committed and serves as the template
- `import.meta.env.VITE_API_URL` is how components/services read it at runtime

**Steps**:

1. In `/frontend`, create `.env.example`:
   ```
   VITE_API_URL=http://localhost:8000
   ```
2. Create `.env` with the same content (for local dev):
   ```
   VITE_API_URL=http://localhost:8000
   ```
3. Confirm `.env` is in `/frontend/.gitignore` (Vite scaffolding usually adds it)

**Verification**:
```bash
cd frontend && npm run dev
# Verify: import.meta.env.VITE_API_URL is defined (add a temporary console.log in App.jsx if unsure)
```

---

## Phase 7: Polish & Cross-Cutting Concerns

**Purpose**: Quality gates, cleanup, and end-to-end verification before the branch is ready for review.

- [ ] T018 [P] Run all quality gates: `vendor/bin/pint --dirty` + `composer run analyse` + `php artisan test`
- [ ] T019 [P] Verify quickstart.md steps work end-to-end (curl ping, curl login, CORS manual browser test)
- [ ] T020 Remove any test stub routes from `routes/api.php` added during T012 testing
- [ ] T021 [P] [FE] Run `npm run build` in `/frontend` — confirm no build errors

---

### T018 — Run All Quality Gates

**Goal**: Every changed file must pass all three gates before the branch is merge-ready.

**Steps** (run in this order):

```bash
# 1. Code style — auto-fixes formatting
vendor/bin/pint --dirty

# 2. Static analysis — Larastan level 5
composer run analyse

# 3. Test suite — all Feature/Api tests
php artisan test tests/Feature/Api/

# 4. Full suite — confirm nothing else broke
php artisan test
```

**Expected output**: No errors. If Larastan reports a type error, fix it — do not suppress with `@phpstan-ignore`.

---

## Dependencies & Execution Order

### Phase Dependencies

- **Phase 1 (Setup)**: No dependencies — start immediately
- **Phase 2 (Foundational)**: Depends on Phase 1 — **BLOCKS** all user story phases
- **Phase 3 (US1 Auth)**: Depends on Phase 2
- **Phase 4 (US2 Roles)**: Depends on Phase 2; benefits from Phase 3 (auth tests reuse token setup pattern)
- **Phase 5 (US3 Contracts)**: Depends on Phase 2; most tasks can run alongside Phase 3
- **Phase 6 (US4 CORS/FE)**: Independent of BE phases — can start after Phase 1 (BE CORS config in T002)
- **Phase 7 (Polish)**: Depends on all previous phases

### Within Each User Story

```
Write tests (RED) → Implement (GREEN) → Quality gates (REFACTOR)
```

Models before services → Services before controllers → Controllers before route wiring

### Parallel Opportunities

- **Phase 1**: T001, T002, T003 can all run in parallel
- **Phase 2**: T004, T005 can run in parallel (different files); T006 and T007 depend on T004 output
- **Phase 3 vs Phase 6**: US1 BE work and US4 FE scaffold are fully independent — can run simultaneously
- **Phase 4 vs Phase 5**: US2 and US3 can run in parallel once Phase 2 is complete

---

## Parallel Example: Phase 6 (FE) alongside Phase 3 (BE)

```
Developer A (BE): T008 → T009 → T010
Developer B (FE): T014 → T015 → T016 → T017
```

Both tracks start simultaneously. FE developer calls ping endpoint once T007 is merged; does not depend on auth being implemented.

---

## Implementation Strategy

### MVP (User Story 1 Only)

1. Phase 1: Setup — install Sanctum, configure CORS, migrate
2. Phase 2: Foundational — trait, base controller, exception handler, routes skeleton
3. Phase 3: US1 — write AuthTest.php, implement LoginRequest + AuthController
4. **STOP**: `php artisan test tests/Feature/Api/AuthTest.php` — all green
5. Deliver: token auth lifecycle working

### Full Feature Delivery

1. MVP above
2. Phase 4: US2 Role access tests and verification
3. Phase 5: US3 Response contract tests
4. Phase 6: US4 React scaffold + CORS browser verify
5. Phase 7: Polish — all gates green

---

## Notes

- [P] tasks = different files, no competing edits — safe to parallelize
- Write tests before implementation — tests failing first proves they actually test something
- Each phase checkpoint is a natural commit point
- Do not add routes to role group skeletons beyond the temporary test stub — real routes come in 002/003
- FE `.env` is gitignored — never commit it; `.env.example` is the committed template
