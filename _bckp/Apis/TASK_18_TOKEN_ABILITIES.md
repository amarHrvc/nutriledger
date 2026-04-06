# Controlling Access with Token Abilities — Task 18

> **Source:** Laravel API Masterclass — Episode 18
> **Created:** 2026-03-11
> **Run Time:** 13m 01s
> **Status:** Study Task

---

## Overview

A ticket-owner check in a policy is good but not enough. Managers should be able to edit any ticket. Admins too. Adding more user types means adding more `if` branches — an unsustainable pattern. Sanctum token abilities solve this: when a token is issued at login, it carries a list of abilities. The policy checks those abilities, not a hardcoded role. A dedicated `Abilities` class holds the ability name strings as constants — preventing typos throughout the codebase.

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| Token abilities | Array of string permissions assigned when creating a token |
| `$user->tokenCan($ability)` | Returns true if the current token has that ability |
| `Abilities` class | Final class with string constants — prevents hardcoding ability strings |
| `getAbilities(User $user)` | Static method returning the right ability set based on user type |
| `is_manager` column | Boolean on `users` — determines which ability set is assigned at login |
| Wildcard `*` ability | Grants everything — **avoid assigning this** as it defeats granular checks |

---

## Task 1 — Create the Abilities Class

### Goal
Define all ability names as constants in one place. Never type ability strings directly in controllers or policies.

### Instructions

Create `app/Permissions/V1/Abilities.php`:

```php
<?php

namespace App\Permissions\V1;

use App\Models\User;

final class Abilities
{
    // Ticket abilities — any ticket
    public const string CreateTicket  = 'ticket:create';
    public const string UpdateTicket  = 'ticket:update';
    public const string ReplaceTicket = 'ticket:replace';
    public const string DeleteTicket  = 'ticket:delete';

    // Ticket abilities — own tickets only
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
        return [
            self::CreateOwnTicket,
            self::UpdateOwnTicket,
            self::DeleteOwnTicket,
        ];
    }
}
```

**Why `final`?** This class has no reason to be subclassed. `final` prevents accidental inheritance and signals that it is a pure constants/utility class.

**Why constants and not plain strings?**

```php
// Bad — typo-prone
$user->tokenCan('ticket:updat'); // no error, silently fails

// Good — caught by IDE and static analysis
$user->tokenCan(Abilities::UpdateTicket);
```

**Never assign the `*` wildcard ability:**

```php
// Do NOT do this:
$user->createToken('token', ['*']);
```

If you assign `*`, then `$user->tokenCan('ticket:own:update')` returns `true` — because `*` matches everything. A manager with `*` would pass the `UpdateOwnTicket` policy check, which was designed to restrict regular users. This breaks the granularity you built. Define explicit ability lists instead.

---

## Task 2 — Add `is_manager` to the Users Table

### Migration

```bash
php artisan make:migration add_is_manager_to_users_table --no-interaction
```

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->boolean('is_manager')->default(false)->after('email');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('is_manager');
    });
}
```

### User model

Add to `$fillable` and `casts()`:

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'is_manager',
];

protected function casts(): array
{
    return [
        'is_manager'        => 'boolean',
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];
}
```

---

## Task 3 — Seed a Manager User

In `database/seeders/DatabaseSeeder.php`:

```php
use App\Models\{User, Ticket};
use Illuminate\Support\Facades\Hash;

public function run(): void
{
    // Manager user
    User::factory()->create([
        'name'       => 'Manager',
        'email'      => 'manager@manager.com',
        'password'   => Hash::make('password'),
        'is_manager' => true,
    ]);

    // Regular users
    $users = User::factory()->count(10)->create();

    Ticket::factory()->count(100)->recycle($users)->create();
}
```

Re-seed: `php artisan db:wipe && php artisan migrate && php artisan db:seed`

---

## Task 4 — Update Login to Assign Abilities

Update `AuthController::login()`:

```php
use App\Permissions\V1\Abilities;
use Carbon\Carbon;

public function login(LoginUserRequest $request): JsonResponse
{
    $request->validated();

    if (! Auth::attempt($request->only('email', 'password'))) {
        return $this->error('Invalid credentials.', 401);
    }

    $user = $request->user();

    return $this->ok('Authenticated.', [
        'token' => $user->createToken(
            'API token for ' . $user->email,
            Abilities::getAbilities($user),
            Carbon::now()->addMonth()
        )->plainTextToken,
    ]);
}
```

Now the token carries the appropriate abilities for this user type. A manager gets all ticket + user abilities. A regular user gets the three `own` abilities.

---

## Task 5 — Update TicketPolicy with Ability Checks

```php
<?php

namespace App\Policies\Api\V1;

use App\Models\{Ticket, User};
use App\Permissions\V1\Abilities;

class TicketPolicy
{
    public function store(User $user): bool
    {
        return $user->tokenCan(Abilities::CreateTicket)
            || $user->tokenCan(Abilities::CreateOwnTicket);
    }

    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->tokenCan(Abilities::UpdateTicket)) {
            return true;
        }

        if ($user->tokenCan(Abilities::UpdateOwnTicket)) {
            return $user->id === $ticket->user_id;
        }

        return false;
    }

    public function replace(User $user, Ticket $ticket): bool
    {
        return $user->tokenCan(Abilities::ReplaceTicket);
    }

    public function destroy(User $user, Ticket $ticket): bool
    {
        if ($user->tokenCan(Abilities::DeleteTicket)) {
            return true;
        }

        if ($user->tokenCan(Abilities::DeleteOwnTicket)) {
            return $user->id === $ticket->user_id;
        }

        return false;
    }
}
```

**`update` policy logic:**
- Manager token has `UpdateTicket` → can update any ticket → return `true`
- Regular user token has `UpdateOwnTicket` → can only update their own → check ownership
- Neither ability → `false`

**`replace` policy:**
- Regular users never get `ReplaceTicket` → only managers can replace tickets

---

## Task 6 — Apply isAble() in All TicketController Write Methods

```php
// store()
$this->isAble('store', Ticket::class);  // No existing ticket — pass the class

// update()
$this->isAble('update', $ticket);

// replace()
$this->isAble('replace', $ticket);

// destroy()
$this->isAble('destroy', $ticket);
```

**Passing `Ticket::class` for store** — when there is no existing model to pass (creating new), pass the model class string. Laravel's policy resolution accepts either an instance or a class name for model-less policy methods.

---

## Task 7 — Pest Tests

```php
use App\Models\{User, Ticket};
use App\Permissions\V1\Abilities;

it('regular user can update own ticket', function () {
    $user   = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->create();

    $token = $user->createToken('test', [Abilities::UpdateOwnTicket])->plainTextToken;

    $this->withToken($token)
        ->patchJson("/api/v1/tickets/{$ticket->id}", [
            'data' => ['attributes' => ['status' => 'C']],
        ])
        ->assertOk();
});

it('regular user cannot update another users ticket', function () {
    $owner  = User::factory()->create();
    $other  = User::factory()->create();
    $ticket = Ticket::factory()->for($owner)->create();

    $token = $other->createToken('test', [Abilities::UpdateOwnTicket])->plainTextToken;

    $this->withToken($token)
        ->patchJson("/api/v1/tickets/{$ticket->id}", [
            'data' => ['attributes' => ['status' => 'C']],
        ])
        ->assertStatus(401);
});

it('manager can update any ticket', function () {
    $manager = User::factory()->create(['is_manager' => true]);
    $owner   = User::factory()->create();
    $ticket  = Ticket::factory()->for($owner)->create();

    $token = $manager->createToken('test', [Abilities::UpdateTicket])->plainTextToken;

    $this->withToken($token)
        ->patchJson("/api/v1/tickets/{$ticket->id}", [
            'data' => ['attributes' => ['status' => 'C']],
        ])
        ->assertOk();
});

it('regular user cannot replace a ticket', function () {
    $user   = User::factory()->create();
    $ticket = Ticket::factory()->for($user)->create();

    $token = $user->createToken('test', [Abilities::UpdateOwnTicket])->plainTextToken;

    $this->withToken($token)
        ->putJson("/api/v1/tickets/{$ticket->id}", [
            'data' => [
                'attributes'    => ['title' => 'T', 'description' => 'D', 'status' => 'A'],
                'relationships' => ['author' => ['data' => ['id' => $user->id]]],
            ],
        ])
        ->assertStatus(401);
});
```

---

## Final Checklist

- [ ] `app/Permissions/V1/Abilities.php` created as `final` class with all string constants
- [ ] `getAbilities(User $user)` returns manager abilities or regular user abilities
- [ ] `is_manager` column added to `users` via migration
- [ ] `is_manager` in `$fillable` and cast to `boolean` in `User` model
- [ ] Manager seeded in `DatabaseSeeder`
- [ ] `AuthController::login()` passes `Abilities::getAbilities($user)` to `createToken()`
- [ ] `TicketPolicy` has `store`, `update`, `replace`, `destroy` methods using ability constants
- [ ] `TicketController` calls `isAble()` in all write methods
- [ ] Regular user cannot update/delete/replace tickets they don't own
- [ ] Manager can update/delete any ticket
- [ ] All Pest tests passing

---

## What's Next (Episode 19)

Granular permissions — regular users should not be able to change the `author_id` of a ticket even during a PATCH. The `UpdateTicketRequest` will prohibit that field based on which ability the token has.
