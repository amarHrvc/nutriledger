# API Versioning — Task 03

> **Source:** Laravel API Masterclass — Episode 04
> **Created:** 2026-03-10
> **Run Time:** 9m 28s
> **Status:** Study Task

---

## Overview

An API is a contract. Every client that consumes it depends on the response structure being stable. If you rename a field, remove a field, or change a data type, you break every client using that field — and they may not even know immediately.

**Versioning** solves this: changes go into a new version (`v2`) while `v1` remains unchanged. Existing clients keep working. New clients opt into `v2`.

Even if you think no one else will ever use your API, version it anyway. You will thank yourself when you come back six months later and want to restructure the responses.

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| API versioning | Isolating breaking changes behind a version prefix (`/api/v1/`) |
| URL versioning | Simplest approach — version in the URL path, not headers |
| Versioned folder structure | `app/Http/Controllers/Api/V1/` mirrors the URL |
| `--resource` flag | Scaffolds all CRUD methods on a controller |
| `Route::apiResource` | Resource routes minus `create` and `edit` (form routes) |
| Separate route files | `routes/api_v1.php` keeps versioned routes isolated |
| `RouteServiceProvider` | Where you register additional route files with their prefixes |

---

## Task 1 — Understand Versioning Strategies

### Goal
Know why URL versioning is the standard and why other approaches are more complex.

### Three versioning strategies:

**1. URL versioning (chosen approach)**

```
GET /api/v1/tickets
GET /api/v2/tickets
```

Pros:
- Obvious — client can see exactly which version they're calling
- Easy to route — different URL → different controller
- Easy to test in Postman or a browser

Cons:
- URLs are technically supposed to identify resources, not versions

**2. Header versioning**

```
GET /api/tickets
Accept: application/vnd.myapp.v1+json
```

Pros: "Cleaner" URLs
Cons: Not visible, harder to test, overkill for most applications

**3. Query string versioning**

```
GET /api/tickets?version=1
```

Pros: Easy to add
Cons: Not a standard, confusing

**Decision:** URL versioning. Simple, explicit, easy to route. Don't over-complicate your software.

---

## Task 2 — Set Up Versioned Folder Structure

### Goal
Reorganise controllers and requests so the folder structure mirrors the URL structure.

### Target folder structure:

```
app/Http/Controllers/
├── Api/
│   ├── AuthController.php          ← auth (no versioning needed)
│   └── V1/
│       └── TicketController.php    ← versioned resource controller
app/Http/Requests/
└── Api/
    └── V1/
        ├── StoreTicketRequest.php
        └── UpdateTicketRequest.php
```

### Why separate API controllers from web controllers?

Auth for a web app (Fortify/session-based) is different from auth for an API (token-based). They are distinct codebases that happen to share models. Mixing them in the same folder leads to confusion.

### Task 3 — Generate the Versioned Controller

### Goal
Use artisan to generate a resource controller in the versioned folder path.

### Instructions

```bash
php artisan make:controller Api/V1/TicketController --resource --requests --no-interaction
```

**Flags explained:**

| Flag | Effect |
|---|---|
| `--resource` | Scaffolds `index`, `store`, `show`, `update`, `destroy`, `create`, `edit` |
| `--requests` | Generates `StoreTicketRequest` and `UpdateTicketRequest`, type-hints them in the controller |

After running this, check the generated files:

- `app/Http/Controllers/Api/V1/TicketController.php`
- `app/Http/Requests/StoreTicketRequest.php` ← wrong location
- `app/Http/Requests/UpdateTicketRequest.php` ← wrong location

Artisan places the requests directly in `app/Http/Requests/`. You need to move them.

### Fix the request namespaces

**Move the files manually** to:

```
app/Http/Requests/Api/V1/StoreTicketRequest.php
app/Http/Requests/Api/V1/UpdateTicketRequest.php
```

**Update the namespace in each request file:**

```php
// Before
namespace App\Http\Requests;

// After
namespace App\Http\Requests\Api\V1;
```

**Update the `use` statements in `TicketController.php`:**

```php
// Before
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;

// After
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
```

### Remove `create` and `edit` methods

APIs do not serve HTML forms. Remove these two methods from `TicketController`:

```php
// DELETE both of these methods entirely
public function create() { ... }
public function edit(Ticket $ticket) { ... }
```

### Add a stub `index` method

For now, return all tickets so you can test the endpoint:

```php
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;

public function index(): JsonResponse
{
    return response()->json(Ticket::all());
}
```

---

## Task 4 — Create a Versioned Route File

### Goal
Keep v1 routes in their own file, separate from the base `api.php`.

### Key Concept — Why separate route files?

Putting all routes for all versions in `api.php` means the file grows indefinitely. More importantly, version prefixes and middleware differ between versions. Separating files keeps each version's routes self-contained.

**Rule of thumb:**
- `routes/api.php` — base routes that are not versioned (login, register)
- `routes/api_v1.php` — all v1 resource routes

### Instructions

**Create `routes/api_v1.php`:**

```php
<?php

use App\Http\Controllers\Api\V1\TicketController;
use Illuminate\Support\Facades\Route;

Route::apiResource('tickets', TicketController::class);
```

**`Route::apiResource` generates exactly these 5 routes:**

| Method | URL | Controller method |
|---|---|---|
| GET | `/tickets` | `index` |
| POST | `/tickets` | `store` |
| GET | `/tickets/{ticket}` | `show` |
| PUT/PATCH | `/tickets/{ticket}` | `update` |
| DELETE | `/tickets/{ticket}` | `destroy` |

Note: no prefix here — the prefix (`api/v1`) is applied when you register this file.

---

## Task 5 — Register the Route File in RouteServiceProvider

### Goal
Tell Laravel to load `routes/api_v1.php` with the prefix `api/v1` and the `api` middleware group.

### Key Concept — How Laravel loads route files

Laravel 12 uses `bootstrap/app.php` to wire up routing. In older versions this was `RouteServiceProvider`. In Laravel 12, you can still use a `RouteServiceProvider`, or register additional route files inline in `bootstrap/app.php`.

### Option A — Via `RouteServiceProvider` (if it exists)

If your project has `app/Providers/RouteServiceProvider.php`, find the `boot()` method. You will see how `api.php` is registered. Add a second registration below it:

```php
Route::middleware('api')
    ->prefix('api/v1')
    ->group(base_path('routes/api_v1.php'));
```

### Option B — Via `bootstrap/app.php` (Laravel 11/12 default)

Open `bootstrap/app.php` and find `->withRouting(...)`. Add an `then` callback:

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
    then: function () {
        Route::middleware('api')
            ->prefix('api/v1')
            ->group(base_path('routes/api_v1.php'));
    },
)
```

**Why `middleware('api')`?** The `api` middleware group applies rate limiting and binds the `auth:sanctum` guard. Any route file serving API traffic should use it.

**Why `prefix('api/v1')` and not write `v1/` in the route file?** The prefix is applied once here. Routes in `api_v1.php` just say `tickets` — the full URL becomes `api/v1/tickets` automatically. This means the route file itself is clean and doesn't repeat the version prefix on every route.

---

## Task 6 — Test the Versioned Endpoint

### Goal
Confirm `GET /api/v1/tickets` returns data.

### Postman

1. New request: `GET http://localhost/api/v1/tickets`
2. Header: `Accept: application/json`
3. Send — JSON array of tickets

### curl

```bash
curl http://localhost/api/v1/tickets \
  -H "Accept: application/json"
```

### Pest Test

```php
use App\Models\{User, Ticket};

it('v1 tickets endpoint returns all tickets', function () {
    Ticket::factory()->count(5)->create();

    $this->getJson('/api/v1/tickets')
        ->assertOk()
        ->assertJsonCount(5);
});
```

---

## Task 7 — Verify Route Registration (Exercise)

### Goal
Use artisan to inspect the registered routes and confirm versioning is correct.

```bash
php artisan route:list --path=api
```

You should see entries like:

```
GET|HEAD   api/v1/tickets .............. tickets.index › Api\V1\TicketController@index
POST       api/v1/tickets .............. tickets.store › Api\V1\TicketController@store
GET|HEAD   api/v1/tickets/{ticket} ..... tickets.show  › Api\V1\TicketController@show
PUT|PATCH  api/v1/tickets/{ticket} ..... tickets.update › Api\V1\TicketController@update
DELETE     api/v1/tickets/{ticket} ..... tickets.destroy › Api\V1\TicketController@destroy
```

Confirm that `create` and `edit` are **not** listed.

---

## Final Checklist

- [ ] `app/Http/Controllers/Api/V1/TicketController.php` created
- [ ] `app/Http/Requests/Api/V1/StoreTicketRequest.php` in correct location with correct namespace
- [ ] `app/Http/Requests/Api/V1/UpdateTicketRequest.php` in correct location with correct namespace
- [ ] `TicketController` imports requests from `Api\V1` namespace
- [ ] `create()` and `edit()` methods removed from `TicketController`
- [ ] `routes/api_v1.php` created with `Route::apiResource`
- [ ] `bootstrap/app.php` (or `RouteServiceProvider`) registers `api_v1.php` with prefix `api/v1`
- [ ] `php artisan route:list --path=api` shows 5 `v1` ticket routes, no `create`/`edit`
- [ ] `GET /api/v1/tickets` returns JSON in Postman
- [ ] Pest test passing

---

## What's Next (Episode 05)

Routes are public right now — anyone can hit `/api/v1/tickets` without authenticating. The next step is Sanctum token authentication: login returns a token, all protected routes require it as a Bearer header.
