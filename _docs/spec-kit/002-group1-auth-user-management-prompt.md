# Spec-Kit Feature Prompt: Group 1 — Auth & User Management API

## Epic Context
**BD Issue:** nutri-ledger-est (P1)  
**Epic Title:** [PLANNING] Group 1 — Auth & User Management  
**Track:** SE (Software Engineering) MVP — Feature Groups 1-3  
**Priority:** P1 (High)

---

## Feature Description

Build a complete REST API for User Management in the NutriLedger application, following Laravel API best practices and JSON:API conventions. This feature completes the Auth & User Management layer by adding full CRUD operations for users, along with proper API resources, tests, and documentation.

**What's Already Implemented:**
- ✅ Sanctum authentication fully configured
- ✅ `AuthController` with `login`, `register`, `logout` endpoints
- ✅ `POST /api/login` — validates credentials, returns token
- ✅ `POST /api/register` — creates user + returns token
- ✅ `POST /api/logout` — revokes current token (requires auth)
- ✅ `RoleMiddleware` for role-based access (`role:admin`, `role:admin,doktor`)
- ✅ `User` model with soft deletes and role helpers (`isAdmin()`, `isDoctor()`, `isPatient()`)
- ✅ `UserPolicy` for authorization rules
- ✅ `ApiResponses` trait for consistent response format

**What Needs to Be Built:**
- ❌ `UserController` with full CRUD (index, store, show, update, destroy) — admin only
- ❌ `UserResource` — JSON:API compliant Eloquent Resource
- ❌ User API routes registered with proper middleware
- ❌ Comprehensive Pest HTTP tests for all endpoints
- ❌ API documentation (Postman collection or inline docs)

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
| GET | `/api/users` | List all users | `auth:sanctum`, `role:admin` |
| POST | `/api/users` | Create new user | `auth:sanctum`, `role:admin` |
| GET | `/api/users/{id}` | View one user | `auth:sanctum`, `role:admin` |
| PUT/PATCH | `/api/users/{id}` | Update user | `auth:sanctum`, `role:admin` |
| DELETE | `/api/users/{id}` | Soft delete user | `auth:sanctum`, `role:admin` |

### 4. Controller Pattern

```php
class UserController extends ApiController
{
    public function index(): AnonymousResourceCollection
    {
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
        
        return $this->created('User created successfully')
            ->setData(['data' => new UserResource($user)]);
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
```

### 6. Authorization Pattern

- **Middleware for broad checks:** `auth:sanctum`, `role:admin`
- **Policies for resource-specific rules:** `UserPolicy::view()`, `update()`, `delete()`
- **Call in controller:** `$this->authorize('view', $user);`

**Important:** Only admins can perform any user management operations. Regular users cannot view, create, update, or delete other users.

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
- 1:1 relationship with `Patient` model
- Uses `HasApiTokens` trait for Sanctum

**Role Values:**
- `admin` — Full system access
- `doktor` — Can manage patients and visits
- `pacijent` — Can only view own data

---

## Routes Configuration

```php
// routes/api.php

// Public auth routes (already exist)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']); // Current user
    
    // Admin-only user management (TO ADD)
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });
});
```

**Note:** `Route::apiResource()` generates 5 routes (no `create` or `edit` — those are for HTML forms)

---

## Implementation Deliverables

### 1. UserController
- Full CRUD methods: `index()`, `store()`, `show()`, `update()`, `destroy()`
- Extends `ApiController` to inherit `ApiResponses` trait
- Uses route model binding for `show()`, `update()`, `destroy()`
- Calls `$this->authorize()` for policy checks
- Returns `UserResource` for all data responses

### 2. UserResource
- JSON:API structure with `type`, `id`, `attributes`, `relationships`, `links`
- CamelCase attribute keys
- Excludes sensitive fields (password, remember_token, etc.)
- Includes `patient` relationship when loaded
- Uses named routes for `links.self`

### 3. Form Requests
- `StoreUserRequest` — validates creation (email unique, password strong)
- `UpdateUserRequest` — validates updates (email unique except self, optional fields)
- Both check `authorize()` for admin role

### 4. Routes
- Register `Route::apiResource('users', UserController::class)` under admin middleware
- Verify with `php artisan route:list --path=api/users`

### 5. Tests
- Minimum 20 Pest HTTP tests covering all CRUD operations
- Test authorization (admin-only access)
- Test validation rules (422 responses)
- Test response structure (JSON:API compliance)
- Test soft deletes

### 6. Documentation
- Postman collection with all user endpoints
- OR inline PHPDoc with example requests/responses
- README section explaining user management API

---

## Success Criteria

- ✅ All 5 CRUD endpoints return correct status codes
- ✅ Only admins can access user management endpoints
- ✅ All responses follow JSON:API structure with camelCase keys
- ✅ Passwords are hashed on create/update
- ✅ Soft deletes work correctly
- ✅ All tests pass (`php artisan test`)
- ✅ Code passes quality gates (Laravel Pint, Larastan level 5)
- ✅ Postman collection or API docs created

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

- Use TDD approach: write tests first (RED), implement feature (GREEN), refactor (REFACTOR)
- Follow existing project patterns (ApiController, ApiResponses, JSON:API)
- All attribute keys must be camelCase in JSON responses
- Use route model binding for cleaner controller methods
- Leverage existing User model features (soft deletes, role helpers)
- Ensure no password leaks in any response
- Admin-only access — no exceptions

---

**Ready for spec-kit.specify → spec-kit.plan → spec-kit.tasks**
