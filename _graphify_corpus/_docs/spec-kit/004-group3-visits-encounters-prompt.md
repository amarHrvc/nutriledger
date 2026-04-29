# Spec-Kit Feature Prompt: Group 3 — Visits & Encounters API

## Epic Context
**BD Issue:** nutri-ledger-9d3 (P1)  
**Epic Title:** [PLANNING] Group 3 — Visits & Encounters  
**Track:** SE (Software Engineering) MVP — Feature Groups 1-3  
**Priority:** P1 (High)  
**Depends On:** 
- nutri-ledger-est (Group 1 — Auth & User Management)
- nutri-ledger-fg2 (Group 2 — Patient Management)

---

## Feature Description

Build a complete REST API for Visit Management (clinical encounters) in the NutriLedger healthcare application. This feature enables doctors to document patient visits (encounters), record clinical notes, and maintain a comprehensive visit history. The API follows Laravel best practices, JSON:API conventions, and implements role-based authorization with specific rules for doctor-patient encounters.

**Core Capabilities:**
- **Visit Creation:** Doctors can create visit records for patients (date + clinical notes)
- **Visit History:** View complete visit history for a patient (chronologically ordered)
- **Visit Details:** Access individual visit information with doctor and patient context
- **Visit Updates:** Doctors can edit notes for visits they conducted
- **Visit Deletion:** Admins can delete visit records (hard delete, no soft deletes)
- **Read-Only Patient Access:** Patients can view their own visit history (cannot create/edit/delete)
- **Doctor Attribution:** Each visit tracks which doctor conducted it
- **Nested Route Structure:** Visits accessed via `/api/patients/{patient}/visits`

**What Makes This Different from Standard CRUD:**
- **Nested RESTful routing:** Visits are always accessed in context of a patient
- **Doctor ownership:** Doctors can only edit visits they created
- **Patient read-only:** Patients can view but not modify their visit history
- **No soft deletes:** Visits are hard-deleted (simpler for MVP, no restoration)
- **Chronological ordering:** Visits always ordered by date (newest first)
- **Clinical notes:** Free-text field for doctor's observations/recommendations
- **Future extensibility:** Visits will later contain vital signs, lab results, medications

---

## Database Structure & Relationships

### `visits` Table
```sql
CREATE TABLE visits (
    id          BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    patient_id  BIGINT UNSIGNED NOT NULL,
    doctor_id   BIGINT UNSIGNED NOT NULL,
    date        DATE NOT NULL,
    notes       TEXT NULL,
    created_at  TIMESTAMP NOT NULL,
    updated_at  TIMESTAMP NOT NULL,
    
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_visits_patient_date (patient_id, date DESC),
    INDEX idx_visits_doctor (doctor_id)
);
```

**Key Fields:**
- `patient_id` — **FK** to patients table (which patient this visit is for)
- `doctor_id` — **FK** to users table (which doctor conducted this visit)
- `date` — Date of the visit/encounter (not datetime — time tracking out of scope)
- `notes` — Clinical notes, observations, recommendations (nullable)
- **No soft deletes** — visits are hard-deleted in MVP

**Important Notes:**
- `doctor_id` references `users.id` directly (not `patients.id`)
- A doctor is any `User` with `role = 'doktor'`
- `notes` is nullable — visits can be logged with date only, notes added later
- `ON DELETE CASCADE` — if patient or doctor is deleted, visits are removed
- Chronological index on `(patient_id, date DESC)` for efficient history queries

### Relationship Diagram

```
Patient (1) ←→ (many) Visit (many) ←→ (1) User (doctor)
                └─ future: VitalSigns, Labs, Medications, Recommendations
```

**Relationship Rules:**
1. One Patient can have many Visits (0..*)
2. One Visit belongs to exactly one Patient (1)
3. One Visit belongs to exactly one Doctor (User with role='doktor') (1)
4. One Doctor (User) can conduct many Visits (0..*)
5. When Patient is deleted, all visits cascade delete
6. When Doctor (User) is deleted, all visits cascade delete
7. Visits are ordered by date DESC (newest first) when querying history

---

## Current Implementation Status

### ✅ Already Implemented (Domain Layer — on feature/3_visits branch)

**Visit Model** (`app/Models/Visit.php` — on feature branch):
```php
class Visit extends Model
{
    use HasFactory;
    // NO SoftDeletes trait — hard delete only
    
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'date',
        'notes',
    ];
    
    // Relationships
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
    
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
    
    // Casts
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
```

**VisitPolicy** (`app/Policies/VisitPolicy.php` — on feature branch):
```php
class VisitPolicy
{
    public function viewAny(User $user, Patient $patient): bool
    {
        // Admin/Doctor: can view any patient's visits
        // Patient: can only view own visits
        if ($user->isPatient() && $user->id !== $patient->user_id) {
            return false;
        }
        return $user->isAdmin() || $user->isDoctor() || $user->isPatient();
    }
    
    public function view(User $user, Visit $visit): bool
    {
        // Admin/Doctor: can view any visit
        // Patient: can only view own visits
        if ($user->isPatient() && $user->id !== $visit->patient->user_id) {
            return false;
        }
        return true;
    }
    
    public function create(User $user): bool
    {
        // Only doctors can create visits
        return $user->isDoctor();
    }
    
    public function update(User $user, Visit $visit): bool
    {
        // Only the doctor who created the visit can edit it
        // OR admins can edit any visit
        return $user->isAdmin() || ($user->isDoctor() && $user->id === $visit->doctor_id);
    }
    
    public function delete(User $user, Visit $visit): bool
    {
        // Only admins can delete visits
        return $user->isAdmin();
    }
}
```

**Visit Factory** (`database/factories/VisitFactory.php` — on feature branch):
```php
public function definition(): array
{
    return [
        'patient_id' => Patient::factory(),
        'doctor_id' => User::factory()->create(['role' => 'doktor'])->id,
        'date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
        'notes' => $this->faker->optional(0.8)->paragraph(),
    ];
}
```

**Visit Migration** — Exists on feature/3_visits branch

**Patient Relationship** — `Patient` model has `visits()` HasMany relationship

**Status:** Visit domain layer is **complete on feature/3_visits branch** but **not merged to develop**.

### ❌ Missing for Full API Implementation

**Need to Build:**
- ❌ **Merge feature/3_visits branch** to develop (prerequisite)
- ❌ `VisitController` (app/Http/Controllers/Api/) — Nested under patients
- ❌ `VisitResource` (app/Http/Resources/Api/) — JSON:API transformation
- ❌ `StoreVisitRequest` — Validation for visit creation
- ❌ `UpdateVisitRequest` — Validation for visit updates
- ❌ Visit API routes — Nested under `/api/patients/{patient}/visits`
- ❌ Comprehensive Pest HTTP tests — 25+ tests for all scenarios

**Current Status:** No API layer exists yet

---

## Technical Requirements

### API Endpoints & Authorization (Nested Routes)

| Method | URL | Action | Auth | Policy | Returns |
|--------|-----|--------|------|--------|---------|
| GET | `/api/patients/{patient}/visits` | List patient visits | `auth:sanctum` | `viewAny` | Collection + pagination |
| POST | `/api/patients/{patient}/visits` | Create visit | `auth:sanctum` | `create` | 201 + VisitResource |
| GET | `/api/patients/{patient}/visits/{visit}` | View one | `auth:sanctum` | `view` | VisitResource |
| PUT/PATCH | `/api/patients/{patient}/visits/{visit}` | Update visit | `auth:sanctum` | `update` | VisitResource |
| DELETE | `/api/patients/{patient}/visits/{visit}` | Delete visit | `auth:sanctum` | `delete` | 204 No Content |

**Why Nested Routes:**
- Visits don't make sense without patient context
- URL clarity: `/api/patients/5/visits` is more RESTful than `/api/visits?patient_id=5`
- Natural access control: policy receives both patient and visit
- Frontend routing aligns with UI structure (Patient → Visit History → Visit Detail)

**Authorization Matrix:**

| Role | List Visits | Create Visit | View Visit | Edit Own Visit | Edit Other's Visit | Delete Visit |
|------|-------------|--------------|------------|----------------|-------------------|--------------|
| Admin | ✅ All patients | ❌ No | ✅ Any | ✅ Yes | ✅ Yes | ✅ Yes |
| Doctor | ✅ All patients | ✅ Yes | ✅ Any | ✅ Yes | ❌ No | ❌ No |
| Patient | ✅ Own only | ❌ No | ✅ Own only | ❌ No | ❌ No | ❌ No |

**Key Authorization Rules:**
1. **Only doctors can create visits** — admins cannot (clinical decision)
2. **Doctors can only edit their own visits** — not other doctors' notes
3. **Only admins can delete visits** — for data integrity
4. **Patients have read-only access** to their own visit history
5. **Visit list is filtered by patient** — no global visit list endpoint

### JSON:API Resource Structure

**Single Visit Response:**
```json
{
    "data": {
        "type": "visit",
        "id": 42,
        "attributes": {
            "date": "2026-03-15",
            "notes": "Patient presents with mild hypertension. Blood pressure 145/90. Recommended dietary changes and follow-up in 2 weeks. Prescribed lisinopril 10mg daily.",
            "createdAt": "2026-03-15T14:30:00Z",
            "updatedAt": "2026-03-15T16:45:00Z"
        },
        "relationships": {
            "patient": {
                "data": { "type": "patient", "id": 5 }
            },
            "doctor": {
                "data": { "type": "user", "id": 12 }
            }
        },
        "includes": {
            "doctor": {
                "type": "user",
                "id": 12,
                "attributes": {
                    "name": "Dr. Sarah Johnson",
                    "email": "s.johnson@hospital.ba",
                    "role": "doktor"
                }
            }
        },
        "links": {
            "self": "http://localhost/api/patients/5/visits/42"
        }
    }
}
```

**Visit History (Collection) Response:**
```json
{
    "data": [
        {
            "type": "visit",
            "id": 42,
            "attributes": {
                "date": "2026-03-15",
                "notes": "Follow-up visit. BP improved to 130/85...",
                "createdAt": "2026-03-15T14:30:00Z"
            },
            "relationships": {
                "doctor": {
                    "data": { "type": "user", "id": 12 }
                }
            },
            "includes": {
                "doctor": {
                    "type": "user",
                    "id": 12,
                    "attributes": {
                        "name": "Dr. Sarah Johnson"
                    }
                }
            }
        },
        {
            "type": "visit",
            "id": 38,
            "attributes": {
                "date": "2026-03-01",
                "notes": "Initial consultation. Patient reports...",
                "createdAt": "2026-03-01T10:15:00Z"
            },
            "relationships": {
                "doctor": {
                    "data": { "type": "user", "id": 12 }
                }
            }
        }
    ],
    "links": {
        "first": "http://localhost/api/patients/5/visits?page=1",
        "last": "http://localhost/api/patients/5/visits?page=3",
        "prev": null,
        "next": "http://localhost/api/patients/5/visits?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 3,
        "per_page": 15,
        "to": 15,
        "total": 37
    }
}
```

**Key Transformation Rules:**
- ✅ CamelCase attribute keys
- ✅ Eager load `doctor` relationship (User) for display
- ✅ Include patient context in relationships (even though it's in the URL)
- ✅ Chronological order: newest visits first (`orderBy('date', 'desc')`)
- ✅ Pagination for large visit histories (some patients have 100+ visits)
- ❌ Do NOT include patient full details in visit list (redundant — already in URL context)

### Controller Pattern (Nested Resource)

**VisitController Pattern:**
```php
class VisitController extends ApiController
{
    public function index(Patient $patient): AnonymousResourceCollection
    {
        $this->authorize('viewAny', [Visit::class, $patient]);
        
        $visits = $patient->visits()
            ->with('doctor')
            ->orderBy('date', 'desc')
            ->paginate();
        
        return VisitResource::collection($visits);
    }
    
    public function store(StoreVisitRequest $request, Patient $patient): JsonResponse
    {
        $visit = $patient->visits()->create([
            'doctor_id' => $request->user()->id,
            'date' => $request->date,
            'notes' => $request->notes,
        ]);
        
        return $this->created(
            'Visit recorded successfully',
            new VisitResource($visit->load('doctor'))
        );
    }
    
    public function show(Patient $patient, Visit $visit): VisitResource
    {
        $this->authorize('view', $visit);
        
        // Ensure visit belongs to patient (prevent sneaky access)
        if ($visit->patient_id !== $patient->id) {
            abort(404);
        }
        
        return new VisitResource($visit->load('doctor', 'patient'));
    }
    
    public function update(UpdateVisitRequest $request, Patient $patient, Visit $visit): JsonResponse
    {
        $this->authorize('update', $visit);
        
        if ($visit->patient_id !== $patient->id) {
            abort(404);
        }
        
        $visit->update($request->validated());
        
        return $this->ok(
            'Visit updated successfully',
            new VisitResource($visit->load('doctor'))
        );
    }
    
    public function destroy(Patient $patient, Visit $visit): JsonResponse
    {
        $this->authorize('delete', $visit);
        
        if ($visit->patient_id !== $patient->id) {
            abort(404);
        }
        
        $visit->delete(); // Hard delete
        
        return $this->noContent();
    }
}
```

**Key Patterns:**
- **Nested route model binding:** Laravel auto-resolves `{patient}` and `{visit}`
- **Relationship check:** Always verify `visit->patient_id === patient->id` (prevent URL manipulation)
- **Doctor auto-assignment:** `doctor_id` is always `$request->user()->id` (can't fake doctor)
- **Eager loading:** Load `doctor` relationship to avoid N+1 queries
- **Chronological order:** Visits ordered by date DESC (newest first)
- **No service layer needed:** Visit CRUD is simple enough for controller

### Form Request Validation

**StoreVisitRequest:**
```php
public function authorize(): bool
{
    return $this->user()->isDoctor();
}

public function rules(): array
{
    return [
        'date'  => ['required', 'date', 'before_or_equal:today'],
        'notes' => ['nullable', 'string', 'max:10000'],
    ];
}

public function messages(): array
{
    return [
        'date.before_or_equal' => 'Visit date cannot be in the future.',
    ];
}
```

**UpdateVisitRequest:**
```php
public function authorize(): bool
{
    // Policy handles doctor ownership check
    return true;
}

public function rules(): array
{
    return [
        'date'  => ['sometimes', 'date', 'before_or_equal:today'],
        'notes' => ['nullable', 'string', 'max:10000'],
    ];
}
```

**Validation Rules:**
- `date` — Required on create, optional on update, cannot be future date
- `notes` — Always optional, max 10,000 characters (long clinical notes)
- `patient_id` — NOT in request (from URL), `doctor_id` — NOT in request (from auth)

### Routes Configuration

```php
// routes/api.php

Route::middleware('auth:sanctum')->group(function () {
    
    // Nested Visit Routes (under patients)
    Route::apiResource('patients.visits', VisitController::class)
        ->scoped(['visit' => 'patient']); // Ensures visit belongs to patient
    
    // Results in these routes:
    // GET    /api/patients/{patient}/visits           → index
    // POST   /api/patients/{patient}/visits           → store
    // GET    /api/patients/{patient}/visits/{visit}   → show
    // PUT    /api/patients/{patient}/visits/{visit}   → update
    // DELETE /api/patients/{patient}/visits/{visit}   → destroy
});
```

**Scoped Route Binding:**
- `->scoped(['visit' => 'patient'])` ensures `visit.patient_id = patient.id`
- Laravel automatically returns 404 if visit doesn't belong to patient
- Extra safety layer beyond controller checks

---

## Testing Requirements (SE M3: Min 25 Tests)

**Required Pest HTTP Test Coverage:**

### Visit Listing Tests (Patient Context)
```php
it('admin can list all visits for any patient');
it('doctor can list all visits for any patient');
it('patient can list their own visits');
it('patient cannot list other patients visits'); // 403
it('unauthenticated request returns 401');
it('visit list is ordered by date descending');
it('visit list includes pagination metadata');
it('visit list eager loads doctor relationship');
it('empty visit history returns empty data array');
```

### Visit Creation Tests
```php
it('doctor can create visit for patient');
it('doctor_id is auto-assigned from authenticated user');
it('admin cannot create visit'); // 403 (only doctors conduct visits)
it('patient cannot create visit'); // 403
it('create requires date field'); // 422
it('create validates date is not in future'); // 422
it('create allows notes to be null');
it('create validates notes max length'); // 422
it('create returns 201 with correct resource structure');
it('patient_id is taken from URL not request body');
```

### Visit Viewing Tests
```php
it('admin can view any visit');
it('doctor can view any visit');
it('patient can view own visits');
it('patient cannot view other patients visits'); // 403
it('viewing non-existent visit returns 404');
it('view includes doctor relationship');
it('view includes patient relationship');
it('view returns correct JSON:API structure');
it('view fails if visit patient_id mismatches URL patient'); // 404
```

### Visit Update Tests
```php
it('doctor can update own visit');
it('doctor cannot update another doctors visit'); // 403
it('admin can update any visit');
it('patient cannot update visits'); // 403
it('update validates date is not future'); // 422
it('update allows partial data (only notes)');
it('update allows partial data (only date)');
it('update returns updated resource');
it('update fails if visit patient_id mismatches URL patient'); // 404
```

### Visit Deletion Tests
```php
it('admin can delete any visit');
it('doctor cannot delete visits'); // 403 (only admins)
it('patient cannot delete visits'); // 403
it('delete hard deletes not soft deletes');
it('deleted visit is completely removed from database');
it('delete returns 204 no content');
it('delete fails if visit patient_id mismatches URL patient'); // 404
```

### Response Format Tests
```php
it('visit resource uses camelCase keys');
it('visit resource includes date as Y-m-d format');
it('visit resource includes doctor in relationships');
it('visit resource includes doctor details in includes');
it('visit resource includes links.self with nested URL');
it('visit collection is ordered newest first');
```

**Minimum:** 30+ tests for comprehensive coverage

---

## Implementation Deliverables

### 0. Prerequisites
- **Merge feature/3_visits branch** to develop
  - Brings Visit model, migration, factory, policy to main codebase
  - Verify all Livewire-era tests pass after merge
  - Run migrations on development database

### 1. VisitController
- Nested resource controller under patients
- Full CRUD: `index()`, `store()`, `show()`, `update()`, `destroy()`
- Validates `visit->patient_id === patient->id` in show/update/destroy
- Auto-assigns `doctor_id` from authenticated user on create
- Eager loads `doctor` relationship
- Orders visits by date DESC in index
- Calls `$this->authorize()` for all protected actions

### 2. VisitResource
- JSON:API structure with `type`, `id`, `attributes`, `relationships`, `links`
- CamelCase attribute keys
- Includes `doctor` in relationships
- Conditionally includes doctor details in `includes` section
- Uses nested route names for links (`patients.visits.show`)

### 3. Form Requests
- `StoreVisitRequest` — Validates date (required, not future), notes (optional)
- `UpdateVisitRequest` — Same rules but all fields optional
- Authorization checks for doctor role

### 4. Routes
- `Route::apiResource('patients.visits', VisitController::class)->scoped(['visit' => 'patient'])`
- Nested under patients, scoped binding for safety
- All routes behind `auth:sanctum` middleware

### 5. Tests
- Minimum 30 Pest HTTP tests covering all scenarios
- Test nested route structure
- Test authorization matrix (admin, doctor, patient roles)
- Test doctor ownership (can only edit own visits)
- Test validation rules
- Test hard deletes
- Test response structure
- Test relationship loading

### 6. Documentation
- Postman collection with all visit endpoints
- Example nested route requests
- API documentation section for visits

---

## Success Criteria

- ✅ feature/3_visits branch successfully merged to develop
- ✅ All 5 CRUD endpoints work with nested routes
- ✅ Doctor can only edit visits they created
- ✅ Admin can delete but not create visits
- ✅ Patients have read-only access to own visits
- ✅ Visit list is chronologically ordered (newest first)
- ✅ `visit->patient_id` is validated against URL `{patient}`
- ✅ Hard deletes work correctly (no soft delete)
- ✅ All responses follow JSON:API with camelCase
- ✅ Doctor relationship loads without N+1 queries
- ✅ All 30+ tests pass
- ✅ Code passes quality gates (Laravel Pint, Larastan level 5)
- ✅ Postman collection or API docs complete

---

## Dependencies & Prerequisites

**Must Be Complete First:**
- ✅ Group 1 (Auth & User Management) — Need authenticated doctors
- ✅ Group 2 (Patient Management) — Visits belong to patients
- ⚠️ feature/3_visits branch — **MUST MERGE** before API work starts

**Already Complete (on feature/3_visits):**
- ✅ Visit model with relationships
- ✅ Visit migration (table exists)
- ✅ Visit factory (functional)
- ✅ VisitPolicy with comprehensive authorization
- ✅ Patient `visits()` HasMany relationship

**Blocks (this work blocks):**
- nutri-ledger-jb2 (M3 — Patterns, Tests & Deployment) — Needs API tests
- Phase 6 FE — Frontend visit management UI
- Phase 7 Polish — Quality gate verification
- Future Groups 4-11 — Vitals, Labs, Meds will nest under visits

---

## Out of Scope (Future Enhancements)

- ❌ Visit templates (pre-fill notes for common conditions)
- ❌ Visit attachments (lab reports, images)
- ❌ Visit time tracking (arrival, start, end times)
- ❌ Visit billing/coding (ICD-10, CPT codes)
- ❌ Visit signatures (doctor signature capture)
- ❌ Visit approval workflow (require approval before finalizing)
- ❌ Visit export (print, PDF, share)
- ❌ Visit search/filtering (by date range, doctor, diagnosis)
- ❌ Visit statistics (average duration, visits per month)
- ❌ Telemedicine visits (virtual vs in-person flag)
- ❌ Soft deletes — intentionally out of scope for MVP

---

## Reference Documents

**Must Read:**
1. `LARAVEL_API_RULES.md` — Core design patterns, nested resources
2. `API_PREREQUISITES_AND_APPROACH.md` — Current state, approach
3. `DB_SCHEMA_FINAL.md` — Visits table structure
4. `3_VISITS_ENCOUNTERS_FEATURE_TASKS.md` — Original Livewire implementation
5. `Apis/TASK_11_NESTED_RESOURCES.md` — Nested route patterns (if exists)
6. `SE_MVP_PLAN.md` — User stories for visits

**User Stories (SE M2/M3):**
- US21: As a doctor, I can create a new visit record for a patient
- US22: As an admin or doctor, I can view the full visit history for a specific patient
- US23: As a doctor, I can view the details of a specific visit
- US24: As a doctor, I can edit the notes of a visit I conducted
- US25: As an admin, I can delete a visit record
- US26: As a patient, I can view my own visit history (read-only)
- US27: As a doctor, I can record the date and clinical notes for each visit

---

## Notes for Spec-Kit

- Use TDD approach: write tests first (RED), implement feature (GREEN), refactor (REFACTOR)
- **Merge feature/3_visits first** — this is a hard prerequisite
- **Nested routes are essential** — visits don't exist without patient context
- **Doctor ownership is critical** — only doctor who created visit can edit
- **Admin paradox:** Can delete but not create visits (clinical decision)
- **Patient read-only:** No exceptions — patients never modify visit history
- **Hard deletes only:** No soft deletes for visits in MVP
- **Chronological order:** Always newest first in collections
- **Scoped binding:** Use `->scoped()` for route safety
- **Relationship checks:** Always validate `visit->patient_id === patient->id`
- **Auto-assign doctor:** Never trust `doctor_id` from request — always use auth user
- **N+1 prevention:** Eager load `doctor` in index and show
- Follow existing project patterns (ApiController, ApiResponses, JSON:API)

---

**Ready for spec-kit.specify → spec-kit.plan → spec-kit.tasks**
