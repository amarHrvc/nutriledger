# Managing Users — Task 21

> **Source:** Laravel API Masterclass — Episode 21
> **Created:** 2026-03-11
> **Run Time:** 16m 38s
> **Status:** Study Task

---

## Overview

This episode adds full user CRUD to the API. The `AuthorsController` is narrowed to return only users who have actually created tickets (a join + distinct query). A separate `UsersController` handles creating, reading, updating, replacing, and deleting user accounts — restricted to managers via `UserPolicy`. Three request classes (`StoreUserRequest`, `ReplaceUserRequest`, `UpdateUserRequest`) share a `BaseUserRequest` that handles password hashing inside `mappedAttributes()`. `UserResource` is updated to expose `isManager`. The `User` model gets `is_manager` in `$fillable` and a boolean cast.

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| Join + `distinct()` for authors | Returns only users who have tickets — prevents listing every user as an author |
| `BaseUserRequest` | Shared base class with `mappedAttributes()` that bcrypts password before storing |
| Password hashing in `mappedAttributes()` | Intercepts the `password` attribute during iteration and wraps the value with `bcrypt()` |
| `UserPolicy` | Policy where every method only checks a single ability — no ownership complexity |
| `is_manager` in `$fillable` and `casts()` | Required for assignment via `create()`/`update()` and for returning `true`/`false` in JSON |
| `AuthorsController::except()` | Authors are read-only — no store/update/delete routes registered |

---

## Task 1 — Scope `AuthorsController::index()` to Ticket Creators Only

Currently `index()` returns every user. Authors should only be users who have created at least one ticket.

```php
// Before
public function index(): UserResourceCollection
{
    return UserResource::collection(
        User::filter(new AuthorFilter(request()))->paginate()
    );
}

// After
public function index(): UserResourceCollection
{
    return UserResource::collection(
        User::select('users.*')
            ->join('tickets', 'users.id', '=', 'tickets.user_id')
            ->filter(new AuthorFilter(request()))
            ->distinct()
            ->paginate()
    );
}
```

**Why `distinct()`?**
The join produces one row per ticket. A user with 10 tickets appears 10 times. `distinct()` collapses those into one row per user.

**Why `select('users.*')`?**
Without an explicit `select`, the joined columns from `tickets` could shadow `users` columns (e.g. both tables have `id`, `created_at`, `updated_at`). Selecting only `users.*` avoids column collisions.

---

## Task 2 — Create `UsersController`

Copy `AuthorsController` as the starting point and adapt:

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\{ReplaceUserRequest, StoreUserRequest, UpdateUserRequest};
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Policies\Api\V1\UserPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class UsersController extends ApiController
{
    protected string $policyClass = UserPolicy::class;

    public function index(): JsonResponse
    {
        return UserResource::collection(
            User::filter(new AuthorFilter(request()))->paginate()
        );
    }

    public function show(int $userId): UserResource|JsonResponse
    {
        try {
            $user = User::findOrFail($userId);
        } catch (ModelNotFoundException) {
            return $this->error('User cannot be found.', 404);
        }

        return new UserResource($user);
    }

    public function store(StoreUserRequest $request): UserResource|JsonResponse
    {
        try {
            $this->isAble('store', User::class);
        } catch (AuthorizationException) {
            return $this->error('You are not authorized to create that resource.', 401);
        }

        return new UserResource(User::create($request->mappedAttributes()));
    }

    public function replace(ReplaceUserRequest $request, int $userId): UserResource|JsonResponse
    {
        try {
            $user = User::findOrFail($userId);
            $this->isAble('replace', $user);
        } catch (ModelNotFoundException) {
            return $this->error('User cannot be found.', 404);
        } catch (AuthorizationException) {
            return $this->error('You are not authorized to update that resource.', 401);
        }

        $user->update($request->mappedAttributes());

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, int $userId): UserResource|JsonResponse
    {
        try {
            $user = User::findOrFail($userId);
            $this->isAble('update', $user);
        } catch (ModelNotFoundException) {
            return $this->error('User cannot be found.', 404);
        } catch (AuthorizationException) {
            return $this->error('You are not authorized to update that resource.', 401);
        }

        $user->update($request->mappedAttributes());

        return new UserResource($user);
    }

    public function destroy(int $userId): JsonResponse
    {
        try {
            $user = User::findOrFail($userId);
            $this->isAble('destroy', $user);
        } catch (ModelNotFoundException) {
            return $this->error('User cannot be found.', 404);
        } catch (AuthorizationException) {
            return $this->error('You are not authorized to delete that resource.', 401);
        }

        $user->delete();

        return $this->ok('User successfully deleted.');
    }
}
```

---

## Task 3 — Create `UserPolicy`

Unlike `TicketPolicy`, user management is manager-only — no ownership checks needed. Every method simply checks a single ability:

```php
<?php

namespace App\Policies\Api\V1;

use App\Models\User;
use App\Permissions\V1\Abilities;

class UserPolicy
{
    public function store(User $user): bool
    {
        return $user->tokenCan(Abilities::CreateUser);
    }

    public function update(User $user, User $model): bool
    {
        return $user->tokenCan(Abilities::UpdateUser);
    }

    public function replace(User $user, User $model): bool
    {
        return $user->tokenCan(Abilities::ReplaceUser);
    }

    public function destroy(User $user, User $model): bool
    {
        return $user->tokenCan(Abilities::DeleteUser);
    }
}
```

**Note on parameter naming:** The second parameter is typed as `User` — Laravel requires it to be a model instance for `authorize()` to resolve the policy correctly. Using `$model` avoids confusion with the `$user` authenticated user.

**No ownership check needed:** Only managers have `CreateUser`, `UpdateUser`, `ReplaceUser`, `DeleteUser` abilities. Regular users never have these, so the policy is just a single ability check per method.

---

## Task 4 — Register `UserPolicy`

In `app/Providers/AppServiceProvider.php` (or the equivalent service provider where `TicketPolicy` is registered):

```php
use App\Models\{Ticket, User};
use App\Policies\Api\V1\{TicketPolicy, UserPolicy};

protected $policies = [
    Ticket::class => TicketPolicy::class,
    User::class   => UserPolicy::class,
];
```

---

## Task 5 — Create `BaseUserRequest`

```php
<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseUserRequest extends FormRequest
{
    public function mappedAttributes(array $otherAttributes = []): array
    {
        $attributeMap = array_merge([
            'data.attributes.name'      => 'name',
            'data.attributes.email'     => 'email',
            'data.attributes.isManager' => 'is_manager',
            'data.attributes.password'  => 'password',
        ], $otherAttributes);

        $attributesToUpdate = [];

        foreach ($attributeMap as $inputKey => $attribute) {
            if ($this->has($inputKey)) {
                $value = $this->input($inputKey);

                if ($attribute === 'password') {
                    $value = bcrypt($value);
                }

                $attributesToUpdate[$attribute] = $value;
            }
        }

        return $attributesToUpdate;
    }
}
```

**Password hashing in `mappedAttributes()`:**

Clients always send passwords in plain text — the API is responsible for hashing before storage. Handling this inside `mappedAttributes()` means every request class that extends `BaseUserRequest` gets automatic hashing for free. There is no risk of accidentally storing a plain-text password through any user write endpoint.

**Why `bcrypt()` and not `Hash::make()`?**
Both work. `bcrypt()` is a Laravel helper that delegates to `Hash::make()` with the bcrypt driver. Either is correct — use whatever is consistent with the rest of the codebase.

---

## Task 6 — Create Request Classes

### `StoreUserRequest`

```php
<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Validation\Rule;

class StoreUserRequest extends BaseUserRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data.attributes.name'      => ['required', 'string'],
            'data.attributes.email'     => ['required', 'string', 'email'],
            'data.attributes.isManager' => ['required', 'boolean'],
            'data.attributes.password'  => ['required', 'string'],
        ];
    }
}
```

### `ReplaceUserRequest`

```php
<?php

namespace App\Http\Requests\Api\V1;

class ReplaceUserRequest extends BaseUserRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data.attributes.name'      => ['required', 'string'],
            'data.attributes.email'     => ['required', 'string', 'email'],
            'data.attributes.isManager' => ['required', 'boolean'],
            'data.attributes.password'  => ['required', 'string'],
        ];
    }
}
```

### `UpdateUserRequest`

```php
<?php

namespace App\Http\Requests\Api\V1;

class UpdateUserRequest extends BaseUserRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data.attributes.name'      => ['sometimes', 'string'],
            'data.attributes.email'     => ['sometimes', 'string', 'email'],
            'data.attributes.isManager' => ['sometimes', 'boolean'],
            'data.attributes.password'  => ['sometimes', 'string'],
        ];
    }
}
```

---

## Task 7 — Update `UserResource`

Add `isManager` to the resource output. Password must never be included:

```php
<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'type'       => 'user',
            'id'         => $this->id,
            'attributes' => [
                'name'      => $this->name,
                'email'     => $this->email,
                'isManager' => $this->is_manager,
            ],
        ];
    }
}
```

**Password is never included.** Even though the database stores a hash, the API should never return it. The resource simply omits the field.

---

## Task 8 — Update `User` Model

Add `is_manager` to `$fillable` and add a boolean cast:

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

**Why `is_manager` in `$fillable`?**
Without it, `User::create($request->mappedAttributes())` silently ignores the `is_manager` field — mass assignment protection strips it. The value would always default to `false` in the database regardless of what was sent.

**Why the boolean cast?**
Without it, the JSON response returns `0` or `1` (the raw database integer). With `'is_manager' => 'boolean'`, it returns `true`/`false` — the correct JSON type.

---

## Task 9 — Add User Routes

In `routes/api_v1.php`:

```php
use App\Http\Controllers\Api\V1\UsersController;

Route::middleware('auth:sanctum')->group(function () {

    // Authors — read-only (only users with tickets)
    Route::apiResource('authors', AuthorsController::class)
        ->except(['store', 'update', 'destroy']);

    Route::apiResource('authors.tickets', AuthorTicketsController::class)
        ->scoped(['ticket' => 'id'])
        ->except('update');

    Route::put('authors/{author}/tickets/{ticket}', [AuthorTicketsController::class, 'replace'])
        ->name('authors.tickets.replace');

    Route::patch('authors/{author}/tickets/{ticket}', [AuthorTicketsController::class, 'update'])
        ->name('authors.tickets.update');

    // Users — full CRUD for managers
    Route::apiResource('users', UsersController::class)
        ->except('update');

    Route::put('users/{user}', [UsersController::class, 'replace'])
        ->name('users.replace');

    Route::patch('users/{user}', [UsersController::class, 'update'])
        ->name('users.update');

    // Tickets
    Route::apiResource('tickets', TicketController::class)
        ->except('update');

    Route::put('tickets/{ticket}', [TicketController::class, 'replace'])
        ->name('tickets.replace');

    Route::patch('tickets/{ticket}', [TicketController::class, 'update'])
        ->name('tickets.update');

});
```

**`AuthorsController` restricted with `->except()`:**
Authors are derived from tickets data — they are not entities you create or delete directly. Only `index` and `show` make sense for the `authors` resource.

---

## Task 10 — Expected JSON Structure

### `POST /api/v1/users` request body

```json
{
    "data": {
        "attributes": {
            "name": "Jane Smith",
            "email": "jane@example.com",
            "password": "secret123",
            "isManager": false
        }
    }
}
```

### Response (password omitted)

```json
{
    "data": {
        "type": "user",
        "id": 15,
        "attributes": {
            "name": "Jane Smith",
            "email": "jane@example.com",
            "isManager": false
        }
    }
}
```

---

## Task 11 — Pest Tests

```php
use App\Models\User;
use App\Permissions\V1\Abilities;

it('manager can create a user', function () {
    $manager = User::factory()->create(['is_manager' => true]);

    $token = $manager->createToken('test', [Abilities::CreateUser])->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/v1/users', [
            'data' => [
                'attributes' => [
                    'name'      => 'New User',
                    'email'     => 'new@example.com',
                    'password'  => 'password',
                    'isManager' => false,
                ],
            ],
        ])
        ->assertCreated()
        ->assertJsonPath('data.attributes.name', 'New User')
        ->assertJsonMissing(['password']);
});

it('regular user cannot create a user', function () {
    $user = User::factory()->create();

    $token = $user->createToken('test', [Abilities::CreateOwnTicket])->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/v1/users', [
            'data' => [
                'attributes' => [
                    'name'      => 'Hacker',
                    'email'     => 'hacker@example.com',
                    'password'  => 'password',
                    'isManager' => true,
                ],
            ],
        ])
        ->assertStatus(401);
});

it('manager can update a user', function () {
    $manager = User::factory()->create(['is_manager' => true]);
    $target  = User::factory()->create(['is_manager' => false]);

    $token = $manager->createToken('test', [Abilities::UpdateUser])->plainTextToken;

    $this->withToken($token)
        ->patchJson("/api/v1/users/{$target->id}", [
            'data' => [
                'attributes' => ['isManager' => true],
            ],
        ])
        ->assertOk()
        ->assertJsonPath('data.attributes.isManager', true);
});

it('manager can delete a user', function () {
    $manager = User::factory()->create(['is_manager' => true]);
    $target  = User::factory()->create();

    $token = $manager->createToken('test', [Abilities::DeleteUser])->plainTextToken;

    $this->withToken($token)
        ->deleteJson("/api/v1/users/{$target->id}")
        ->assertOk()
        ->assertJsonPath('message', 'User successfully deleted.');

    $this->assertDatabaseMissing('users', ['id' => $target->id]);
});

it('response does not include password', function () {
    $manager = User::factory()->create(['is_manager' => true]);
    $user    = User::factory()->create();

    $token = $manager->createToken('test', [Abilities::CreateUser])->plainTextToken;

    $response = $this->withToken($token)
        ->postJson('/api/v1/users', [
            'data' => [
                'attributes' => [
                    'name'      => 'Test',
                    'email'     => 'test@test.com',
                    'password'  => 'secret',
                    'isManager' => false,
                ],
            ],
        ])
        ->assertCreated();

    expect($response->json())->not->toHaveKey('data.attributes.password');
});

it('authors index returns only users with tickets', function () {
    $withTicket    = User::factory()->hasTickets(2)->create();
    $withoutTicket = User::factory()->create();

    $this->actingAs($withTicket)
        ->getJson('/api/v1/authors')
        ->assertOk()
        ->assertJsonFragment(['id' => $withTicket->id])
        ->assertJsonMissing(['id' => $withoutTicket->id]);
});
```

---

## Final Checklist

- [ ] `AuthorsController::index()` uses `join('tickets', ...)` + `distinct()` + `select('users.*')`
- [ ] `UsersController` created with full CRUD (`index`, `show`, `store`, `replace`, `update`, `destroy`)
- [ ] `protected string $policyClass = UserPolicy::class` on `UsersController`
- [ ] `UserPolicy` created in `app/Policies/Api/V1/` with `store`, `update`, `replace`, `destroy` methods
- [ ] Each `UserPolicy` method checks a single ability from `Abilities::CreateUser` etc.
- [ ] `UserPolicy` registered in the service provider: `User::class => UserPolicy::class`
- [ ] `BaseUserRequest` created with `mappedAttributes()` that hashes `password` with `bcrypt()`
- [ ] `StoreUserRequest`, `ReplaceUserRequest`, `UpdateUserRequest` extend `BaseUserRequest`
- [ ] `UpdateUserRequest` uses `sometimes` for all fields
- [ ] `UserResource` includes `isManager` and excludes `password`
- [ ] `User::$fillable` includes `is_manager`
- [ ] `User::casts()` casts `is_manager` to `boolean`
- [ ] Routes added for `users` (full CRUD with separate PUT and PATCH)
- [ ] `authors` routes restricted with `->except(['store', 'update', 'destroy'])`
- [ ] All Pest tests passing

---

## What's Next (Episode 22)

Documentation and additional topics — the core API functionality is now complete. Next steps cover things like API documentation generation, additional permission considerations, and production-readiness topics.
