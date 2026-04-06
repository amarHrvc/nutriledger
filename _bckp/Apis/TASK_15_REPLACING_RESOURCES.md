# Replacing Resources with PUT Requests — Task 15

> **Source:** Laravel API Masterclass — Episode 15
> **Created:** 2026-03-11
> **Run Time:** 14m 17s
> **Status:** Study Task

---

## Overview

The `update` controller method conventionally handles both PUT and PATCH. This episode separates them into two distinct methods — `replace()` for PUT and `update()` for PATCH — because they are conceptually different operations. Keeping them separate makes intent explicit and simplifies the per-method validation logic. Route setup also gets cleaned up: all versioned routes are grouped under a single middleware call.

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| PUT vs PATCH | PUT replaces all fields; PATCH updates only supplied fields |
| `replace()` method | Custom controller method for PUT — not the built-in `update()` |
| `Route::apiResource()->except('update')` | Remove the default PATCH route to add PUT separately |
| Middleware route group | Wrapping all versioned routes in one `Route::middleware()->group()` |
| `ReplaceTicketRequest` | Same rules as StoreTicketRequest but all fields always required |
| Description visibility fix | Change `when(routeIs('tickets.show'))` to `when(!routeIs([index routes]))` |

---

## Task 1 — Understand PUT vs PATCH

| | PUT | PATCH |
|---|---|---|
| Semantic | Replace the entire resource | Update specific fields |
| All fields required? | Yes | No — only send what you want to change |
| Validation | All fields `required` | All fields `sometimes` |
| DB operation | `update([all fields])` | `update([only provided fields])` |
| Request class | `ReplaceTicketRequest` | `UpdateTicketRequest` |

**Why a separate `replace()` method?**

Laravel's `update()` handles PATCH by convention. Adding `replace()` for PUT gives each verb its own named method with its own request class and its own validation rules. No conditional logic needed inside the method itself — the route resolves which handler runs.

---

## Task 2 — Create ReplaceTicketRequest

Copy `StoreTicketRequest` as the starting point. The difference: no conditional — all fields are always required, including `author.data.id`.

```php
<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReplaceTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data.attributes.title'                     => ['required', 'string'],
            'data.attributes.description'               => ['required', 'string'],
            'data.attributes.status'                    => ['required', 'string', Rule::in(['A', 'C', 'H', 'X'])],
            'data.relationships.author.data.id'         => ['required', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'data.attributes.status' => 'The data.attributes.status value is invalid. Please use A, C, H, or X.',
        ];
    }
}
```

---

## Task 3 — Clean Up Route File: Single Middleware Group

Before adding the PUT route, clean up `routes/api_v1.php`. Instead of chaining `->middleware('auth:sanctum')` on each route line, wrap everything in a group:

```php
<?php

use App\Http\Controllers\Api\V1\{AuthorTicketsController, AuthorsController, TicketController};
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('tickets', TicketController::class)
        ->except('update');

    Route::put('tickets/{ticket}', [TicketController::class, 'replace'])
        ->name('tickets.replace');

    Route::apiResource('authors', AuthorsController::class)
        ->except('update');

    Route::put('authors/{author}', [AuthorsController::class, 'replace'])
        ->name('authors.replace');

    Route::apiResource('authors.tickets', AuthorTicketsController::class)
        ->scoped(['ticket' => 'id'])
        ->except('update');

    Route::put('authors/{author}/tickets/{ticket}', [AuthorTicketsController::class, 'replace'])
        ->name('authors.tickets.replace');

});
```

**`->except('update')`** removes the PATCH route from the `apiResource` registration so you can add it back manually later (Episode 16). Without this, Laravel registers PATCH pointing at the `update()` method, but the `replace()` method for PUT needs to be added separately.

---

## Task 4 — Implement TicketController::replace()

```php
use App\Http\Requests\Api\V1\ReplaceTicketRequest;

public function replace(ReplaceTicketRequest $request, int $ticketId): TicketResource|JsonResponse
{
    try {
        $ticket = Ticket::findOrFail($ticketId);
    } catch (ModelNotFoundException) {
        return $this->error('Ticket cannot be found.', 404);
    }

    $ticket->update([
        'title'       => $request->input('data.attributes.title'),
        'description' => $request->input('data.attributes.description'),
        'status'      => $request->input('data.attributes.status'),
        'user_id'     => $request->input('data.relationships.author.data.id'),
    ]);

    return new TicketResource($ticket);
}
```

`update()` on an Eloquent model modifies and saves in one call. The model is fetched fresh from the DB before updating via `findOrFail`, so the returned resource reflects the saved state.

---

## Task 5 — Fix Description Visibility in TicketResource

After testing `replace()`, you will notice `description` is missing from the response. The `when()` condition was:

```php
// Before — description only on tickets.show
'description' => $this->when(
    $request->routeIs('tickets.show'),
    $this->description
),
```

The `replace` route is `tickets.replace`, not `tickets.show`. Description should be visible on every route *except* the index/collection routes:

```php
// After — description omitted only on index/collection routes
'description' => $this->when(
    ! $request->routeIs(['tickets.index', 'authors.tickets.index']),
    $this->description
),
```

**Rule:** description is expensive to include for lists (100 tickets × paragraph each). It is fine for any single-resource response — show, replace, update.

---

## Task 6 — Add replace() to AuthorTicketsController

```php
public function replace(ReplaceTicketRequest $request, int $authorId, int $ticketId): TicketResource|JsonResponse
{
    try {
        $ticket = Ticket::findOrFail($ticketId);
    } catch (ModelNotFoundException) {
        return $this->error('Ticket cannot be found.', 404);
    }

    if ($ticket->user_id !== $authorId) {
        return $this->error('Ticket cannot be found.', 404);
    }

    $ticket->update([
        'title'       => $request->input('data.attributes.title'),
        'description' => $request->input('data.attributes.description'),
        'status'      => $request->input('data.attributes.status'),
        'user_id'     => $request->input('data.relationships.author.data.id'),
    ]);

    return new TicketResource($ticket);
}
```

---

## Task 7 — Postman Test

### Setup

1. Create a ticket via POST first (note the ID returned)
2. New PUT request:
   - Method: `PUT`
   - URL: `http://localhost/api/v1/tickets/{id}`
   - Headers: `Accept: application/json`, `Content-Type: application/json`, `Authorization: Bearer {{bearer}}`
   - Body:

```json
{
    "data": {
        "attributes": {
            "title": "Changed title",
            "description": "Changed description",
            "status": "C"
        },
        "relationships": {
            "author": {
                "data": {
                    "id": 1
                }
            }
        }
    }
}
```

3. Send without `author.data.id` → expect validation error (field required)
4. Valid payload → expect updated `TicketResource` with `description` present

---

## Task 8 — Pest Tests

```php
use App\Models\{User, Ticket};

it('replaces a ticket with PUT', function () {
    $ticket = Ticket::factory()->create(['status' => 'A']);
    $user   = User::factory()->create();

    $this->actingAs(User::factory()->create())
        ->putJson("/api/v1/tickets/{$ticket->id}", [
            'data' => [
                'attributes'    => [
                    'title'       => 'Replaced title',
                    'description' => 'Replaced description',
                    'status'      => 'C',
                ],
                'relationships' => ['author' => ['data' => ['id' => $user->id]]],
            ],
        ])
        ->assertOk()
        ->assertJsonPath('data.attributes.title', 'Replaced title')
        ->assertJsonPath('data.attributes.status', 'C');
});

it('replace requires all fields', function () {
    $ticket = Ticket::factory()->create();

    $this->actingAs(User::factory()->create())
        ->putJson("/api/v1/tickets/{$ticket->id}", [
            'data' => ['attributes' => ['title' => 'Only title']],
        ])
        ->assertUnprocessable();
});

it('replace returns description in response', function () {
    $ticket = Ticket::factory()->create();
    $user   = User::factory()->create();

    $response = $this->actingAs(User::factory()->create())
        ->putJson("/api/v1/tickets/{$ticket->id}", [
            'data' => [
                'attributes'    => [
                    'title'       => 'Title',
                    'description' => 'My description',
                    'status'      => 'A',
                ],
                'relationships' => ['author' => ['data' => ['id' => $user->id]]],
            ],
        ])
        ->assertOk();

    expect($response->json('data.attributes.description'))->toBe('My description');
});
```

---

## Final Checklist

- [ ] `ReplaceTicketRequest` created with all fields required (no conditional route check)
- [ ] `routes/api_v1.php` uses single `Route::middleware()->group()` wrapping all routes
- [ ] `Route::apiResource()->except('update')` removes default PATCH route
- [ ] Manual `Route::put()` added for `tickets.replace`
- [ ] `TicketController::replace()` uses `findOrFail` + try/catch
- [ ] `description` in `TicketResource` now uses `when(!routeIs([index routes]))`
- [ ] `AuthorTicketsController::replace()` checks ticket belongs to author
- [ ] Manual `Route::put()` added for `authors.tickets.replace`
- [ ] All Pest tests passing

---

## What's Next (Episode 16)

PATCH requests — partial updates. The challenge: you don't know which fields the client sent. Building a `mappedAttributes()` helper on a base request class solves this cleanly.
