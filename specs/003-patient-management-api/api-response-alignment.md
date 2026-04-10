# API Response Alignment Plan
## Patient Management — Align with User API + ApiResponses Trait

**Decision**: All single-resource API responses use the `ApiResponses` trait envelope, not raw JSON:API.
**Governed by**: `.specify/memory/constitution.md` Principle VII (v2.2.0)

---

## Reference Implementation — User API

The User API is the canonical pattern. All future APIs must replicate it.

### Response shapes

| Operation | Method | Shape |
|---|---|---|
| Create | `$this->created('...', ['user' => new UserResource($user)])` | `{message, status:201, data:{user:{type,id,attributes,relationships}}}` |
| Read single | `$this->ok('...', ['user' => new UserResource($user)])` | `{message, status:200, data:{user:{type,id,attributes,relationships}}}` |
| Update | `$this->ok('...', ['user' => new UserResource($user)])` | `{message, status:200, data:{user:{type,id,attributes,relationships}}}` |
| List (paginated) | `$this->paginated('...', UserResource::collection(...))` | `{message, status:200, data:[...], meta:{...}, links:{...}}` |
| Delete | `$this->noContent()` | 204 no body |

### Test assertion pattern (single resource)

```php
// Structure
->assertJsonStructure([
    'data' => ['user' => ['type', 'id', 'attributes']],
])

// Path assertions
->assertJsonPath('data.user.type', 'users')
->assertJsonPath('data.user.id', $user->id)
->assertJsonPath('data.user.attributes.name', 'John')

// Attribute key check
$response->json('data.user.attributes')
```

### Test assertion pattern (list)

```php
->assertJsonStructure([
    'data' => [['type', 'id', 'attributes']],
    'meta' => ['current_page', 'last_page', 'per_page', 'total'],
    'links' => ['first', 'last'],
])
$response->json('data')  // array of resources
```

---

## Patient API — Required Changes

### PatientController (store — already partially implemented)

**Wrong:**
```php
return $this->created('Patient created successfully.', [new PatientResource($patient)]);
```

**Correct:**
```php
return $this->created('Patient created successfully.', ['patient' => new PatientResource($patient)]);
```

Same pattern for all other methods when implemented:
```php
// show(), update()
return $this->ok('Patient retrieved.', ['patient' => new PatientResource($patient->load(['socioeconomic', 'user']))]);
return $this->ok('Patient updated successfully.', ['patient' => new PatientResource($patient)]);

// destroy()
return $this->noContent();
```

### PatientApiTest — single-resource assertion changes

| Test | Wrong | Correct |
|---|---|---|
| T011 create | `data.type`, `data.id`, `data.attributes`, `data.relationships`, `included` | `data.patient.type`, `data.patient.id`, `data.patient.attributes`, `data.patient.relationships` — no `included` |
| T015 show | same + `assertJsonPath('data.type',...)`, `assertJsonPath('data.id',...)` | `data.patient.*` + `assertJsonPath('data.patient.type',...)` — no `included` |
| T016 update | `assertJsonPath('data.attributes.firstName', ...)` | `assertJsonPath('data.patient.attributes.firstName', ...)` |
| T021 envelope | `data.type`, `data.id`, `data.attributes`, `data.relationships` | `data.patient.type` etc. |
| T022 self-view | `assertJsonPath('data.id', ...)` | `assertJsonPath('data.patient.id', ...)` |

List tests (T013, T014, T018, T026) — no changes needed. `data` is an array in paginated responses.

### PatientResource::with()

`with()` is only called by Laravel's Resource response mechanism. Since we use the `ApiResponses` trait,
`with()` is never invoked. The `included` key does not appear in our responses. Remove `with()` from
`PatientResource` to avoid dead code.

---

## Upcoming Tasks — Convention for New Tests

When writing tests for show(), update(), US2, US3, edge cases:

```php
// Single resource — always use data.patient.*
->assertJsonPath('data.patient.type', 'patient')
->assertJsonPath('data.patient.id', (string) $patient->id)
->assertJsonPath('data.patient.attributes.firstName', 'Jane')

// List — data is flat array
$response->json('data')  // array, pluck id directly
```

For future feature groups (Visits, etc.), substitute the resource name:
`data.visit.*`, `data.lab_result.*` etc.

---

## Standard Baked Into Constitution (Principle VII)

This rule governs the response envelope for all APIs in this project:

- Single resource → `$this->created/ok('...', ['<resource>' => new <Resource>($model)])`
- List → `$this->paginated('...', <Resource>::collection(...))`
- Delete → `$this->noContent()`
- Test single → `data.<resource>.*` path prefix
- Test list → `data` is a flat array, `meta` and `links` alongside
- No raw `->response()->setStatusCode()` on Resources
- No `included` key — embed related data inline in attributes or relationships if needed

---

## Files Changed by This Plan

| File | Change |
|---|---|
| `constitution.md` | +Principle VII API Response Envelope |
| `CLAUDE.md` | +response envelope rule |
| `app/Http/Controllers/Api/PatientController.php` | fix store() response key |
| `app/Http/Resources/Api/PatientResource.php` | remove with() |
| `tests/Feature/Patient/PatientApiTest.php` | fix T011, T015, T016, T021, T022 |
| `specs/003-patient-management-api/spec.md` | update FR-006, acceptance criteria |
| beads `nutri-ledger-1u3` | update design code response lines |
