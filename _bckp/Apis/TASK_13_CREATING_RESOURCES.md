# Creating Resources with POST Requests — Task 13

> **Source:** Laravel API Masterclass — Episode 13
> **Created:** 2026-03-11
> **Run Time:** 16m 41s
> **Status:** Study Task

---

## Overview

Fetching data is done. Now we handle writes. Creating a resource means: accept a structured JSON body, validate it, store it in the database, return the newly created resource. The request format mirrors the JSON:API response structure — the client sends data in the same shape the server returns it.

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| Nested validation keys | `data.attributes.title`, `data.relationships.author.data.id` |
| `Rule::in()` / `in:A,C,H,X` | Restricts a field to a set of allowed values |
| `messages()` on Form Request | Custom error messages per field/rule |
| Security through obscurity | Return 200 with error payload instead of 500 for server errors |
| `Ticket::create($array)` | Mass assignment — requires `$fillable` on the model |
| Conditional validation rules | Different rules depending on which route triggered the request |

---

## Task 1 — Understand the Request Body Format

### Goal
Define what the POST body should look like before writing any validation.

### JSON:API request convention

The request body mirrors the response payload. The client sends:

```json
{
    "data": {
        "attributes": {
            "title": "My first ticket",
            "description": "Something is broken.",
            "status": "A"
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

**Why this structure?**
- Keeps client and server speaking the same language
- Separates attributes from relationships cleanly
- `type` fields from the response are optional in requests — we skip them

**Dot notation in validation rules** — Laravel uses `.` to navigate nested arrays:
- `data.attributes.title` → `$request->input('data.attributes.title')`
- `data.relationships.author.data.id` → the author ID

---

## Task 2 — Write StoreTicketRequest

### Instructions

Open `app/Http/Requests/Api/V1/StoreTicketRequest.php`:

```php
<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'data.attributes.title'                  => ['required', 'string'],
            'data.attributes.description'            => ['required', 'string'],
            'data.attributes.status'                 => ['required', 'string', Rule::in(['A', 'C', 'H', 'X'])],
        ];

        if ($this->routeIs('tickets.store')) {
            $rules['data.relationships.author.data.id'] = ['required', 'integer'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'data.attributes.status' => 'The data.attributes.status value is invalid. Please use A, C, H, or X.',
        ];
    }
}
```

**Why `authorize()` returns `true`?**
The `auth:sanctum` middleware already rejects unauthenticated requests before the controller runs. Returning false here would block *every* request — including authenticated ones. Authorization of specific actions (e.g. "can this user create a ticket for another user?") happens in policies, not here.

**Why the conditional on route?**
When the client hits `POST /api/v1/tickets`, they must provide the `author.data.id`. When they hit `POST /api/v1/authors/{author}/tickets`, the author ID is in the URL — no need to send it in the body. One request class serves both routes.

**`Rule::in(['A', 'C', 'H', 'X'])`** — validates against an exact list of allowed values. The string shorthand `'in:A,C,H,X'` also works.

---

## Task 3 — Implement TicketController::store()

### Instructions

```php
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Models\{Ticket, User};
use Illuminate\Database\Eloquent\ModelNotFoundException;

public function store(StoreTicketRequest $request): JsonResponse|TicketResource
{
    try {
        $user = User::findOrFail($request->input('data.relationships.author.data.id'));
    } catch (ModelNotFoundException) {
        return $this->ok('User not found', [
            'error' => 'The provided user ID does not exist.',
        ]);
    }

    $ticket = Ticket::create([
        'title'       => $request->input('data.attributes.title'),
        'description' => $request->input('data.attributes.description'),
        'status'      => $request->input('data.attributes.status'),
        'user_id'     => $user->id,
    ]);

    return new TicketResource($ticket);
}
```

**Why `ok()` instead of a 404 for missing user?**

A `404` on a `POST /tickets` is semantically confusing — the tickets endpoint exists. The missing entity is the referenced user, which is an application-level issue, not a routing issue.

More importantly: security through obscurity. Attackers use automated tools watching for 500s and 404s that reveal internal structure. Returning a consistent 200 with a structured error in the payload hides implementation details. The message says enough for a developer to fix the issue without revealing database schema or IDs.

This is a design choice — not universal law. Many APIs do return 422/404 here. The key point is: be consistent throughout your API.

---

## Task 4 — Add `$fillable` to Ticket Model

Without `$fillable`, `Ticket::create()` throws a `MassAssignmentException`.

```php
// app/Models/Ticket.php
protected $fillable = [
    'title',
    'description',
    'status',
    'user_id',
];
```

---

## Task 5 — Create Ticket via AuthorTicketsController

### Goal
Support `POST /api/v1/authors/{author}/tickets` — author ID comes from URL, not body.

### Instructions

In `app/Http/Controllers/Api/V1/AuthorTicketsController.php`:

```php
public function store(StoreTicketRequest $request, int $authorId): TicketResource
{
    $ticket = Ticket::create([
        'title'       => $request->input('data.attributes.title'),
        'description' => $request->input('data.attributes.description'),
        'status'      => $request->input('data.attributes.status'),
        'user_id'     => $authorId,
    ]);

    return new TicketResource($ticket);
}
```

No `findOrFail` needed for the user — Laravel's route model binding already returns 404 if the author doesn't exist. The author ID from the URL is passed directly as `user_id`.

---

## Task 6 — Test in Postman

### Setup

New request in tickets collection:
- Method: `POST`
- URL: `http://localhost/api/v1/tickets`
- Headers: `Accept: application/json`, `Content-Type: application/json`, `Authorization: Bearer {{bearer}}`
- Body (raw JSON):

```json
{
    "data": {
        "attributes": {
            "title": "First ticket",
            "description": "This is the first ticket we created.",
            "status": "A"
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

### Test cases to run

1. Send with no body → expect validation errors for all required fields
2. Set `status` to `T` → expect custom message: "Please use A, C, H, or X"
3. Set `author.data.id` to 9999 → expect "The provided user ID does not exist"
4. Valid payload → expect 200 with `TicketResource` structure

---

## Task 7 — Pest Tests

```php
use App\Models\{User, Ticket};

it('creates a ticket with valid payload', function () {
    $user = User::factory()->create();

    $this->actingAs(User::factory()->create())
        ->postJson('/api/v1/tickets', [
            'data' => [
                'attributes' => [
                    'title'       => 'Test ticket',
                    'description' => 'Test description',
                    'status'      => 'A',
                ],
                'relationships' => [
                    'author' => ['data' => ['id' => $user->id]],
                ],
            ],
        ])
        ->assertOk()
        ->assertJsonPath('data.type', 'ticket')
        ->assertJsonPath('data.attributes.title', 'Test ticket');

    $this->assertDatabaseHas('tickets', ['title' => 'Test ticket']);
});

it('returns error for invalid status value', function () {
    $user = User::factory()->create();

    $this->actingAs(User::factory()->create())
        ->postJson('/api/v1/tickets', [
            'data' => [
                'attributes' => [
                    'title'       => 'Test',
                    'description' => 'Test',
                    'status'      => 'T',
                ],
                'relationships' => ['author' => ['data' => ['id' => $user->id]]],
            ],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['data.attributes.status']);
});

it('returns validation errors for missing fields', function () {
    $this->actingAs(User::factory()->create())
        ->postJson('/api/v1/tickets', [])
        ->assertUnprocessable();
});

it('creates ticket via author route without body relationships', function () {
    $author = User::factory()->create();

    $this->actingAs(User::factory()->create())
        ->postJson("/api/v1/authors/{$author->id}/tickets", [
            'data' => [
                'attributes' => [
                    'title'       => 'Author route ticket',
                    'description' => 'Created via author route',
                    'status'      => 'A',
                ],
            ],
        ])
        ->assertOk()
        ->assertJsonPath('data.attributes.title', 'Author route ticket');

    $this->assertDatabaseHas('tickets', [
        'title'   => 'Author route ticket',
        'user_id' => $author->id,
    ]);
});
```

---

## Final Checklist

- [ ] `StoreTicketRequest` has nested validation rules for `data.attributes.*` and `data.relationships.*`
- [ ] `status` uses `Rule::in(['A', 'C', 'H', 'X'])`
- [ ] `messages()` provides a custom message for invalid status
- [ ] `data.relationships.author.data.id` rule only added for `tickets.store` route
- [ ] `TicketController::store()` uses try/catch with `findOrFail` for user
- [ ] Missing user returns 200 with error payload (not 500)
- [ ] `Ticket::$fillable` includes `title`, `description`, `status`, `user_id`
- [ ] Returns `new TicketResource($ticket)`
- [ ] `AuthorTicketsController::store()` uses author ID from URL
- [ ] All Pest tests passing

---

## What's Next (Episode 14)

Deleting a resource. The challenge is controlling the response when the resource doesn't exist — Laravel's default 404 leaks too much information.
