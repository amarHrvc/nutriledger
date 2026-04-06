# API Foundations — Task 01

> **Source:** Laravel API Masterclass — Lesson 1
> **Created:** 2026-03-10
> **Branch:** `feature/api-foundations` (or apply concepts to current API branch)
> **Status:** Study Task

---

## Overview

Modern web applications are built as two separate layers:

- **Frontend (SPA)** — React, Vue, Angular — generates markup in the browser, handles user interaction
- **Backend (API)** — Laravel — serves JSON data, enforces business rules, handles database access

The backend is essentially a **data access layer**. It does not render HTML. Every response is JSON. The client decides how to display it.

Laravel has first-class support for this via `routes/api.php`. Every route registered there is automatically prefixed with `/api`. You can change that prefix, but the convention is to keep it.

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| `routes/api.php` | API-only route file, auto-prefixed with `/api` |
| `response()->json()` | Returns a JSON HTTP response with a status code |
| `AuthController` | Controller handling auth endpoints (login, register, logout) |
| `ApiResponses` trait | Reusable helper methods for consistent JSON responses |
| Postman | HTTP client for testing API endpoints outside the browser |

---

## Task 1 — First JSON Route

### Goal
Register a GET route at `/api` that returns a JSON response.

### Key Concept — Why not use the browser for everything?
Browsers automatically send `GET` requests. But APIs need `POST`, `PUT`, `PATCH`, `DELETE` too. A proper HTTP client (Postman, curl, Insomnia) lets you control method, headers, and body.

### Instructions

Open `routes/api.php`. A `/user` route already exists (Sanctum-protected). Add below it:

```php
Route::get('/', function () {
    return response()->json([
        'message' => 'hello API',
    ], 200);
});
```

**`response()->json($data, $statusCode)` explained:**
- First arg: PHP array — Laravel serialises it to JSON
- Second arg: HTTP status code — defaults to 200 if omitted, but be explicit
- Laravel sets `Content-Type: application/json` automatically

### Test It

Visit `http://localhost/api` in the browser. You should see:

```json
{
    "message": "hello API"
}
```

### Pest Test

```php
it('root API endpoint returns JSON greeting', function () {
    $this->getJson('/api')
        ->assertOk()
        ->assertJson(['message' => 'hello API']);
});
```

> `getJson()` automatically sets `Accept: application/json` header and parses the response as JSON.

---

## Task 2 — AuthController

### Goal
Move the route logic into a dedicated controller and scaffold the `login` endpoint.

### Key Concept — Why a controller?
Route closures are fine for prototyping but do not scale. Controllers group related endpoints, support dependency injection, can be tested in isolation, and are reusable across route groups.

### Instructions

**Step 1 — Create the controller:**

```bash
php artisan make:controller AuthController --no-interaction
```

This creates `app/Http/Controllers/AuthController.php`.

**Step 2 — Add the `login` method:**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function login(): JsonResponse
    {
        return response()->json([
            'message' => 'hello login',
        ], 200);
    }
}
```

> Always declare explicit return types. `JsonResponse` is `Illuminate\Http\JsonResponse`.

**Step 3 — Register the route in `routes/api.php`:**

```php
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);
```

Why `POST`? Login submits credentials (username, password). Credentials must not appear in the URL (that would be a `GET`). `POST` puts them in the request body.

### Test It

You cannot test `POST /api/login` in a browser address bar. Use Postman:

1. Set method: `POST`
2. Set URL: `http://localhost/api/login`
3. Add header: `Accept: application/json`
4. Send

Expected response:

```json
{
    "message": "hello login"
}
```

Or use curl:

```bash
curl -X POST http://localhost/api/login \
  -H "Accept: application/json"
```

### Pest Test

```php
it('login endpoint responds with 200', function () {
    $this->postJson('/api/login')
        ->assertOk()
        ->assertJsonStructure(['message']);
});
```

---

## Task 3 — ApiResponses Trait

### Goal
Build a reusable trait with named response helpers so controllers never call `response()->json()` directly.

### Key Concept — Why a trait and not a base controller?

PHP is single-inheritance. If `AuthController` extends a `BaseApiController`, it cannot also extend anything else. A trait is mixed in at the `use` statement — it works alongside any inheritance chain, and can be used by non-controller classes too (e.g., in tests or service classes).

### Key Concept — Embedding status in the body

There are two places the status code lives:

1. **HTTP header** — e.g., `HTTP/1.1 200 OK` — read by the client automatically
2. **Response body** — `{"status": 200, ...}` — readable by the app code consuming the API

Best practice: put it in both. Clients that check `response.status` get it from the header. App-level logging or debugging can read it from the body.

### Instructions

**Step 1 — Create the file:**

Create `app/Traits/ApiResponses.php` (manually — no artisan command for traits):

```php
<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    protected function success(string $message, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'status'  => $statusCode,
        ], $statusCode);
    }

    protected function ok(string $message): JsonResponse
    {
        return $this->success($message, 200);
    }
}
```

**What each method does:**

| Method | Status | Use when |
|---|---|---|
| `success($message, $code)` | Any 2xx | Base method — generic success |
| `ok($message)` | 200 | Request succeeded, data returned |

**Step 2 — Use the trait in `AuthController`:**

```php
use App\Traits\ApiResponses;

class AuthController extends Controller
{
    use ApiResponses;

    public function login(): JsonResponse
    {
        return $this->ok('hello login');
    }
}
```

Before: `response()->json(['message' => 'hello login', 'status' => 200], 200)`
After: `$this->ok('hello login')`

Same result, far less noise.

### Test It

`POST /api/login` should now return:

```json
{
    "message": "hello login",
    "status": 200
}
```

### Pest Tests

```php
it('login response includes status field in body', function () {
    $this->postJson('/api/login')
        ->assertOk()
        ->assertJson([
            'message' => 'hello login',
            'status'  => 200,
        ]);
});
```

---

## Task 4 — Extend the Trait (Exercise)

### Goal
Add more named response helpers to cover the full range of common API responses.

### Instructions

Extend `app/Traits/ApiResponses.php` with the following methods:

**`created(string $message): JsonResponse`**
- Status: 201
- Use when: a new resource was created (`POST` that stores something)

**`noContent(): JsonResponse`**
- Status: 204
- Use when: action succeeded but there is nothing to return (e.g., delete)
- Note: 204 responses must have no body — do not pass a message to `json()`

**`error(string $message, int $statusCode): JsonResponse`**
- Status: caller provides it (400, 401, 403, 404, 422, 500, etc.)
- Use when: something went wrong

```php
protected function created(string $message): JsonResponse
{
    return $this->success($message, 201);
}

protected function noContent(): JsonResponse
{
    return response()->json(null, 204);
}

protected function error(string $message, int $statusCode): JsonResponse
{
    return response()->json([
        'message' => $message,
        'status'  => $statusCode,
    ], $statusCode);
}
```

**Status code reference:**

| Code | Name | When |
|---|---|---|
| 200 | OK | Generic success, data returned |
| 201 | Created | Resource created |
| 204 | No Content | Success, nothing to return |
| 400 | Bad Request | Malformed request |
| 401 | Unauthorized | Not authenticated |
| 403 | Forbidden | Authenticated but not allowed |
| 404 | Not Found | Resource does not exist |
| 422 | Unprocessable Entity | Validation failed |
| 500 | Internal Server Error | Server-side failure |

### Pest Tests

```php
it('created helper returns 201 with status in body', function () {
    // Call directly on a mock controller or via a test route
    $trait = new class {
        use \App\Traits\ApiResponses;
        public function call(): \Illuminate\Http\JsonResponse
        {
            return $this->created('Resource created');
        }
    };

    $response = $trait->call();

    expect($response->getStatusCode())->toBe(201)
        ->and($response->getData(true))->toMatchArray([
            'message' => 'Resource created',
            'status'  => 201,
        ]);
});

it('error helper returns correct status code', function () {
    $trait = new class {
        use \App\Traits\ApiResponses;
        public function call(): \Illuminate\Http\JsonResponse
        {
            return $this->error('Not found', 404);
        }
    };

    $response = $trait->call();

    expect($response->getStatusCode())->toBe(404)
        ->and($response->getData(true)['status'])->toBe(404);
});
```

---

## Task 5 — Scaffold Register and Logout (Exercise)

### Goal
Add stub methods to `AuthController` for `register` and `logout`. No real logic yet — just routing and response shape.

### Instructions

**Add to `AuthController`:**

```php
public function register(): JsonResponse
{
    return $this->ok('hello register');
}

public function logout(): JsonResponse
{
    return $this->ok('hello logout');
}
```

**Register routes in `routes/api.php`:**

```php
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout',   [AuthController::class, 'logout']);
```

> `logout` will later require authentication middleware (`auth:sanctum`). For now it is open.

### Pest Tests

```php
it('register endpoint responds', function () {
    $this->postJson('/api/register')
        ->assertOk();
});

it('logout endpoint responds', function () {
    $this->postJson('/api/logout')
        ->assertOk();
});
```

---

## Postman Setup

### Why Postman?

Browsers only let you easily trigger `GET` requests via the address bar. APIs need `POST`, `PUT`, `PATCH`, `DELETE`, custom headers (`Accept`, `Authorization`), and request bodies. Postman covers all of this.

### Basic Usage

1. Download from [postman.com](https://postman.com) or use the web version
2. Create a new **Collection** called `nutri-ledger API`
3. Add a **Request** per endpoint

For each request:
- Set the **method** (GET, POST, etc.)
- Set the **URL**: `http://localhost/api/login`
- Under **Headers**, add: `Accept: application/json`
- For `POST` with a body: set **Body → raw → JSON**

### Alternatives

**curl (terminal):**
```bash
# GET
curl http://localhost/api -H "Accept: application/json"

# POST
curl -X POST http://localhost/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

**VS Code REST Client** (`.http` files):
```http
POST http://localhost/api/login
Accept: application/json
Content-Type: application/json

{
    "email": "test@example.com",
    "password": "password"
}
```

---

## Final Checklist

- [ ] `GET /api` route returns `{"message":"hello API"}`
- [ ] `AuthController` created at `app/Http/Controllers/AuthController.php`
- [ ] `POST /api/login` route registered and working
- [ ] `app/Traits/ApiResponses.php` created with `success()` and `ok()`
- [ ] `AuthController` uses the `ApiResponses` trait
- [ ] Trait extended with `created()`, `noContent()`, `error()`
- [ ] `register` and `logout` stub methods added with routes
- [ ] All routes tested via Postman or curl
- [ ] Pest tests written and passing

---

## What's Next (Lesson 2)

- Sanctum installation — token-based API authentication
- Real `login` logic: validate credentials with `Auth::attempt()`, issue a Sanctum token, return it
- Real `register` logic: create `User`, hash password, return token
- Real `logout`: revoke the current token via `$request->user()->currentAccessToken()->delete()`
- Protecting routes with `auth:sanctum` middleware
