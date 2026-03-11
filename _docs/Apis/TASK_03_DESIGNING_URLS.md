# Designing API URLs — Task 02

> **Source:** Laravel API Masterclass — Episode 03
> **Created:** 2026-03-10
> **Run Time:** 8m 04s
> **Status:** Study Task

---

## Overview

Every application has a user interface. For an API, **the URL is the UI**. The consumers of your API are developers — they interact with it entirely through URLs. A well-designed URL structure is the difference between an API people enjoy using and one they complain about.

Key insight: **resources map directly to Eloquent models**. An API is a data access layer, so your URL segments name the things (resources) the client wants to access.

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| Resource | A named entity in your system — maps to an Eloquent model |
| Resource URL | `/api/tickets` (collection) or `/api/tickets/{id}` (single item) |
| `foreignId()->constrained()` | Migration helper for FK with index + constraint |
| `faker->words(3, true)` | Returns a string, not an array (second arg matters) |
| `factory()->recycle($collection)` | Assigns random items from an existing collection instead of creating new ones |
| `apiResource` route | Like `Route::resource` but omits `create` and `edit` (form-only routes — APIs don't need them) |

---

## Task 1 — Design Your URL Structure

### Goal
Before writing a single line of code, map out what resources your API exposes and what URLs represent them.

### Key Concept — What is a resource?

A resource is any named thing your application manages. It almost always corresponds to an Eloquent model:

| Resource | URL (collection) | URL (single item) |
|---|---|---|
| tickets | `GET /api/tickets` | `GET /api/tickets/{id}` |
| users | `GET /api/users` | `GET /api/users/{id}` |
| contracts | `GET /api/contracts` | `GET /api/contracts/{id}` |

**Rules for good resource URLs:**
- Use **nouns**, never verbs — `/api/tickets`, not `/api/getTickets`
- Use **plural** — `/api/tickets`, not `/api/ticket`
- Use **lowercase** — `/api/tickets`, not `/api/Tickets`
- Actions are expressed by the **HTTP method**, not the URL segment

| HTTP Method | URL | Action |
|---|---|---|
| GET | `/api/tickets` | List all tickets |
| POST | `/api/tickets` | Create a new ticket |
| GET | `/api/tickets/{id}` | View one ticket |
| PUT/PATCH | `/api/tickets/{id}` | Update one ticket |
| DELETE | `/api/tickets/{id}` | Delete one ticket |

### Exercise
Before proceeding, write down the resources your own application has. For each one, write the 5 URLs above.

---

## Task 2 — Create the Ticket Model

### Goal
Generate a `Ticket` model with migration and factory using a single artisan command.

### Instructions

**Step 1 — Create model + migration + factory:**

```bash
php artisan make:model Ticket --migration --factory --no-interaction
```

Or shorthand:

```bash
php artisan make:model Ticket -mf --no-interaction
```

**Step 2 — Write the migration.**

Open the generated migration in `database/migrations/`. Add columns:

```php
public function up(): void
{
    Schema::create('tickets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained();
        $table->string('title');
        $table->text('description');
        $table->string('status');
        $table->timestamps();
    });
}
```

**`foreignId('user_id')->constrained()` explained:**
- `foreignId` creates an unsigned `BIGINT` column named `user_id`
- `constrained()` adds a foreign key constraint pointing to `users.id`
- It also creates a database index on `user_id` automatically

**Step 3 — Write the `$fillable` array in `app/Models/Ticket.php`:**

```php
protected $fillable = [
    'user_id',
    'title',
    'description',
    'status',
];
```

**Step 4 — Add the relationship in `Ticket.php`:**

```php
use Illuminate\Database\Eloquent\Relations\BelongsTo;

public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

**Step 5 — Add the inverse relationship in `User.php`:**

```php
use Illuminate\Database\Eloquent\Relations\HasMany;

public function tickets(): HasMany
{
    return $this->hasMany(Ticket::class);
}
```

**Step 6 — Run the migration:**

```bash
php artisan migrate --no-interaction
```

---

## Task 3 — Build the Factory

### Goal
Write a factory that generates realistic ticket data with a controlled set of status values.

### Key Concept — `faker->words(n, true)`

`$this->faker->words(3)` returns an **array** of 3 words. Pass `true` as the second argument to get a **string**:

```php
$this->faker->words(3, true) // "lorem ipsum dolor"
$this->faker->words(3)       // ["lorem", "ipsum", "dolor"]
```

Always use `true` when the column is a string.

### Key Concept — Status as single character

Rather than an Enum (which requires a migration change to add values), a plain string column with a defined set of values kept in the factory/seeder is simpler to evolve.

### Instructions

Open `database/factories/TicketFactory.php` and fill the `definition()` method:

```php
use App\Models\User;

public function definition(): array
{
    return [
        'user_id'     => User::factory(),
        'title'       => $this->faker->words(3, true),
        'description' => $this->faker->paragraph(),
        'status'      => $this->faker->randomElement(['A', 'C', 'H', 'X']),
    ];
}
```

**Status codes:**

| Code | Meaning |
|---|---|
| A | Active |
| C | Completed |
| H | On Hold |
| X | Cancelled |

**`User::factory()` as a value** — when `user_id` is set to a factory, it creates a new User *unless* you call `recycle()` on the parent factory (see Task 4).

---

## Task 4 — Seed the Database

### Goal
Use the seeder to create 10 Users and 100 Tickets that share those users via `recycle()`.

### Key Concept — `factory()->recycle($collection)`

Without `recycle()`:
```php
Ticket::factory()->count(100)->create();
// Creates 100 tickets AND 100 users (one per ticket)
```

With `recycle()`:
```php
$users = User::factory()->count(10)->create();
Ticket::factory()->count(100)->recycle($users)->create();
// Creates 100 tickets, assigns a random user from the 10 — no extra users created
```

`recycle()` reuses existing model instances from the collection. Each ticket gets one of the 10 users picked at random.

### Instructions

Open `database/seeders/DatabaseSeeder.php`:

```php
use App\Models\User;
use App\Models\Ticket;

public function run(): void
{
    $users = User::factory()->count(10)->create();

    Ticket::factory()
        ->count(100)
        ->recycle($users)
        ->create();
}
```

**Run the seeder:**

```bash
php artisan db:seed --no-interaction
```

Verify in your database client — you should have 10 users and 100 tickets.

---

## Task 5 — Register the Tickets Route

### Goal
Add a GET route to return all tickets and verify it in Postman.

### Key Concept — `Route::apiResource` vs `Route::resource`

`Route::resource` generates 7 routes:

| Method | URL | Action | Notes |
|---|---|---|---|
| GET | `/tickets` | index | |
| GET | `/tickets/create` | create | **Form page — not needed in APIs** |
| POST | `/tickets` | store | |
| GET | `/tickets/{id}` | show | |
| GET | `/tickets/{id}/edit` | edit | **Form page — not needed in APIs** |
| PUT/PATCH | `/tickets/{id}` | update | |
| DELETE | `/tickets/{id}` | destroy | |

`Route::apiResource` generates 5 routes — it drops `create` and `edit`. APIs don't serve HTML forms, so those routes are irrelevant.

### Instructions

**Temporary scaffold in `routes/api.php`** (will be moved to a versioned file in Task 03):

```php
use App\Models\Ticket;

Route::get('/tickets', function () {
    return Ticket::all();
});
```

> Returning a collection directly from a route/controller serialises it to JSON automatically. In a real implementation you'd use an Eloquent Resource — covered in a later episode.

### Test in Postman

1. New request: `GET http://localhost/api/tickets`
2. Header: `Accept: application/json`
3. Send — you should receive a JSON array of 100 ticket objects

### Pest Test

```php
use App\Models\{User, Ticket};

it('returns all tickets as JSON', function () {
    Ticket::factory()->count(3)->create();

    $this->getJson('/api/tickets')
        ->assertOk()
        ->assertJsonCount(3);
});
```

---

## Checklist

- [ ] `Ticket` model created at `app/Models/Ticket.php`
- [ ] Migration has `user_id` FK, `title`, `description`, `status` columns
- [ ] `foreignId()->constrained()` used for `user_id`
- [ ] `Ticket` factory uses `faker->words(3, true)` for title
- [ ] Factory status uses `randomElement(['A', 'C', 'H', 'X'])`
- [ ] Seeder creates 10 users and 100 tickets with `recycle()`
- [ ] `GET /api/tickets` returns JSON array
- [ ] Verified in Postman
- [ ] Pest test passing

---

## What's Next (Episode 04)

Once the URL structure is established, the next concern is **versioning** — how to make changes to the API without breaking existing clients.
