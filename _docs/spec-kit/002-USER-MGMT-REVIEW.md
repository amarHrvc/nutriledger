# User Management Prompt Review & Refinement

**Reviewed:** 2026-03-19  
**Prompt File:** `002-group1-auth-user-management-prompt.md`  
**Status:** Needs Minor Updates

---

## ✅ What's Accurate

### 1. Architecture & Standards
- ✅ ApiController pattern is correct
- ✅ ApiResponses trait exists and documented correctly
- ✅ JSON:API structure is accurate
- ✅ Status code reference is comprehensive
- ✅ URL design rules are correct

### 2. Domain Layer Status
- ✅ User model exists with soft deletes ✓
- ✅ User model has role helpers (isAdmin, isDoctor, isPatient) ✓
- ✅ User model has patient() HasOne relationship ✓
- ✅ User model has toSimpleData() helper method ✓
- ✅ AuthController exists with login/logout ✓

### 3. Testing Requirements
- ✅ Test structure is comprehensive
- ✅ 20+ tests recommended (meets SE M3 minimum)
- ✅ Coverage includes CRUD, auth, validation, response format

---

## ⚠️ Issues Found & Required Updates

### Issue 1: UserController Already Exists (Stub)
**Current State:**
```php
// backend/app/Http/Controllers/Api/UserController.php
class UserController extends ApiController
{
    public function show(string $id): JsonResponse
    {
        return $this->ok('OK')->setData(['data' => ['user' => User::where('id', $id)->first()->toSimpleData()]]);
    }
    // Other methods are empty stubs
}
```

**Prompt Says:**
> ❌ `UserController` — TO BUILD

**Update Needed:**
```markdown
**What Exists (Partial Implementation):**
- ⚠️ `UserController` EXISTS but incomplete
  - ✅ `show()` method implemented (basic)
  - ❌ `index()`, `store()`, `update()`, `destroy()` are empty stubs
  - ⚠️ `show()` doesn't use route model binding
  - ⚠️ `show()` doesn't use UserResource
  - ⚠️ `show()` doesn't call authorization policy
  - ⚠️ Response format doesn't follow JSON:API structure
```

---

### Issue 2: UserPolicy is Incomplete
**Current State:**
```php
// backend/app/Policies/UserPolicy.php
class UserPolicy
{
    // viewAny() - COMMENTED OUT
    // view() - COMMENTED OUT
    
    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'doktor';
    }
    
    public function update(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'doktor';
    }
    
    public function delete(User $user, User $userToDelete): bool
    {
        // Prevents self-deletion
        // Admin can delete any user
        // Doctor can delete patients only
        return /* complex logic */;
    }
    
    // restore() - COMMENTED OUT
    // forceDelete() - COMMENTED OUT
}
```

**Issues:**
1. `viewAny()` and `view()` are commented out (needed for API)
2. `create()` allows doctors to create users (should be admin-only for User Management API)
3. `update()` allows doctors (should be admin-only)
4. `delete()` uses `session()->flash()` (inappropriate for API)
5. `restore()` and `forceDelete()` are commented out

**Prompt Says:**
> ✅ `UserPolicy` for authorization rules

**Update Needed:**
```markdown
**What Exists (Needs Refinement):**
- ⚠️ `UserPolicy` EXISTS but incomplete/incorrect for API use
  - ❌ `viewAny()` commented out (need to uncomment)
  - ❌ `view()` commented out (need to uncomment)
  - ⚠️ `create()` allows doctors (should be admin-only)
  - ⚠️ `update()` allows doctors (should be admin-only)
  - ⚠️ `delete()` uses session flash (inappropriate for API)
  - ❌ `restore()` commented out (need to implement)
  - ❌ `forceDelete()` commented out (need to implement)
  
**Authorization Matrix for User Management API:**
| Role | viewAny | view | create | update | delete | restore | forceDelete |
|------|---------|------|--------|--------|--------|---------|-------------|
| Admin | ✅ | ✅ | ✅ | ✅ | ✅ (not self) | ✅ | ✅ |
| Doctor | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Patient | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

**Note:** The current UserPolicy was designed for Livewire UI (where doctors manage patient accounts). For the User Management API, only admins should have access.
```

---

### Issue 3: Missing API Resources Directory
**Current State:**
```bash
$ ls backend/app/Http/Resources/Api/
# Directory doesn't exist
```

**Prompt Says:**
> ❌ UserResource.php — TO BUILD

**Update Needed:** ✅ Already correct, just note directory needs to be created

---

### Issue 4: Missing Form Request Files
**Current State:**
```bash
$ ls backend/app/Http/Requests/Api/
LoginRequest.php  # Only this exists
```

**Prompt Says:**
> ❌ StoreUserRequest.php — TO BUILD
> ❌ UpdateUserRequest.php — TO BUILD

**Update Needed:** ✅ Already correct

---

### Issue 5: Routes - Partial Implementation
**Current State:**
```php
// routes/api.php
Route::get('/user/{id}', [UserController::class, 'show']);  // Exists
// But no full apiResource() registration
```

**Prompt Says:**
> ⚠️ HAS AUTH, NEEDS USER ROUTES

**Update Needed:**
```markdown
**Current Routes:**
- ✅ `GET /api/user/{id}` exists (manual route)
- ❌ Full `apiResource()` not registered
- ❌ No middleware protection on show route
- ❌ index, store, update, destroy routes missing

**Need to:**
1. Remove manual `GET /api/user/{id}` route
2. Replace with `Route::apiResource('users', UserController::class)`
3. Wrap in `role:admin` middleware
```

---

### Issue 6: AuthController Missing `me()` Endpoint
**Prompt Mentions:**
```php
Route::get('/user', [AuthController::class, 'me']); // Current user
```

**Current State:**
```php
// AuthController has login, register, logout
// But no me() method
```

**Update Needed:**
```markdown
**Additional Task:**
- ❌ `AuthController::me()` method missing
  - Should return authenticated user's profile
  - Should use UserResource for response
  - Should be at `GET /api/user` (not `/api/users/{id}`)
```

---

### Issue 7: Register Endpoint is Stub
**Current State:**
```php
public function register(): JsonResponse
{
    return $this->ok('hello register');
}
```

**Prompt Says:**
> ✅ `POST /api/register` — creates user + returns token

**Update Needed:**
```markdown
**What's Incomplete:**
- ⚠️ `AuthController::register()` is a stub (returns "hello register")
  - Need to implement full registration logic
  - Need RegisterRequest validation
  - Need to hash password
  - Need to create token
  - Need to return user + token
```

---

## 🔧 Recommended Refinements

### 1. Add "Current Implementation Status" Section
Insert after "Feature Description":

```markdown
## Current Implementation Status

### ✅ Fully Implemented
- Sanctum authentication configured
- AuthController login/logout working (register is stub)
- RoleMiddleware registered
- User model complete (soft deletes, role helpers)
- ApiResponses trait complete

### ⚠️ Partially Implemented
- **UserController:** Exists but only `show()` has implementation (incomplete)
  - `show()` doesn't follow JSON:API structure
  - `show()` doesn't use route model binding
  - `show()` doesn't call authorization
  - Other methods are empty stubs
  
- **UserPolicy:** Exists but designed for Livewire (not API)
  - `viewAny()` and `view()` commented out
  - `create()` and `update()` allow doctors (should be admin-only)
  - `delete()` uses session flash (inappropriate for API)
  - `restore()` and `forceDelete()` commented out

### ❌ Missing Components
- UserResource (JSON:API transformation)
- StoreUserRequest (form validation)
- UpdateUserRequest (form validation)
- RegisterRequest (for auth registration)
- API routes (apiResource registration)
- API tests (zero exist)
- `AuthController::me()` endpoint
```

---

### 2. Update UserPolicy Authorization Matrix
Replace the simple "admin-only" note with detailed requirements:

```markdown
### UserPolicy Requirements (Admin-Only)

**Current UserPolicy is designed for Livewire UI** where doctors can manage patient accounts. For the **User Management API**, authorization should be **admin-only**.

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

**Implementation:**
```php
// app/Policies/UserPolicy.php
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
```

**Note:** Remove session flash from delete() — APIs should not use sessions.
```

---

### 3. Add Restore/ForceDelete Endpoints
The prompt doesn't mention these, but UserPolicy has them commented out:

```markdown
### Extended User Endpoints (Soft Delete Management)

| Method | URL | Action | Middleware |
|--------|-----|--------|-----------|
| POST/PATCH | `/api/users/{id}/restore` | Restore soft-deleted user | `auth:sanctum`, `role:admin` |
| DELETE | `/api/users/{id}/force` | Permanently delete user | `auth:sanctum`, `role:admin` |

**Implementation:**
```php
// UserController
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
```

**Routes:**
```php
Route::post('users/{user}/restore', [UserController::class, 'restore']);
Route::delete('users/{user}/force', [UserController::class, 'forceDelete']);
```
```

---

### 4. Add AuthController Missing Functionality

```markdown
## Additional AuthController Requirements

### 1. Complete Register Endpoint
**Current:** Returns "hello register" stub

**Need to implement:**
```php
public function register(RegisterRequest $request): JsonResponse
{
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role ?? 'pacijent',  // Default to patient
    ]);
    
    $token = $user->createToken('api-token', ['*'], Carbon::now()->addDays(1))->plainTextToken;
    
    return $this->created('User registered successfully', [
        'token' => $token,
        'user' => new UserResource($user),
    ]);
}
```

### 2. Add `me()` Endpoint
**Purpose:** Return authenticated user's profile

```php
public function me(Request $request): JsonResponse
{
    return $this->ok('Profile retrieved', new UserResource($request->user()));
}
```

**Route:**
```php
Route::get('/user', [AuthController::class, 'me']);  // GET /api/user
```

**Note:** This is different from `GET /api/users/{id}` (admin-only user management)
```

---

### 5. Fix `show()` Method Example
Current UserController show() has issues. Update the example:

```markdown
### Current show() Implementation Issues

**Current Code:**
```php
public function show(string $id): JsonResponse
{
    return $this->ok('OK')->setData(['data' => ['user' => User::where('id', $id)->first()->toSimpleData()]]);
}
```

**Problems:**
1. ❌ Uses `string $id` instead of route model binding
2. ❌ Uses `->where()->first()` instead of `->findOrFail()`
3. ❌ Doesn't call authorization (`$this->authorize('view', $user)`)
4. ❌ Uses `toSimpleData()` instead of UserResource
5. ❌ Response structure doesn't match JSON:API
6. ❌ No null check (will error if user not found)

**Correct Implementation:**
```php
public function show(User $user): UserResource
{
    $this->authorize('view', $user);
    
    return new UserResource($user);
}
```

**Why this is better:**
- ✅ Route model binding auto-finds user or returns 404
- ✅ Authorization check via policy
- ✅ UserResource handles JSON:API structure
- ✅ Clean, readable code
```

---

### 6. Add toSimpleData() Note
The User model has a custom `toSimpleData()` method. Clarify usage:

```markdown
## User Model Notes

### toSimpleData() Helper
The User model has a `toSimpleData()` method:
```php
public function toSimpleData(): array
{
    return $this->only(['id', 'name', 'email', 'role']);
}
```

**Usage:** This is used in **AuthController** for login/register responses (minimal profile data).

**For User Management API:** Use **UserResource** instead (full JSON:API structure with relationships).

**Don't confuse:**
- `toSimpleData()` — Auth endpoints (login, register) → minimal data
- `UserResource` — User CRUD endpoints → full JSON:API structure
```

---

## 📋 Updated Deliverables Checklist

```markdown
## Implementation Deliverables (Revised)

### 1. UserPolicy Fixes
- [ ] Uncomment `viewAny()` method → admin-only
- [ ] Uncomment `view()` method → admin-only
- [ ] Fix `create()` → admin-only (remove doctor)
- [ ] Fix `update()` → admin-only (remove doctor)
- [ ] Fix `delete()` → remove session flash, admin-only, prevent self-delete
- [ ] Uncomment `restore()` → admin-only
- [ ] Uncomment `forceDelete()` → admin-only

### 2. UserController Implementation
- [ ] Fix `show()` → use route model binding, call authorize, use UserResource
- [ ] Implement `index()` → paginated list, include soft-deleted, UserResource::collection
- [ ] Implement `store()` → StoreUserRequest, hash password, return 201
- [ ] Implement `update()` → UpdateUserRequest, hash password if provided
- [ ] Implement `destroy()` → soft delete, return 204
- [ ] Add `restore()` → restore soft-deleted user
- [ ] Add `forceDelete()` → permanent deletion

### 3. AuthController Completion
- [ ] Implement `register()` → RegisterRequest, hash password, create token
- [ ] Add `me()` → return authenticated user profile via UserResource

### 4. UserResource
- [ ] Create `app/Http/Resources/Api/UserResource.php`
- [ ] JSON:API structure (type, id, attributes, relationships, links)
- [ ] CamelCase keys
- [ ] Exclude password, remember_token
- [ ] Include patient relationship when loaded

### 5. Form Requests
- [ ] Create `StoreUserRequest` → validate name, email (unique), password (min 8), role
- [ ] Create `UpdateUserRequest` → same rules but all optional, email unique except self
- [ ] Create `RegisterRequest` → for AuthController registration

### 6. Routes
- [ ] Remove manual `GET /api/user/{id}` route
- [ ] Register `Route::apiResource('users', UserController::class)` under admin middleware
- [ ] Add `POST /api/users/{user}/restore` route
- [ ] Add `DELETE /api/users/{user}/force` route
- [ ] Add `GET /api/user` route for AuthController::me()
- [ ] Complete `POST /api/register` implementation

### 7. Tests
- [ ] Minimum 25 tests (increased from 20 due to additional endpoints)
- [ ] Test all CRUD operations
- [ ] Test restore and forceDelete
- [ ] Test me() endpoint
- [ ] Test complete register flow
- [ ] Test authorization (admin-only)
- [ ] Test validation rules
- [ ] Test response structure
- [ ] Test soft deletes

### 8. Documentation
- [ ] Postman collection with all endpoints
- [ ] Include restore/forceDelete examples
- [ ] Include me() endpoint
- [ ] README section
```

---

## 🎯 Summary of Changes

### Critical Updates
1. **UserPolicy:** Needs complete rewrite for admin-only API access
2. **UserController:** Existing `show()` needs refactoring, other methods need implementation
3. **AuthController:** `register()` is stub, `me()` is missing
4. **Routes:** Manual route exists, needs to be replaced with apiResource

### Additions
1. Add restore/forceDelete endpoints
2. Add me() endpoint documentation
3. Add RegisterRequest
4. Clarify toSimpleData() vs UserResource usage

### Test Count
- Increased from 20+ to 25+ tests (due to additional endpoints)

---

## ✅ Verdict

**Prompt Quality:** 8.5/10  
**Needs Updates:** Yes (critical: UserPolicy, UserController status, AuthController completeness)  
**Overall:** Excellent foundation, but needs updates to reflect partial implementation and add missing details

**Recommendation:** Update prompt with "Current Implementation Status" section before invoking spec-kit.specify
