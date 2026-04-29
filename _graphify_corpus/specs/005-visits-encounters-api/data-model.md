# Data Model: Visits & Encounters REST API

**Branch**: `005-visits-encounters-api` | **Date**: 2026-04-10

## Entity: Visit

**Status**: Table and model already exist on branch. Minor model correction needed (date cast).

### Fields

| Field | Type | Required | Constraints |
|---|---|---|---|
| `id` | bigint unsigned | auto | Primary key, auto-increment |
| `patient_id` | bigint unsigned | yes | FK → patients.id, CASCADE DELETE |
| `doctor_id` | bigint unsigned | yes | FK → users.id, CASCADE DELETE; auto-assigned from authenticated user, never from request body |
| `date` | date | yes | Must be today or in the past (`before_or_equal:today`) |
| `notes` | text | no | Nullable; max 10,000 characters |
| `created_at` | timestamp | auto | Set on creation |
| `updated_at` | timestamp | auto | Updated on every save |

**No soft deletes.** Deleted visits are permanently removed.

### Model Correction

Current cast: `'date' => 'datetime'` — produces ISO-8601 datetime string in output.
Required cast: `'date' => 'date'` — produces `Y-m-d` string, consistent with spec and frontend expectations.

File: `app/Models/Visit.php`

### Relationships

| Relationship | Type | Related Model | FK | Notes |
|---|---|---|---|---|
| `patient()` | BelongsTo | `Patient` | `patient_id` | The patient this visit is for |
| `doctor()` | BelongsTo | `User` | `doctor_id` | The doctor who conducted the visit |
| `visits()` on Patient | HasMany | `Visit` | `patient_id` | Already defined on Patient model |

### Indexes (existing migration)

| Index | Columns | Purpose |
|---|---|---|
| PK | `id` | Primary lookup |
| FK index | `patient_id` | Visit history queries |
| FK index | `doctor_id` | Doctor's visit queries |

### Ordering

All collection queries MUST order by `date DESC` (newest visit first). Secondary sort by `created_at DESC` to break ties on same-day visits.

---

## Entity: VisitPolicy (corrections)

**Status**: Exists but requires two corrections.

**File**: `app/Policies/VisitPolicy.php`

### Current vs Required

| Method | Current | Required | Reason |
|---|---|---|---|
| `viewAny(User)` | admin \|\| doctor | admin \|\| doctor \|\| patient | Patients must be able to list their own visits; cross-patient access blocked by route scoping (404), not by this policy |
| `create(User)` | admin \|\| doctor | doctor only | Spec explicitly states admins cannot create visits (clinical decision) |
| `view(User, Visit)` | correct | — | No change |
| `update(User, Visit)` | correct | — | No change |
| `delete(User, Visit)` | correct | — | No change |

---

## Entity: StoreVisitRequest (corrections)

**Status**: Exists but requires corrections.

**File**: `app/Http/Requests/StoreVisitRequest.php`

| Field | Current Rules | Required Rules | Change |
|---|---|---|---|
| `date` | `required\|date` | `required\|date\|before_or_equal:today` | Add future-date guard |
| `notes` | `nullable\|string\|max:500` | `nullable\|string\|max:10000` | Increase limit |
| `doctor_id` | `nullable\|exists:users,id` | REMOVE | Never accepted from request |

---

## New Entity: UpdateVisitRequest

**File to create**: `app/Http/Requests/UpdateVisitRequest.php`
(Matches `UpdatePatientRequest` location — NOT in `Api/` subdirectory.)

| Field | Rules | Notes |
|---|---|---|
| `date` | `sometimes\|date\|before_or_equal:today` | Optional on update |
| `notes` | `nullable\|string\|max:10000` | Optional on update |

`authorize()`: MUST call policy — matches `UpdatePatientRequest` pattern (Constitution Principle II):
```php
public function authorize(): bool
{
    $visit = $this->route('visit');
    return $this->user()->can('update', $visit);
}
```
Because the FormRequest handles the `update` policy check, `VisitController::update()` must NOT call `$this->authorize()` separately (same pattern as `PatientController::update()`).

---

## New Entity: VisitResource

**File to create**: `app/Http/Resources/Api/VisitResource.php`

### Output Structure

Follows `PatientResource` pattern exactly — no `includes` block (no precedent in the project).
Doctor name exposed as a denormalized attribute (`doctorName`) so the frontend gets it in one response without a second request.

```json
{
  "type": "visit",
  "id": "42",
  "attributes": {
    "date": "2026-03-15",
    "notes": "Clinical notes...",
    "doctorName": "Dr. Sarah Johnson",
    "createdAt": "2026-03-15T14:30:00+00:00",
    "updatedAt": "2026-03-15T16:45:00+00:00"
  },
  "relationships": {
    "patient": {
      "data": { "type": "patient", "id": "5" }
    },
    "doctor": {
      "data": { "type": "user", "id": "12" }
    }
  }
}
```

Rules:
- All attribute keys: camelCase
- `date`: output as `Y-m-d` string (from model `date` cast)
- `doctorName`: `whenLoaded('doctor', fn() => $this->doctor->name)` — present only when doctor loaded (all endpoints load it)
- `createdAt` / `updatedAt`: ISO-8601 via `->toIso8601String()`
- `id`: cast to string (consistency with PatientResource)
- No `includes` section — not present in PatientResource or UserResource

---

## New Entity: VisitController

**File to create**: `app/Http/Controllers/Api/VisitController.php`

Extends `ApiController`. Five methods: `index`, `store`, `show`, `update`, `destroy`.

### index
- Authorize: `viewAny` policy
- Eager load `doctor`
- Order: `orderBy('date', 'desc')->orderBy('created_at', 'desc')`
- Return: `$this->paginated(..., VisitResource::collection(...))`

### store
- Authorize: via `StoreVisitRequest::authorize()` (calls policy `create`)
- Auto-assign: `doctor_id = $request->user()->id`
- Return: `$this->created('Visit recorded successfully.', ['visit' => new VisitResource($visit->load('doctor'))])`

### show
- Authorize: `view` policy
- Eager load `doctor`, `patient`
- Return: `$this->ok('Visit retrieved successfully.', ['visit' => new VisitResource($visit->load('doctor', 'patient'))])`
- Note: scoped route binding handles patient mismatch → 404 automatically

### update
- Authorize: handled by `UpdateVisitRequest::authorize()` — do NOT call `$this->authorize()` in controller (same as PatientController::update)
- Only update validated fields (`$request->validated()`)
- Return: `$this->ok('Visit updated successfully.', ['visit' => new VisitResource($visit->load('doctor'))])`

### destroy
- Authorize: `delete` policy
- Hard delete: `$visit->delete()`
- Return: `$this->noContent()`

---

## Route Registration

**File to update**: `routes/api.php`

```php
Route::apiResource('patients.visits', VisitController::class)
    ->scoped(['visit' => 'patient']);
```

Placed inside the existing `auth:sanctum` middleware group. No additional role middleware needed — policy handles role-based access per action.

Generated routes:
| Method | URI | Name | Action |
|---|---|---|---|
| GET | `/api/patients/{patient}/visits` | `patients.visits.index` | index |
| POST | `/api/patients/{patient}/visits` | `patients.visits.store` | store |
| GET | `/api/patients/{patient}/visits/{visit}` | `patients.visits.show` | show |
| PUT/PATCH | `/api/patients/{patient}/visits/{visit}` | `patients.visits.update` | update |
| DELETE | `/api/patients/{patient}/visits/{visit}` | `patients.visits.destroy` | destroy |
