# Designing Response Payloads — Task 07

> **Source:** Laravel API Masterclass — Episode 07
> **Created:** 2026-03-10
> **Run Time:** 11m 30s
> **Status:** Study Task

---

## Overview

Returning a raw Eloquent model directly from a route is a shortcut that creates real problems: sensitive columns leak into the response, field names are snake_case when JSON convention is camelCase, and you have no control over the structure. **Eloquent Resources** solve all of this — they are a transformation layer between your model and your JSON output.

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| Eloquent Resource | Class that transforms a model into a structured JSON payload |
| `toArray()` | Method where you define the response structure |
| `TicketResource::collection($tickets)` | Wraps a collection — used in `index()` |
| `new TicketResource($ticket)` | Wraps a single model — used in `show()` |
| JSON:API spec | A widely-used convention for structuring JSON API responses |
| `$wrap` | Static property to customise the top-level key (default: `data`) |
| `paginate()` | Returns a `LengthAwarePaginator` — resources wrap this automatically |
| Named routes | `route('tickets.show', $ticket)` uses `Route::apiResource` naming |

---

## Task 1 — Why Not Dump the Model Directly?

### Problem 1 — Sensitive data

A `User` model might have `password`, `remember_token`, `two_factor_secret` on it. Returning `User::all()` exposes all of those. You could add `$hidden`, but then you are mixing response logic into your model.

### Problem 2 — snake_case

Laravel models use `snake_case` (matching the database). JSON convention is `camelCase`. Clients consuming your API (especially JavaScript) expect `createdAt`, not `created_at`.

### Problem 3 — No structure

A raw model is a flat key-value dump. The JSON:API specification (and most well-designed APIs) have structure: a `type` field, an `id`, nested `attributes`, `relationships`, `links`. This is impossible to achieve by dumping the model.

---

## Task 2 — The JSON:API Document Structure

You do not have to follow JSON:API exactly, but adopting its core ideas gives you a consistent, professional structure.

**Single resource document:**

```json
{
    "data": {
        "type": "ticket",
        "id": 1,
        "attributes": {
            "title": "My ticket",
            "description": "Some description",
            "status": "A",
            "createdAt": "2024-01-01T00:00:00Z",
            "updatedAt": "2024-01-01T00:00:00Z"
        },
        "relationships": {
            "author": {
                "data": {
                    "type": "user",
                    "id": 3
                }
            }
        },
        "links": {
            "self": "http://localhost/api/v1/tickets/1"
        }
    }
}
```

**Collection document (with pagination):**

```json
{
    "data": [ ... ],
    "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
    "meta": { "current_page": 1, "total": 100, ... }
}
```

The `links` and `meta` on collections are added automatically by Laravel when you return a paginated resource.

---

## Task 3 — Create the TicketResource

### Instructions

```bash
php artisan make:resource Api/V1/TicketResource --no-interaction
```

This creates `app/Http/Resources/Api/V1/TicketResource.php`.

Open it and implement `toArray()`:

```php
<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    // Uncomment to change the wrapper key from 'data' to something else:
    // public static $wrap = 'ticket';

    public function toArray(Request $request): array
    {
        return [
            'type'          => 'ticket',
            'id'            => $this->id,
            'attributes'    => [
                'title'       => $this->title,
                'description' => $this->description,
                'status'      => $this->status,
                'createdAt'   => $this->created_at,
                'updatedAt'   => $this->updated_at,
            ],
            'relationships' => [
                'author' => [
                    'data' => [
                        'type' => 'user',
                        'id'   => $this->user_id,
                    ],
                ],
            ],
            'links'         => [
                'self' => route('tickets.show', ['ticket' => $this->id]),
            ],
        ];
    }
}
```

**`$this->property` inside a Resource** — the resource wraps the model. `$this` gives you access to all model attributes and relationships as if you were on the model itself.

**`route('tickets.show', $ticket)`** — `Route::apiResource('tickets', ...)` automatically names routes: `tickets.index`, `tickets.store`, `tickets.show`, `tickets.update`, `tickets.destroy`. Use these named routes to generate URLs — they stay correct even if the URL prefix changes.

**camelCase in attributes** — `created_at` on the model becomes `createdAt` in the JSON. The client receives consistent camelCase. The database column name is an implementation detail.

---

## Task 4 — Use the Resource in TicketController

### Instructions

Update `app/Http/Controllers/Api/V1/TicketController.php`:

```php
use App\Http\Resources\Api\V1\TicketResource;
use App\Models\Ticket;

public function index(): AnonymousResourceCollection
{
    return TicketResource::collection(Ticket::paginate());
}

public function show(Ticket $ticket): TicketResource
{
    return new TicketResource($ticket);
}
```

**`::collection()` vs `new TicketResource()`:**

| Method | Use for | Adds `data` array wrapper |
|---|---|---|
| `TicketResource::collection($tickets)` | Collections | Yes — array of transformed items |
| `new TicketResource($ticket)` | Single models | Yes — single transformed item |

**`paginate()` vs `all()`:**

- `Ticket::all()` — all rows, no pagination metadata
- `Ticket::paginate()` — 15 rows per page (default), adds `links` and `meta` to the response automatically when wrapped in a resource collection
- Change per-page: `Ticket::paginate(25)`

---

## Task 5 — Customise the Wrapper Key (Exercise)

The default top-level key is `data`. To understand how `$wrap` works, temporarily change it:

```php
public static $wrap = 'ticket';
```

Make a request — the response now has `"ticket": { ... }` instead of `"data": { ... }`.

Then revert it. The spec uses `data`. Leave it as the default.

---

## Task 6 — Pest Tests

```php
use App\Models\{User, Ticket};

it('ticket index returns paginated JSON:API structure', function () {
    Ticket::factory()->count(3)->create();

    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/tickets')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'type',
                    'id',
                    'attributes' => ['title', 'description', 'status', 'createdAt', 'updatedAt'],
                    'relationships' => ['author' => ['data' => ['type', 'id']]],
                    'links' => ['self'],
                ],
            ],
            'links',
            'meta',
        ]);
});

it('ticket show returns single JSON:API resource', function () {
    $ticket = Ticket::factory()->create();

    $this->actingAs(User::factory()->create())
        ->getJson("/api/v1/tickets/{$ticket->id}")
        ->assertOk()
        ->assertJsonPath('data.type', 'ticket')
        ->assertJsonPath('data.id', $ticket->id)
        ->assertJsonStructure([
            'data' => ['type', 'id', 'attributes', 'relationships', 'links'],
        ]);
});

it('ticket attributes use camelCase keys', function () {
    $ticket = Ticket::factory()->create();

    $response = $this->actingAs(User::factory()->create())
        ->getJson("/api/v1/tickets/{$ticket->id}");

    $attributes = $response->json('data.attributes');

    expect($attributes)->toHaveKeys(['createdAt', 'updatedAt'])
        ->and($attributes)->not->toHaveKey('created_at');
});
```

---

## Final Checklist

- [ ] `app/Http/Resources/Api/V1/TicketResource.php` created
- [ ] `toArray()` returns: `type`, `id`, `attributes`, `relationships`, `links`
- [ ] Attributes use camelCase keys
- [ ] `links.self` uses `route('tickets.show', ...)`
- [ ] `index()` uses `TicketResource::collection(Ticket::paginate())`
- [ ] `show()` uses `new TicketResource($ticket)`
- [ ] Response has `data`, `links`, `meta` wrapper keys on collection
- [ ] All Pest tests passing

---

## What's Next (Episode 08)

The `description` field is expensive to include on a collection response — clients listing tickets don't display it. The next episode covers `when()` and `mergeWhen()` to conditionally include or omit fields based on which route is being called.
