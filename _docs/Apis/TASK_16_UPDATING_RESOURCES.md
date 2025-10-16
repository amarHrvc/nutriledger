# Updating Resources with PATCH Requests — Task 16

> **Source:** Laravel API Masterclass — Episode 16
> **Created:** 2026-03-11
> **Run Time:** 10m 26s
> **Status:** Study Task

---

## Overview

PATCH is partial — the client sends only the fields it wants to change. The controller cannot assume all fields are present. The solution is a `mappedAttributes()` helper that iterates over a known map of input keys to model columns, includes only what was actually provided, and returns the array to pass to `update()`. This helper lives on a `BaseTicketRequest` class shared across all three ticket request classes.

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| `mappedAttributes()` | Builds an update array from only the fields the client provided |
| `$request->has($key)` | True if the key is present in the request, even if null |
| `BaseTicketRequest` | Shared base class — holds `mappedAttributes()` and `messages()` |
| `sometimes` validation rule | Field is only validated if it is present in the request |
| `UpdateTicketRequest` | Uses `sometimes` instead of `required` |

---

## Task 1 — The Problem with Partial Updates

With a PUT (replace), every field is required, so you can safely do:

```php
$ticket->update([
    'title'       => $request->input('data.attributes.title'),
    'description' => $request->input('data.attributes.description'),
    'status'      => $request->input('data.attributes.status'),
    'user_id'     => $request->input('data.relationships.author.data.id'),
]);
```

With PATCH, the client might only send `status`. If you include `title` in the update array, it becomes `null` and overwrites the existing value. You need to build the array dynamically — include only what was provided.

---

## Task 2 — Create BaseTicketRequest

### Goal
Extract the shared `messages()` method and add `mappedAttributes()` to a base class that all three request classes extend.

### Instructions

Create `app/Http/Requests/Api/V1/BaseTicketRequest.php`:

```php
<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseTicketRequest extends FormRequest
{
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

    public function messages(): array
    {
        return [
            'data.attributes.status' => 'The data.attributes.status value is invalid. Please use A, C, H, or X.',
        ];
    }
}
```

**`mappedAttributes()` explained:**

1. Define the attribute map — input key on the left, model column on the right
2. Accept `$otherAttributes` to merge in extra mappings (used in Episode 20 for URL-sourced params)
3. Iterate the map — `$this->has($inputKey)` checks if the client provided this key
4. If present, add `$attribute => $value` to the update array
5. Return only what was provided

**`$this->has()` vs `isset()`** — `has()` returns true if the key exists in the request input, even if its value is `null`. `isset()` would return false for null values, accidentally skipping intentional nullifications.

---

## Task 3 — Update the Three Request Classes

### StoreTicketRequest

```php
class StoreTicketRequest extends BaseTicketRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rules = [
            'data.attributes.title'                     => ['required', 'string'],
            'data.attributes.description'               => ['required', 'string'],
            'data.attributes.status'                    => ['required', 'string', Rule::in(['A', 'C', 'H', 'X'])],
        ];

        if ($this->routeIs('tickets.store')) {
            $rules['data.relationships.author.data.id'] = ['required', 'integer'];
        }

        return $rules;
    }
    // messages() inherited from BaseTicketRequest
}
```

### ReplaceTicketRequest

```php
class ReplaceTicketRequest extends BaseTicketRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'data.attributes.title'                     => ['required', 'string'],
            'data.attributes.description'               => ['required', 'string'],
            'data.attributes.status'                    => ['required', 'string', Rule::in(['A', 'C', 'H', 'X'])],
            'data.relationships.author.data.id'         => ['required', 'integer'],
        ];
    }
    // messages() inherited
}
```

### UpdateTicketRequest

```php
class UpdateTicketRequest extends BaseTicketRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'data.attributes.title'                     => ['sometimes', 'string'],
            'data.attributes.description'               => ['sometimes', 'string'],
            'data.attributes.status'                    => ['sometimes', 'string', Rule::in(['A', 'C', 'H', 'X'])],
            'data.relationships.author.data.id'         => ['sometimes', 'integer'],
        ];
    }
    // messages() inherited
}
```

**`sometimes`** — the field is only validated if it is present in the request. If absent, validation passes and `mappedAttributes()` simply won't include it.

---

## Task 4 — Implement TicketController::update()

```php
use App\Http\Requests\Api\V1\UpdateTicketRequest;

public function update(UpdateTicketRequest $request, int $ticketId): TicketResource|JsonResponse
{
    try {
        $ticket = Ticket::findOrFail($ticketId);
    } catch (ModelNotFoundException) {
        return $this->error('Ticket cannot be found.', 404);
    }

    $ticket->update($request->mappedAttributes());

    return new TicketResource($ticket);
}
```

One line does the update: `$request->mappedAttributes()` returns only the fields the client sent.

---

## Task 5 — Refactor store() and replace() to Use mappedAttributes()

Now that `mappedAttributes()` exists, clean up the earlier methods:

### store()

```php
public function store(StoreTicketRequest $request): TicketResource|JsonResponse
{
    try {
        $user = User::findOrFail($request->input('data.relationships.author.data.id'));
    } catch (ModelNotFoundException) {
        return $this->ok('User not found', ['error' => 'The provided user ID does not exist.']);
    }

    $ticket = Ticket::create($request->mappedAttributes());

    return new TicketResource($ticket);
}
```

### replace()

```php
public function replace(ReplaceTicketRequest $request, int $ticketId): TicketResource|JsonResponse
{
    try {
        $ticket = Ticket::findOrFail($ticketId);
    } catch (ModelNotFoundException) {
        return $this->error('Ticket cannot be found.', 404);
    }

    $ticket->update($request->mappedAttributes());

    return new TicketResource($ticket);
}
```

---

## Task 6 — Add PATCH Routes

Add the PATCH routes back to `routes/api_v1.php` (they were removed in Episode 15 via `->except('update')`):

```php
Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('tickets', TicketController::class)
        ->except('update');

    Route::put('tickets/{ticket}', [TicketController::class, 'replace'])
        ->name('tickets.replace');

    Route::patch('tickets/{ticket}', [TicketController::class, 'update'])
        ->name('tickets.update');

    // ... authors routes follow the same pattern

});
```

---

## Task 7 — Implement AuthorTicketsController::update()

```php
use App\Http\Requests\Api\V1\UpdateTicketRequest;

public function update(UpdateTicketRequest $request, int $authorId, int $ticketId): TicketResource|JsonResponse
{
    try {
        $ticket = Ticket::findOrFail($ticketId);
    } catch (ModelNotFoundException) {
        return $this->error('Ticket cannot be found.', 404);
    }

    if ($ticket->user_id !== $authorId) {
        return $this->error('Ticket cannot be found.', 404);
    }

    $ticket->update($request->mappedAttributes());

    return new TicketResource($ticket);
}
```

---

## Task 8 — Postman Test

### Setup

Copy the PUT request, change type to `PATCH`. Remove most body fields — keep only what you want to change:

```json
{
    "data": {
        "attributes": {
            "title": "Changed title three",
            "status": "C"
        }
    }
}
```

Send → expect updated title and status, description unchanged.

Then GET the ticket to confirm only the provided fields changed.

---

## Task 9 — Pest Tests

```php
use App\Models\{User, Ticket};

it('updates only provided fields with PATCH', function () {
    $ticket = Ticket::factory()->create([
        'title'  => 'Original title',
        'status' => 'A',
    ]);

    $this->actingAs(User::factory()->create())
        ->patchJson("/api/v1/tickets/{$ticket->id}", [
            'data' => ['attributes' => ['status' => 'C']],
        ])
        ->assertOk()
        ->assertJsonPath('data.attributes.status', 'C');

    // Title unchanged
    $this->assertDatabaseHas('tickets', [
        'id'     => $ticket->id,
        'title'  => 'Original title',
        'status' => 'C',
    ]);
});

it('patch validates provided fields only', function () {
    $ticket = Ticket::factory()->create();

    $this->actingAs(User::factory()->create())
        ->patchJson("/api/v1/tickets/{$ticket->id}", [
            'data' => ['attributes' => ['status' => 'INVALID']],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['data.attributes.status']);
});

it('patch with no fields does not fail', function () {
    $ticket = Ticket::factory()->create(['title' => 'Unchanged']);

    $this->actingAs(User::factory()->create())
        ->patchJson("/api/v1/tickets/{$ticket->id}", ['data' => ['attributes' => []]])
        ->assertOk();

    $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'title' => 'Unchanged']);
});
```

---

## Final Checklist

- [ ] `BaseTicketRequest` created with `mappedAttributes()` and `messages()`
- [ ] `StoreTicketRequest`, `ReplaceTicketRequest`, `UpdateTicketRequest` extend `BaseTicketRequest`
- [ ] `UpdateTicketRequest` uses `sometimes` instead of `required`
- [ ] `TicketController::update()` uses `UpdateTicketRequest` and `$request->mappedAttributes()`
- [ ] `store()` and `replace()` refactored to use `mappedAttributes()`
- [ ] PATCH routes added back to `api_v1.php`
- [ ] `AuthorTicketsController::update()` implemented
- [ ] Only sent fields are updated — existing fields unchanged
- [ ] All Pest tests passing

---

## What's Next (Episode 17)

Authorization — not just "is the user authenticated?" but "is this specific user allowed to do this to this specific resource?" Policies solve this cleanly.
