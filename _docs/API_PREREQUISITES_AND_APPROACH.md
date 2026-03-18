# API Prerequisites & Approach Summary

**Date:** 2026-03-19  
**Context:** SE Track — Rest API Foundation  
**Current Status:** Basic auth endpoints implemented, patient/visit APIs pending

---

## 📚 Documentation Sources Analyzed

1. **LARAVEL_API_RULES.md** — Comprehensive Laravel REST API design rules & patterns
2. **Apis/** folder — 21 task documents from Laravel API Masterclass
3. **SE_MVP_PLAN.md** — SE track milestones, user stories, and deliverables
4. **IMPLEMENTATION_STATUS.md** — Current implementation state (git-verified)
5. **Current routes/api.php** — Shows auth foundation already in place

---

## ✅ What's Already Implemented (Backend Foundation)

### 1. Domain Layer (Complete & Reusable)
All models, migrations, factories, and policies are done and committed to master:

| Entity | Status | Components |
|--------|--------|------------|
| Users | ✅ Complete | Migration, Model, Factory, Policy, RoleMiddleware |
| Patients | ✅ Complete | Migration, Model, Factory, Policy, Form Requests |
| Patient Socioeconomic | ✅ Complete | Migration, Model, Factory (1:1 with Patient) |
| Visits | ⚠️ On feature branch | Migration, Model, Factory, Policy (needs merge) |

### 2. Authentication Layer (Implemented)
Current `routes/api.php` shows:
- ✅ Sanctum auth configured (`auth:sanctum` middleware)
- ✅ `AuthController` with `login`, `register`, `logout`
- ✅ `POST /api/login` — returns token
- ✅ `POST /api/register` — creates user + token
- ✅ `POST /api/logout` — revokes token (requires auth)
- ✅ `RoleMiddleware` registered (`role:admin`, `role:admin,doktor`)
- ✅ Test stub routes for role verification

### 3. Tools & Configuration
- ✅ Sanctum installed and configured
- ✅ CORS configured for frontend origin
- ✅ Role-based middleware working
- ✅ Pest test framework in place (~160 tests, mostly Livewire-era)

---

## ❌ What's Missing for Full API Implementation

### 1. Resource Controllers (Priority 1)
None of the CRUD controllers exist yet:

**Needed:**
- `app/Http/Controllers/Api/UserController.php` — User CRUD (admin only)
- `app/Http/Controllers/Api/PatientController.php` — Patient CRUD (role-gated)
- `app/Http/Controllers/Api/VisitController.php` — Visit CRUD (doctor + patient read-only)

**Current Status:** Only `show()` method exists on UserController (line 15 in routes/api.php)

### 2. Eloquent Resources (Priority 1)
Transform models into structured JSON responses:

**Needed:**
```
app/Http/Resources/Api/
├── UserResource.php
├── PatientResource.php
├── PatientSocioeconomicResource.php
└── VisitResource.php
```

**Format:** JSON:API structure with `type`, `id`, `attributes`, `relationships`, `links`

### 3. API Routes (Priority 1)
Currently only auth routes exist. Need resource routes:

**User Routes (admin only):**
```php
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('users', UserController::class);
});
```

**Patient Routes (role-gated via policy):**
```php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('patients', PatientController::class);
});
```

**Visit Routes (nested under patients):**
```php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('patients.visits', VisitController::class);
});
```

### 4. Service Layer (Optional but Recommended)
Service classes to extract business logic from controllers:

**Recommended:**
```
app/Services/
├── UserService.php
├── PatientService.php
└── VisitService.php
```

**Purpose:** Keep controllers thin, handle complex operations (e.g., patient + socioeconomic creation together)

### 5. API Tests (Required for SE M3)
**Current:** 0 REST API tests (all tests are Livewire-based)  
**Required:** Minimum 5 Pest HTTP tests covering:
- Auth flow (login, token validation, logout)
- CRUD operations with role-based authorization
- Policy enforcement
- Validation error handling
- Response structure compliance

---

## 🎯 Recommended API Approach (From Documentation)

### Architecture: Unversioned (Level 0)
Start with flat structure under `app/Http/Controllers/Api/` — no `V1/` versioning yet:

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── ApiController.php        ← base controller with ApiResponses trait
│   │       ├── AuthController.php       ✅ EXISTS
│   │       ├── UserController.php       ⚠️ PARTIAL
│   │       ├── PatientController.php    ❌ MISSING
│   │       └── VisitController.php      ❌ MISSING
│   ├── Requests/
│   │   └── Api/
│   │       ├── LoginRequest.php
│   │       ├── StorePatientRequest.php  ✅ EXISTS (reuse)
│   │       └── UpdatePatientRequest.php ✅ EXISTS (reuse)
│   └── Resources/
│       └── Api/
│           ├── UserResource.php         ❌ MISSING
│           ├── PatientResource.php      ❌ MISSING
│           └── VisitResource.php        ❌ MISSING
├── Policies/
│   ├── PatientPolicy.php               ✅ EXISTS
│   └── VisitPolicy.php                 ⚠️ ON FEATURE BRANCH
└── Traits/
    └── ApiResponses.php                 ✅ EXISTS
```

### Response Format Standards

#### 1. Use ApiResponses Trait
All controllers extend `ApiController` which uses `ApiResponses` trait:

**Available methods:**
```php
$this->ok($message)                     // 200
$this->created($message)                // 201
$this->noContent()                      // 204
$this->error($message, $statusCode)     // 4xx, 5xx
```

**Response structure:**
```json
{
    "message": "Success message",
    "status": 200,
    "data": { ... }
}
```

#### 2. JSON:API Resource Structure
All Eloquent Resources should return:

```json
{
    "data": {
        "type": "patient",
        "id": 1,
        "attributes": {
            "firstName": "John",
            "lastName": "Doe",
            "dateOfBirth": "1990-01-01",
            "createdAt": "2024-01-01T00:00:00Z"
        },
        "relationships": {
            "user": {
                "data": { "type": "user", "id": 5 }
            }
        },
        "links": {
            "self": "http://localhost/api/patients/1"
        }
    }
}
```

**Key patterns:**
- ✅ Use camelCase for attribute keys (not snake_case from DB)
- ✅ Include `type`, `id`, `attributes`, `relationships`, `links`
- ✅ Collections get `links` and `meta` for pagination
- ✅ Use `::collection()` for lists, `new Resource()` for single items

#### 3. Controller Pattern
```php
class PatientController extends ApiController
{
    public function index(): AnonymousResourceCollection
    {
        $patients = Patient::query()
            ->when(!auth()->user()->isAdmin(), fn($q) => 
                $q->where('user_id', auth()->id())
            )
            ->paginate();
            
        return PatientResource::collection($patients);
    }
    
    public function show(Patient $patient): PatientResource
    {
        $this->authorize('view', $patient);
        
        return new PatientResource(
            $patient->load('user', 'socioeconomic')
        );
    }
    
    public function store(StorePatientRequest $request): JsonResponse
    {
        $patient = Patient::create($request->validated());
        
        return $this->created('Patient created successfully')
            ->setData(new PatientResource($patient));
    }
}
```

---

## 📋 Implementation Priorities

### Phase 1: Patient API (Current Sprint)
Based on bd issue tracker showing `nutri-ledger-8fa` (Phase 6 FE) is ready:

1. **Create PatientController** with full CRUD
2. **Create PatientResource** with JSON:API structure
3. **Register patient routes** with policy middleware
4. **Write Pest tests** for patient endpoints
5. **Verify with Postman/curl**

### Phase 2: Visit API
1. **Merge feature/3_visits branch** to bring Visit model to master
2. **Create VisitController** (nested under patients)
3. **Create VisitResource**
4. **Register visit routes**
5. **Write tests**

### Phase 3: User API (Admin Only)
1. **Complete UserController** (currently has only `show()`)
2. **Create UserResource**
3. **Register admin-only routes**
4. **Write tests**

---

## 🔑 Key API Design Rules (From LARAVEL_API_RULES.md)

### URL Design
- ✅ Use nouns, not verbs: `/api/patients`, not `/api/getPatients`
- ✅ Use plural: `/api/patients`, not `/api/patient`
- ✅ Use lowercase: `/api/patients`, not `/api/Patients`
- ✅ Actions via HTTP method, not URL: `DELETE /api/patients/1`, not `/api/patients/1/delete`

### Route Groups
```php
// Auth routes (open)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    
    // Resources with policy authorization
    Route::apiResource('patients', PatientController::class);
    Route::apiResource('patients.visits', VisitController::class);
    
    // Admin-only
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });
});
```

### Authorization Pattern
1. **Middleware for broad checks** — `auth:sanctum`, `role:admin`
2. **Policies for resource-specific rules** — `PatientPolicy::view()`, `update()`, etc.
3. **Call in controller** — `$this->authorize('view', $patient);`

### Error Handling
- **401 Unauthorized** — No auth token or invalid token
- **403 Forbidden** — Authenticated but policy denies access
- **404 Not Found** — Resource doesn't exist
- **422 Unprocessable Entity** — Validation failed
- **500 Internal Server Error** — Unexpected failure

---

## 🧪 Testing Strategy

### Required Test Coverage (SE M3)
Minimum 5 Pest HTTP tests:

**Auth Tests:**
```php
it('login returns token with valid credentials');
it('logout revokes token');
it('unauthenticated requests return 401');
```

**Patient CRUD Tests:**
```php
it('admin can list all patients');
it('doctor can list all patients');
it('patient can only see own record');
it('admin can create patient');
it('patient cannot create patient'); // 403
it('admin can update patient');
it('admin can delete patient');
it('deleted patients are soft-deleted');
```

**Policy Tests:**
```php
it('doctor can view any patient');
it('patient cannot view other patients');
it('admin can view deleted patients');
```

**Response Format Tests:**
```php
it('patient resource has correct JSON:API structure');
it('attributes use camelCase keys');
it('relationships include user data');
```

---

## 📦 Deliverables Summary

### For SE M2 (May 3, 2026)
**Backend API:**
- ✅ Sanctum auth (done)
- ✅ Login/logout/register endpoints (done)
- ❌ User CRUD API (admin only) — **PENDING**
- ❌ Patient CRUD API (role-gated) — **PENDING**
- ❌ Eloquent Resources for User, Patient, Socioeconomic — **PENDING**
- ❌ Service layer (optional) — **PENDING**

**Frontend React:**
- ❌ All UI pages — **NOT STARTED**
- **Note:** bd tracker shows `nutri-ledger-8fa.1` (T014: Scaffold Vite + React) is ready to claim

### For SE M3 (June 7, 2026)
- ❌ Visit CRUD API — **PENDING**
- ❌ Service + Repository pattern demonstration — **PENDING**
- ❌ 5+ Pest HTTP tests — **PENDING**
- ❌ Deployment to Railway/Fly.io — **PENDING**

---

## 🚀 Next Steps

### Immediate Action Items

1. **Claim ready bd issue:**
   ```bash
   bd update nutri-ledger-8fa.1 --claim
   ```
   Task: T014: Scaffold Vite + React project in /frontend

2. **Create Patient API (Priority 1):**
   - [ ] Create `PatientController` with full CRUD
   - [ ] Create `PatientResource` + `PatientSocioeconomicResource`
   - [ ] Register routes with policy middleware
   - [ ] Write 5+ Pest tests
   - [ ] Test with Postman/curl

3. **Merge Visit Feature:**
   - [ ] Merge `feature/3_visits` branch to get Visit model
   - [ ] Create `VisitController` (nested routes)
   - [ ] Create `VisitResource`
   - [ ] Write tests

4. **Frontend Scaffolding:**
   - [ ] Follow T014-T017 from bd tracker
   - [ ] Set up Vite + React in `/frontend` folder
   - [ ] Configure Axios client for API calls
   - [ ] Create smoke test component

---

## 📚 Reference Documents

**Must Read (in order):**
1. `LARAVEL_API_RULES.md` — Core design patterns
2. `Apis/TASK_01_API_FOUNDATIONS.md` — Response trait, controllers
3. `Apis/TASK_03_DESIGNING_URLS.md` — Resource naming, routing
4. `Apis/TASK_07_RESPONSE_PAYLOADS.md` — Eloquent Resources, JSON:API
5. `SE_MVP_PLAN.md` — User stories, milestones, deliverables

**For Advanced Topics:**
- `TASK_05_SANCTUM_AUTH.md` — Token auth (already implemented)
- `TASK_08_CONDITIONAL_FIELDS.md` — Optional includes, field visibility
- `TASK_09_OPTIONAL_INCLUDES.md` — Lazy loading relationships
- `TASK_10_FILTERING.md` — Query filters, search
- `TASK_12_SORTING.md` — Sortable collections
- `TASK_17_POLICIES.md` — Authorization patterns

---

## ✅ Summary

**Current State:** Auth foundation is solid. Domain models are complete. Ready to build resource controllers and APIs.

**Approach:** Unversioned REST API following JSON:API conventions, using Eloquent Resources for transformation, policies for authorization, and ApiResponses trait for consistent responses.

**Priority:** Patient API first (frontend needs it), then Visit API (completes SE M2), then User API (admin features).

**Success Criteria:** All endpoints return correct JSON:API structure, policy enforcement works, 5+ tests pass, frontend can authenticate and perform CRUD operations.
