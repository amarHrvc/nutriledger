# Applying Granular Permissions — Task 19

> **Source:** Laravel API Masterclass — Episode 19
> **Created:** 2026-03-11
> **Run Time:** 11m 18s
> **Status:** Study Task

---

## Overview

Token abilities let you apply permissions at a fine-grained level — not just "can you update tickets?" but "can you update the `author_id` on a ticket?" Regular users with `UpdateOwnTicket` should not be able to reassign a ticket to a different user. The fix lives in `UpdateTicketRequest` using the `prohibited` validation rule. This episode also adds `CreateOwnTicket` to separate regular user ticket creation from manager ticket creation, fixes `StoreTicketRequest` to use the `exists` rule instead of a manual user lookup, and adds `prepareForValidation` to handle the author route automatically.

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| `prohibited` validation rule | Rejects the field entirely if it is present in the request |
| Conditional rule override | Adding `prohibited` to a specific field only when a given ability is present |
| `CreateOwnTicket` ability | Separate ability for regular users — ensures `author_id` must equal the authenticated user's ID |
| `exists:users,id` validation rule | Validates the provided author ID exists in the database — replaces manual `findOrFail` |
| `size:$id` rule | Forces the submitted value to be exactly the authenticated user's ID |
| `prepareForValidation()` | Lifecycle method on `FormRequest` — runs before validation to inject/transform data |
| `$this->merge()` | Adds data to the request inside `prepareForValidation()` |

---

## Task 1 — The Problem: Regular Users Should Not Change `author_id`

When a regular user sends a PATCH request they can include `data.relationships.author.data.id` in the body. The current `UpdateTicketRequest` does not block this. `mappedAttributes()` would then include `user_id` in the update array, reassigning the ticket.

There are two naive approaches, and why they fail:

**Drop from `mappedAttributes()` for regular users:**
```php
// Don't do this — it silently ignores the field
// The user still gets a 200 OK, which is misleading
if (!$user->tokenCan(Abilities::UpdateOwnTicket)) {
    $attributeMap['data.relationships.author.data.id'] = 'user_id';
}
```
Problem: if `author_id` was supplied and we silently drop it, we still process and return a 200 — the client thinks they changed it. That is an invalid request that should be rejected.

**The correct approach — `prohibited` rule in `UpdateTicketRequest`:**
If the field is present and the user shouldn't be able to send it, reject the whole request with a validation error.

---

## Task 2 — Add `prohibited` Rule to `UpdateTicketRequest`

```php
<?php

namespace App\Http\Requests\Api\V1;

use App\Permissions\V1\Abilities;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends BaseTicketRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'data.attributes.title'                     => ['sometimes', 'string'],
            'data.attributes.description'               => ['sometimes', 'string'],
            'data.attributes.status'                    => ['sometimes', 'string', Rule::in(['A', 'C', 'H', 'X'])],
            'data.relationships.author.data.id'         => ['sometimes', 'integer'],
        ];

        if ($this->user()->tokenCan(Abilities::UpdateOwnTicket)) {
            $rules['data.relationships.author.data.id'] = ['prohibited'];
        }

        return $rules;
    }
}
```

**`prohibited` explained:**
- If the field is absent → passes validation normally
- If the field is present → validation fails with a "prohibited" error
- The request is rejected before any update happens — correct behaviour

**Why not just drop the field from the map?**
- Dropping silently still processes the request — the client gets a 200 with the title changed but `author_id` ignored, which is misleading
- `prohibited` tells the client explicitly: "you should not have sent this field"

---

## Task 3 — Add `CreateOwnTicket` Ability

The `CreateTicket` ability was intended for managers. Regular users should have `CreateOwnTicket`, not `CreateTicket`. The distinction matters because the `StoreTicketRequest` needs to enforce that regular users can only create tickets for themselves.

In `app/Permissions/V1/Abilities.php`, add the new constant and update `getAbilities()`:

```php
final class Abilities
{
    // Manager abilities — any resource
    public const string CreateTicket  = 'ticket:create';
    public const string UpdateTicket  = 'ticket:update';
    public const string ReplaceTicket = 'ticket:replace';
    public const string DeleteTicket  = 'ticket:delete';

    // Regular user abilities — own tickets only
    public const string CreateOwnTicket  = 'ticket:own:create';
    public const string UpdateOwnTicket  = 'ticket:own:update';
    public const string DeleteOwnTicket  = 'ticket:own:delete';

    // User management abilities
    public const string CreateUser  = 'user:create';
    public const string UpdateUser  = 'user:update';
    public const string ReplaceUser = 'user:replace';
    public const string DeleteUser  = 'user:delete';

    public static function getAbilities(User $user): array
    {
        if ($user->is_manager) {
            return [
                self::CreateTicket,
                self::UpdateTicket,
                self::ReplaceTicket,
                self::DeleteTicket,
                self::CreateUser,
                self::UpdateUser,
                self::ReplaceUser,
                self::DeleteUser,
            ];
        }

        // Regular user — own tickets only
        // Do NOT assign '*' — it would grant every ability including manager ones
        return [
            self::CreateOwnTicket,
            self::UpdateOwnTicket,
            self::DeleteOwnTicket,
        ];
    }
}
```

---

## Task 4 — Fix `StoreTicketRequest`

**Current problem:** The controller manually calls `User::findOrFail($authorId)` to validate the user exists. This should be handled by validation rules.

**New approach:** Use `exists:users,id` and, for regular users, `size:$userId` to enforce that the submitted author matches the authenticated user.

```php
<?php

namespace App\Http\Requests\Api\V1;

use App\Permissions\V1\Abilities;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends BaseTicketRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();

        $rules = [
            'data.attributes.title'                     => ['required', 'string'],
            'data.attributes.description'               => ['required', 'string'],
            'data.attributes.status'                    => ['required', 'string', Rule::in(['A', 'C', 'H', 'X'])],
            'data.relationships.author.data.id'         => ['required', 'integer', 'exists:users,id'],
        ];

        if ($this->routeIs('tickets.store')) {
            if ($user->tokenCan(Abilities::CreateOwnTicket)) {
                $rules['data.relationships.author.data.id'][] = 'size:' . $user->id;
            }
        }

        return $rules;
    }
}
```

**How this works:**
- `exists:users,id` — validates that the provided author ID corresponds to a real user row (replaces the manual `try { User::findOrFail() }` in the controller)
- `size:$user->id` — for regular users, the submitted author ID must equal the authenticated user's own ID
- This produces two distinct validation errors when both fail: "ID is invalid" (not in users table) and "must be {id}" (exists but not yours)
- The `size:` rule on integers checks that the value equals the given number

**Remove the manual lookup from `TicketController::store()`:**

```php
// Before
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

// After
public function store(StoreTicketRequest $request): TicketResource|JsonResponse
{
    try {
        $this->isAble('store', Ticket::class);
    } catch (AuthorizationException) {
        return $this->error('You are not authorized to create that resource.', 401);
    }

    $ticket = Ticket::create($request->mappedAttributes());

    return new TicketResource($ticket);
}
```

The `ModelNotFoundException` catch is removed because `exists:users,id` now handles the user-not-found case as a validation error (422), not a 404.

---

## Task 5 — Update `TicketPolicy::store()`

Now that `CreateOwnTicket` exists as a distinct ability, update the policy:

```php
public function store(User $user): bool
{
    return $user->tokenCan(Abilities::CreateTicket)
        || $user->tokenCan(Abilities::CreateOwnTicket);
}
```

Also fix `TicketController::store()` to pass the model class string correctly:

```php
// Passing model class string (no existing model to pass for create):
$this->isAble('store', Ticket::class);
```

---

## Task 6 — Add `prepareForValidation` for the Author Route

When creating a ticket via `POST /api/v1/authors/{author}/tickets`, the `author_id` comes from the URL — not the JSON body. The validation rule `exists:users,id` and `size:$userId` expect the value to be in the request. Use `prepareForValidation()` to inject it:

```php
// In StoreTicketRequest:

protected function prepareForValidation(): void
{
    if ($this->routeIs('authors.tickets.store')) {
        $this->merge([
            'data' => array_merge($this->input('data', []), [
                'relationships' => [
                    'author' => [
                        'data' => [
                            'id' => $this->route('author'),
                        ],
                    ],
                ],
            ]),
        ]);
    }
}
```

**`prepareForValidation()` explained:**
- Runs before the `rules()` method
- `$this->merge()` adds data to the request's input
- `$this->route('author')` reads the `{author}` route parameter
- After merging, `data.relationships.author.data.id` is present in the request as if the client had sent it in the body
- The same `exists:users,id` and `size:` rules now apply cleanly to both routes

---

## Task 7 — Avoid the `*` Wildcard Ability

Add a comment inside `Abilities::getAbilities()` to document the danger:

```php
public static function getAbilities(User $user): array
{
    if ($user->is_manager) {
        return [
            self::CreateTicket,
            // ...
        ];
        // Do NOT assign '*' — tokenCan('*') returns true for ALL abilities,
        // including CreateOwnTicket, UpdateOwnTicket, etc.
        // This would let a manager bypass ownership checks that are meant for regular users.
    }
    // ...
}
```

**Why this matters concretely:**
If a manager token has `*`, then `$user->tokenCan(Abilities::UpdateOwnTicket)` returns `true`. That triggers the `prohibited` rule in `UpdateTicketRequest`, blocking the manager from changing `author_id` — even though managers are supposed to be allowed to do that. Explicit ability lists avoid this class of bug.

---

## Task 8 — Pest Tests

```php
use App\Models\{User, Ticket};
use App\Permissions\V1\Abilities;

it('regular user cannot change author_id via PATCH', function () {
    $user   = User::factory()->create();
    $other  = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->create();

    $token = $user->createToken('test', [Abilities::UpdateOwnTicket])->plainTextToken;

    $this->withToken($token)
        ->patchJson("/api/v1/tickets/{$ticket->id}", [
            'data' => [
                'relationships' => [
                    'author' => ['data' => ['id' => $other->id]],
                ],
            ],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['data.relationships.author.data.id']);
});

it('manager can change author_id via PATCH', function () {
    $manager = User::factory()->create(['is_manager' => true]);
    $owner   = User::factory()->create();
    $other   = User::factory()->create();
    $ticket  = Ticket::factory()->for($owner)->create();

    $token = $manager->createToken('test', [Abilities::UpdateTicket])->plainTextToken;

    $this->withToken($token)
        ->patchJson("/api/v1/tickets/{$ticket->id}", [
            'data' => [
                'relationships' => [
                    'author' => ['data' => ['id' => $other->id]],
                ],
            ],
        ])
        ->assertOk();
});

it('regular user can only create a ticket for themselves', function () {
    $user  = User::factory()->create();
    $other = User::factory()->create();

    $token = $user->createToken('test', [Abilities::CreateOwnTicket])->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/v1/tickets', [
            'data' => [
                'attributes'    => ['title' => 'T', 'description' => 'D', 'status' => 'A'],
                'relationships' => ['author' => ['data' => ['id' => $other->id]]],
            ],
        ])
        ->assertUnprocessable();
});

it('regular user can create a ticket for themselves', function () {
    $user = User::factory()->create();

    $token = $user->createToken('test', [Abilities::CreateOwnTicket])->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/v1/tickets', [
            'data' => [
                'attributes'    => ['title' => 'T', 'description' => 'D', 'status' => 'A'],
                'relationships' => ['author' => ['data' => ['id' => $user->id]]],
            ],
        ])
        ->assertCreated();
});

it('provides exists validation error for non-existent author id', function () {
    $user = User::factory()->create();

    $token = $user->createToken('test', [Abilities::CreateOwnTicket])->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/v1/tickets', [
            'data' => [
                'attributes'    => ['title' => 'T', 'description' => 'D', 'status' => 'A'],
                'relationships' => ['author' => ['data' => ['id' => 9999]]],
            ],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['data.relationships.author.data.id']);
});
```

---

## Final Checklist

- [ ] `UpdateTicketRequest::rules()` adds `prohibited` on `author.data.id` when user has `UpdateOwnTicket`
- [ ] `Abilities::CreateOwnTicket = 'ticket:own:create'` added as a constant
- [ ] `getAbilities()` assigns `CreateOwnTicket` (not `CreateTicket`) to regular users
- [ ] `TicketPolicy::store()` checks `CreateTicket || CreateOwnTicket`
- [ ] `StoreTicketRequest` uses `exists:users,id` instead of a manual user lookup
- [ ] `StoreTicketRequest` adds `size:$user->id` when user has `CreateOwnTicket`
- [ ] `TicketController::store()` removes `try { User::findOrFail() }` block
- [ ] `TicketController::store()` calls `$this->isAble('store', Ticket::class)` and catches `AuthorizationException`
- [ ] `StoreTicketRequest::prepareForValidation()` merges route author ID for `authors.tickets.store`
- [ ] No `*` wildcard assigned in `getAbilities()`
- [ ] All Pest tests passing

---

## What's Next (Episode 20)

The `AuthorTicketsController` needs the same policy checks and simplifications. The `firstOrFail` pattern replaces the two-step find-then-check ownership pattern, and `prepareForValidation` makes `mappedAttributes()` work correctly for the author route.
