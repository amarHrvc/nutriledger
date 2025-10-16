# Writing Filters — Task 10

> **Source:** Laravel API Masterclass — Episode 10
> **Created:** 2026-03-10
> **Run Time:** 16m 16s
> **Status:** Study Task

---

## Overview

Filtering is one of the most common API requirements. The naive approach — checking query parameters directly in the controller — works but doesn't scale. After two or three filters the controller becomes a wall of `if` statements. This episode builds a clean, extensible filtering system: a `QueryFilter` base class and a `TicketFilter` subclass, wired up via an Eloquent local scope.

The controller ends up with a single line regardless of how many filter parameters the client sends.

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| `scopeFilter()` | Eloquent local scope — allows `Ticket::filter($filters)` syntax |
| `QueryFilter` (abstract) | Base class that iterates request params and dispatches to methods |
| `TicketFilter` | Subclass with specific filter methods (`status`, `title`, `createdAt`, `updatedAt`) |
| `filter[status]=C` | Namespaced query param — separates filters from other params like `include`, `sort` |
| `whereIn()` | Filters rows where column value is in a set |
| `where('col', 'like', $val)` | Partial string match with `%` wildcards |
| `whereBetween()` | Filters rows where column is within a date range |
| `whereDate()` | Filters rows by a single date (ignores time component) |

---

## Task 1 — Design the Filter System

### Goal
Understand the architecture before writing any code.

### Target usage in the controller

```php
// TicketController::index()
return TicketResource::collection(
    Ticket::filter($filters)->paginate()
);
```

- `$filters` is a `TicketFilter` instance injected automatically via the service container
- `filter()` is a local scope on the `Ticket` model
- `TicketFilter` has methods matching filter parameter names — called automatically

### How the dispatch works

The `QueryFilter` base class iterates over filter query parameters. For each one, if a method with that name exists on the subclass, it calls it with the value:

```
?filter[status]=C
→ QueryFilter::apply() iterates filter[] array
→ finds 'status' => 'C'
→ calls $this->status('C')
→ TicketFilter::status() adds whereIn to the builder
```

No `if` statements in the controller or the base class. Adding a new filter means adding one method to `TicketFilter`.

---

## Task 2 — Create the QueryFilter Base Class

### Instructions

Create the folder `app/Http/Filters/V1/` and the file `QueryFilter.php`:

```php
<?php

namespace App\Http\Filters\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    protected Builder $builder;

    public function __construct(protected Request $request) {}

    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $this->builder;
    }

    protected function filter(array $filters): Builder
    {
        foreach ($filters as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $this->builder;
    }
}
```

**`apply()` walkthrough:**

1. Store the Eloquent builder on the instance
2. Iterate over all query parameters on the request
3. If a method named after the parameter exists on this class (or subclass), call it with the parameter's value
4. Return the modified builder

**Why `filter()` as a protected method?**

The outer loop in `apply()` iterates over everything in the query string: `include`, `sort`, `filter`, etc. When it hits the `filter` key, the value is an array (`['status' => 'C', 'title' => 'something']`). The `filter()` method handles that nested array with its own inner loop — keeping filters namespaced under `filter[...]`.

---

## Task 3 — Create the TicketFilter Class

### Instructions

Create `app/Http/Filters/V1/TicketFilter.php`:

```php
<?php

namespace App\Http\Filters\V1;

use Illuminate\Database\Eloquent\Builder;

class TicketFilter extends QueryFilter
{
    public function include(string $value): Builder
    {
        return $this->builder->with(explode(',', $value));
    }

    public function status(string $value): Builder
    {
        return $this->builder->whereIn('status', explode(',', strtoupper($value)));
    }

    public function title(string $value): Builder
    {
        $likeString = str_replace('*', '%', $value);

        return $this->builder->where('title', 'like', $likeString);
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

**Filter method explanations:**

**`status`**
- Client sends: `filter[status]=C` or `filter[status]=C,X` (multiple)
- `explode(',', 'C,X')` → `['C', 'X']`
- `whereIn('status', ['C', 'X'])` → `WHERE status IN ('C', 'X')`

**`title`**
- Client sends: `filter[title]=*eum*` (wildcards)
- `str_replace('*', '%', '*eum*')` → `%eum%`
- `where('title', 'like', '%eum%')` → `WHERE title LIKE '%eum%'`
- Use `*` in the URL (client-friendly) — convert to `%` for SQL

**`createdAt`**
- Client sends single date: `filter[createdAt]=2024-02-01`
- `whereDate('created_at', '2024-02-01')` — compares date part only, ignores time
- Client sends range: `filter[createdAt]=2024-02-01,2024-02-05`
- `whereBetween('created_at', ['2024-02-01', '2024-02-05'])`

---

## Task 4 — Add the `scopeFilter` to the Ticket Model

### Goal
Enable the `Ticket::filter($filters)` syntax in the controller.

### Key Concept — Eloquent local scopes

A method named `scope{Name}` on a model becomes callable as `Model::name()` on the query builder. The first argument is always the query builder (injected by Eloquent), followed by your custom arguments.

```php
// In the model:
public function scopeFilter(Builder $query, QueryFilter $filters): Builder
{
    return $filters->apply($query);
}

// In the controller:
Ticket::filter($filters)  // Laravel injects $query automatically
```

### Instructions

In `app/Models/Ticket.php`:

```php
use App\Http\Filters\V1\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

public function scopeFilter(Builder $query, QueryFilter $filters): Builder
{
    return $filters->apply($query);
}
```

---

## Task 5 — Update TicketController to Use Filters

### Instructions

```php
use App\Http\Filters\V1\TicketFilter;

public function index(TicketFilter $filters): AnonymousResourceCollection
{
    return TicketResource::collection(
        Ticket::filter($filters)->paginate()
    );
}
```

Laravel's service container resolves `TicketFilter` automatically. `TicketFilter` extends `QueryFilter` which has `Request` in its constructor — the container injects the current `Request` as well. One line of code in the controller handles all filters.

---

## Task 6 — Namespaced Filter Query Parameters

### Goal
Separate filter parameters (`filter[status]=C`) from other parameters (`include=author`, `sort=title`) in the URL.

### Why namespace filters?

Without namespacing:

```
GET /api/v1/tickets?status=C&include=author&sort=title
```

The `apply()` loop in `QueryFilter` iterates over everything in the query string. `include` and `sort` would need to be handled inside `TicketFilter`, mixing concerns. Adding a new parameter from a different concern requires modifying the filter class.

With namespacing:

```
GET /api/v1/tickets?filter[status]=C&include=author&sort=-title
```

The `apply()` loop sees three top-level keys: `filter`, `include`, `sort`. `filter` calls `QueryFilter::filter()` which handles the nested array internally. `include` and `sort` call their own dedicated methods.

### What changes in the URL:

| Before | After |
|---|---|
| `?status=C` | `?filter[status]=C` |
| `?status=C,X` | `?filter[status]=C,X` |
| `?title=*eum*` | `?filter[title]=*eum*` |
| `?createdAt=2024-02-01` | `?filter[createdAt]=2024-02-01` |

The `include` parameter is NOT nested — it is not a filter:

```
?include=author&filter[status]=C
```

---

## Task 7 — Example Requests

```
# Active tickets only
GET /api/v1/tickets?filter[status]=A

# Completed and cancelled
GET /api/v1/tickets?filter[status]=C,X

# Tickets with 'eum' in the title
GET /api/v1/tickets?filter[title]=*eum*

# Tickets created on Feb 1 2024
GET /api/v1/tickets?filter[createdAt]=2024-02-01

# Tickets created between Feb 3 and Feb 5
GET /api/v1/tickets?filter[createdAt]=2024-02-03,2024-02-05

# Completed tickets, including author info
GET /api/v1/tickets?filter[status]=C&include=author
```

---

## Task 8 — Pest Tests

```php
use App\Models\{User, Ticket};

it('filters tickets by status', function () {
    Ticket::factory()->create(['status' => 'A']);
    Ticket::factory()->create(['status' => 'C']);
    Ticket::factory()->create(['status' => 'X']);

    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/tickets?filter[status]=C')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

it('filters tickets by multiple statuses', function () {
    Ticket::factory()->create(['status' => 'A']);
    Ticket::factory()->create(['status' => 'C']);
    Ticket::factory()->create(['status' => 'X']);

    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/tickets?filter[status]=C,X')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('filters tickets by title wildcard', function () {
    Ticket::factory()->create(['title' => 'lorem ipsum dolor']);
    Ticket::factory()->create(['title' => 'something else entirely']);

    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/tickets?filter[title]=*ipsum*')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

it('unknown filter params are ignored safely', function () {
    Ticket::factory()->count(3)->create();

    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/tickets?filter[nonexistent]=value')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});
```

---

## Final Checklist

- [ ] `app/Http/Filters/V1/QueryFilter.php` created (abstract, with `apply()` and `filter()`)
- [ ] `app/Http/Filters/V1/TicketFilter.php` created with `status`, `title`, `createdAt`, `updatedAt`, `include` methods
- [ ] `Ticket` model has `scopeFilter(Builder $query, QueryFilter $filters): Builder`
- [ ] `TicketController::index()` accepts `TicketFilter $filters` and calls `Ticket::filter($filters)->paginate()`
- [ ] `filter[status]=C` filters by status (single and multiple with comma)
- [ ] `filter[title]=*word*` filters by partial title match
- [ ] `filter[createdAt]=date` and `filter[createdAt]=from,to` work
- [ ] Unknown filter params do not cause errors
- [ ] All Pest tests passing

---

## What's Next (Episode 11)

Filters work on the ticket collection. But filtering tickets by the author who submitted them should go through the author endpoint, not a ticket filter param. This leads to **nested resources**: `GET /api/v1/authors/{author}/tickets`.
