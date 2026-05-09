# Spec-Kit Feature Prompt: Group 1 — Auth & User Management API

## Epic Context
**BD Issue:** nutri-ledger-est (P1)  
**Epic Title:** [PLANNING] Group 1 — Auth & User Management  
**Track:** SE (Software Engineering) MVP — Feature Groups 1-3  
**Priority:** P1 (High)

---

## Feature Description

Build a complete REST API for User Management in the NutriLedger application, following Laravel API best practices and JSON:API conventions. This feature completes the Auth & User Management layer by adding full CRUD operations for users, along with proper API resources, tests, and documentation.

---

## Current Implementation Status

### ✅ Fully Implemented
- **Sanctum:** Authentication fully configured and working
- **RoleMiddleware:** Registered and functional (`role:admin`, `role:admin,doktor`)
- **User Model:** Complete with soft deletes, role helpers (`isAdmin()`, `isDoctor()`, `isPatient()`), `patient()` relationship, `toSimpleData()` helper
- **ApiResponses Trait:** Complete with `ok()`, `created()`, `noContent()`, `error()` methods
- **AuthController (Partial):**
  - ✅ `login()` — validates credentials, returns token (working)
  - ✅ `logout()` — revokes current token (working)
  - ❌ `register()` — **stub only** (returns "hello register")
  - ❌ `me()` — **missing** (needs to return authenticated user profile)

### ⚠️ Partially Implemented (Needs Refactoring)

**UserController:**
- ✅ File exists at `app/Http/Controllers/Api/UserController.php`
- ⚠️ `show()` method has basic implementation but incorrect:
  - Uses `string $id` parameter instead of route model binding
  - Doesn't call authorization (`$this->authorize()`)
  - Uses `toSimpleData()` instead of UserResource
  - Response doesn't follow JSON:API structure
- ❌ All other methods (`index`, `store`, `update`, `destroy`) are empty stubs

**UserPolicy:**
- ✅ File exists at `app/Policies/UserPolicy.php`
- ⚠️ **Designed for Livewire UI**, not appropriate for User Management API:
  - `create()` and `update()` allow **doctors** (should be **admin-only** for User Management API)
  - `delete()` uses `session()->flash()` (inappropriate for API)
  - `viewAny()` and `view()` are **commented out** (need to uncomment and implement)
  - `restore()` and `forceDelete()` are **commented out** (need to implement)

**Routes:**
- ⚠️ Manual route exists: `GET /api/user/{id}` (should be replaced with apiResource)
- ❌ Full `Route::apiResource('users', UserController::class)` not registered

### ❌ Missing Components
- **UserResource** — JSON:API compliant Eloquent Resource
- **StoreUserRequest** — Form validation for user creation
- **UpdateUserRequest** — Form validation for user updates
- **RegisterRequest** — Form validation for registration
- **Restore/ForceDelete endpoints** — Soft delete management
- **API Tests** — Zero API tests exist (all tests are Livewire-based)
- **API Documentation** — No Postman collection or docs

---

## Technical Requirements

### Architecture Pattern: Unversioned REST API (Level 0)

**Project Structure:**
```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── ApiController.php          ✅ EXISTS (base with ApiResponses trait)
│   │       ├── AuthController.php         ✅ EXISTS (login, register, logout)
│   │       └── UserController.php         ❌ TO BUILD (full CRUD)
│   ├── Requests/
│   │   └── Api/
│   │       ├── LoginRequest.php           ✅ EXISTS
│   │       ├── RegisterRequest.php        ✅ EXISTS
│   │       ├── StoreUserRequest.php       ❌ TO BUILD
│   │       └── UpdateUserRequest.php      ❌ TO BUILD
│   └── Resources/
│       └── Api/
│           └── UserResource.php           ❌ TO BUILD
├── Models/
│   └── User.php                           ✅ EXISTS (SoftDeletes, role helpers)
├── Policies/
│   └── UserPolicy.php                     ✅ EXISTS
└── Traits/
    └── ApiResponses.php                   ✅ EXISTS
routes/
└── api.php                                ⚠️ HAS AUTH, NEEDS USER ROUTES
```

---

## API Foundation & Standards

### 1. Response Format (ApiResponses Trait)

All controllers extend `ApiController` which uses the `ApiResponses` trait. **Never call `response()->json()` directly.**

**Available Methods:**
```php
$this->ok($message, $data = [])           // 200 OK
$this->created($message, $data = [])      // 201 Created
$this->noContent()                        // 204 No Content
$this->error($message, $statusCode)       // 4xx, 5xx errors
```

**Standard Response Structure:**
```json
{
    "message": "Success message",
    "status": 200,
    "data": { ... }
}
```

**Status Code Reference:**
- **200 OK** — Request succeeded, data returned
- **201 Created** — New resource created
- **204 No Content** — Success, nothing to return (e.g., delete)
- **400 Bad Request** — Invalid query parameters
- **401 Unauthorized** — Not authenticated (no token or invalid token)
- **403 Forbidden** — Authenticated but not allowed (prefer 401 in APIs)
- **404 Not Found** — Resource doesn't exist
- **422 Unprocessable Entity** — Validation failed (auto-handled by Laravel)
- **500 Internal Server Error** — Unexpected server failure

### 2. JSON:API Resource Structure

All Eloquent Resources must follow JSON:API conventions:

```json
{
    "data": {
        "type": "user",
        "id": 1,
        "attributes": {
            "name": "John Doe",
            "email": "john@example.com",
            "role": "admin",
            "createdAt": "2024-01-01T00:00:00Z",
            "updatedAt": "2024-01-01T00:00:00Z"
        },
        "relationships": {
            "patient": {
                "data": { "type": "patient", "id": 5 }
            }
        },
        "links": {
            "self": "http://localhost/api/users/1"
        }
    }
}
```

**Key Patterns:**
- ✅ Use `type`, `id`, `attributes`, `relationships`, `links` structure
- ✅ Use **camelCase** for attribute keys (not snake_case from DB)
- ✅ Collections get automatic `links` and `meta` for pagination
- ✅ Use `::collection()` for lists, `new Resource()` for single items
- ❌ Never expose sensitive fields (`password`, `remember_token`, etc.)

### 3. URL Design Rules

- **Nouns, not verbs:** `/api/users`, not `/api/getUsers`
- **Plural:** `/api/users`, not `/api/user`
- **Lowercase:** `/api/users`, not `/api/Users`
- **HTTP method expresses action**, not URL segment

**User API Endpoints:**
| Method | URL | Action | Middleware |
|--------|-----|--------|-----------|
| GET | `/api/users` | List all users (incl. soft-deleted) | `auth:sanctum`, `role:admin` |
| POST | `/api/users` | Create new user | `auth:sanctum`, `role:admin` |
| GET | `/api/users/{id}` | View one user | `auth:sanctum`, `role:admin` |
| PUT/PATCH | `/api/users/{id}` | Update user | `auth:sanctum`, `role:admin` |
| DELETE | `/api/users/{id}` | Soft delete user | `auth:sanctum`, `role:admin` |
| POST | `/api/users/{id}/restore` | Restore soft-deleted user | `auth:sanctum`, `role:admin` |
| DELETE | `/api/users/{id}/force` | Permanently delete user | `auth:sanctum`, `role:admin` |
| GET | `/api/user` | Get current user profile | `auth:sanctum` |

### 4. Controller Pattern

```php
class UserController extends ApiController
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', User::class);
        
        $users = User::query()
            ->withTrashed()  // Admin can see soft-deleted users
            ->paginate();
            
        return UserResource::collection($users);
    }
    
    public function show(User $user): UserResource
    {
        $this->authorize('view', $user);
        
        return new UserResource($user);
    }
    
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create([
            ...$request->validated(),
            'password' => Hash::make($request->password),
        ]);
        
        return $this->created('User created successfully', new UserResource($user));
    }
    
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);
        
        $data = $request->validated();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $user->update($data);
        
        return $this->ok('User updated successfully', new UserResource($user));
    }
    
    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);
        
        $user->delete();  // Soft delete
        
        return $this->noContent();
    }
    
    public function restore(User $user): JsonResponse
    {
        $this->authorize('restore', $user);
        
        $user->restore();
        
        return $this->ok('User restored successfully', new UserResource($user));
    }
    
    public function forceDelete(User $user): JsonResponse
    {
        $this->authorize('forceDelete', $user);
        
        $user->forceDelete();  // Permanent deletion
        
        return $this->noContent();
    }
}
```

**Current show() Implementation Issues:**
The existing `show()` method has several problems:
```php
// ❌ Current (incorrect)
public function show(string $id): JsonResponse
{
    return $this->ok('OK')->setData(['data' => ['user' => User::where('id', $id)->first()->toSimpleData()]]);
}

// Problems:
// 1. Uses string $id instead of route model binding
// 2. Uses ->where()->first() instead of automatic 404 handling
// 3. Doesn't call authorization
// 4. Uses toSimpleData() instead of UserResource
// 5. Response doesn't follow JSON:API structure
// 6. No null check (will error if user not found)
```

**Correct Implementation:**
```php
// ✅ Correct
public function show(User $user): UserResource
{
    $this->authorize('view', $user);
    return new UserResource($user);
}
```

### 5. Form Request Pattern

```php
class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }
    
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', 'in:admin,doktor,pacijent'],
        ];
    }
}

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }
    
    public function rules(): array
    {
        return [
            'name'     => ['sometimes', 'string', 'max:255'],
            'email'    => ['sometimes', 'email', 'unique:users,email,' . $this->user->id],
            'password' => ['sometimes', 'string', 'min:8'],
            'role'     => ['sometimes', 'in:admin,doktor,pacijent'],
        ];
    }
}

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;  // Public endpoint
    }
    
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['sometimes', 'in:admin,doktor,pacijent'],  // Optional, defaults to pacijent
        ];
    }
}
```

### 6. Authorization Pattern

- **Middleware for broad checks:** `auth:sanctum`, `role:admin`
- **Policies for resource-specific rules:** `UserPolicy::viewAny()`, `view()`, `create()`, `update()`, `delete()`, `restore()`, `forceDelete()`
- **Call in controller:** `$this->authorize('view', $user);`

**Important:** Only admins can perform any user management operations. Regular users (doctors, patients) cannot view, create, update, or delete other users.

### 7. UserPolicy Requirements (Admin-Only for User Management API)

**Current UserPolicy Authorization:**
The existing UserPolicy was designed for Livewire UI where doctors can manage patient accounts. For the **User Management API**, authorization must be **admin-only**.

**Correct Authorization Matrix:**
| Method | Admin | Doctor | Patient | Note |
|--------|-------|--------|---------|------|
| viewAny | ✅ Yes | ❌ No | ❌ No | Admin can list all users |
| view | ✅ Yes | ❌ No | ❌ No | Admin can view any user |
| create | ✅ Yes | ❌ No | ❌ No | Only admin creates users |
| update | ✅ Yes | ❌ No | ❌ No | Admin updates any user |
| delete | ✅ Yes (not self) | ❌ No | ❌ No | Admin deletes (except self) |
| restore | ✅ Yes | ❌ No | ❌ No | Admin restores soft-deleted |
| forceDelete | ✅ Yes | ❌ No | ❌ No | Admin force-deletes |

**Required UserPolicy Implementation:**
```php
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, User $model): bool
    {
        // Cannot delete self
        if ($user->id === $model->id) {
            return false;
        }
        
        return $user->isAdmin();
    }

    public function restore(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->isAdmin();
    }
}
```

**Note:** Remove any session-related code (like `session()->flash()`) — APIs should not use sessions.

---

## Testing Requirements (SE M3 Requirement: Min 5 Tests)

**Required Pest HTTP Test Coverage:**

### Auth Tests (Already Exist)
```php
it('login returns token with valid credentials');
it('logout revokes token');
it('unauthenticated requests return 401');
it('invalid credentials return 401');
it('missing fields return 422 validation errors');
```

### User CRUD Tests (To Build)
```php
// List Users
it('admin can list all users');
it('admin can see soft-deleted users in list');
it('non-admin cannot list users'); // 403 or 401
it('unauthenticated request returns 401');

// View User
it('admin can view any user');
it('non-admin cannot view users'); // 403
it('viewing non-existent user returns 404');

// Create User
it('admin can create new user');
it('non-admin cannot create user'); // 403
it('create validates required fields'); // 422
it('create validates email uniqueness'); // 422
it('create hashes password');

// Update User
it('admin can update user');
it('admin can change user role');
it('admin can update password (hashed)');
it('non-admin cannot update users'); // 403
it('update validates email uniqueness'); // 422

// Delete User
it('admin can soft delete user');
it('non-admin cannot delete user'); // 403
it('deleted users are soft-deleted not hard-deleted');
it('deleted users can be restored'); // if restore endpoint exists

// Response Format Tests
it('user resource has correct JSON:API structure');
it('user attributes use camelCase keys');
it('user resource excludes password field');
it('user resource includes relationships when loaded');
it('user collection includes pagination metadata');
```

**Minimum:** 20+ tests for comprehensive coverage

---

## User Model Context

**Current User Model Features:**
- `id`, `name`, `email`, `password`, `role`, timestamps
- Soft deletes enabled (`deleted_at`)
- Role stored as plain string: `admin`, `doktor`, `pacijent`
- Helper methods: `isAdmin()`, `isDoctor()`, `isPatient()`, `initials()`
- 1:1 relationship with `Patient` model via `patient()` HasOne
- Uses `HasApiTokens` trait for Sanctum
- Has `toSimpleData()` method (returns `['id', 'name', 'email', 'role']`)

**Role Values:**
- `admin` — Full system access
- `doktor` — Can manage patients and visits
- `pacijent` — Can only view own data

**Important: toSimpleData() vs UserResource**
- **toSimpleData():** Used in **AuthController** for login/register responses (minimal profile data)
- **UserResource:** Used in **User Management API** for full JSON:API structure with relationships
- Don't confuse the two — AuthController uses simple data, UserController uses full resources

---

## Routes Configuration

```php
// routes/api.php

// Public auth routes
Route::post('/login', [AuthController::class, 'login']);      // ✅ Works
Route::post('/register', [AuthController::class, 'register']); // ⚠️ Stub (needs implementation)

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']); // ✅ Works
    Route::get('/user', [AuthController::class, 'me']);        // ❌ Missing method
    
    // Admin-only user management
    Route::middleware('role:admin')->group(function () {
        // Remove existing manual route: GET /api/user/{id}
        
        // Replace with full resource routes
        Route::apiResource('users', UserController::class);
        
        // Add soft delete management routes
        Route::post('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::delete('users/{user}/force', [UserController::class, 'forceDelete'])->name('users.forceDelete');
    });
});
```

**Route Actions Generated by apiResource:**
- `GET /api/users` → index
- `POST /api/users` → store
- `GET /api/users/{user}` → show
- `PUT/PATCH /api/users/{user}` → update
- `DELETE /api/users/{user}` → destroy

**Additional Custom Routes:**
- `POST /api/users/{user}/restore` → restore (soft-deleted user)
- `DELETE /api/users/{user}/force` → forceDelete (permanent deletion)
- `GET /api/user` → me (current user profile, not admin-only)

**Note:** `Route::apiResource()` generates 5 routes (no `create` or `edit` — those are for HTML forms)

---

## Implementation Deliverables

### 1. UserPolicy Refactoring
- [ ] Uncomment and implement `viewAny()` → admin-only
- [ ] Uncomment and implement `view()` → admin-only
- [ ] Fix `create()` → admin-only (remove doctor access)
- [ ] Fix `update()` → admin-only (remove doctor access)
- [ ] Fix `delete()` → remove session flash, admin-only, prevent self-delete
- [ ] Uncomment and implement `restore()` → admin-only
- [ ] Uncomment and implement `forceDelete()` → admin-only

### 2. UserController Implementation
- [ ] Refactor `show()` → use route model binding, call authorize, use UserResource
- [ ] Implement `index()` → paginated list with `withTrashed()`, UserResource::collection
- [ ] Implement `store()` → StoreUserRequest, hash password, return 201 with UserResource
- [ ] Implement `update()` → UpdateUserRequest, hash password if provided, call authorize
- [ ] Implement `destroy()` → soft delete, call authorize, return 204
- [ ] Add `restore()` → restore soft-deleted user, call authorize
- [ ] Add `forceDelete()` → permanent deletion, call authorize

### 3. AuthController Completion
- [ ] Implement `register()` → RegisterRequest, hash password, create token, return user+token
- [ ] Add `me()` → return authenticated user profile via UserResource

### 4. UserResource
- [ ] Create `app/Http/Resources/Api/UserResource.php`
- [ ] JSON:API structure (type, id, attributes, relationships, links)
- [ ] CamelCase attribute keys
- [ ] Exclude sensitive fields (password, remember_token, email_verified_at)
- [ ] Include `patient` relationship when loaded
- [ ] Use named routes for `links.self`

### 5. Form Requests
- [ ] Create `StoreUserRequest` → validate name, email (unique), password (min 8), role
- [ ] Create `UpdateUserRequest` → same rules but all optional, email unique except self
- [ ] Create `RegisterRequest` → for AuthController registration (password_confirmation)

### 6. Routes
- [ ] Remove manual `GET /api/user/{id}` route from api.php
- [ ] Register `Route::apiResource('users', UserController::class)` under admin middleware
- [ ] Add `POST /api/users/{user}/restore` route
- [ ] Add `DELETE /api/users/{user}/force` route
- [ ] Add `GET /api/user` route for AuthController::me()
- [ ] Verify with `php artisan route:list --path=api/users`

### 7. Tests
- [ ] Minimum 25 Pest HTTP tests covering:
  - All CRUD operations (index, store, show, update, destroy)
  - Restore and forceDelete operations
  - me() endpoint
  - Complete register flow
  - Authorization (admin-only for user management)
  - Validation rules (422 responses)
  - Response structure (JSON:API compliance)
  - Soft delete behavior
  - Self-delete prevention

### 8. Documentation
- [ ] Postman collection with all 10 endpoints
- [ ] Include restore/forceDelete examples
- [ ] Include me() endpoint example
- [ ] README section explaining user management API

---

## Success Criteria

- ✅ All 10 endpoints return correct status codes (5 CRUD + 2 soft delete + 1 me + 2 auth)
- ✅ Only admins can access user management endpoints (not doctors/patients)
- ✅ Admin cannot delete themselves (self-delete prevented)
- ✅ All responses follow JSON:API structure with camelCase keys
- ✅ Passwords are hashed on create/update/register
- ✅ Soft deletes work correctly (can restore, can force delete)
- ✅ UserPolicy enforces admin-only access (no session flash)
- ✅ Existing `show()` method refactored to use route model binding
- ✅ AuthController `register()` fully implemented (not stub)
- ✅ AuthController `me()` endpoint added
- ✅ All 25+ tests pass (`php artisan test`)
- ✅ Code passes quality gates (Laravel Pint, Larastan level 5)
- ✅ Postman collection or API docs created with all endpoints

---

## Dependencies & Prerequisites

**Already Complete:**
- ✅ Sanctum installed and configured
- ✅ CORS configured for frontend origin
- ✅ `User` model with soft deletes
- ✅ `UserPolicy` for authorization
- ✅ `RoleMiddleware` registered
- ✅ `ApiResponses` trait implemented
- ✅ Auth endpoints working (login, register, logout)

**Blocks (this work blocks):**
- Phase 6 FE — Frontend needs user management UI
- Phase 7 Polish — Needs passing API tests

---

## Out of Scope

- ❌ User profile updates (self-service) — use auth endpoints for that
- ❌ Password reset flow — handled by Fortify, not user management
- ❌ Email verification — handled by Fortify
- ❌ Two-factor authentication — handled by Fortify
- ❌ User avatar uploads — future enhancement
- ❌ User activity logs — future enhancement
- ❌ API versioning — Level 0 (unversioned) for now

---

## Reference Documents

**Must Read:**
1. `LARAVEL_API_RULES.md` — Core design patterns
2. `API_PREREQUISITES_AND_APPROACH.md` — Current state + missing pieces
3. `Apis/TASK_01_API_FOUNDATIONS.md` — Response trait, controllers
4. `Apis/TASK_07_RESPONSE_PAYLOADS.md` — Eloquent Resources, JSON:API

**User Stories (SE M2):**
- US4: As an admin, I can register new user accounts with a specified role
- US5: As an admin, I can deactivate (soft-delete) a user account without losing their data
- US6: As an admin, I can restore a soft-deleted user account
- US7: As an admin, I can permanently delete a user account (future)
- US8: As a user, I can update my own profile information (separate endpoint)

---

## Notes for Spec-Kit

- **IMPORTANT:** UserController and UserPolicy already exist but need refactoring for API use
- **CRITICAL:** UserPolicy currently allows doctors — must change to admin-only for User Management API
- Use TDD approach: write tests first (RED), implement feature (GREEN), refactor (REFACTOR)
- Follow existing project patterns (ApiController, ApiResponses, JSON:API)
- All attribute keys must be camelCase in JSON responses
- Use route model binding for cleaner controller methods (refactor existing show() method)
- Leverage existing User model features (soft deletes, role helpers, toSimpleData())
- Ensure no password leaks in any response
- Remove session-related code from UserPolicy (APIs don't use sessions)
- Admin-only access — no exceptions (doctors/patients have no access to user management)
- Prevent admin self-deletion (check in policy)
- Complete AuthController register() stub and add me() endpoint

---

**Ready for spec-kit.specify → spec-kit.plan → spec-kit.tasks**
