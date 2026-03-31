# Sorting Data — Task 12

> **Source:** Laravel API Masterclass — Episode 12
> **Created:** 2026-03-10
> **Run Time:** 13m 03s
> **Status:** Study Task

---

## Overview

Sorting follows the same architectural pattern as filtering — it lives in `QueryFilter`, not in the controller. A single `sort` query parameter accepts a comma-separated list of columns. Prefix a column with `-` for descending order. A whitelist on each filter subclass prevents clients from sorting by arbitrary column names. camelCase-to-snake_case mapping handles the translation between API names and database column names.

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| `?sort=title` | Sort by title ascending |
| `?sort=-title` | Sort by title descending (`-` prefix) |
| `?sort=status,-title` | Multi-column sort |
| `protected array $sortable` | Whitelist of allowed sort columns per filter subclass |
| `substr($str, 1)` | Strip the leading `-` to get the clean column name |
| `in_array()` + `array_key_exists()` | Check both the whitelist values and keys |
| camelCase → snake_case mapping | `'createdAt' => 'created_at'` in the whitelist array |
| `AuthorFilter` | Copy of `TicketFilter` adapted for `User` model attributes |

---

## Task 1 — Design the Sort Parameter Format

### Goal
Define the URL convention before implementing.

### Format

```
?sort=column              # ascending
?sort=-column             # descending (minus prefix)
?sort=status,title        # multiple columns, both ascending
?sort=status,-title       # status ascending, title descending
```

**Why not `?sort[title]=asc`?**

It is verbose. Every extra column requires another key. With a comma-separated single value, you can sort by 5 columns without making the URL unreadable. Two possible values (ascending/descending) map perfectly to presence/absence of a prefix.

---

## Task 2 — Add Sort Logic to QueryFilter

### Goal
Implement a `sort()` protected method in `QueryFilter` that handles the `?sort=` parameter.

### Instructions

Add to `app/Http/Filters/V1/QueryFilter.php`:

```php
protected array $sortable = [];

protected function sort(string $value): Builder
{
    $sortAttributes = explode(',', $value);

    foreach ($sortAttributes as $sortAttribute) {
        $direction = 'asc';

        if (str_starts_with($sortAttribute, '-')) {
            $direction     = 'desc';
            $sortAttribute = substr($sortAttribute, 1);
        }

        if (! $this->isSortable($sortAttribute)) {
            continue;
        }

        $columnName = $this->resolveColumnName($sortAttribute);

        $this->builder->orderBy($columnName, $direction);
    }

    return $this->builder;
}

private function isSortable(string $attribute): bool
{
    return in_array($attribute, $this->sortable)
        || array_key_exists($attribute, $this->sortable);
}

private function resolveColumnName(string $attribute): string
{
    if (array_key_exists($attribute, $this->sortable)) {
        return $this->sortable[$attribute];
    }

    return $attribute;
}
```

**`sort()` walkthrough:**

1. Explode the comma-separated value into individual attributes
2. For each attribute, default direction to `asc`
3. If it starts with `-`: set direction to `desc`, strip the `-` from the attribute name
4. Check the whitelist — if not allowed, `continue` (skip it silently)
5. Resolve the column name (handles camelCase aliases)
6. Add `orderBy` to the builder

**`isSortable()` — why check both `in_array` and `array_key_exists`?**

The `$sortable` whitelist supports two formats:

```php
protected array $sortable = [
    'title',                    // simple value — in_array('title', $sortable) = true
    'createdAt' => 'created_at', // keyed alias — array_key_exists('createdAt', $sortable) = true
];
```

Plain values cover simple columns. Key-value pairs cover camelCase-to-snake_case translations.

**`resolveColumnName()` — the camelCase bridge:**

```php
$sortable = ['title', 'createdAt' => 'created_at'];

resolveColumnName('title')     // 'title'    (not a key → return as-is)
resolveColumnName('createdAt') // 'created_at' (is a key → return value)
```

---

## Task 3 — Whitelist Sortable Columns in TicketFilter

### Goal
Specify which columns clients are allowed to sort by on the tickets endpoint.

### Instructions

Add `$sortable` to `app/Http/Filters/V1/TicketFilter.php`:

```php
protected array $sortable = [
    'title',
    'status',
    'createdAt' => 'created_at',
    'updatedAt' => 'updated_at',
];
```

**What happens with an unsupported column?**

```
GET /api/v1/tickets?sort=whatever
```

`isSortable('whatever')` returns `false`. The attribute is skipped via `continue`. No error. The query runs without that sort — results are returned in default order.

---

## Task 4 — Create the AuthorFilter

### Goal
Build a `QueryFilter` subclass for the `User` model so authors can be filtered and sorted.

### Instructions

Create `app/Http/Filters/V1/AuthorFilter.php`:

```php
<?php

namespace App\Http\Filters\V1;

use Illuminate\Database\Eloquent\Builder;

class AuthorFilter extends QueryFilter
{
    protected array $sortable = [
        'name',
        'email',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at',
    ];

    public function id(string $value): Builder
    {
        return $this->builder->whereIn('id', explode(',', $value));
    }

    public function name(string $value): Builder
    {
        $likeString = str_replace('*', '%', $value);

        return $this->builder->where('name', 'like', $likeString);
    }

    public function email(string $value): Builder
    {
        $likeString = str_replace('*', '%', $value);

        return $this->builder->where('email', 'like', $likeString);
    }

    public function createdAt(string $value): Builder
    {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            return $this->builder->whereBetween('created_at', $dates);
        }

        return $this->builder->whereDate('created_at', $dates[0]);
    }

    public function updatedAt(string $value): Builder
    {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            return $this->builder->whereBetween('updated_at', $dates);
        }

        return $this->builder->whereDate('updated_at', $dates[0]);
    }
}
```

**Reuse from TicketFilter:**
- `createdAt` and `updatedAt` are identical — same logic, same column names
- `name` and `email` use the same wildcard pattern as `title`
- `id` allows filtering by multiple author IDs: `filter[id]=1,6,10`

---

## Task 5 — Add scopeFilter to User Model

### Instructions

In `app/Models/User.php`:

```php
use App\Http\Filters\V1\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

public function scopeFilter(Builder $query, QueryFilter $filters): Builder
{
    return $filters->apply($query);
}
```

---

## Task 6 — Update AuthorsController

### Instructions

```php
use App\Http\Filters\V1\AuthorFilter;

public function index(AuthorFilter $filters): AnonymousResourceCollection
{
    return UserResource::collection(
        User::filter($filters)->paginate()
    );
}
```

---

## Task 7 — Sorting on Nested Resources

The `AuthorTicketsController` already uses `TicketFilter`. Because `sort()` is in `QueryFilter` (the base class), it is inherited automatically. No changes needed.

```
# Sort author 1's tickets by title descending
GET /api/v1/authors/1/tickets?sort=-title
Authorization: Bearer {{bearer}}
Accept: application/json
```

---

## Task 8 — Example Requests

```
# Sort tickets by title A→Z
GET /api/v1/tickets?sort=title

# Sort tickets by title Z→A
GET /api/v1/tickets?sort=-title

# Sort tickets by status, then title descending
GET /api/v1/tickets?sort=status,-title

# Sort completed tickets by createdAt (oldest first)
GET /api/v1/tickets?filter[status]=C&sort=createdAt

# Sort authors by name
GET /api/v1/authors?sort=name

# Filter authors by multiple IDs, sorted by email
GET /api/v1/authors?filter[id]=1,6,10&sort=email

# Ignore unsupported sort column silently
GET /api/v1/tickets?sort=nonexistent,title
```

---

## Task 9 — Pest Tests

```php
use App\Models\{User, Ticket};

it('sorts tickets by title ascending', function () {
    Ticket::factory()->create(['title' => 'zebra ticket']);
    Ticket::factory()->create(['title' => 'alpha ticket']);

    $response = $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/tickets?sort=title')
        ->assertOk();

    $titles = collect($response->json('data'))->pluck('attributes.title');

    expect($titles->first())->toBe('alpha ticket')
        ->and($titles->last())->toBe('zebra ticket');
});

it('sorts tickets by title descending with minus prefix', function () {
    Ticket::factory()->create(['title' => 'zebra ticket']);
    Ticket::factory()->create(['title' => 'alpha ticket']);

    $response = $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/tickets?sort=-title')
        ->assertOk();

    $titles = collect($response->json('data'))->pluck('attributes.title');

    expect($titles->first())->toBe('zebra ticket');
});

it('ignores unsupported sort columns without error', function () {
    Ticket::factory()->count(3)->create();

    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/tickets?sort=nonexistent')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

it('sorts by camelCase createdAt mapped to snake_case column', function () {
    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/tickets?sort=createdAt')
        ->assertOk();
    // No error = mapping worked
});

it('sorts authors by name', function () {
    User::factory()->create(['name' => 'Zara Smith']);
    User::factory()->create(['name' => 'Aaron Brown']);

    $response = $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/authors?sort=name')
        ->assertOk();

    expect($response->json('data.0.attributes.name'))->toBe('Aaron Brown');
});

it('filters and sorts can be combined', function () {
    Ticket::factory()->create(['status' => 'A', 'title' => 'b']);
    Ticket::factory()->create(['status' => 'A', 'title' => 'a']);
    Ticket::factory()->create(['status' => 'C', 'title' => 'z']);

    $response = $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/tickets?filter[status]=A&sort=title')
        ->assertOk()
        ->assertJsonCount(2, 'data');

    expect($response->json('data.0.attributes.title'))->toBe('a');
});
```

---

## Final Checklist

- [ ] `sort()` method added to `QueryFilter` with whitelist check
- [ ] `isSortable()` checks both `in_array` (values) and `array_key_exists` (keys)
- [ ] `resolveColumnName()` maps camelCase to snake_case for keyed entries
- [ ] `TicketFilter::$sortable` has `title`, `status`, `createdAt => created_at`, `updatedAt => updated_at`
- [ ] `app/Http/Filters/V1/AuthorFilter.php` created with `id`, `name`, `email`, `createdAt`, `updatedAt` methods
- [ ] `AuthorFilter::$sortable` has `name`, `email`, `createdAt => created_at`, `updatedAt => updated_at`
- [ ] `User` model has `scopeFilter()`
- [ ] `AuthorsController::index()` uses `User::filter($filters)->paginate()`
- [ ] Sort works on nested `authors/{author}/tickets` endpoint with no extra code
- [ ] Unknown sort columns ignored silently
- [ ] All Pest tests passing

---

## Series Complete — What Was Built

| Episode | Topic |
|---|---|
| 01 | Project setup, first JSON route, AuthController, ApiResponses trait |
| 02 | API versioning — folder structure, versioned controllers, route files |
| 03 | URL design — resources, Ticket model, factory, seeder, first endpoint |
| 04 | Sanctum login — LoginRequest, token issuance, route protection |
| 05 | Token revocation — logout, currentAccessToken, expiration |
| 06 | Response payloads — Eloquent Resources, JSON:API structure |
| 07 | Conditional fields — `when()`, `mergeWhen()`, `whenLoaded()` |
| 08 | Optional includes — `ApiController`, `?include=author`, `load()` |
| 09 | Filtering — `QueryFilter`, `TicketFilter`, `scopeFilter`, namespaced params |
| 10 | Nested resources — `authors.tickets`, `AuthorTicketsController`, rename |
| 11 | Sorting — `sort()` in `QueryFilter`, whitelist, camelCase mapping |
