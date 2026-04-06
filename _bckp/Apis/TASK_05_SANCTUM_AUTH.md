# Sanctum Token Authentication — Task 04

> **Source:** Laravel API Masterclass — Episode 05
> **Created:** 2026-03-10
> **Run Time:** 9m 10s
> **Status:** Study Task

---

## Overview

Session-based auth (cookies, `$_SESSION`) works for browser apps. APIs are stateless — there is no browser session to maintain. The client could be a mobile app, a React SPA, or another server. The standard solution is **token-based authentication**:

1. Client sends credentials (email + password) once → receives a **token**
2. Client stores the token
3. Every subsequent request includes the token in the `Authorization` header
4. Server validates the token on each request

**Laravel Sanctum** handles all of this. It is already installed when you create a Laravel project.

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| Sanctum | Laravel's first-party token auth package |
| Personal access token | A hashed string tied to a user, stored in `personal_access_tokens` table |
| Bearer token | HTTP `Authorization: Bearer <token>` header |
| `createToken($name)->plainTextToken` | Returns the unhashed token (only available at creation time) |
| `Auth::attempt($credentials)` | Validates email/password against the DB, returns bool |
| `auth:sanctum` middleware | Guards a route — requires a valid Bearer token |
| Form Request | Dedicated class for validating a single endpoint's input |

---

## Task 1 — Create the LoginUserRequest

### Goal
Build a Form Request that validates the login payload before it reaches the controller.

### Key Concept — `authorize()` must return `true`

Every Form Request has an `authorize()` method. If it returns `false`, Laravel rejects the request with a 403 **before** validation even runs. For login, there is no logged-in user to check permissions for — everyone should be allowed to attempt login. Always return `true` here.

### Instructions

```bash
php artisan make:request Api/LoginUserRequest --no-interaction
```

This creates `app/Http/Requests/Api/LoginUserRequest.php`.

Open it and fill in:

```php
<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }
}
```

**Rules explained:**

| Field | Rules | Reason |
|---|---|---|
| `email` | `required`, `string`, `email` | Must exist, must be a string, must pass email format check |
| `password` | `required`, `string`, `min:8` | Must exist, must be a string, minimum 8 chars |

> `min:8` on login does not enforce a password policy — it just prevents obviously wrong attempts (e.g., submitting a 3-character string as a password). The actual password check happens via `Auth::attempt()`.

---

## Task 2 — Move AuthController into the Api Folder

### Goal
Relocate `AuthController` from `app/Http/Controllers/` to `app/Http/Controllers/Api/` and update its namespace.

### Why move it?

API auth (tokens) and web auth (sessions/Fortify) are separate concerns. If your app has both a browser interface and an API, they use different authentication mechanisms. Keeping them in the same controller creates confusion. The `Api/` folder signals clearly that this controller belongs to the API.

### Instructions

**Move the file:**

```
app/Http/Controllers/AuthController.php
→
app/Http/Controllers/Api/AuthController.php
```

**Update the namespace:**

```php
// Before
namespace App\Http\Controllers;

// After
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
```

**Update `routes/api.php` to use the new namespace:**

```php
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);
```

---

## Task 3 — Add `error()` to the ApiResponses Trait

### Goal
Add a method for returning error responses (4xx/5xx) from the `ApiResponses` trait.

### Instructions

Open `app/Traits/ApiResponses.php` and add:

```php
protected function error(string $message, int $statusCode): JsonResponse
{
    return response()->json([
        'message' => $message,
        'status'  => $statusCode,
    ], $statusCode);
}
```

Unlike `ok()` and `created()`, `error()` does not have a default status code — the caller must be explicit. A 401 is completely different from a 500; don't let a default mask the wrong status.

---

## Task 4 — Update `success()` to Accept Optional Data

### Goal
Allow successful responses to carry a `data` payload alongside the message.

### Key Concept — Why add `data` to the response?

The login endpoint must return the token. The message is `"authenticated"`. The token is not a message — it is structured data. Putting it in `message` would be wrong. Adding a separate `data` key keeps the structure clean:

```json
{
    "message": "authenticated",
    "status": 200,
    "data": {
        "token": "1|abc123..."
    }
}
```

### Instructions

Update `success()` in `app/Traits/ApiResponses.php`:

```php
protected function success(string $message, mixed $data = [], int $statusCode = 200): JsonResponse
{
    return response()->json([
        'message' => $message,
        'status'  => $statusCode,
        'data'    => $data,
    ], $statusCode);
}
```

Update `ok()` to pass `data` through:

```php
protected function ok(string $message, mixed $data = []): JsonResponse
{
    return $this->success($message, $data, 200);
}
```

> Existing callers that pass only a message still work — `$data` defaults to `[]`.

---

## Task 5 — Implement the Login Method

### Goal
Build real login logic: validate input, attempt authentication, fetch the user, issue a Sanctum token, return it.

### Key Concept — `Auth::attempt()`

`Auth::attempt(['email' => $email, 'password' => $password])` does three things:
1. Looks up the user by email
2. Hashes the submitted password and compares it to the stored hash
3. Returns `true` if they match, `false` otherwise

It does **not** create a session (in API context). You only use it to verify credentials.

### Key Concept — `createToken($name)->plainTextToken`

```php
$user->createToken('API token for ' . $user->email)->plainTextToken
```

- `createToken($name)` stores a **hashed** token in the `personal_access_tokens` table and returns a `NewAccessToken` object
- `->plainTextToken` gives you the **unhashed** string — this is the **only time** it's available
- The plaintext token is what the client stores and sends as a Bearer header on future requests
- If you lose it, the user has to log in again to get a new one

### Instructions

Update `app/Http/Controllers/Api/AuthController.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponses;

    public function login(LoginUserRequest $request): JsonResponse
    {
        $request->validated();

        if (! Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Invalid credentials.', 401);
        }

        $user = $request->user();

        return $this->ok('Authenticated.', [
            'token' => $user->createToken('API token for ' . $user->email)->plainTextToken,
        ]);
    }
}
```

**Flow walkthrough:**

```
1. Request hits POST /api/login
2. LoginUserRequest validates email + password fields
3. Auth::attempt() checks credentials against the DB
4. If wrong → return 401 with "Invalid credentials."
5. If correct → fetch the authenticated user
6. Create a Sanctum token → get plain text value
7. Return 200 with token in data
```

**`$request->user()` after `Auth::attempt()`** — after a successful `attempt()`, the user is resolved from the current request. This is the standard way to get the authenticated user within a request lifecycle.

### Expected Response

Successful login:
```json
{
    "message": "Authenticated.",
    "status": 200,
    "data": {
        "token": "1|LTbpVjbMHlMN4y9xv3aPO7..."
    }
}
```

Failed login:
```json
{
    "message": "Invalid credentials.",
    "status": 401
}
```

---

## Task 6 — Protect Routes with `auth:sanctum`

### Goal
Wrap versioned routes in the `auth:sanctum` middleware so unauthenticated requests are rejected.

### Key Concept — Bearer token flow

```
Client                           Server
  │                                │
  │── POST /api/login ────────────>│
  │<─ 200 { "token": "1|abc..." } ─│
  │                                │
  │── GET /api/v1/tickets ────────>│
  │   Authorization: Bearer 1|abc  │
  │<─ 200 [tickets array] ─────────│
```

The token is included in the `Authorization` header as `Bearer <token>`. Sanctum intercepts the request, looks up the token in `personal_access_tokens`, finds the associated user, and makes it available via `$request->user()` for the duration of the request.

### Instructions

Update `routes/api_v1.php`:

```php
<?php

use App\Http\Controllers\Api\V1\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tickets', TicketController::class);
});
```

Or equivalently, using the fluent syntax:

```php
Route::apiResource('tickets', TicketController::class)
    ->middleware('auth:sanctum');
```

---

## Task 7 — Test the Full Auth Flow in Postman

### Step 1 — Attempt unauthenticated request

`GET http://localhost/api/v1/tickets`
Header: `Accept: application/json`

Expected response: `401 Unauthenticated`

```json
{
    "message": "Unauthenticated."
}
```

### Step 2 — Login to get a token

`POST http://localhost/api/login`
Headers:
- `Accept: application/json`
- `Content-Type: application/json`

Body (raw JSON):
```json
{
    "email": "user@example.com",
    "password": "password"
}
```

Expected response:
```json
{
    "message": "Authenticated.",
    "status": 200,
    "data": {
        "token": "1|LTbpVjbMHlMN4y9xv3aPO7..."
    }
}
```

Copy the `token` value.

### Step 3 — Authenticated request

`GET http://localhost/api/v1/tickets`
Headers:
- `Accept: application/json`
- `Authorization: Bearer 1|LTbpVjbMHlMN4y9xv3aPO7...` ← paste token here

Expected response: `200` with JSON array of tickets.

---

## Task 8 — Write Pest Tests

### Login tests

```php
use App\Models\User;

it('returns a token on successful login', function () {
    $user = User::factory()->create();

    $this->postJson('/api/login', [
        'email'    => $user->email,
        'password' => 'password',
    ])
        ->assertOk()
        ->assertJsonStructure([
            'data' => ['token'],
        ]);
});

it('returns 401 for invalid credentials', function () {
    User::factory()->create(['email' => 'user@example.com']);

    $this->postJson('/api/login', [
        'email'    => 'user@example.com',
        'password' => 'wrongpassword',
    ])
        ->assertUnauthorized()
        ->assertJson(['status' => 401]);
});

it('validates login request fields', function (array $body) {
    $this->postJson('/api/login', $body)
        ->assertUnprocessable();
})->with([
    'missing email'        => [['password' => 'password']],
    'invalid email format' => [['email' => 'not-an-email', 'password' => 'password']],
    'missing password'     => [['email' => 'user@example.com']],
    'short password'       => [['email' => 'user@example.com', 'password' => 'short']],
]);
```

### Protected route tests

```php
use App\Models\{User, Ticket};

it('rejects unauthenticated ticket list request', function () {
    $this->getJson('/api/v1/tickets')
        ->assertUnauthorized();
});

it('returns tickets for authenticated user', function () {
    $user = User::factory()->create();
    Ticket::factory()->count(3)->create();

    $this->actingAs($user)
        ->getJson('/api/v1/tickets')
        ->assertOk()
        ->assertJsonCount(3);
});
```

> `actingAs($user)` in an API test context works with Sanctum's `auth:sanctum` guard — it bypasses token issuance and treats the user as authenticated directly.

---

## Final Checklist

- [ ] `app/Http/Requests/Api/LoginUserRequest.php` created with `email` + `password` rules
- [ ] `authorize()` returns `true` in `LoginUserRequest`
- [ ] `AuthController` moved to `app/Http/Controllers/Api/AuthController.php`
- [ ] Namespace updated to `App\Http\Controllers\Api`
- [ ] `routes/api.php` updated to import `Api\AuthController`
- [ ] `error()` method added to `ApiResponses` trait
- [ ] `success()` and `ok()` accept optional `$data` param
- [ ] `login()` uses `Auth::attempt()`, returns `plainTextToken` in `data`
- [ ] `routes/api_v1.php` wrapped in `auth:sanctum` middleware
- [ ] Unauthenticated request to `/api/v1/tickets` returns 401
- [ ] Login returns token, authenticated ticket request returns data
- [ ] All Pest tests passing

---

## What's Next (Episode 06)

Token revocation: the `logout` endpoint. When a user logs out, their token should be deleted from `personal_access_tokens` so it cannot be reused. Also: registering new users via the API (`register` endpoint).
