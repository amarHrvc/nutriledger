# M2 Tasks — Release 1: Sanctum + User/Patient API + React SPA
**Deadline:** May 3 2026
**Branch:** `feature/se-pivot` (create from `master`)

---

## BACKEND TASKS

---

### BE-M2-01 — Branch Setup + GitHub Collaborators

**Goal:** Create the pivot branch and add SE partner as collaborator.

**Inputs:** none

**Outputs:** branch `feature/se-pivot` exists on GitHub; collaborators added

**Steps:**
```bash
git checkout master
git pull origin master
git checkout -b feature/se-pivot
git push -u origin feature/se-pivot
```

Then add collaborators on GitHub:
- Go to repo Settings → Collaborators → Add people
- Add: `Ajla115`, `amilacausevic`

**Verification:** Both users receive a GitHub invitation. Branch appears on GitHub.

---

### BE-M2-02 — Install Sanctum + Add HasApiTokens to User

**Goal:** Enable token-based API authentication.

**Inputs:**
- `app/Models/User.php` — currently has `TwoFactorAuthenticatable` but NOT `HasApiTokens`
- `bootstrap/app.php` — current middleware registration

**Outputs:**
- `app/Models/User.php` — `HasApiTokens` added
- `config/sanctum.php` — created by installer
- `database/migrations/*_create_personal_access_tokens_table.php` — created by installer

**Steps:**

1. Install Sanctum:
```bash
composer require laravel/sanctum
php artisan sanctum:install --no-interaction
php artisan migrate
```

2. Edit `app/Models/User.php` — add `HasApiTokens` to the `use` statement:
```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable, SoftDeletes, HasApiTokens;
    // ...
}
```

3. Run Pint:
```bash
vendor/bin/pint --dirty
```

**Verification:**
```bash
php artisan tinker
# >>> $user = \App\Models\User::first();
# >>> $token = $user->createToken('test')->plainTextToken;
# >>> echo $token;
# Should print a token string
```

---

### BE-M2-03 — Register api.php Route File

**Goal:** Tell Laravel to load `routes/api.php` with the `api` route prefix and `sanctum` guard.

**Inputs:**
- `bootstrap/app.php` — currently only loads `web.php`

**Outputs:**
- `bootstrap/app.php` — updated with `api:` routing entry

**Current state (`bootstrap/app.php`):**
```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```

**Updated state:**
```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
    apiPrefix: 'api',
)
```

**Steps:**
1. Open `bootstrap/app.php`.
2. Add `api:` and `apiPrefix:` entries as shown above.
3. Run Pint: `vendor/bin/pint --dirty`

**Verification:**
```bash
php artisan route:list --path=api
# Should list routes once api.php is created in BE-M2-04
```

---

### BE-M2-04 — Create routes/api.php

**Goal:** Define all auth, user, and patient API route groups.

**Inputs:**
- `app/Policies/PatientPolicy.php`, `UserPolicy.php`
- Controllers created in BE-M2-05 through BE-M2-09

**Output:** `routes/api.php`

**Complete file:**
```php
<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/login', [AuthController::class, 'login']);

// Authenticated
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);

    // Admin-only user management
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });

    // Patient management (admin + doctor)
    Route::apiResource('patients', PatientController::class);
});
```

**Steps:**
1. Create `routes/api.php` with the content above.
2. Controllers will be filled in subsequent tasks.
3. Run `php artisan route:list --path=api` to verify routes registered.

**Verification:** All routes appear in `php artisan route:list --path=api`.

---

### BE-M2-05 — Create AuthController

**Goal:** Handle login (issue Sanctum token) and logout (revoke token).

**Inputs:** `app/Models/User.php`

**Output:** `app/Http/Controllers/Api/AuthController.php`

**Steps:**
1. Create the controller:
```bash
php artisan make:controller Api/AuthController --no-interaction
```

2. Fill the file:
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => new UserResource($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
```

3. Run Pint: `vendor/bin/pint --dirty`

**Verification:**
```bash
php artisan test --filter=login
```

---

### BE-M2-06 — Create UserService

**Goal:** Encapsulate user create/update/delete logic so UserController stays thin.

**Inputs:** `app/Models/User.php`

**Output:** `app/Services/UserService.php`

**Steps:**
1. Create the class:
```bash
php artisan make:class Services/UserService --no-interaction
```

2. Fill the file:
```php
<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(array $data): User
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'] ?? 'pacijent',
        ]);
    }

    public function updateUser(User $user, array $data): User
    {
        $user->fill([
            'name'  => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
            'role'  => $data['role'] ?? $user->role,
        ]);

        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return $user;
    }

    public function softDeleteUser(User $user): void
    {
        $user->delete();
    }
}
```

3. Run Pint: `vendor/bin/pint --dirty`

**Verification:** No static analysis errors: `composer run analyse`

---

### BE-M2-07 — Create PatientService

**Goal:** Encapsulate patient create/update/delete/list logic.

**Inputs:** `app/Models/Patient.php`, `app/Models/User.php`

**Output:** `app/Services/PatientService.php`

**Steps:**
1. Create:
```bash
php artisan make:class Services/PatientService --no-interaction
```

2. Fill the file:
```php
<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class PatientService
{
    public function listForUser(User $user): Collection
    {
        if ($user->isAdmin() || $user->isDoctor()) {
            return Patient::with('socioeconomic')->get();
        }

        // Patient sees only their own record
        return Patient::with('socioeconomic')
            ->where('user_id', $user->id)
            ->get();
    }

    public function createPatient(array $data): Patient
    {
        $patient = Patient::create($data);

        // Always create an empty socioeconomic record alongside
        $patient->socioeconomic()->create([]);

        return $patient->load('socioeconomic');
    }

    public function updatePatient(Patient $patient, array $data): Patient
    {
        $patient->fill($data);
        $patient->save();

        return $patient->load('socioeconomic');
    }

    public function softDeletePatient(Patient $patient): void
    {
        $patient->delete();
    }
}
```

3. Run Pint: `vendor/bin/pint --dirty`

**Verification:** `composer run analyse` — no errors.

---

### BE-M2-08 — Create UserController (API)

**Goal:** Admin-only CRUD for users via REST.

**Inputs:** `app/Services/UserService.php`, `app/Http/Resources/UserResource.php` (BE-M2-11)

**Output:** `app/Http/Controllers/Api/UserController.php`

**Steps:**
1. Create:
```bash
php artisan make:controller Api/UserController --api --no-interaction
```

2. Fill the file:
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection(User::all());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', 'in:admin,doktor,pacijent'],
        ]);

        $user = $this->userService->createUser($data);

        return response()->json(new UserResource($user), 201);
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    public function update(Request $request, User $user): UserResource
    {
        $data = $request->validate([
            'name'     => ['sometimes', 'string', 'max:255'],
            'email'    => ['sometimes', 'email', 'unique:users,email,'.$user->id],
            'password' => ['sometimes', 'string', 'min:8'],
            'role'     => ['sometimes', 'in:admin,doktor,pacijent'],
        ]);

        return new UserResource($this->userService->updateUser($user, $data));
    }

    public function destroy(User $user): JsonResponse
    {
        $this->userService->softDeleteUser($user);

        return response()->json(null, 204);
    }
}
```

3. Run Pint: `vendor/bin/pint --dirty`

**Verification:** `php artisan route:list --path=api/users` shows 5 routes.

---

### BE-M2-09 — Create PatientController (API)

**Goal:** CRUD for patients, policy-gated per role.

**Inputs:** `app/Services/PatientService.php`, `app/Policies/PatientPolicy.php`, `app/Http/Resources/PatientResource.php` (BE-M2-11)

**Output:** `app/Http/Controllers/Api/PatientController.php`

**Steps:**
1. Create:
```bash
php artisan make:controller Api/PatientController --api --no-interaction
```

2. Fill the file:
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Services\PatientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PatientController extends Controller
{
    public function __construct(private readonly PatientService $patientService) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Patient::class);

        return PatientResource::collection(
            $this->patientService->listForUser(auth()->user())
        );
    }

    public function store(StorePatientRequest $request): JsonResponse
    {
        $patient = $this->patientService->createPatient($request->validated());

        return response()->json(new PatientResource($patient), 201);
    }

    public function show(Patient $patient): PatientResource
    {
        $this->authorize('view', $patient);

        return new PatientResource($patient->load('socioeconomic'));
    }

    public function update(UpdatePatientRequest $request, Patient $patient): PatientResource
    {
        return new PatientResource(
            $this->patientService->updatePatient($patient, $request->validated())
        );
    }

    public function destroy(Patient $patient): JsonResponse
    {
        $this->authorize('delete', $patient);
        $this->patientService->softDeletePatient($patient);

        return response()->json(null, 204);
    }
}
```

3. Run Pint: `vendor/bin/pint --dirty`

**Verification:** `php artisan route:list --path=api/patients` shows 5 routes.

---

### BE-M2-10 — Fill StorePatientRequest + UpdatePatientRequest

**Goal:** Replace stub validation rules with real rules. Fix `authorize()` returning `false`.

**Known issue:** Both requests currently have `authorize(): bool { return false; }` and empty `rules()`. This blocks all requests.

**Inputs:**
- `app/Http/Requests/StorePatientRequest.php` — stub
- `app/Http/Requests/UpdatePatientRequest.php` — stub
- `app/Policies/PatientPolicy.php` — `create()` method returns bool

**Outputs:** Both request files with working `authorize()` and validation rules.

**StorePatientRequest.php:**
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Patient::class) ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'user_id'                  => ['required', 'integer', 'exists:users,id', 'unique:patients,user_id'],
            'first_name'               => ['required', 'string', 'max:255'],
            'last_name'                => ['required', 'string', 'max:255'],
            'date_of_birth'            => ['required', 'date', 'before:today'],
            'gender'                   => ['required', 'in:M,F'],
            'phone'                    => ['required', 'string', 'max:255'],
            'address'                  => ['nullable', 'string', 'max:255'],
            'city'                     => ['nullable', 'string', 'max:255'],
            'postal_code'              => ['nullable', 'string', 'max:20'],
            'emergency_contact_name'   => ['required', 'string', 'max:255'],
            'emergency_contact_phone'  => ['required', 'string', 'max:255'],
            'blood_type'               => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'allergies'                => ['nullable', 'string'],
            'medical_notes'            => ['nullable', 'string'],
        ];
    }
}
```

**UpdatePatientRequest.php:**
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var \App\Models\Patient $patient */
        $patient = $this->route('patient');

        return $this->user()?->can('update', $patient) ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'first_name'               => ['sometimes', 'string', 'max:255'],
            'last_name'                => ['sometimes', 'string', 'max:255'],
            'date_of_birth'            => ['sometimes', 'date', 'before:today'],
            'gender'                   => ['sometimes', 'in:M,F'],
            'phone'                    => ['sometimes', 'string', 'max:255'],
            'address'                  => ['nullable', 'string', 'max:255'],
            'city'                     => ['nullable', 'string', 'max:255'],
            'postal_code'              => ['nullable', 'string', 'max:20'],
            'emergency_contact_name'   => ['sometimes', 'string', 'max:255'],
            'emergency_contact_phone'  => ['sometimes', 'string', 'max:255'],
            'blood_type'               => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'allergies'                => ['nullable', 'string'],
            'medical_notes'            => ['nullable', 'string'],
        ];
    }
}
```

3. Run Pint: `vendor/bin/pint --dirty`

**Verification:**
```bash
php artisan test --filter=PatientApi
```

---

### BE-M2-11 — Create Eloquent Resources

**Goal:** Standardise JSON output structure for all three entities.

**Inputs:** `app/Models/User.php`, `Patient.php`, `PatientSocioeconomic.php`

**Outputs:**
- `app/Http/Resources/UserResource.php`
- `app/Http/Resources/PatientResource.php`
- `app/Http/Resources/PatientSocioeconomicResource.php`

**Steps:**
```bash
php artisan make:resource UserResource --no-interaction
php artisan make:resource PatientResource --no-interaction
php artisan make:resource PatientSocioeconomicResource --no-interaction
```

**UserResource.php:**
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'role'  => $this->role,
        ];
    }
}
```

**PatientSocioeconomicResource.php:**
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientSocioeconomicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'marital_status'      => $this->marital_status,
            'employment_status'   => $this->employment_status,
            'income_level'        => $this->income_level,
            'has_health_insurance'=> $this->has_health_insurance,
            'education_level'     => $this->education_level,
        ];
    }
}
```

**PatientResource.php:**
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                       => $this->id,
            'user_id'                  => $this->user_id,
            'first_name'               => $this->first_name,
            'last_name'                => $this->last_name,
            'full_name'                => $this->full_name,
            'date_of_birth'            => $this->date_of_birth?->toDateString(),
            'gender'                   => $this->gender,
            'phone'                    => $this->phone,
            'address'                  => $this->address,
            'city'                     => $this->city,
            'blood_type'               => $this->blood_type,
            'allergies'                => $this->allergies,
            'medical_notes'            => $this->medical_notes,
            'socioeconomic'            => new PatientSocioeconomicResource($this->whenLoaded('socioeconomic')),
        ];
    }
}
```

**Run Pint:** `vendor/bin/pint --dirty`

**Verification:** `composer run analyse` — no errors.

---

### BE-M2-12 — Write Pest HTTP Tests (M2)

**Goal:** 5 tests covering the happy path and access control for auth + patient endpoints.

**Inputs:** All controllers and resources from BE-M2-05 to BE-M2-11.

**Output:** `tests/Feature/Api/PatientApiTest.php`

**Steps:**
```bash
php artisan make:test Api/PatientApiTest --pest --no-interaction
```

**File content:**
```php
<?php

declare(strict_types=1);

use App\Models\Patient;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// Test 1: Login with valid credentials returns token
test('login with valid credentials returns token', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);

    $response = $this->postJson('/api/login', [
        'email'    => $user->email,
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['token', 'user' => ['id', 'email', 'role']]);
});

// Test 2: Admin sees all patients
test('admin can list all patients', function () {
    $admin    = User::factory()->create(['role' => 'admin']);
    $patients = Patient::factory()->count(3)->create();

    $response = $this->actingAs($admin)->getJson('/api/patients');

    $response->assertOk()
        ->assertJsonCount(3, 'data');
});

// Test 3: Create patient with valid data
test('doctor can create a patient', function () {
    $doctor = User::factory()->create(['role' => 'doktor']);
    $user   = User::factory()->create(['role' => 'pacijent']);

    $response = $this->actingAs($doctor)->postJson('/api/patients', [
        'user_id'                 => $user->id,
        'first_name'              => 'Ana',
        'last_name'               => 'Kovač',
        'date_of_birth'           => '1990-05-15',
        'gender'                  => 'F',
        'phone'                   => '+387 61 123 456',
        'emergency_contact_name'  => 'Marko Kovač',
        'emergency_contact_phone' => '+387 62 987 654',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.first_name', 'Ana');

    $this->assertDatabaseHas('patients', ['first_name' => 'Ana']);
});

// Test 4: Patient can only see their own record
test('patient cannot view another patient record', function () {
    $patientUser    = User::factory()->create(['role' => 'pacijent']);
    $otherPatient   = Patient::factory()->create();

    $this->actingAs($patientUser)
        ->getJson("/api/patients/{$otherPatient->id}")
        ->assertForbidden();
});

// Test 5: Unauthenticated request to protected route returns 401
test('unauthenticated request returns 401', function () {
    $this->getJson('/api/patients')
        ->assertUnauthorized();
});
```

**Run tests:**
```bash
php artisan test tests/Feature/Api/PatientApiTest.php
```

**Verification:** All 5 tests pass.

---

## FRONTEND TASKS

---

### FE-M2-01 — Scaffold /frontend with React + Vite + TypeScript

**Goal:** Create the React SPA skeleton in a `/frontend` subfolder.

**Inputs:** none

**Output:** `frontend/` directory with working Vite + React + TS setup

**Steps:**
```bash
cd D:/_Learn/_PhpstormProjects/nutri-ledger
npm create vite@latest frontend -- --template react-ts
cd frontend
npm install
npm install axios react-router-dom zustand
npm install -D @types/react @types/react-dom
```

Test the dev server:
```bash
npm run dev
# Visit http://localhost:5173 — should show Vite + React default page
```

**Verification:** `npm run dev` starts without errors. Page loads at `localhost:5173`.

---

### FE-M2-02 — Configure Axios API Client

**Goal:** Create a pre-configured axios instance that attaches the Bearer token from localStorage.

**Inputs:** none

**Output:** `frontend/src/api/client.ts`

**File content:**
```typescript
import axios from 'axios';

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL ?? 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Attach Bearer token from localStorage on every request
apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Handle 401 globally — clear token and redirect to login
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default apiClient;
```

**Add to `frontend/.env.local`:**
```
VITE_API_BASE_URL=http://localhost:8000/api
```

**Verification:** Import `apiClient` in `main.tsx` and `console.log(apiClient.defaults.baseURL)` — should print the correct URL.

---

### FE-M2-03 — Auth State Management (Zustand)

**Goal:** Store token + user in Zustand with localStorage persistence.

**Inputs:** `frontend/src/api/client.ts`

**Output:** `frontend/src/store/authStore.ts`

**File content:**
```typescript
import { create } from 'zustand';
import { persist } from 'zustand/middleware';

interface AuthUser {
  id: number;
  name: string;
  email: string;
  role: 'admin' | 'doktor' | 'pacijent';
}

interface AuthState {
  token: string | null;
  user: AuthUser | null;
  setAuth: (token: string, user: AuthUser) => void;
  clearAuth: () => void;
  isAuthenticated: () => boolean;
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      token: null,
      user: null,
      setAuth: (token, user) => {
        localStorage.setItem('auth_token', token);
        set({ token, user });
      },
      clearAuth: () => {
        localStorage.removeItem('auth_token');
        set({ token: null, user: null });
      },
      isAuthenticated: () => get().token !== null,
    }),
    { name: 'auth-storage' }
  )
);
```

**Verification:** Import the store in a component and log `useAuthStore.getState()` — should return `{ token: null, user: null }` on first run.

---

### FE-M2-04 — React Router v6 Setup + Protected Routes

**Goal:** Configure client-side routing with a `ProtectedRoute` that redirects to `/login` when unauthenticated.

**Inputs:** `frontend/src/store/authStore.ts`

**Outputs:**
- `frontend/src/router/index.tsx`
- `frontend/src/router/ProtectedRoute.tsx`
- Updated `frontend/src/main.tsx`

**ProtectedRoute.tsx:**
```tsx
import { Navigate } from 'react-router-dom';
import { useAuthStore } from '../store/authStore';

export function ProtectedRoute({ children }: { children: React.ReactNode }) {
  const isAuthenticated = useAuthStore((s) => s.isAuthenticated());
  return isAuthenticated ? <>{children}</> : <Navigate to="/login" replace />;
}
```

**router/index.tsx:**
```tsx
import { createBrowserRouter } from 'react-router-dom';
import { LoginPage } from '../pages/auth/LoginPage';
import { PatientListPage } from '../pages/patients/PatientListPage';
import { PatientCreatePage } from '../pages/patients/PatientCreatePage';
import { PatientViewPage } from '../pages/patients/PatientViewPage';
import { PatientEditPage } from '../pages/patients/PatientEditPage';
import { ProtectedRoute } from './ProtectedRoute';

export const router = createBrowserRouter([
  { path: '/login', element: <LoginPage /> },
  {
    path: '/',
    element: <ProtectedRoute><PatientListPage /></ProtectedRoute>,
  },
  {
    path: '/patients',
    element: <ProtectedRoute><PatientListPage /></ProtectedRoute>,
  },
  {
    path: '/patients/create',
    element: <ProtectedRoute><PatientCreatePage /></ProtectedRoute>,
  },
  {
    path: '/patients/:id',
    element: <ProtectedRoute><PatientViewPage /></ProtectedRoute>,
  },
  {
    path: '/patients/:id/edit',
    element: <ProtectedRoute><PatientEditPage /></ProtectedRoute>,
  },
]);
```

**main.tsx — update to use RouterProvider:**
```tsx
import React from 'react';
import ReactDOM from 'react-dom/client';
import { RouterProvider } from 'react-router-dom';
import { router } from './router';
import './index.css';

ReactDOM.createRoot(document.getElementById('root')!).render(
  <React.StrictMode>
    <RouterProvider router={router} />
  </React.StrictMode>
);
```

**Verification:** Navigate to `http://localhost:5173/` → redirects to `/login`. Navigate to `http://localhost:5173/patients` → redirects to `/login`.

---

### FE-M2-05 — Login Page

**Goal:** POST to `/api/login`, store token in Zustand, redirect to patient list.

**Inputs:** `frontend/src/api/client.ts`, `frontend/src/store/authStore.ts`

**Output:** `frontend/src/pages/auth/LoginPage.tsx`

**File content:**
```tsx
import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuthStore } from '../../store/authStore';
import apiClient from '../../api/client';

export function LoginPage() {
  const [email, setEmail]       = useState('');
  const [password, setPassword] = useState('');
  const [error, setError]       = useState<string | null>(null);
  const setAuth                 = useAuthStore((s) => s.setAuth);
  const navigate                = useNavigate();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);

    try {
      const response = await apiClient.post('/login', { email, password });
      setAuth(response.data.token, response.data.user);
      navigate('/patients');
    } catch {
      setError('Invalid email or password.');
    }
  };

  return (
    <div style={{ maxWidth: 400, margin: '80px auto', padding: '0 16px' }}>
      <h1>nutri-ledger</h1>
      <form onSubmit={handleSubmit}>
        <div>
          <label>Email</label>
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
          />
        </div>
        <div>
          <label>Password</label>
          <input
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
          />
        </div>
        {error && <p style={{ color: 'red' }}>{error}</p>}
        <button type="submit">Log In</button>
      </form>
    </div>
  );
}
```

**Verification:**
1. Start Laravel: `composer run dev`
2. Start React: `npm run dev`
3. Go to `http://localhost:5173/login`
4. Enter valid credentials → redirected to `/patients`
5. Enter wrong credentials → error message shown

---

### FE-M2-06 — Patient List Page

**Goal:** Fetch and display all patients in a table. Admin/doctor only.

**Inputs:** `GET /api/patients`

**Output:** `frontend/src/pages/patients/PatientListPage.tsx`

**File content:**
```tsx
import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import apiClient from '../../api/client';

interface Patient {
  id: number;
  first_name: string;
  last_name: string;
  date_of_birth: string;
  gender: string;
  phone: string;
}

export function PatientListPage() {
  const [patients, setPatients] = useState<Patient[]>([]);
  const [loading, setLoading]   = useState(true);

  useEffect(() => {
    apiClient.get('/patients')
      .then((res) => setPatients(res.data.data))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return <p>Loading...</p>;
  }

  return (
    <div>
      <h1>Patients</h1>
      <Link to="/patients/create">+ New Patient</Link>
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>DOB</th>
            <th>Gender</th>
            <th>Phone</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {patients.map((p) => (
            <tr key={p.id}>
              <td>{p.first_name} {p.last_name}</td>
              <td>{p.date_of_birth}</td>
              <td>{p.gender}</td>
              <td>{p.phone}</td>
              <td>
                <Link to={`/patients/${p.id}`}>View</Link>
                {' | '}
                <Link to={`/patients/${p.id}/edit`}>Edit</Link>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
```

**Verification:** Navigate to `/patients` — table lists all patients from the API.

---

### FE-M2-07 — Create Patient Page

**Goal:** Form that POSTs to `/api/patients` and redirects to patient list on success.

**Inputs:** `POST /api/patients`

**Output:** `frontend/src/pages/patients/PatientCreatePage.tsx`

**File content:**
```tsx
import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import apiClient from '../../api/client';

export function PatientCreatePage() {
  const navigate = useNavigate();
  const [form, setForm] = useState({
    user_id: '',
    first_name: '',
    last_name: '',
    date_of_birth: '',
    gender: 'F',
    phone: '',
    emergency_contact_name: '',
    emergency_contact_phone: '',
  });
  const [errors, setErrors] = useState<Record<string, string[]>>({});

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setErrors({});

    try {
      await apiClient.post('/patients', form);
      navigate('/patients');
    } catch (err: any) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors ?? {});
      }
    }
  };

  const fieldError = (field: string) =>
    errors[field]?.[0] ? <span style={{ color: 'red' }}>{errors[field][0]}</span> : null;

  return (
    <div>
      <h1>New Patient</h1>
      <form onSubmit={handleSubmit}>
        <div>
          <label>User ID (linked account)</label>
          <input name="user_id" value={form.user_id} onChange={handleChange} required />
          {fieldError('user_id')}
        </div>
        <div>
          <label>First Name</label>
          <input name="first_name" value={form.first_name} onChange={handleChange} required />
          {fieldError('first_name')}
        </div>
        <div>
          <label>Last Name</label>
          <input name="last_name" value={form.last_name} onChange={handleChange} required />
          {fieldError('last_name')}
        </div>
        <div>
          <label>Date of Birth</label>
          <input type="date" name="date_of_birth" value={form.date_of_birth} onChange={handleChange} required />
          {fieldError('date_of_birth')}
        </div>
        <div>
          <label>Gender</label>
          <select name="gender" value={form.gender} onChange={handleChange}>
            <option value="F">Female</option>
            <option value="M">Male</option>
          </select>
        </div>
        <div>
          <label>Phone</label>
          <input name="phone" value={form.phone} onChange={handleChange} required />
          {fieldError('phone')}
        </div>
        <div>
          <label>Emergency Contact Name</label>
          <input name="emergency_contact_name" value={form.emergency_contact_name} onChange={handleChange} required />
        </div>
        <div>
          <label>Emergency Contact Phone</label>
          <input name="emergency_contact_phone" value={form.emergency_contact_phone} onChange={handleChange} required />
        </div>
        <button type="submit">Create Patient</button>
      </form>
    </div>
  );
}
```

**Verification:** Fill form and submit → new patient appears in `/patients` list. Submit with missing fields → validation errors shown inline.

---

### FE-M2-08 — View Patient Page

**Goal:** Display full patient detail from `GET /api/patients/{id}`.

**Inputs:** `GET /api/patients/{id}`

**Output:** `frontend/src/pages/patients/PatientViewPage.tsx`

**File content:**
```tsx
import { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import apiClient from '../../api/client';

interface Patient {
  id: number;
  first_name: string;
  last_name: string;
  date_of_birth: string;
  gender: string;
  phone: string;
  blood_type: string | null;
  allergies: string | null;
  medical_notes: string | null;
}

export function PatientViewPage() {
  const { id }                = useParams<{ id: string }>();
  const [patient, setPatient] = useState<Patient | null>(null);

  useEffect(() => {
    apiClient.get(`/patients/${id}`)
      .then((res) => setPatient(res.data.data));
  }, [id]);

  if (!patient) {
    return <p>Loading...</p>;
  }

  return (
    <div>
      <h1>{patient.first_name} {patient.last_name}</h1>
      <p><strong>DOB:</strong> {patient.date_of_birth}</p>
      <p><strong>Gender:</strong> {patient.gender}</p>
      <p><strong>Phone:</strong> {patient.phone}</p>
      <p><strong>Blood Type:</strong> {patient.blood_type ?? '—'}</p>
      <p><strong>Allergies:</strong> {patient.allergies ?? '—'}</p>
      <p><strong>Medical Notes:</strong> {patient.medical_notes ?? '—'}</p>
      <Link to={`/patients/${id}/edit`}>Edit</Link>
      {' | '}
      <Link to="/patients">Back to list</Link>
    </div>
  );
}
```

**Verification:** Navigate to `/patients/1` — patient data displayed. Wrong ID → API returns 404, page shows "Loading..." (add error handling as stretch goal).

---

### FE-M2-09 — Edit Patient Page

**Goal:** Pre-populate form with current patient data and PUT on submit.

**Inputs:** `GET /api/patients/{id}`, `PUT /api/patients/{id}`

**Output:** `frontend/src/pages/patients/PatientEditPage.tsx`

**File content:**
```tsx
import { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import apiClient from '../../api/client';

export function PatientEditPage() {
  const { id }   = useParams<{ id: string }>();
  const navigate = useNavigate();
  const [form, setForm] = useState({
    first_name: '',
    last_name: '',
    date_of_birth: '',
    gender: 'F',
    phone: '',
    blood_type: '',
    allergies: '',
    medical_notes: '',
  });
  const [errors, setErrors] = useState<Record<string, string[]>>({});

  useEffect(() => {
    apiClient.get(`/patients/${id}`).then((res) => {
      const p = res.data.data;
      setForm({
        first_name:    p.first_name,
        last_name:     p.last_name,
        date_of_birth: p.date_of_birth,
        gender:        p.gender,
        phone:         p.phone,
        blood_type:    p.blood_type ?? '',
        allergies:     p.allergies ?? '',
        medical_notes: p.medical_notes ?? '',
      });
    });
  }, [id]);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setErrors({});

    try {
      await apiClient.put(`/patients/${id}`, form);
      navigate(`/patients/${id}`);
    } catch (err: any) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors ?? {});
      }
    }
  };

  const fieldError = (field: string) =>
    errors[field]?.[0] ? <span style={{ color: 'red' }}>{errors[field][0]}</span> : null;

  return (
    <div>
      <h1>Edit Patient</h1>
      <form onSubmit={handleSubmit}>
        <div><label>First Name</label>
          <input name="first_name" value={form.first_name} onChange={handleChange} />
          {fieldError('first_name')}
        </div>
        <div><label>Last Name</label>
          <input name="last_name" value={form.last_name} onChange={handleChange} />
          {fieldError('last_name')}
        </div>
        <div><label>Date of Birth</label>
          <input type="date" name="date_of_birth" value={form.date_of_birth} onChange={handleChange} />
        </div>
        <div><label>Gender</label>
          <select name="gender" value={form.gender} onChange={handleChange}>
            <option value="F">Female</option>
            <option value="M">Male</option>
          </select>
        </div>
        <div><label>Phone</label>
          <input name="phone" value={form.phone} onChange={handleChange} />
        </div>
        <div><label>Blood Type</label>
          <select name="blood_type" value={form.blood_type} onChange={handleChange}>
            <option value="">—</option>
            {['A+','A-','B+','B-','AB+','AB-','O+','O-'].map((bt) => (
              <option key={bt} value={bt}>{bt}</option>
            ))}
          </select>
        </div>
        <div><label>Allergies</label>
          <textarea name="allergies" value={form.allergies} onChange={handleChange} />
        </div>
        <div><label>Medical Notes</label>
          <textarea name="medical_notes" value={form.medical_notes} onChange={handleChange} />
        </div>
        <button type="submit">Save Changes</button>
      </form>
    </div>
  );
}
```

**Verification:** Navigate to `/patients/1/edit` — form pre-filled with existing data. Edit and save → redirected to view page with updated data.
