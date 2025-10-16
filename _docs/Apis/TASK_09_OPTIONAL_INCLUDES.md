# Optional Includes via Query Parameters — Task 09

> **Source:** Laravel API Masterclass — Episode 09
> **Created:** 2026-03-10
> **Run Time:** 11m 44s
> **Status:** Study Task

---

## Overview

Eager-loading related data adds size to every response whether the client needs it or not. The solution: make includes opt-in via a query parameter. The client signals which relationships they want: `?include=author`. The server loads that relationship only when requested. If the parameter is absent, the include key is omitted entirely.

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| `?include=author` | Query parameter signalling the client wants related data |
| `ApiController` base class | Shared include-parsing logic reused by all resource controllers |
| `include(string $relationship): bool` | Checks if the client requested a specific relationship |
| `explode(',', $value)` | Parses comma-separated includes: `?include=author,comments` |
| `strtolower()` | Normalises case so `Author` and `author` both work |
| `$ticket->load('user')` | Eager loads a relationship on an already-retrieved model |
| `whenLoaded()` | In the resource — includes data only if the relation was loaded |

---

## Task 1 — Create the ApiController Base Class

### Goal
Build a shared base controller with an `include()` method that all versioned resource controllers can extend.

### Key Concept — Trait vs base class

Both work. A trait was already used for `ApiResponses`. Using a base class here is purely a deliberate choice to demonstrate the alternative. The method itself has no meaningful reason to prefer one over the other.

### Instructions

```bash
php artisan make:controller Api/V1/ApiController --no-interaction
```

Open `app/Http/Controllers/Api/V1/ApiController.php`:

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function include(string $relationship): bool
    {
        $param = request()->get('include');

        if (! isset($param)) {
            return false;
        }

        $includeValues = explode(',', strtolower($param));

        return in_array(strtolower($relationship), $includeValues);
    }
}
```

**Step-by-step logic:**

1. Get the `include` query parameter from the request
2. If it is not set at all → return `false` immediately (nothing to include)
3. Explode on `,` — supports multiple includes: `?include=author,comments`
4. Normalise to lowercase — `Author`, `AUTHOR`, `author` all become `author`
5. Check if the requested `$relationship` (also lowercased) is in the array

---

## Task 2 — Extend ApiController in Resource Controllers

### Goal
Make `TicketController` and `UsersController` extend `ApiController` instead of the base `Controller`.

### Instructions

In `app/Http/Controllers/Api/V1/TicketController.php`:

```php
use App\Http\Controllers\Api\V1\ApiController;

class TicketController extends ApiController
{
    // ...
}
```

Same for `UsersController`:

```php
class UsersController extends ApiController
{
    // ...
}
```

---

## Task 3 — Opt-In Author Include in TicketController

### Goal
Load the `user` relationship on tickets only when the client sends `?include=author`.

### Instructions

Update `show()` in `TicketController`:

```php
public function show(Ticket $ticket): TicketResource
{
    if ($this->include('author')) {
        $ticket->load('user');
    }

    return new TicketResource($ticket);
}
```

Update `index()` to support includes on collections:

```php
public function index(): AnonymousResourceCollection
{
    $tickets = Ticket::query();

    if ($this->include('author')) {
        $tickets = $tickets->with('user');
    }

    return TicketResource::collection($tickets->paginate());
}
```

**`load()` vs `with()`:**

| Method | Use on | When |
|---|---|---|
| `$model->load('relation')` | Already-fetched model instance | `show()` — single model already retrieved by route model binding |
| `Query::with('relation')` | Query builder | `index()` — building the query before fetching |

### Resource side — `whenLoaded()` in TicketResource

This was already set up in Task 08. `whenLoaded('user')` checks if the relation is loaded:

```php
'includes' => $this->whenLoaded('user', fn() => new UserResource($this->user)),
```

No change needed here — it works automatically with the opt-in loading above.

**Result:**

| Request | Response |
|---|---|
| `GET /api/v1/tickets/1` | No `includes` key |
| `GET /api/v1/tickets/1?include=author` | `includes` key with `UserResource` data |

---

## Task 4 — Opt-In Ticket Include in UsersController

### Goal
Allow `GET /api/v1/users?include=tickets` to return each user with their tickets embedded.

### Add the tickets relationship to the User model

```php
// app/Models/User.php
use Illuminate\Database\Eloquent\Relations\HasMany;

public function tickets(): HasMany
{
    return $this->hasMany(Ticket::class);
}
```

### Update UsersController

```php
public function index(): AnonymousResourceCollection
{
    $users = User::query();

    if ($this->include('tickets')) {
        $users = $users->with('tickets');
    }

    return UserResource::collection($users->paginate());
}

public function show(User $user): UserResource
{
    if ($this->include('tickets')) {
        $user->load('tickets');
    }

    return new UserResource($user);
}
```

### Add includes to UserResource

```php
use App\Http\Resources\Api\V1\TicketResource;

'includes' => $this->whenLoaded('tickets', fn() => TicketResource::collection($this->tickets)),
```

---

## Task 5 — Standardise Links Format

### Goal
Make the `links` format inside resources consistent with Laravel's paginated `links` output.

### The inconsistency

Laravel paginates responses with:

```json
"links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
}
```

But the resource currently generates:

```json
"links": [
    { "self": "http://..." }
]
```

An array of objects vs a plain object. Clients should not have to handle two different link shapes.

### Fix in TicketResource

Change from array of objects:

```php
// Before
'links' => [
    ['self' => route('tickets.show', ['ticket' => $this->id])],
],
```

To a plain object:

```php
// After
'links' => [
    'self' => route('tickets.show', ['ticket' => $this->id]),
],
```

### Add links to UserResource

```php
'links' => [
    'self' => route('users.show', ['user' => $this->id]),
],
```

### Add user links inside TicketResource relationships section

```php
'relationships' => [
    'author' => [
        'data'  => [
            'type' => 'user',
            'id'   => $this->user_id,
        ],
        'links' => [
            'self' => route('users.show', ['user' => $this->user_id]),
        ],
    ],
],
```

---

## Task 6 — Complete TicketResource

After all tasks, the full `toArray()` should look like this:

```php
public function toArray(Request $request): array
{
    return [
        'type'          => 'ticket',
        'id'            => $this->id,
        'attributes'    => [
            'title'       => $this->title,
            'description' => $this->when(
                $request->routeIs('tickets.show'),
                $this->description
            ),
            'status'    => $this->status,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ],
        'relationships' => [
            'author' => [
                'data'  => [
                    'type' => 'user',
                    'id'   => $this->user_id,
                ],
                'links' => [
                    'self' => route('users.show', ['user' => $this->user_id]),
                ],
            ],
        ],
        'includes'      => $this->whenLoaded('user', fn() => new UserResource($this->user)),
        'links'         => [
            'self' => route('tickets.show', ['ticket' => $this->id]),
        ],
    ];
}
```

---

## Task 7 — Pest Tests

```php
use App\Models\{User, Ticket};

it('includes are absent by default on ticket show', function () {
    $ticket = Ticket::factory()->create();

    $this->actingAs(User::factory()->create())
        ->getJson("/api/v1/tickets/{$ticket->id}")
        ->assertOk()
        ->assertJsonMissingPath('data.includes');
});

it('includes author when ?include=author provided', function () {
    $ticket = Ticket::factory()->create();

    $this->actingAs(User::factory()->create())
        ->getJson("/api/v1/tickets/{$ticket->id}?include=author")
        ->assertOk()
        ->assertJsonPath('data.includes.type', 'user');
});

it('includes tickets for user when requested', function () {
    $user = User::factory()->create();
    Ticket::factory()->count(3)->for($user)->create();

    $this->actingAs(User::factory()->create())
        ->getJson("/api/v1/users/{$user->id}?include=tickets")
        ->assertOk()
        ->assertJsonCount(3, 'data.includes');
});

it('include is case insensitive', function () {
    $ticket = Ticket::factory()->create();

    $this->actingAs(User::factory()->create())
        ->getJson("/api/v1/tickets/{$ticket->id}?include=AUTHOR")
        ->assertOk()
        ->assertJsonPath('data.includes.type', 'user');
});
```

---

## Final Checklist

- [ ] `app/Http/Controllers/Api/V1/ApiController.php` created
- [ ] `include()` method parses `?include=`, explodes on comma, normalises to lowercase
- [ ] `TicketController` and `UsersController` extend `ApiController`
- [ ] `TicketController::show()` calls `$ticket->load('user')` when `$this->include('author')`
- [ ] `TicketController::index()` uses `->with('user')` when `$this->include('author')`
- [ ] `User` model has `tickets()` `HasMany` relationship
- [ ] `UsersController::show()` and `index()` load tickets on opt-in
- [ ] `UserResource` has `includes` using `whenLoaded('tickets', ...)`
- [ ] `links` in all resources use plain object format (not array of objects)
- [ ] All Pest tests passing

---

## What's Next (Episode 10)

Includes are solved. The next problem: filtering. Clients need to query tickets by status, title, and date. Doing this in the controller creates messy, hard-to-test code. The solution is a dedicated `QueryFilter` system.
