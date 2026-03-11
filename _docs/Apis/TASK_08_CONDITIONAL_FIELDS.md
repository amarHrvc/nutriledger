# Conditionally Omitting and Including Data — Task 08

> **Source:** Laravel API Masterclass — Episode 08
> **Created:** 2026-03-10
> **Run Time:** 11m 35s
> **Status:** Study Task

---

## Overview

A collection response and a single-item response for the same resource often need different fields. The `description` of a ticket is useful when viewing one ticket in detail — but wasteful when listing 100 tickets. Creating two separate resource classes to handle this duplicates code. Laravel resources provide `when()` and `mergeWhen()` to conditionally include fields within a single resource class.

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| `$this->when($condition, $value)` | Include a field only when the condition is true |
| `$this->mergeWhen($condition, $array)` | Include multiple fields at once under one condition |
| `$request->routeIs('pattern')` | Check the current route name, supports wildcards |
| `whenLoaded($relation)` | Include a relationship only if it was eagerly loaded |
| `UserResource` | Resource class for the User model |
| `UsersController` | Resource controller for users, following the same pattern as TicketController |

---

## Task 1 — Conditionally Omit Description with `when()`

### Goal
Include `description` in `show` responses but exclude it from `index` (collection) responses.

### Key Concept — `when($condition, $value)`

The field is included in the array **only when** the condition evaluates to truthy. When false, the key is omitted entirely — it does not appear as `null`.

```php
'description' => $this->when(
    $request->routeIs('tickets.show'),
    $this->description
),
```

**`$request->routeIs('tickets.show')`** — checks whether the current request matches the named route. `Route::apiResource('tickets', ...)` generates the name `tickets.show` for the single-item endpoint.

### Instructions

Update `toArray()` in `TicketResource`:

```php
public function toArray(Request $request): array
{
    return [
        'type'       => 'ticket',
        'id'         => $this->id,
        'attributes' => [
            'title'       => $this->title,
            'description' => $this->when(
                $request->routeIs('tickets.show'),
                $this->description
            ),
            'status'    => $this->status,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ],
        // ... relationships, links
    ];
}
```

**Verify:**
- `GET /api/v1/tickets` → `description` key is absent
- `GET /api/v1/tickets/1` → `description` key is present

---

## Task 2 — Scaffold the UsersController

### Goal
Create a resource controller for users, following the same structure as `TicketController`.

### Instructions

```bash
php artisan make:controller Api/V1/UsersController --resource --model=User --requests --no-interaction
```

Move the generated requests to the versioned folder:

```
app/Http/Requests/StoreUserRequest.php   → app/Http/Requests/Api/V1/StoreUserRequest.php
app/Http/Requests/UpdateUserRequest.php  → app/Http/Requests/Api/V1/UpdateUserRequest.php
```

Update the namespace in each:

```php
namespace App\Http\Requests\Api\V1;
```

Update `use` statements in `UsersController`:

```php
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
```

Remove `create()` and `edit()` methods — APIs do not serve forms.

Add stub `index()` and `show()` implementations:

```php
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;

public function index(): AnonymousResourceCollection
{
    return UserResource::collection(User::paginate());
}

public function show(User $user): UserResource
{
    return new UserResource($user);
}
```

Register routes in `routes/api_v1.php`:

```php
use App\Http\Controllers\Api\V1\UsersController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tickets', TicketController::class);
    Route::apiResource('users',   UsersController::class);
});
```

---

## Task 3 — Create the UserResource

### Instructions

```bash
php artisan make:resource Api/V1/UserResource --no-interaction
```

Open `app/Http/Resources/Api/V1/UserResource.php`:

```php
public function toArray(Request $request): array
{
    return [
        'type'       => 'user',
        'id'         => $this->id,
        'attributes' => [
            'name'  => $this->name,
            'email' => $this->email,
            $this->mergeWhen(
                $request->routeIs('users.*'),
                [
                    'emailVerifiedAt' => $this->email_verified_at,
                    'createdAt'       => $this->created_at,
                    'updatedAt'       => $this->updated_at,
                ]
            ),
        ],
        'links' => [
            'self' => route('users.show', ['user' => $this->id]),
        ],
    ];
}
```

### Key Concept — `mergeWhen()` vs three separate `when()` calls

Without `mergeWhen()`, you would write:

```php
'emailVerifiedAt' => $this->when($request->routeIs('users.*'), $this->email_verified_at),
'createdAt'       => $this->when($request->routeIs('users.*'), $this->created_at),
'updatedAt'       => $this->when($request->routeIs('users.*'), $this->updated_at),
```

With `mergeWhen()`:

```php
$this->mergeWhen(
    $request->routeIs('users.*'),
    [
        'emailVerifiedAt' => $this->email_verified_at,
        'createdAt'       => $this->created_at,
        'updatedAt'       => $this->updated_at,
    ]
),
```

One condition, multiple fields. The entire block is included or excluded as a unit.

**`routeIs('users.*')` with wildcard** — matches `users.index`, `users.show`, `users.store`, `users.update`, `users.destroy`. All user routes include the extra fields; ticket routes referencing a user (via `includes`) do not.

---

## Task 4 — Include User in Ticket Resource with `whenLoaded()`

### Goal
Add user data into `TicketResource` responses, but only when the relationship was explicitly eager-loaded.

### Key Concept — `whenLoaded($relation)`

If you access `$this->user` inside a resource and the relationship was not loaded, Laravel fires an N+1 query for every ticket in the collection. `whenLoaded()` prevents this: it only serialises the relationship if it was already loaded (via `with()` or `load()`). If not loaded, the key is omitted entirely.

### Instructions

First, ensure the `Ticket` model has the `user` relationship:

```php
// app/Models/Ticket.php
use Illuminate\Database\Eloquent\Relations\BelongsTo;

public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

Add the `includes` key to `TicketResource::toArray()`:

```php
'includes' => $this->whenLoaded('user', fn() => new UserResource($this->user)),
```

Full `toArray()` with all parts:

```php
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
            'data' => [
                'type' => 'user',
                'id'   => $this->user_id,
            ],
        ],
    ],
    'includes'      => $this->whenLoaded('user', fn() => new UserResource($this->user)),
    'links'         => [
        'self' => route('tickets.show', ['ticket' => $this->id]),
    ],
];
```

When `user` is not loaded → `includes` key is absent from the response.
When `user` is loaded → `includes` contains the transformed `UserResource`.

---

## Task 5 — Pest Tests

```php
use App\Models\{User, Ticket};

it('description is absent from ticket index', function () {
    Ticket::factory()->count(2)->create();

    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/tickets')
        ->assertOk()
        ->assertJsonMissing(['description']);
});

it('description is present on ticket show', function () {
    $ticket = Ticket::factory()->create();

    $this->actingAs(User::factory()->create())
        ->getJson("/api/v1/tickets/{$ticket->id}")
        ->assertOk()
        ->assertJsonPath('data.attributes.description', $ticket->description);
});

it('user index includes email_verified_at', function () {
    User::factory()->count(2)->create();

    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/users')
        ->assertOk()
        ->assertJsonPath('data.0.attributes.emailVerifiedAt', null);
});

it('ticket includes are absent when user not loaded', function () {
    $ticket = Ticket::factory()->create();

    $response = $this->actingAs(User::factory()->create())
        ->getJson("/api/v1/tickets/{$ticket->id}")
        ->assertOk();

    expect($response->json('data.includes'))->toBeNull();
});
```

---

## Final Checklist

- [ ] `description` uses `when($request->routeIs('tickets.show'), ...)` in `TicketResource`
- [ ] `description` absent from index response, present in show response
- [ ] `UsersController` created at `app/Http/Controllers/Api/V1/UsersController.php`
- [ ] `StoreUserRequest` and `UpdateUserRequest` in `Api/V1/` with correct namespaces
- [ ] `UserResource` created with `name`, `email` always, extra fields via `mergeWhen('users.*')`
- [ ] `users` route registered in `api_v1.php`
- [ ] `Ticket` model has `user()` `BelongsTo` relationship
- [ ] `TicketResource` has `includes` using `whenLoaded('user', ...)`
- [ ] All Pest tests passing

---

## What's Next (Episode 09)

Currently, includes are hardcoded — the ticket controller always loads (or never loads) the user. The next episode makes includes **opt-in via a query parameter**: `?include=author`.
