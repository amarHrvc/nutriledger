# Deleting Resources with DELETE Requests — Task 14

> **Source:** Laravel API Masterclass — Episode 14
> **Created:** 2026-03-11
> **Run Time:** 8m 11s
> **Status:** Study Task

---

## Overview

Deletion is the simplest write operation but has one non-obvious concern: controlling the error response when the resource does not exist. Using route model binding (`Ticket $ticket`) lets Laravel handle the 404 automatically — but that automatic response exposes internal exception details. Taking the ID as a plain integer and using `findOrFail` in a try/catch gives you full control over the response.

---

## Concepts Covered

| Concept | What It Is |
|---|---|
| Raw ID vs route model binding | Integer ID + `findOrFail` gives you error response control |
| `ModelNotFoundException` | Thrown by `findOrFail` when the record is not found |
| 404 in API context | Acceptable for "resource not found" — not a server error |
| Nested delete | Check `ticket->user_id === authorId` before deleting |
| `ApiController` inheritance | `AuthorTicketsController` must extend `ApiController` to use `error()` |

---

## Task 1 — Why Not Use Route Model Binding Here?

### Default behavior with model binding

```php
// Route model binding:
public function destroy(Ticket $ticket): JsonResponse
{
    $ticket->delete();
    return $this->ok('Ticket successfully deleted.');
}
```

If `{ticket}` does not exist, Laravel throws a `ModelNotFoundException` and returns its default 404 response — which includes the full exception class name, stack trace hints, and other implementation details in development mode. Even in production, the response format is Laravel's default, not your structured JSON:API format.

### Controlled approach

```php
public function destroy(int $ticketId): JsonResponse
{
    try {
        $ticket = Ticket::findOrFail($ticketId);
    } catch (ModelNotFoundException) {
        return $this->error('Ticket cannot be found.', 404);
    }

    $ticket->delete();

    return $this->ok('Ticket successfully deleted.');
}
```

You decide the message, the structure, and the status code. The response is consistent with every other error in your API.

**Note on 404 vs 200 for missing resources:**

For `DELETE`, a 404 is semantically correct and security-acceptable. A missing resource is not a server error — it is a client request for something that does not exist. Unlike a `POST` that triggers internal processing, a `DELETE` that hits a missing resource reveals nothing about server state.

---

## Task 2 — Implement TicketController::destroy()

```php
use Illuminate\Database\Eloquent\ModelNotFoundException;

public function destroy(int $ticketId): JsonResponse
{
    try {
        $ticket = Ticket::findOrFail($ticketId);
    } catch (ModelNotFoundException) {
        return $this->error('Ticket cannot be found.', 404);
    }

    $ticket->delete();

    return $this->ok('Ticket successfully deleted.');
}
```

---

## Task 3 — Fix TicketController::show()

The `show()` method has the same problem — if the ticket does not exist, Laravel's default exception handler fires. Apply the same pattern:

```php
public function show(int $ticketId): TicketResource|JsonResponse
{
    try {
        $ticket = Ticket::findOrFail($ticketId);
    } catch (ModelNotFoundException) {
        return $this->error('Ticket cannot be found.', 404);
    }

    if ($this->include('author')) {
        $ticket->load('user');
    }

    return new TicketResource($ticket);
}
```

---

## Task 4 — Add ApiController Inheritance to AuthorTicketsController

Before implementing delete on the nested controller, fix the inheritance. `AuthorTicketsController` needs to extend `ApiController` to access `error()` and `ok()` (via the `ApiResponses` trait that `ApiController` uses):

```php
// Before
class AuthorTicketsController extends Controller

// After
use App\Http\Controllers\Api\V1\ApiController;

class AuthorTicketsController extends ApiController
```

---

## Task 5 — Implement AuthorTicketsController::destroy()

For nested resources, deleting requires two checks:
1. The ticket exists
2. The ticket belongs to the specified author

```php
public function destroy(int $authorId, int $ticketId): JsonResponse
{
    try {
        $ticket = Ticket::findOrFail($ticketId);
    } catch (ModelNotFoundException) {
        return $this->error('Ticket cannot be found.', 404);
    }

    if ($ticket->user_id !== $authorId) {
        return $this->error('Ticket cannot be found.', 404);
    }

    $ticket->delete();

    return $this->ok('Ticket successfully deleted.');
}
```

**Why return 404 when the ticket doesn't belong to the author?**

The URL `DELETE /api/v1/authors/5/tickets/42` is a request for ticket 42 *belonging to author 5*. If ticket 42 exists but belongs to author 7, it does not exist *at that URL*. Returning 404 is correct and reveals nothing about whether ticket 42 exists at all.

Returning 403 (Forbidden) would confirm that the ticket exists but is not accessible — which is more information than an attacker needs.

---

## Task 6 — Postman Tests

### Delete a ticket (TicketController)

```
DELETE http://localhost/api/v1/tickets/103
Authorization: Bearer {{bearer}}
Accept: application/json
```

Expected: `200` with `{"message": "Ticket successfully deleted.", "status": 200}`

### Delete a non-existent ticket

```
DELETE http://localhost/api/v1/tickets/800
```

Expected: `404` with `{"message": "Ticket cannot be found.", "status": 404}`

### Delete a ticket via author route

```
DELETE http://localhost/api/v1/authors/1/tickets/102
```

Expected: `200` on success, `404` if ticket doesn't belong to author 1

---

## Task 7 — Pest Tests

```php
use App\Models\{User, Ticket};

it('deletes a ticket successfully', function () {
    $ticket = Ticket::factory()->create();

    $this->actingAs(User::factory()->create())
        ->deleteJson("/api/v1/tickets/{$ticket->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Ticket successfully deleted.');

    $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
});

it('returns 404 for non-existent ticket on delete', function () {
    $this->actingAs(User::factory()->create())
        ->deleteJson('/api/v1/tickets/99999')
        ->assertNotFound()
        ->assertJsonPath('status', 404);
});

it('deletes ticket via author route when ticket belongs to author', function () {
    $author = User::factory()->create();
    $ticket = Ticket::factory()->for($author)->create();

    $this->actingAs(User::factory()->create())
        ->deleteJson("/api/v1/authors/{$author->id}/tickets/{$ticket->id}")
        ->assertOk();

    $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
});

it('returns 404 via author route when ticket belongs to different author', function () {
    $author  = User::factory()->create();
    $other   = User::factory()->create();
    $ticket  = Ticket::factory()->for($other)->create();

    $this->actingAs(User::factory()->create())
        ->deleteJson("/api/v1/authors/{$author->id}/tickets/{$ticket->id}")
        ->assertNotFound();

    $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);
});

it('show returns 404 with structured response for missing ticket', function () {
    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/tickets/99999')
        ->assertNotFound()
        ->assertJsonStructure(['message', 'status']);
});
```

---

## Final Checklist

- [ ] `TicketController::destroy()` uses integer ID + `findOrFail` + try/catch
- [ ] Returns `ok('Ticket successfully deleted.')` on success
- [ ] Returns `error('Ticket cannot be found.', 404)` on missing
- [ ] `TicketController::show()` updated with same pattern
- [ ] `AuthorTicketsController` extends `ApiController`
- [ ] `AuthorTicketsController::destroy()` checks ticket exists AND belongs to author
- [ ] Both return 404 when ticket is not found or doesn't belong to author
- [ ] All Pest tests passing

---

## What's Next (Episode 15)

PUT requests — full resource replacement. Unlike PATCH (partial update), PUT requires all fields and replaces the entire record. This warrants a separate `replace()` method distinct from `update()`.
