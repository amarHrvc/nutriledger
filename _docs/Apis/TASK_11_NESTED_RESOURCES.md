# Nested Resources — Task 11

> **Source:** Laravel API Masterclass — Episode 11
> **Created:** 2026-03-10
> **Run Time:** 6m 52s
> **Status:** Study Task

---

## Overview

When a client wants tickets submitted by a specific user, the correct URL is not `GET /api/v1/tickets?filter[userId]=5`. If you know the user ID, you should go through the user endpoint. Nested resources model this parent-child relationship in the URL: `GET /api/v1/authors/{author}/tickets`.

This episode also renames `users` to `authors` — making a semantic distinction that matters as the system grows (an author is a user who submits tickets; an assignee will be a different user type).

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| Nested resource | A resource URL that is scoped under a parent: `/authors/{author}/tickets` |
| `AuthorTicketsController` | Handles ticket endpoints scoped to a specific author |
| `UsersController` → `AuthorsController` | Rename to reflect semantic meaning |
| Route naming after rename | `users.*` routes become `authors.*` |
| Filters on nested resources | `TicketFilter` reused on the nested endpoint with no changes |

---

## Task 1 — Rename Users to Authors

### Why rename?

The system will eventually have two types of users: the person who *submitted* the ticket (author) and the person it is *assigned to* (assignee). Calling both "users" creates confusion. The rename makes the distinction explicit now, before the codebase grows.

### Step 1 — Rename the controller file

```
app/Http/Controllers/Api/V1/UsersController.php
→
app/Http/Controllers/Api/V1/AuthorsController.php
```

Update the class name inside the file:

```php
class AuthorsController extends ApiController
```

Update the route parameter name from `$user` to `$author`:

```php
public function show(User $author): UserResource
{
    if ($this->include('tickets')) {
        $author->load('tickets');
    }

    return new UserResource($author);
}
```

Laravel's route model binding resolves the parameter name to the model. Renaming it to `$author` does not break the binding — it still resolves a `User` model. The parameter name is just a variable name.

### Step 2 — Update routes in `api_v1.php`

```php
// Before
Route::apiResource('users', UsersController::class);

// After
use App\Http\Controllers\Api\V1\AuthorsController;

Route::apiResource('authors', AuthorsController::class);
```

Named routes change automatically:

| Before | After |
|---|---|
| `users.index` | `authors.index` |
| `users.show` | `authors.show` |
| `users.store` | `authors.store` |
| `users.update` | `authors.update` |
| `users.destroy` | `authors.destroy` |

### Step 3 — Update UserResource route references

In `app/Http/Resources/Api/V1/UserResource.php`:

```php
// Before
$this->mergeWhen($request->routeIs('users.*'), [...])
'self' => route('users.show', ['user' => $this->id]),

// After
$this->mergeWhen($request->routeIs('authors.*'), [...])
'self' => route('authors.show', ['author' => $this->id]),
```

### Step 4 — Update TicketResource relationship links

In `app/Http/Resources/Api/V1/TicketResource.php`:

```php
// Before
'self' => route('users.show', ['user' => $this->user_id]),

// After
'self' => route('authors.show', ['author' => $this->user_id]),
```

---

## Task 2 — Create the AuthorTicketsController

### Goal
Handle `GET /api/v1/authors/{author}/tickets` — tickets scoped to a specific author.

### Instructions

```bash
php artisan make:controller Api/V1/AuthorTicketsController --no-interaction
```

Open `app/Http/Controllers/Api/V1/AuthorTicketsController.php`:

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Resources\Api\V1\TicketResource;
use App\Models\{Ticket, User};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthorTicketsController extends ApiController
{
    public function index(User $author, TicketFilter $filters): AnonymousResourceCollection
    {
        return TicketResource::collection(
            Ticket::where('user_id', $author->id)
                ->filter($filters)
                ->paginate()
        );
    }
}
```

**Key points:**

- `User $author` — route model binding resolves the `{author}` URL segment to a User instance
- `TicketFilter $filters` — same filter system from Episode 10, injected automatically
- `Ticket::where('user_id', $author->id)` — scopes the query to that author's tickets before filtering
- All `TicketFilter` methods still work: `filter[status]=C`, `filter[title]=*word*`, etc.

---

## Task 3 — Register the Nested Route

### Instructions

In `routes/api_v1.php`, add the nested resource route inside the `auth:sanctum` group:

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tickets',                        TicketController::class);
    Route::apiResource('authors',                        AuthorsController::class);
    Route::apiResource('authors.tickets',                AuthorTicketsController::class)
        ->scoped(['ticket' => 'id']);
});
```

**`Route::apiResource('authors.tickets', ...)`** — Laravel interprets the dot notation as a parent-child relationship. It generates:

| Method | URL | Controller method |
|---|---|---|
| GET | `/authors/{author}/tickets` | `index` |
| POST | `/authors/{author}/tickets` | `store` |
| GET | `/authors/{author}/tickets/{ticket}` | `show` |
| PUT/PATCH | `/authors/{author}/tickets/{ticket}` | `update` |
| DELETE | `/authors/{author}/tickets/{ticket}` | `destroy` |

`->scoped(['ticket' => 'id'])` — tells Laravel how to scope the child (`ticket`) to the parent (`author`) when doing route model binding for the nested item. Without this, Laravel cannot verify the ticket belongs to the author.

For now, only `index` is implemented. The other methods can be added in later episodes.

---

## Task 4 — Test the Nested Endpoint

### Postman

```
# All tickets by author 1
GET /api/v1/authors/1/tickets
Authorization: Bearer {{bearer}}
Accept: application/json

# Completed tickets by author 1
GET /api/v1/authors/1/tickets?filter[status]=C
Authorization: Bearer {{bearer}}
Accept: application/json

# Completed and cancelled tickets by author 1
GET /api/v1/authors/1/tickets?filter[status]=C,X
Authorization: Bearer {{bearer}}
Accept: application/json
```

The response is identical in structure to `GET /api/v1/tickets` — same `TicketResource` wrapping — but results are scoped to that author.

---

## Task 5 — Pest Tests

```php
use App\Models\{User, Ticket};

it('author tickets index returns only that authors tickets', function () {
    $author = User::factory()->create();
    $other  = User::factory()->create();

    Ticket::factory()->count(3)->for($author)->create();
    Ticket::factory()->count(2)->for($other)->create();

    $this->actingAs(User::factory()->create())
        ->getJson("/api/v1/authors/{$author->id}/tickets")
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

it('author tickets can be filtered by status', function () {
    $author = User::factory()->create();
    Ticket::factory()->create(['user_id' => $author->id, 'status' => 'A']);
    Ticket::factory()->create(['user_id' => $author->id, 'status' => 'C']);

    $this->actingAs(User::factory()->create())
        ->getJson("/api/v1/authors/{$author->id}/tickets?filter[status]=C")
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

it('authors endpoint returns 404 for unknown author', function () {
    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/authors/99999/tickets')
        ->assertNotFound();
});

it('authors route replaces users route', function () {
    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/users')
        ->assertNotFound();

    $this->getJson('/api/v1/authors')
        ->assertOk();
});
```

---

## Final Checklist

- [ ] `UsersController.php` renamed to `AuthorsController.php` with class name updated
- [ ] Route param `$user` renamed to `$author` in `AuthorsController`
- [ ] `api_v1.php` uses `authors` resource route, not `users`
- [ ] `UserResource` uses `authors.*` in `routeIs()` and `route('authors.show', ...)`
- [ ] `TicketResource` relationship links use `route('authors.show', ...)`
- [ ] `AuthorTicketsController` created with `index()` scoping tickets to author
- [ ] Nested route `authors.tickets` registered with `->scoped(['ticket' => 'id'])`
- [ ] `filter[status]=C,X` works on the nested endpoint
- [ ] `/api/v1/users` returns 404, `/api/v1/authors` returns 200
- [ ] All Pest tests passing

---

## What's Next (Episode 12)

Filtering is done. The final piece of query control is **sorting** — clients need to specify a sort column and direction without the controller knowing about it. Sorting will be added to the `QueryFilter` base class so it works for all resources automatically.
