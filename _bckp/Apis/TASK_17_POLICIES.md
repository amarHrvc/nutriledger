# Using Policies for User Authorization — Task 17

> **Source:** Laravel API Masterclass — Episode 17
> **Created:** 2026-03-11
> **Run Time:** 7m 45s
> **Status:** Study Task

---

## Overview

Authentication answers "who are you?" Authorization answers "what are you allowed to do?" Sanctum handles authentication. Policies handle authorization. They keep all permission logic for a resource in one place, separate from the controller. This episode sets up a versioned `TicketPolicy` and an `isAble()` helper on `ApiController` that makes calling policies clean and version-aware.

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| Laravel Policy | Class with methods matching controller actions — each method returns bool |
| `$this->authorize($ability, $model)` | Throws `AuthorizationException` if policy returns false |
| `$policyClass` on controller | Tells `isAble()` which policy version to use |
| `isAble($ability, $model)` | Wrapper around `authorize()` that passes the versioned policy class |
| `AuthorizationException` | Thrown when `authorize()` fails — catch it to return a structured response |

---

## Task 1 — Why Policies?

### The naive approach

```php
// In the controller — quickly becomes messy
public function update(UpdateTicketRequest $request, int $ticketId): ...
{
    $ticket = Ticket::findOrFail($ticketId);

    if ($request->user()->id !== $ticket->user_id) {
        return $this->error('You are not authorized to update that resource.', 401);
    }

    // ... update logic
}
```

This works for one check. Add roles, token abilities, and admin overrides and the controller bloats fast. More importantly, the same check needs to exist in `replace()`, `destroy()`, and `AuthorTicketsController`. Duplication leads to inconsistency.

### Policies

```php
// TicketPolicy::update()
public function update(User $user, Ticket $ticket): bool
{
    return $user->id === $ticket->user_id;
}

// In the controller — one call, all logic in the policy
$this->isAble('update', $ticket);
```

All authorization rules for tickets are in `TicketPolicy`. When requirements change, you change one file.

---

## Task 2 — Create the Versioned TicketPolicy

```bash
php artisan make:policy Api/V1/TicketPolicy --no-interaction
```

This creates `app/Policies/Api/V1/TicketPolicy.php`.

Add the `update` method:

```php
<?php

namespace App\Policies\Api\V1;

use App\Models\{Ticket, User};

class TicketPolicy
{
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->user_id;
    }
}
```

More policy methods will be added in Episode 18.

---

## Task 3 — Register the Policy

Open `app/Providers/AuthServiceProvider.php` (or in Laravel 12, the service provider in `bootstrap/providers.php`):

```php
use App\Models\Ticket;
use App\Policies\Api\V1\TicketPolicy;

protected $policies = [
    Ticket::class => TicketPolicy::class,
];
```

**Version problem:** If you later add `V2\TicketPolicy`, both versions are mapped to the same `Ticket` model. Laravel's automatic policy discovery would not know which version to use for a given request.

This is solved in the next task.

---

## Task 4 — Add `isAble()` to ApiController

### The problem

`$this->authorize('update', $ticket)` uses the policy registered for the `Ticket` model — but that could be V1 or V2. To be explicit, pass an array with the model and the policy class:

```php
$this->authorize('update', [$ticket, TicketPolicy::class]);
```

But repeating this in every controller method across every versioned controller is verbose. Wrap it:

### Instructions

Update `app/Http/Controllers/Api/V1/ApiController.php`:

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;

class ApiController extends Controller
{
    use ApiResponses;

    protected string $policyClass;

    public function include(string $relationship): bool
    {
        $param = request()->get('include');

        if (! isset($param)) {
            return false;
        }

        $includeValues = explode(',', strtolower($param));

        return in_array(strtolower($relationship), $includeValues);
    }

    public function isAble(string $ability, mixed $targetModel): void
    {
        $this->authorize($ability, [$targetModel, $this->policyClass]);
    }
}
```

**`$this->policyClass`** — declared on `ApiController`, set by each subclass. `TicketController` sets it to `TicketPolicy::class`. If a V2 controller existed, it would set it to `V2\TicketPolicy::class`. The `isAble()` call stays identical in both versions.

**`isAble()` throws, not returns** — `$this->authorize()` throws `AuthorizationException` when the policy returns false. It does not return a boolean. You catch this exception to return your structured error response.

---

## Task 5 — Set policyClass on TicketController

```php
use App\Policies\Api\V1\TicketPolicy;

class TicketController extends ApiController
{
    protected string $policyClass = TicketPolicy::class;

    // ...
}
```

---

## Task 6 — Apply isAble() in update()

```php
use Illuminate\Auth\Access\AuthorizationException;

public function update(UpdateTicketRequest $request, int $ticketId): TicketResource|JsonResponse
{
    try {
        $ticket = Ticket::findOrFail($ticketId);
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

The two catch blocks handle the two failure modes independently with distinct messages.

---

## Task 7 — Pest Tests

```php
use App\Models\{User, Ticket};

it('allows user to update their own ticket', function () {
    $user   = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->create(['status' => 'A']);

    $this->actingAs($user)
        ->patchJson("/api/v1/tickets/{$ticket->id}", [
            'data' => ['attributes' => ['status' => 'C']],
        ])
        ->assertOk()
        ->assertJsonPath('data.attributes.status', 'C');
});

it('prevents user from updating another users ticket', function () {
    $owner  = User::factory()->create();
    $other  = User::factory()->create();
    $ticket = Ticket::factory()->for($owner)->create();

    $this->actingAs($other)
        ->patchJson("/api/v1/tickets/{$ticket->id}", [
            'data' => ['attributes' => ['status' => 'C']],
        ])
        ->assertStatus(401)
        ->assertJsonPath('message', 'You are not authorized to update that resource.');
});
```

---

## Final Checklist

- [ ] `app/Policies/Api/V1/TicketPolicy.php` created
- [ ] `TicketPolicy::update()` checks `$user->id === $ticket->user_id`
- [ ] Policy registered in `AuthServiceProvider::$policies`
- [ ] `ApiController::isAble()` calls `$this->authorize($ability, [$model, $this->policyClass])`
- [ ] `ApiController::$policyClass` property declared
- [ ] `ApiResponses` trait used on `ApiController`
- [ ] `TicketController::$policyClass = TicketPolicy::class` set
- [ ] `update()` calls `$this->isAble('update', $ticket)`
- [ ] `AuthorizationException` caught, returns 401 with structured message
- [ ] All Pest tests passing

---

## What's Next (Episode 18)

Policies answer "can this user do this?" but not "is this user a manager who can edit anyone's tickets?" Token abilities make per-user permission granularity possible without a full role system.
