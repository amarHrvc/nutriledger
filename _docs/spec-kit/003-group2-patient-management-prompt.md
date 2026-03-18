# Spec-Kit Feature Prompt: Group 2 — Patient Management API

## Epic Context
**BD Issue:** nutri-ledger-fg2 (P1)  
**Epic Title:** [PLANNING] Group 2 — Patient Management  
**Track:** SE (Software Engineering) MVP — Feature Groups 1-3  
**Priority:** P1 (High)  
**Depends On:** nutri-ledger-est (Group 1 — Auth & User Management)

---

## Feature Description

Build a comprehensive REST API for Patient Management in the NutriLedger healthcare application. This feature enables authorized users (admins and doctors) to manage patient records including personal information, medical metadata, and socioeconomic data. The API follows Laravel best practices, JSON:API conventions, and implements role-based authorization with multi-level access control.

**Core Capabilities:**
- **Patient Registration:** Create patient profiles linked to user accounts (1:1 relationship)
- **Patient Listing:** View all patients (admin/doctor) or own record only (patient role)
- **Patient Details:** Access comprehensive patient information including medical and socioeconomic data
- **Patient Updates:** Modify patient information with proper authorization
- **Patient Archival:** Soft delete patient records (admin/doctor only) with restoration capability
- **Socioeconomic Integration:** Automatically include socioeconomic data when loaded
- **Policy-Based Access:** Enforce fine-grained authorization rules via PatientPolicy

**What Makes This Different from Standard CRUD:**
- **Dual-entity creation:** Creating a patient also creates associated socioeconomic record
- **Role-based filtering:** Patient role sees only their own record; admin/doctor see all
- **Relationship loading:** Smart inclusion of user and socioeconomic data
- **Medical data handling:** Sensitive health information (blood type, allergies, medical notes)
- **1:1 User linkage:** Each patient is tied to exactly one user account
- **Soft deletes with policy control:** Only admin/doctor can archive/restore patients

---

## Database Structure & Relationships

### Primary Tables

#### `patients` Table
```sql
CREATE TABLE patients (
    id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id                 BIGINT UNSIGNED UNIQUE NOT NULL,
    first_name              VARCHAR(255) NOT NULL,
    last_name               VARCHAR(255) NOT NULL,
    date_of_birth           DATE NOT NULL,
    gender                  ENUM('M', 'F') NOT NULL,
    phone                   VARCHAR(255) NOT NULL,
    address                 VARCHAR(255) NULL,
    city                    VARCHAR(255) NULL,
    postal_code             VARCHAR(255) NULL,
    emergency_contact_name  VARCHAR(255) NOT NULL,
    emergency_contact_phone VARCHAR(255) NOT NULL,
    blood_type              ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NULL,
    allergies               TEXT NULL,
    medical_notes           TEXT NULL,
    deleted_at              TIMESTAMP NULL,
    created_at              TIMESTAMP NOT NULL,
    updated_at              TIMESTAMP NOT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_patient_name (last_name, first_name),
    INDEX idx_patient_deleted_at (deleted_at)
);
```

**Key Fields:**
- `user_id` — **Unique FK** to users table (1:1 relationship)
- `first_name`, `last_name` — Patient's legal name (separate from user.name)
- `date_of_birth` — For age calculation, care planning
- `gender` — M (Male) or F (Female) — medical relevance
- `blood_type` — Critical medical metadata (nullable)
- `allergies` — Free-text allergies list (tech debt: should be separate table)
- `medical_notes` — Doctor's clinical notes
- `deleted_at` — Soft delete support (archived patients)

#### `patient_socioeconomic` Table
```sql
CREATE TABLE patient_socioeconomic (
    id                           BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    patient_id                   BIGINT UNSIGNED UNIQUE NOT NULL,
    marital_status               ENUM(...) NULL,
    number_of_dependents         INT NULL,
    living_arrangement           ENUM(...) NULL,
    employment_status            ENUM(...) NULL,
    occupation                   VARCHAR(255) NULL,
    income_level                 ENUM('low','lower_middle','middle','upper_middle','high') NULL,
    has_health_insurance         BOOLEAN DEFAULT false,
    education_level              ENUM(...) NULL,
    smoking_status               ENUM('never','former','current_light','current_heavy') NULL,
    alcohol_consumption          ENUM('none','occasional','moderate','heavy') NULL,
    physical_activity_level      ENUM('sedentary','lightly_active','moderately_active','very_active') NULL,
    has_family_support           BOOLEAN DEFAULT false,
    has_caregiver                BOOLEAN DEFAULT false,
    transportation_access        ENUM(...) NULL,
    food_security_status         ENUM(...) NULL,
    dietary_restrictions_cultural TEXT NULL,
    additional_notes             TEXT NULL,
    created_at                   TIMESTAMP NOT NULL,
    updated_at                   TIMESTAMP NOT NULL,
    
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);
```

**Purpose:** Social determinants of health data for comprehensive care planning.

**Key Fields:**
- `patient_id` — **Unique FK** to patients table (1:1 relationship)
- `marital_status`, `living_arrangement` — Living situation
- `employment_status`, `occupation`, `income_level` — Economic factors
- `smoking_status`, `alcohol_consumption`, `physical_activity_level` — Lifestyle factors
- `food_security_status` — Nutrition access
- All fields nullable — collected incrementally during care

### Relationship Diagram

```
User (1) ←→ (1) Patient (1) ←→ (1) PatientSocioeconomic
  └─ role: pacijent            └─ visits (1→many)
```

**Relationship Rules:**
1. One User can be linked to at most one Patient
2. One Patient must have exactly one User
3. One Patient can have at most one PatientSocioeconomic record
4. When Patient is created, PatientSocioeconomic is auto-created (empty)
5. When Patient is deleted (soft), associated data remains (for restoration)
6. When User is hard-deleted, Patient cascades (ON DELETE CASCADE)

---

## Current Implementation Status

### ✅ Already Implemented (Domain Layer Complete)

**Patient Model** (`app/Models/Patient.php`):
```php
class Patient extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'date_of_birth', 'gender',
        'address', 'city', 'postal_code', 'phone',
        'emergency_contact_name', 'emergency_contact_phone',
        'blood_type', 'allergies', 'medical_notes',
    ];
    
    // Relationships
    public function user(): BelongsTo;
    public function socioeconomic(): HasOne;
    public function visits(): HasMany;
    
    // Accessor
    protected function fullName(): Attribute; // "FirstName LastName"
    
    // Casts
    protected function casts(): ['date_of_birth' => 'date'];
}
```

**PatientSocioeconomic Model** (`app/Models/PatientSocioeconomic.php`):
```php
class PatientSocioeconomic extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'patient_id', 'marital_status', 'number_of_dependents',
        'living_arrangement', 'employment_status', 'occupation',
        'income_level', 'has_health_insurance', 'education_level',
        'smoking_status', 'alcohol_consumption', 'physical_activity_level',
        'has_family_support', 'has_caregiver', 'transportation_access',
        'food_security_status', 'dietary_restrictions_cultural', 'additional_notes',
    ];
    
    public function patient(): BelongsTo;
    
    protected function casts(): [
        'has_health_insurance' => 'boolean',
        'has_family_support' => 'boolean',
        'has_caregiver' => 'boolean',
        'number_of_dependents' => 'integer',
    ];
}
```

**PatientPolicy** (`app/Policies/PatientPolicy.php`):
```php
class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }
    
    public function view(User $user, Patient $patient): bool
    {
        // Admin/Doctor: all patients
        // Patient: own record only
        if ($user->isPatient() && $user->id !== $patient->user_id) {
            return false;
        }
        return true;
    }
    
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }
    
    public function update(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isDoctor() 
            || ($user->isPatient() && $user->id === $patient->user_id);
    }
    
    public function delete(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }
    
    public function restore(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }
    
    public function forceDelete(User $user, Patient $patient): bool
    {
        return $user->isAdmin();
    }
}
```

**Patient Factory** — Fully functional with realistic data generation

**Migrations** — Both tables created and migrated

### ❌ Missing for Full API Implementation

**Need to Build:**
- ❌ `PatientController` (app/Http/Controllers/Api/) — Full CRUD with nested socioeconomic
- ❌ `PatientResource` (app/Http/Resources/Api/) — JSON:API compliant transformation
- ❌ `PatientSocioeconomicResource` — Nested in PatientResource
- ❌ `StorePatientRequest` — Validation for patient + socioeconomic creation
- ❌ `UpdatePatientRequest` — Validation for updates
- ❌ Patient API routes — Registered with policy middleware
- ❌ Comprehensive Pest HTTP tests — 25+ tests for all scenarios

**Current Status:** Stub form requests exist but have no validation rules

---

## Technical Requirements

### API Endpoints & Authorization

| Method | URL | Action | Auth | Policy | Returns |
|--------|-----|--------|------|--------|---------|
| GET | `/api/patients` | List all (filtered by role) | `auth:sanctum` | `viewAny` | Collection + pagination |
| POST | `/api/patients` | Create patient + socioeconomic | `auth:sanctum` | `create` | 201 + PatientResource |
| GET | `/api/patients/{id}` | View one | `auth:sanctum` | `view` | PatientResource |
| PUT/PATCH | `/api/patients/{id}` | Update patient + socioeconomic | `auth:sanctum` | `update` | PatientResource |
| DELETE | `/api/patients/{id}` | Soft delete | `auth:sanctum` | `delete` | 204 No Content |
| POST | `/api/patients/{id}/restore` | Restore soft-deleted | `auth:sanctum` | `restore` | PatientResource |

**Authorization Matrix:**

| Role | List All | View Own | View Others | Create | Update Own | Update Others | Delete | Restore |
|------|----------|----------|-------------|--------|------------|---------------|--------|---------|
| Admin | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Doctor | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Patient | ❌ | ✅ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ |

### JSON:API Resource Structure

**Single Patient Response:**
```json
{
    "data": {
        "type": "patient",
        "id": 1,
        "attributes": {
            "firstName": "John",
            "lastName": "Doe",
            "fullName": "John Doe",
            "dateOfBirth": "1990-01-15",
            "age": 34,
            "gender": "M",
            "phone": "+387 61 234 567",
            "address": "123 Main St",
            "city": "Sarajevo",
            "postalCode": "71000",
            "emergencyContactName": "Jane Doe",
            "emergencyContactPhone": "+387 61 345 678",
            "bloodType": "A+",
            "allergies": "Penicillin, Pollen",
            "medicalNotes": "Hypertension, regular checkups needed",
            "createdAt": "2024-01-01T00:00:00Z",
            "updatedAt": "2024-06-01T10:30:00Z"
        },
        "relationships": {
            "user": {
                "data": { "type": "user", "id": 5 }
            },
            "socioeconomic": {
                "data": { "type": "patient_socioeconomic", "id": 1 }
            }
        },
        "includes": {
            "socioeconomic": {
                "type": "patient_socioeconomic",
                "id": 1,
                "attributes": {
                    "maritalStatus": "married",
                    "numberOfDependents": 2,
                    "employmentStatus": "employed_full_time",
                    "occupation": "Software Engineer",
                    "incomeLevel": "middle",
                    "hasHealthInsurance": true,
                    "smokingStatus": "never",
                    "alcoholConsumption": "occasional",
                    "physicalActivityLevel": "moderately_active",
                    "hasFamilySupport": true,
                    "foodSecurityStatus": "food_secure"
                }
            }
        },
        "links": {
            "self": "http://localhost/api/patients/1"
        }
    }
}
```

**Collection Response (with pagination):**
```json
{
    "data": [ /* array of patient resources */ ],
    "links": {
        "first": "http://localhost/api/patients?page=1",
        "last": "http://localhost/api/patients?page=10",
        "prev": null,
        "next": "http://localhost/api/patients?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 10,
        "path": "http://localhost/api/patients",
        "per_page": 15,
        "to": 15,
        "total": 142
    }
}
```

**Key Transformation Rules:**
- ✅ All attribute keys in **camelCase** (not snake_case)
- ✅ Include computed `fullName` accessor
- ✅ Include computed `age` from dateOfBirth (if needed)
- ✅ Eager load `socioeconomic` when appropriate
- ✅ Never expose `user_id` directly (use relationships)
- ✅ Include `includes` section when relationships are loaded

### Controller Pattern (with Service Layer)

**Recommended: Introduce Service Layer here**

**Why Service Layer:**
- Creating a patient requires creating both `Patient` and `PatientSocioeconomic` records
- Business logic should not live in controllers
- Makes testing easier (mock service instead of database)
- Follows Single Responsibility Principle

**Structure:**
```
app/
└── Services/
    └── PatientService.php
```

**PatientController Pattern:**
```php
class PatientController extends ApiController
{
    public function __construct(
        private readonly PatientService $patientService
    ) {}
    
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Patient::query()
            ->with(['user', 'socioeconomic'])
            ->when(
                $request->user()->isPatient(),
                fn($q) => $q->where('user_id', $request->user()->id)
            );
        
        $patients = $query->paginate();
        
        return PatientResource::collection($patients);
    }
    
    public function store(StorePatientRequest $request): JsonResponse
    {
        $patient = $this->patientService->createPatient(
            $request->validated()
        );
        
        return $this->created(
            'Patient registered successfully',
            new PatientResource($patient->load('user', 'socioeconomic'))
        );
    }
    
    public function show(Patient $patient): PatientResource
    {
        $this->authorize('view', $patient);
        
        return new PatientResource(
            $patient->load('user', 'socioeconomic')
        );
    }
    
    public function update(UpdatePatientRequest $request, Patient $patient): JsonResponse
    {
        $this->authorize('update', $patient);
        
        $patient = $this->patientService->updatePatient(
            $patient,
            $request->validated()
        );
        
        return $this->ok(
            'Patient updated successfully',
            new PatientResource($patient->load('user', 'socioeconomic'))
        );
    }
    
    public function destroy(Patient $patient): JsonResponse
    {
        $this->authorize('delete', $patient);
        
        $patient->delete();
        
        return $this->noContent();
    }
    
    public function restore(int $id): JsonResponse
    {
        $patient = Patient::withTrashed()->findOrFail($id);
        
        $this->authorize('restore', $patient);
        
        $patient->restore();
        
        return $this->ok(
            'Patient restored successfully',
            new PatientResource($patient->load('user', 'socioeconomic'))
        );
    }
}
```

**PatientService Pattern:**
```php
class PatientService
{
    public function createPatient(array $data): Patient
    {
        DB::beginTransaction();
        
        try {
            // Split data
            $patientData = Arr::only($data, [
                'user_id', 'first_name', 'last_name', 'date_of_birth',
                'gender', 'phone', 'address', 'city', 'postal_code',
                'emergency_contact_name', 'emergency_contact_phone',
                'blood_type', 'allergies', 'medical_notes',
            ]);
            
            $socioData = Arr::only($data, [
                'marital_status', 'number_of_dependents', /* ... */
            ]);
            
            // Create patient
            $patient = Patient::create($patientData);
            
            // Create socioeconomic (even if empty)
            $patient->socioeconomic()->create($socioData);
            
            DB::commit();
            
            return $patient;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function updatePatient(Patient $patient, array $data): Patient
    {
        DB::beginTransaction();
        
        try {
            // Update patient fields
            $patientData = Arr::only($data, [/* patient fields */]);
            $patient->update($patientData);
            
            // Update socioeconomic fields if provided
            $socioData = Arr::only($data, [/* socio fields */]);
            if (!empty($socioData)) {
                $patient->socioeconomic()->updateOrCreate(
                    ['patient_id' => $patient->id],
                    $socioData
                );
            }
            
            DB::commit();
            
            return $patient->fresh();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

### Form Request Validation

**StorePatientRequest:**
```php
public function rules(): array
{
    return [
        // Patient fields (required)
        'user_id'                 => ['required', 'exists:users,id', 'unique:patients,user_id'],
        'first_name'              => ['required', 'string', 'max:255'],
        'last_name'               => ['required', 'string', 'max:255'],
        'date_of_birth'           => ['required', 'date', 'before:today'],
        'gender'                  => ['required', 'in:M,F'],
        'phone'                   => ['required', 'string', 'max:255'],
        'emergency_contact_name'  => ['required', 'string', 'max:255'],
        'emergency_contact_phone' => ['required', 'string', 'max:255'],
        
        // Patient fields (optional)
        'address'      => ['nullable', 'string', 'max:255'],
        'city'         => ['nullable', 'string', 'max:255'],
        'postal_code'  => ['nullable', 'string', 'max:255'],
        'blood_type'   => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
        'allergies'    => ['nullable', 'string'],
        'medical_notes' => ['nullable', 'string'],
        
        // Socioeconomic fields (all optional)
        'marital_status'          => ['nullable', 'in:single,married,divorced,widowed,separated,other'],
        'number_of_dependents'    => ['nullable', 'integer', 'min:0'],
        'employment_status'       => ['nullable', 'in:employed_full_time,employed_part_time,self_employed,unemployed,retired,student,unable_to_work,other'],
        'income_level'            => ['nullable', 'in:low,lower_middle,middle,upper_middle,high'],
        'has_health_insurance'    => ['nullable', 'boolean'],
        'smoking_status'          => ['nullable', 'in:never,former,current_light,current_heavy'],
        'alcohol_consumption'     => ['nullable', 'in:none,occasional,moderate,heavy'],
        'physical_activity_level' => ['nullable', 'in:sedentary,lightly_active,moderately_active,very_active'],
        // ... more socioeconomic fields
    ];
}
```

**UpdatePatientRequest:**
- Same rules but all fields are `sometimes` instead of `required`
- `user_id` cannot be changed after creation (exclude from update)
- Email uniqueness check excludes current patient's user

### Routes Configuration

```php
// routes/api.php

Route::middleware('auth:sanctum')->group(function () {
    
    // Patient Management
    Route::apiResource('patients', PatientController::class);
    
    // Patient Restore (soft delete reversal)
    Route::post('/patients/{id}/restore', [PatientController::class, 'restore'])
        ->name('patients.restore');
        
    // Future: nested routes for visits
    // Route::apiResource('patients.visits', VisitController::class);
});
```

**Note:** Authorization is handled by PatientPolicy, called via `$this->authorize()` in controller

---

## Testing Requirements (SE M3: Min 25 Tests)

**Required Pest HTTP Test Coverage:**

### Patient Listing Tests
```php
it('admin can list all patients with pagination');
it('doctor can list all patients with pagination');
it('patient can list only their own record');
it('patient cannot see other patients in list');
it('unauthenticated request returns 401');
it('list includes pagination metadata');
it('list eager loads user and socioeconomic relationships');
it('list can include soft-deleted patients for admin');
```

### Patient Creation Tests
```php
it('admin can create patient with all fields');
it('doctor can create patient with all fields');
it('patient cannot create patients'); // 403
it('create requires user_id that exists');
it('create requires unique user_id');
it('create validates required fields'); // 422
it('create validates date_of_birth is before today'); // 422
it('create validates gender is M or F'); // 422
it('create validates blood_type enum values'); // 422
it('create automatically creates empty socioeconomic record');
it('create can include socioeconomic data');
it('create returns 201 with correct resource structure');
```

### Patient Viewing Tests
```php
it('admin can view any patient');
it('doctor can view any patient');
it('patient can view own record');
it('patient cannot view other patients'); // 403
it('viewing non-existent patient returns 404');
it('view includes user relationship');
it('view includes socioeconomic relationship');
it('view returns correct JSON:API structure');
```

### Patient Update Tests
```php
it('admin can update any patient');
it('doctor can update any patient');
it('patient can update own record');
it('patient cannot update other patients'); // 403
it('update validates optional fields'); // 422
it('update can modify patient fields only');
it('update can modify socioeconomic fields only');
it('update can modify both patient and socioeconomic');
it('update cannot change user_id after creation'); // 422
it('update returns updated resource');
```

### Patient Deletion Tests
```php
it('admin can soft delete patient');
it('doctor can soft delete patient');
it('patient cannot delete patients'); // 403
it('delete soft deletes not hard deletes');
it('deleted patient is hidden from normal queries');
it('deleted patient can be queried with withTrashed');
it('delete returns 204 no content');
```

### Patient Restoration Tests
```php
it('admin can restore soft deleted patient');
it('doctor can restore soft deleted patient');
it('patient cannot restore patients'); // 403
it('restore makes patient visible again');
it('restore returns restored resource');
```

### Response Format Tests
```php
it('patient resource uses camelCase keys');
it('patient resource includes fullName accessor');
it('patient resource excludes sensitive fields');
it('patient resource includes relationships section');
it('patient resource includes links.self');
it('socioeconomic resource uses camelCase keys');
it('socioeconomic resource includes all social determinants');
```

**Minimum:** 35+ tests for comprehensive coverage

---

## Implementation Deliverables

### 1. PatientController
- Full CRUD: `index()`, `store()`, `show()`, `update()`, `destroy()`
- Additional: `restore()` for soft delete reversal
- Uses `PatientService` for complex operations
- Calls `$this->authorize()` for all protected actions
- Returns `PatientResource` for all data responses
- Eager loads `user` and `socioeconomic` relationships

### 2. PatientService
- `createPatient(array $data): Patient` — Creates patient + socioeconomic atomically
- `updatePatient(Patient $patient, array $data): Patient` — Updates both records
- Uses DB transactions for data integrity
- Handles array splitting (patient vs socioeconomic fields)

### 3. PatientResource & PatientSocioeconomicResource
- JSON:API structure with `type`, `id`, `attributes`, `relationships`, `links`
- CamelCase attribute keys
- Includes `fullName` accessor
- Conditionally includes socioeconomic via `includes` section
- Uses named routes for links

### 4. Form Requests
- `StorePatientRequest` — Validates creation (30+ fields, patient + socio)
- `UpdatePatientRequest` — Validates updates (all optional except constraints)
- Authorizes via policy checks
- Returns 422 with field-specific errors on validation failure

### 5. Routes
- `Route::apiResource('patients', PatientController::class)`
- `Route::post('/patients/{id}/restore', ...)`
- All routes behind `auth:sanctum` middleware
- Policy authorization called in controller methods

### 6. Tests
- Minimum 35 Pest HTTP tests covering all scenarios
- Test all CRUD operations
- Test authorization matrix (admin, doctor, patient roles)
- Test validation rules comprehensively
- Test soft deletes and restoration
- Test response structure compliance
- Test relationship loading

### 7. Documentation
- Postman collection with all patient endpoints
- Example requests/responses for each endpoint
- API documentation in README or separate API.md file

---

## Success Criteria

- ✅ All 6 CRUD endpoints + restore return correct status codes
- ✅ Role-based filtering works (patient sees own, admin/doctor see all)
- ✅ PatientPolicy enforces all authorization rules correctly
- ✅ Creating patient auto-creates socioeconomic record
- ✅ Updating works for patient fields, socioeconomic fields, or both
- ✅ Soft deletes work correctly with restoration
- ✅ All responses follow JSON:API structure with camelCase
- ✅ Relationships (user, socioeconomic) load correctly
- ✅ All 35+ tests pass
- ✅ Code passes quality gates (Laravel Pint, Larastan level 5)
- ✅ Postman collection or API docs complete

---

## Dependencies & Prerequisites

**Already Complete:**
- ✅ Patient model with soft deletes, relationships, accessors
- ✅ PatientSocioeconomic model with relationships, casts
- ✅ PatientPolicy with comprehensive authorization rules
- ✅ Patient factory with realistic data generation
- ✅ Migrations for both tables
- ✅ User model with role helpers (isAdmin, isDoctor, isPatient)
- ✅ ApiResponses trait for consistent responses
- ✅ Sanctum authentication configured

**Blocks (this work blocks):**
- Group 3 (Visits & Encounters) — Visits belong to patients
- Phase 6 FE — Frontend patient management UI
- Phase 7 Polish — Needs passing API tests

---

## Out of Scope (Future Enhancements)

- ❌ Patient photo/avatar upload
- ❌ Patient document attachments (lab results, prescriptions)
- ❌ Patient consent forms management
- ❌ Patient appointment scheduling (separate feature)
- ❌ Patient messaging/communication
- ❌ Patient portal self-registration (admin/doctor registers patients)
- ❌ Allergies as separate table (tech debt acknowledged)
- ❌ Advanced search/filtering (name, DOB, blood type)
- ❌ Export patient data (CSV, PDF)
- ❌ Patient audit log (who updated what, when)

---

## Reference Documents

**Must Read:**
1. `LARAVEL_API_RULES.md` — Core design patterns, service layer
2. `API_PREREQUISITES_AND_APPROACH.md` — Current state, missing pieces
3. `DB_SCHEMA_FINAL.md` — Complete database structure
4. `Apis/TASK_01_API_FOUNDATIONS.md` — Response trait, controllers
5. `Apis/TASK_07_RESPONSE_PAYLOADS.md` — Eloquent Resources, JSON:API
6. `2_PATIENT_MANAGEMENT_FEATURE_TASKS.md` — Original Livewire implementation (for context)

**User Stories (SE M2):**
- US10: As an admin or doctor, I can register a new patient with their personal and medical details
- US11: As an admin or doctor, I can view the full list of all patients
- US12: As a doctor, I can view detailed information for a specific patient
- US13: As a patient, I can view my own profile and medical information
- US14: As an admin or doctor, I can update a patient's personal information
- US15: As an admin or doctor, I can update a patient's medical metadata
- US16: As an admin, I can soft-delete a patient record
- US17: As an admin, I can restore a soft-deleted patient record
- US18: As an admin or doctor, I can view and update a patient's socioeconomic profile

---

## Notes for Spec-Kit

- Use TDD approach: write tests first (RED), implement feature (GREEN), refactor (REFACTOR)
- **Service layer is essential** — dual-entity creation requires transaction management
- Patient + socioeconomic are created together but updated independently
- All socioeconomic fields are nullable — data collected incrementally
- Role-based filtering is critical — patients see only own record
- Soft deletes with restoration — never hard delete in MVP
- CamelCase in JSON responses is non-negotiable
- Eager load relationships to avoid N+1 queries
- Medical data is sensitive — handle with care, proper authorization
- Follow existing project patterns (ApiController, ApiResponses, JSON:API)

---

**Ready for spec-kit.specify → spec-kit.plan → spec-kit.tasks**
