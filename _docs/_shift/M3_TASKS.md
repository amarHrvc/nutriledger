# M3 Tasks — Release 2: Visit API + Patterns + Tests + Deploy
**Deadline:** Jun 7 2026
**Branch:** `feature/se-pivot` (continue from M2)
**Prerequisite:** All M2 tasks complete and tests passing.

---

## BACKEND TASKS

---

### BE-M3-01 — Create VisitResource

**Goal:** Standardise JSON output for visits, including the doctor as a nested UserResource.

**Inputs:**
- `app/Models/Visit.php` — fields: id, patient_id, doctor_id, date, notes
- `app/Http/Resources/UserResource.php` (from BE-M2-11)

**Output:** `app/Http/Resources/VisitResource.php`

**Steps:**
```bash
php artisan make:resource VisitResource --no-interaction
```

**File content:**
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'patient_id' => $this->patient_id,
            'doctor'     => new UserResource($this->whenLoaded('doctor')),
            'date'       => $this->date,
            'notes'      => $this->notes,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
```

Run Pint: `vendor/bin/pint --dirty`

**Verification:** `composer run analyse` — no errors.

---

### BE-M3-02 — Create UpdateVisitRequest

**Goal:** Validation + authorization for PUT /api/visits/{id}.

**Inputs:**
- `app/Policies/VisitPolicy.php` — `update()` allows admin or the doctor who created it
- `app/Http/Requests/StoreVisitRequest.php` — reference for rule style

**Output:** `app/Http/Requests/UpdateVisitRequest.php`

**Steps:**
```bash
php artisan make:request UpdateVisitRequest --no-interaction
```

**File content:**
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var \App\Models\Visit $visit */
        $visit = $this->route('visit');

        return $this->user()?->can('update', $visit) ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'date'  => ['sometimes', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
```

Run Pint: `vendor/bin/pint --dirty`

**Verification:** `composer run analyse` — no errors.

---

### BE-M3-03 — Create VisitService

**Goal:** Encapsulate visit create/update/delete logic.

**Inputs:** `app/Models/Visit.php`, `app/Models/Patient.php`

**Output:** `app/Services/VisitService.php`

**Steps:**
```bash
php artisan make:class Services/VisitService --no-interaction
```

**File content:**
```php
<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;

class VisitService
{
    public function createVisit(Patient $patient, User $doctor, array $data): Visit
    {
        $visit = $patient->visits()->create([
            'doctor_id' => $doctor->id,
            'date'      => $data['date'],
            'notes'     => $data['notes'] ?? null,
        ]);

        return $visit->load('doctor');
    }

    public function updateVisit(Visit $visit, array $data): Visit
    {
        $visit->fill($data);
        $visit->save();

        return $visit->load('doctor');
    }

    public function deleteVisit(Visit $visit): void
    {
        $visit->delete();
    }
}
```

Run Pint: `vendor/bin/pint --dirty`

**Verification:** `composer run analyse` — no errors.

---

### BE-M3-04 — Create VisitController (API)

**Goal:** CRUD for visits scoped to patient, with policy gates.

**Known issue:** `VisitPolicy::view()` calls `$user->ispatient()` (lowercase — wrong method name). Fix to `$user->isPatient()` before wiring the controller.

**Fix first — `app/Policies/VisitPolicy.php` line 28:**
```php
// BEFORE (broken):
if ($user->ispatient()) {

// AFTER (correct):
if ($user->isPatient()) {
```

**Inputs:**
- `app/Services/VisitService.php`
- `app/Http/Resources/VisitResource.php`
- `app/Http/Requests/StoreVisitRequest.php`, `UpdateVisitRequest.php`
- `app/Policies/VisitPolicy.php`

**Output:** `app/Http/Controllers/Api/VisitController.php`

**Steps:**
```bash
php artisan make:controller Api/VisitController --api --no-interaction
```

**File content:**
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Http\Resources\VisitResource;
use App\Models\Patient;
use App\Models\Visit;
use App\Services\VisitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VisitController extends Controller
{
    public function __construct(private readonly VisitService $visitService) {}

    public function index(Patient $patient): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Visit::class);

        return VisitResource::collection(
            $patient->visits()->with('doctor')->get()
        );
    }

    public function store(StoreVisitRequest $request, Patient $patient): JsonResponse
    {
        $visit = $this->visitService->createVisit(
            $patient,
            $request->user(),
            $request->validated()
        );

        return response()->json(new VisitResource($visit), 201);
    }

    public function show(Visit $visit): VisitResource
    {
        $this->authorize('view', $visit);

        return new VisitResource($visit->load('doctor'));
    }

    public function update(UpdateVisitRequest $request, Visit $visit): VisitResource
    {
        return new VisitResource(
            $this->visitService->updateVisit($visit, $request->validated())
        );
    }

    public function destroy(Visit $visit): JsonResponse
    {
        $this->authorize('delete', $visit);
        $this->visitService->deleteVisit($visit);

        return response()->json(null, 204);
    }
}
```

Run Pint: `vendor/bin/pint --dirty`

**Verification:** `php artisan route:list --path=api` — visit routes visible.

---

### BE-M3-05 — Add Visit Routes to api.php

**Goal:** Register nested visit routes under patients and standalone visit routes.

**Inputs:** `routes/api.php` (from BE-M2-04)

**Updated `routes/api.php` (add inside the `auth:sanctum` group):**
```php
// Visit routes — nested under patient
Route::get('patients/{patient}/visits', [VisitController::class, 'index']);
Route::post('patients/{patient}/visits', [VisitController::class, 'store']);

// Standalone visit routes
Route::get('visits/{visit}', [VisitController::class, 'show']);
Route::put('visits/{visit}', [VisitController::class, 'update']);
Route::delete('visits/{visit}', [VisitController::class, 'destroy']);
```

Also add the import at the top of `api.php`:
```php
use App\Http\Controllers\Api\VisitController;
```

Run Pint: `vendor/bin/pint --dirty`

**Verification:**
```bash
php artisan route:list --path=api/visits
# Shows: GET api/visits/{visit}, PUT api/visits/{visit}, DELETE api/visits/{visit}
php artisan route:list --path=api/patients
# Shows: GET api/patients/{patient}/visits, POST api/patients/{patient}/visits
```

---

### BE-M3-06 — Implement Repository Pattern

**Goal:** Abstract DB access from service layer. Bind repositories in a service provider.

**Outputs:**
- `app/Repositories/UserRepository.php`
- `app/Repositories/PatientRepository.php`
- `app/Repositories/VisitRepository.php`
- `app/Providers/RepositoryServiceProvider.php`
- Updated `bootstrap/providers.php`

**Steps:**

1. Create repository classes:
```bash
php artisan make:class Repositories/UserRepository --no-interaction
php artisan make:class Repositories/PatientRepository --no-interaction
php artisan make:class Repositories/VisitRepository --no-interaction
```

2. **UserRepository.php:**
```php
<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    public function all(): Collection
    {
        return User::all();
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->fill($data)->save();
        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
```

3. **PatientRepository.php:**
```php
<?php

namespace App\Repositories;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class PatientRepository
{
    public function allWithSocioeconomic(): Collection
    {
        return Patient::with('socioeconomic')->get();
    }

    public function forUser(User $user): Collection
    {
        return Patient::with('socioeconomic')
            ->where('user_id', $user->id)
            ->get();
    }

    public function findById(int $id): ?Patient
    {
        return Patient::with('socioeconomic')->find($id);
    }

    public function create(array $data): Patient
    {
        $patient = Patient::create($data);
        $patient->socioeconomic()->create([]);
        return $patient->load('socioeconomic');
    }

    public function update(Patient $patient, array $data): Patient
    {
        $patient->fill($data)->save();
        return $patient->load('socioeconomic');
    }

    public function delete(Patient $patient): void
    {
        $patient->delete();
    }
}
```

4. **VisitRepository.php:**
```php
<?php

namespace App\Repositories;

use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Collection;

class VisitRepository
{
    public function forPatient(Patient $patient): Collection
    {
        return $patient->visits()->with('doctor')->get();
    }

    public function findById(int $id): ?Visit
    {
        return Visit::with('doctor')->find($id);
    }

    public function create(array $data): Visit
    {
        $visit = Visit::create($data);
        return $visit->load('doctor');
    }

    public function update(Visit $visit, array $data): Visit
    {
        $visit->fill($data)->save();
        return $visit->load('doctor');
    }

    public function delete(Visit $visit): void
    {
        $visit->delete();
    }
}
```

5. **Create RepositoryServiceProvider:**
```bash
php artisan make:provider RepositoryServiceProvider --no-interaction
```

**RepositoryServiceProvider.php:**
```php
<?php

namespace App\Providers;

use App\Repositories\PatientRepository;
use App\Repositories\UserRepository;
use App\Repositories\VisitRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(UserRepository::class, fn () => new UserRepository());
        $this->app->singleton(PatientRepository::class, fn () => new PatientRepository());
        $this->app->singleton(VisitRepository::class, fn () => new VisitRepository());
    }

    public function boot(): void {}
}
```

6. Register in `bootstrap/providers.php`:
```php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\RepositoryServiceProvider::class,
];
```

7. Update services to use repositories — example for `PatientService`:
```php
// Inject PatientRepository via constructor instead of calling Patient::query() directly
public function __construct(private readonly PatientRepository $patientRepository) {}

public function listForUser(User $user): Collection
{
    if ($user->isAdmin() || $user->isDoctor()) {
        return $this->patientRepository->allWithSocioeconomic();
    }
    return $this->patientRepository->forUser($user);
}
```

Run Pint: `vendor/bin/pint --dirty`

**Verification:**
```bash
php artisan test tests/Feature/Api/PatientApiTest.php
# All tests must still pass after refactor
```

---

### BE-M3-07 — Implement Observer Pattern (PatientObserver)

**Goal:** Fire hooks on patient `created` and `deleted` events. Log each event.

**Inputs:** `app/Models/Patient.php`

**Outputs:**
- `app/Observers/PatientObserver.php`
- Updated `app/Providers/AppServiceProvider.php`

**Steps:**

1. Create observer:
```bash
php artisan make:observer PatientObserver --model=Patient --no-interaction
```

2. **PatientObserver.php:**
```php
<?php

namespace App\Observers;

use App\Models\Patient;
use Illuminate\Support\Facades\Log;

class PatientObserver
{
    public function created(Patient $patient): void
    {
        Log::info('Patient created', [
            'patient_id' => $patient->id,
            'name'       => $patient->first_name.' '.$patient->last_name,
        ]);
    }

    public function deleted(Patient $patient): void
    {
        Log::warning('Patient soft-deleted', [
            'patient_id' => $patient->id,
            'name'       => $patient->first_name.' '.$patient->last_name,
            'deleted_at' => $patient->deleted_at,
        ]);
    }
}
```

3. Register in `app/Providers/AppServiceProvider.php`:
```php
<?php

namespace App\Providers;

use App\Models\Patient;
use App\Observers\PatientObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Patient::observe(PatientObserver::class);
    }
}
```

Run Pint: `vendor/bin/pint --dirty`

**Verification:**
```bash
php artisan tinker
# >>> \App\Models\Patient::factory()->create();
# Check storage/logs/laravel.log — should contain "Patient created" entry
```

---

### BE-M3-08 — Write Remaining Pest HTTP Tests (Visit Endpoints)

**Goal:** 5 additional tests covering visit endpoints and edge cases.

**Inputs:** All visit controllers and policies from BE-M3-01 to BE-M3-05.

**Output:** `tests/Feature/Api/VisitApiTest.php`

**Steps:**
```bash
php artisan make:test Api/VisitApiTest --pest --no-interaction
```

**File content:**
```php
<?php

declare(strict_types=1);

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// Test 1: Doctor can create a visit for a patient
test('doctor can create a visit for a patient', function () {
    $doctor  = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    $response = $this->actingAs($doctor)->postJson("/api/patients/{$patient->id}/visits", [
        'date'  => '2026-04-10',
        'notes' => 'Initial consultation.',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.date', '2026-04-10');

    $this->assertDatabaseHas('visits', ['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);
});

// Test 2: Admin can list all visits for a patient
test('admin can list visits for a patient', function () {
    $admin   = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    Visit::factory()->count(2)->create(['patient_id' => $patient->id]);

    $response = $this->actingAs($admin)->getJson("/api/patients/{$patient->id}/visits");

    $response->assertOk()
        ->assertJsonCount(2, 'data');
});

// Test 3: Patient can view their own visit
test('patient can view their own visit', function () {
    $patientUser = User::factory()->create(['role' => 'pacijent']);
    $patient     = Patient::factory()->create(['user_id' => $patientUser->id]);
    $visit       = Visit::factory()->create(['patient_id' => $patient->id]);

    $this->actingAs($patientUser)
        ->getJson("/api/visits/{$visit->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $visit->id);
});

// Test 4: Patient cannot view another patient's visit
test('patient cannot view another patient visit', function () {
    $patientUser  = User::factory()->create(['role' => 'pacijent']);
    $otherPatient = Patient::factory()->create();
    $visit        = Visit::factory()->create(['patient_id' => $otherPatient->id]);

    $this->actingAs($patientUser)
        ->getJson("/api/visits/{$visit->id}")
        ->assertForbidden();
});

// Test 5: Admin can delete a visit
test('admin can delete a visit', function () {
    $admin   = User::factory()->create(['role' => 'admin']);
    $visit   = Visit::factory()->create();

    $this->actingAs($admin)
        ->deleteJson("/api/visits/{$visit->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('visits', ['id' => $visit->id]);
});
```

**Run tests:**
```bash
php artisan test tests/Feature/Api/VisitApiTest.php
```

**Verification:** All 5 tests pass.

---

### BE-M3-09 — Deploy Laravel API to Railway

**Goal:** Deploy the Laravel API to a public URL for the M3 submission.

**Inputs:** `composer.json`, `.env.example`

**Output:** Public API URL (e.g. `https://nutri-ledger-api.up.railway.app`)

**Steps:**

1. Create `Dockerfile` in project root:
```dockerfile
FROM php:8.4-fpm-alpine

RUN apk add --no-cache nginx bash curl \
    && docker-php-ext-install pdo pdo_mysql opcache

WORKDIR /var/www

COPY . .
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN composer install --optimize-autoloader --no-dev \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 8080
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
```

2. Create `railway.json` in project root:
```json
{
  "$schema": "https://railway.app/railway.schema.json",
  "build": {
    "builder": "DOCKERFILE"
  },
  "deploy": {
    "startCommand": "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT",
    "healthcheckPath": "/up",
    "restartPolicyType": "ON_FAILURE"
  }
}
```

3. Push branch to GitHub.

4. On [railway.app](https://railway.app):
   - New project → Deploy from GitHub repo
   - Select `feature/se-pivot` branch
   - Add environment variables from `.env.example`: `APP_KEY`, `DB_*`, `APP_URL`
   - Add a MySQL plugin for the database

5. After deploy succeeds, copy the public URL.

6. Update `config/cors.php` to allow the React frontend's deployed domain.

**Verification:**
```bash
curl https://your-app.up.railway.app/up
# Returns: {"status":"ok",...}
```

---

## FRONTEND TASKS

---

### FE-M3-01 — Visit List Page per Patient

**Goal:** Show all visits for a patient at `/patients/:id/visits`.

**Inputs:** `GET /api/patients/{patient}/visits`

**Output:** `frontend/src/pages/visits/VisitListPage.tsx`

**Steps:**

Add route to `frontend/src/router/index.tsx`:
```tsx
import { VisitListPage } from '../pages/visits/VisitListPage';
import { VisitCreatePage } from '../pages/visits/VisitCreatePage';
import { VisitViewPage } from '../pages/visits/VisitViewPage';

// Add inside the router array:
{
  path: '/patients/:patientId/visits',
  element: <ProtectedRoute><VisitListPage /></ProtectedRoute>,
},
{
  path: '/patients/:patientId/visits/create',
  element: <ProtectedRoute><VisitCreatePage /></ProtectedRoute>,
},
{
  path: '/visits/:id',
  element: <ProtectedRoute><VisitViewPage /></ProtectedRoute>,
},
```

**VisitListPage.tsx:**
```tsx
import { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import apiClient from '../../api/client';

interface Visit {
  id: number;
  date: string;
  notes: string | null;
  doctor: { id: number; name: string } | null;
}

export function VisitListPage() {
  const { patientId }      = useParams<{ patientId: string }>();
  const [visits, setVisits] = useState<Visit[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    apiClient.get(`/patients/${patientId}/visits`)
      .then((res) => setVisits(res.data.data))
      .finally(() => setLoading(false));
  }, [patientId]);

  if (loading) {
    return <p>Loading...</p>;
  }

  return (
    <div>
      <h1>Visit History</h1>
      <Link to={`/patients/${patientId}/visits/create`}>+ New Visit</Link>
      <Link to={`/patients/${patientId}`} style={{ marginLeft: 16 }}>← Back to Patient</Link>
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Doctor</th>
            <th>Notes</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {visits.map((v) => (
            <tr key={v.id}>
              <td>{v.date}</td>
              <td>{v.doctor?.name ?? '—'}</td>
              <td>{v.notes ?? '—'}</td>
              <td>
                <Link to={`/visits/${v.id}`}>View</Link>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
      {visits.length === 0 && <p>No visits recorded yet.</p>}
    </div>
  );
}
```

Also add a link to the visit list from `PatientViewPage.tsx`:
```tsx
<Link to={`/patients/${id}/visits`}>View Visits</Link>
```

**Verification:** Navigate to `/patients/1/visits` — table lists visits. Empty state shown when none exist.

---

### FE-M3-02 — Create Visit Page

**Goal:** Form that POSTs to `/api/patients/{patient}/visits` and redirects to visit list.

**Inputs:** `POST /api/patients/{patient}/visits`

**Output:** `frontend/src/pages/visits/VisitCreatePage.tsx`

**File content:**
```tsx
import { useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import apiClient from '../../api/client';

export function VisitCreatePage() {
  const { patientId } = useParams<{ patientId: string }>();
  const navigate      = useNavigate();
  const [form, setForm] = useState({ date: '', notes: '' });
  const [errors, setErrors] = useState<Record<string, string[]>>({});

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setErrors({});

    try {
      await apiClient.post(`/patients/${patientId}/visits`, form);
      navigate(`/patients/${patientId}/visits`);
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
      <h1>New Visit</h1>
      <form onSubmit={handleSubmit}>
        <div>
          <label>Date</label>
          <input type="date" name="date" value={form.date} onChange={handleChange} required />
          {fieldError('date')}
        </div>
        <div>
          <label>Clinical Notes</label>
          <textarea name="notes" value={form.notes} onChange={handleChange} rows={5} />
          {fieldError('notes')}
        </div>
        <button type="submit">Save Visit</button>
      </form>
    </div>
  );
}
```

**Verification:** Fill form and submit → new visit appears in visit list. Missing date → validation error shown.

---

### FE-M3-03 — View Visit Page

**Goal:** Display visit detail from `GET /api/visits/{id}`.

**Inputs:** `GET /api/visits/{id}`

**Output:** `frontend/src/pages/visits/VisitViewPage.tsx`

**File content:**
```tsx
import { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import apiClient from '../../api/client';

interface Visit {
  id: number;
  patient_id: number;
  date: string;
  notes: string | null;
  doctor: { id: number; name: string } | null;
}

export function VisitViewPage() {
  const { id }             = useParams<{ id: string }>();
  const [visit, setVisit]  = useState<Visit | null>(null);

  useEffect(() => {
    apiClient.get(`/visits/${id}`)
      .then((res) => setVisit(res.data.data));
  }, [id]);

  if (!visit) {
    return <p>Loading...</p>;
  }

  return (
    <div>
      <h1>Visit — {visit.date}</h1>
      <p><strong>Doctor:</strong> {visit.doctor?.name ?? '—'}</p>
      <p><strong>Notes:</strong></p>
      <p>{visit.notes ?? 'No notes recorded.'}</p>
      <Link to={`/patients/${visit.patient_id}/visits`}>← Back to Visit List</Link>
    </div>
  );
}
```

**Verification:** Navigate to `/visits/1` — visit date, doctor, and notes displayed.

---

### FE-M3-04 — Deploy React SPA to Netlify (or Railway)

**Goal:** Deploy the React build to a public URL for the M3 submission.

**Inputs:** `frontend/` directory with all pages complete

**Output:** Public SPA URL (e.g. `https://nutri-ledger.netlify.app`)

**Option A — Netlify (recommended, free):**

1. Build the frontend:
```bash
cd frontend
npm run build
```

2. Create `frontend/netlify.toml`:
```toml
[build]
  command = "npm run build"
  publish = "dist"

[[redirects]]
  from = "/*"
  to = "/index.html"
  status = 200
```

3. Push `frontend/` to its own GitHub repo (or use the same monorepo).

4. On [netlify.app](https://app.netlify.com):
   - New site from GitHub
   - Set build command: `npm run build`
   - Set publish directory: `dist`
   - Add env var: `VITE_API_BASE_URL=https://your-railway-api.up.railway.app/api`

5. Deploy. Copy the public URL.

**Option B — Railway (if preferred):**

Add `frontend/railway.json`:
```json
{
  "build": { "builder": "NIXPACKS" },
  "deploy": {
    "startCommand": "npx serve -s dist -l $PORT"
  }
}
```

Add `frontend/package.json` deploy script:
```json
"scripts": {
  "build": "vite build",
  "preview": "vite preview"
}
```

**Verification:**
- Visit the public URL — React app loads
- Login with valid credentials → redirected to patient list
- CORS: no browser errors from cross-origin API calls

---

## Final Checklist

Before submitting M3:

- [ ] All 10+ Pest tests pass (`php artisan test`)
- [ ] Static analysis clean (`composer run analyse`)
- [ ] `VisitPolicy::view()` calls `isPatient()` (not `ispatient()`) — see BE-M3-04
- [ ] PatientObserver logs to `storage/logs/laravel.log` on create/delete
- [ ] Repositories injected into services (not calling Eloquent directly in services)
- [ ] Both BE and FE deployed to public URLs
- [ ] API URL added to M3 documentation
- [ ] GitHub collaborators `Ajla115` and `amilacausevic` have access
- [ ] M1 deliverables committed to `_docs/_shift/M1_deliverables/`
