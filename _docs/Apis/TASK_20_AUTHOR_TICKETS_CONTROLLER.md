# Implementing the AuthorTicketsController — Task 20

> **Source:** Laravel API Masterclass — Episode 20
> **Created:** 2026-03-11
> **Run Time:** 11m 31s
> **Status:** Study Task

---

## Overview

The `AuthorTicketsController` handles ticket operations scoped to a specific author. It mirrors `TicketController` but with URL-sourced author IDs and ownership constraints. This episode completes the full CRUD for that controller: reordering parameters for consistency, using `firstOrFail` with a `where` clause to eliminate two-step ownership checks, adding policy checks to every write method, and catching `AuthorizationException` consistently. The `mappedAttributes($otherAttributes)` extension added in Episode 16 is put to use here for the `store` method.

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| `where(['id' => $id, 'user_id' => $authorId])->firstOrFail()` | Single query combining existence + ownership — cleaner than find-then-check |
| `$otherAttributes` in `mappedAttributes()` | Injects route parameters into the update array without modifying the base map |
| `prepareForValidation()` on `StoreTicketRequest` | Injects the URL author ID into the validated request data before rules run |
| `protected string $policyClass` | Set on `AuthorTicketsController` — inherited from `ApiController` |
| `AuthorizationException` catch per method | Every write method catches authorization failure independently |
| Parameter ordering convention | Request object is always the first parameter in controller methods |

---

## Task 1 — Reorder Parameters in `store()`

The existing `store()` had parameters in the wrong order. Convention across the whole API is `(Request $request, int $id, ...)`:

```php
// Before
public function store(int $authorId, StoreTicketRequest $request)

// After
public function store(StoreTicketRequest $request, int $authorId)
```

This aligns with `replace()`, `update()`, and `destroy()` in the same controller.

---

## Task 2 — Update `StoreTicketRequest::prepareForValidation()`

The `authorId` in the author route comes from the URL, not the request body. `prepareForValidation()` injects it into the right place in the request data so that existing validation rules — including `exists:users,id` and `size:$user->id` — work without modification.

The condition must cover both routes. Update `StoreTicketRequest`:

```php
protected function prepareForValidation(): void
{
    $authorId = match (true) {
        $this->routeIs('tickets.store')         => null,
        $this->routeIs('authors.tickets.store') => $this->route('author'),
        default                                  => null,
    };

    if ($authorId !== null) {
        $this->merge([
            'data' => array_merge($this->input('data', []), [
                'relationships' => [
                    'author' => [
                        'data' => ['id' => $authorId],
                    ],
                ],
            ]),
        ]);
    }
}
```

**Simpler version used in the episode:**

```php
protected function prepareForValidation(): void
{
    if ($this->routeIs('authors.tickets.store')) {
        $this->merge([
            'data' => [
                'relationships' => [
                    'author' => [
                        'data' => ['id' => $this->route('author')],
                    ],
                ],
            ],
        ]);
    }
}
```

After this, `data.relationships.author.data.id` is present and will be validated by the existing rules. The controller does not need to inject it manually.

---

## Task 3 — Implement `store()` with Policy Check

```php
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Models\Ticket;
use Illuminate\Auth\Access\AuthorizationException;

public function store(StoreTicketRequest $request, int $authorId): TicketResource|JsonResponse
{
    try {
        $this->isAble('store', Ticket::class);
    } catch (AuthorizationException) {
        return $this->error('You are not authorized to create that resource.', 401);
    }

    $ticket = Ticket::create(
        $request->mappedAttributes([
            'author' => 'user_id',
        ])
    );

    return new TicketResource($ticket);
}
```

**`mappedAttributes(['author' => 'user_id'])` explained:**

The base `$attributeMap` in `BaseTicketRequest` only knows about JSON body keys like `data.relationships.author.data.id`. The route parameter `{author}` is not in the JSON body — it is a route segment. After `prepareForValidation()` injects it, it is available as `data.relationships.author.data.id` in the request data, so the base map handles it without needing `$otherAttributes`.

However, if `prepareForValidation()` injects it as a different key, you can use `$otherAttributes` to map a route-parameter key to a model attribute:

```php
// Alternative approach — pass route param directly without prepareForValidation:
$ticket = Ticket::create(
    $request->mappedAttributes([
        'author' => 'user_id',
    ])
);
// This only works if 'author' is added to the request data via merge() beforehand.
```

The `array_merge` inside `mappedAttributes()` ensures anything in `$otherAttributes` overrides the base map:

```php
// BaseTicketRequest::mappedAttributes()
public function mappedAttributes(array $otherAttributes = []): array
{
    $attributeMap = array_merge([
        'data.attributes.title'             => 'title',
        'data.attributes.description'       => 'description',
        'data.attributes.status'            => 'status',
        'data.relationships.author.data.id' => 'user_id',
        'data.attributes.createdAt'         => 'created_at',
        'data.attributes.updatedAt'         => 'updated_at',
    ], $otherAttributes);

    $attributesToUpdate = [];

    foreach ($attributeMap as $inputKey => $attribute) {
        if ($this->has($inputKey)) {
            $attributesToUpdate[$attribute] = $this->input($inputKey);
        }
    }

    return $attributesToUpdate;
}
```

---

## Task 4 — Simplify `replace()` with `firstOrFail`

The previous implementation fetched the ticket, then manually checked `$ticket->user_id !== $authorId`. Replace with a single query:

```php
// Before — two-step check
$ticket = Ticket::findOrFail($ticketId);
if ($ticket->user_id !== $authorId) {
    return $this->error('Ticket cannot be found.', 404);
}

// After — single query
$ticket = Ticket::where(['id' => $ticketId, 'user_id' => $authorId])->firstOrFail();
```

**Full `replace()` method:**

```php
use App\Http\Requests\Api\V1\ReplaceTicketRequest;

public function replace(ReplaceTicketRequest $request, int $authorId, int $ticketId): TicketResource|JsonResponse
{
    try {
        $ticket = Ticket::where(['id' => $ticketId, 'user_id' => $authorId])->firstOrFail();
        $this->isAble('replace', $ticket);
    } catch (ModelNotFoundException) {
        return $this->error('Ticket cannot be found.', 404);
    } catch (AuthorizationException) {
        return $this->error('You are not authorized to update that resource.', 401);
    }

    $ticket->update($request->mappedAttributes());

    return new TicketResource($ticket);
}
```

**Why `firstOrFail` with `where` is better:**
- One round-trip to the database instead of two
- The ticket is only returned if both conditions are met — no possibility of accidentally exposing a ticket that exists but belongs to a different author
- Cleaner code — no conditional after the fetch

---

## Task 5 — Implement `update()` with Policy Check

```php
use App\Http\Requests\Api\V1\UpdateTicketRequest;

public function update(UpdateTicketRequest $request, int $authorId, int $ticketId): TicketResource|JsonResponse
{
    try {
        $ticket = Ticket::where(['id' => $ticketId, 'user_id' => $authorId])->firstOrFail();
        $this->isAble('update', $ticket);
    } catch (ModelNotFoundException) {
        return $this->error('Ticket cannot be found.', 404);
    } catch (AuthorizationException) {
        return $this->error('You are not authorized to update that resource.', 401);
    }

    $ticket->update($request->mappedAttributes());

    return new TicketResource($ticket);
}
```

---

## Task 6 — Implement `destroy()` with Policy Check

```php
public function destroy(int $authorId, int $ticketId): JsonResponse
{
    try {
        $ticket = Ticket::where(['id' => $ticketId, 'user_id' => $authorId])->firstOrFail();
        $this->isAble('destroy', $ticket);
    } catch (ModelNotFoundException) {
        return $this->error('Ticket cannot be found.', 404);
    } catch (AuthorizationException) {
        return $this->error('You are not authorized to delete that resource.', 401);
    }

    $ticket->delete();

    return $this->ok('Ticket successfully deleted.');
}
```

---

## Task 7 — Set `$policyClass` on `AuthorTicketsController`

`isAble()` on `ApiController` reads `$this->policyClass`. Add it to `AuthorTicketsController`:

```php
use App\Policies\Api\V1\TicketPolicy;

class AuthorTicketsController extends ApiController
{
    protected string $policyClass = TicketPolicy::class;

    // ...
}
```

---

## Task 8 — Full `AuthorTicketsController` (after all changes)

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\{ReplaceTicketRequest, StoreTicketRequest, UpdateTicketRequest};
use App\Models\Ticket;
use App\Policies\Api\V1\TicketPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class AuthorTicketsController extends ApiController
{
    protected string $policyClass = TicketPolicy::class;

    public function index(int $authorId): mixed
    {
        // ... same as before — filter + paginate for the given author
    }

    public function store(StoreTicketRequest $request, int $authorId): TicketResource|JsonResponse
    {
        try {
            $this->isAble('store', Ticket::class);
        } catch (AuthorizationException) {
            return $this->error('You are not authorized to create that resource.', 401);
        }

        $ticket = Ticket::create($request->mappedAttributes());

        return new TicketResource($ticket);
    }

    public function show(int $authorId, int $ticketId): TicketResource|JsonResponse
    {
        try {
            $ticket = Ticket::where(['id' => $ticketId, 'user_id' => $authorId])->firstOrFail();
        } catch (ModelNotFoundException) {
            return $this->error('Ticket cannot be found.', 404);
        }

        return new TicketResource($ticket);
    }

    public function replace(ReplaceTicketRequest $request, int $authorId, int $ticketId): TicketResource|JsonResponse
    {
        try {
            $ticket = Ticket::where(['id' => $ticketId, 'user_id' => $authorId])->firstOrFail();
            $this->isAble('replace', $ticket);
        } catch (ModelNotFoundException) {
            return $this->error('Ticket cannot be found.', 404);
        } catch (AuthorizationException) {
            return $this->error('You are not authorized to update that resource.', 401);
        }

        $ticket->update($request->mappedAttributes());

        return new TicketResource($ticket);
    }

    public function update(UpdateTicketRequest $request, int $authorId, int $ticketId): TicketResource|JsonResponse
    {
        try {
            $ticket = Ticket::where(['id' => $ticketId, 'user_id' => $authorId])->firstOrFail();
            $this->isAble('update', $ticket);
        } catch (ModelNotFoundException) {
            return $this->error('Ticket cannot be found.', 404);
        } catch (AuthorizationException) {
            return $this->error('You are not authorized to update that resource.', 401);
        }

        $ticket->update($request->mappedAttributes());

        return new TicketResource($ticket);
    }

    public function destroy(int $authorId, int $ticketId): JsonResponse
    {
        try {
            $ticket = Ticket::where(['id' => $ticketId, 'user_id' => $authorId])->firstOrFail();
            $this->isAble('destroy', $ticket);
        } catch (ModelNotFoundException) {
            return $this->error('Ticket cannot be found.', 404);
        } catch (AuthorizationException) {
            return $this->error('You are not authorized to delete that resource.', 401);
        }

        $ticket->delete();

        return $this->ok('Ticket successfully deleted.');
    }
}
```

---

## Task 9 — Pest Tests

```php
use App\Models\{User, Ticket};
use App\Permissions\V1\Abilities;

it('regular user can create a ticket via author route', function () {
    $user = User::factory()->create();

    $token = $user->createToken('test', [Abilities::CreateOwnTicket])->plainTextToken;

    $this->withToken($token)
        ->postJson("/api/v1/authors/{$user->id}/tickets", [
            'data' => [
                'attributes' => [
                    'title'       => 'My ticket',
                    'description' => 'Description',
                    'status'      => 'A',
                ],
            ],
        ])
        ->assertCreated();
});

it('regular user cannot create a ticket for a different author via author route', function () {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $token = $user->createToken('test', [Abilities::CreateOwnTicket])->plainTextToken;

    $this->withToken($token)
        ->postJson("/api/v1/authors/{$other->id}/tickets", [
            'data' => [
                'attributes' => [
                    'title'       => 'Their ticket',
                    'description' => 'D',
                    'status'      => 'A',
                ],
            ],
        ])
        ->assertUnprocessable();
});

it('returns 404 when ticket does not belong to author', function () {
    $owner  = User::factory()->create();
    $other  = User::factory()->create();
    $ticket = Ticket::factory()->for($other)->create();

    $this->actingAs($owner)
        ->patchJson("/api/v1/authors/{$owner->id}/tickets/{$ticket->id}", [
            'data' => ['attributes' => ['status' => 'C']],
        ])
        ->assertNotFound();
});

it('regular user cannot delete another authors ticket via author route', function () {
    $owner  = User::factory()->create();
    $other  = User::factory()->create();
    $ticket = Ticket::factory()->for($other)->create();

    $token = $owner->createToken('test', [Abilities::DeleteOwnTicket])->plainTextToken;

    $this->withToken($token)
        ->deleteJson("/api/v1/authors/{$owner->id}/tickets/{$ticket->id}")
        ->assertNotFound();

    $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);
});
```

---

## Final Checklist

- [ ] `AuthorTicketsController` parameter order: `(Request $request, int $authorId, int $ticketId)`
- [ ] `protected string $policyClass = TicketPolicy::class` set on the controller
- [ ] `StoreTicketRequest::prepareForValidation()` injects route `{author}` for `authors.tickets.store`
- [ ] `store()` calls `isAble('store', Ticket::class)` and catches `AuthorizationException`
- [ ] `replace()`, `update()`, `destroy()` use `where(['id' => $ticketId, 'user_id' => $authorId])->firstOrFail()`
- [ ] All write methods catch both `ModelNotFoundException` and `AuthorizationException`
- [ ] Authorization error messages are specific: "create", "update", "delete"
- [ ] No more manual two-step ownership check (find then compare `user_id`)
- [ ] All Pest tests passing

---

## What's Next (Episode 21)

User management — a `UsersController` to create, update, replace, and delete user accounts. Only managers have access. The `AuthorsController` is scoped to only return users who have created tickets. `UserPolicy`, `BaseUserRequest`, and password hashing inside `mappedAttributes()`.
