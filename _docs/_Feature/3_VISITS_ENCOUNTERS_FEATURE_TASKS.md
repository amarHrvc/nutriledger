# Visits & Encounters Feature - Development Tasks

> **Created:** 2026-02-28
> **Feature Group:** 3 - Visits & Encounters
> **Status:** NOT STARTED
> **Approach:** Feature-by-Feature with TDD
> **Continues from:** Feature Group 2 (Tasks 1–20)

---

## Feature Overview

Build a complete Visits & Encounters system where:
- Each **Visit** belongs to a **Patient** and is conducted by a **Doctor** (User)
- Doctors and Admins can create, view, edit, and delete visits
- Patients can view their own visit history (read-only)
- The Visit Detail page is the hub for nested data: Vitals, Labs, Medications, Recommendations

---

## ALL VISITS FEATURE TASKS

1. ⏳ TASK 21: Visit Database Migration
2. ⏳ TASK 22: Visit Model & Relationships
3. ⏳ TASK 23: Visit Factory
4. ⏳ TASK 24: Visit Policy (Authorization)
5. ⏳ TASK 25: Visit Routes
6. ⏳ TASK 26: Visit List Component (per patient)
7. ⏳ TASK 27: Create Visit Component
8. ⏳ TASK 28: View Visit Detail Component
9. ⏳ TASK 29: Edit Visit Component
10. ⏳ TASK 30: Delete Visit Component

**Checkpoint:** All visit tests green before proceeding to Feature Group 4 (Vital Signs)

---

## COMPLETED TASKS

*(none yet)*

---

## EXPANDED TASKS

---

## TASK 21: Visit Database Migration

### Goal
Create the `visits` table with all required columns, foreign keys, and constraints.

### Key Concepts
- **Foreign Keys**: `patient_id` → `patients.id`, `doctor_id` → `users.id`
- **Nullable fields**: `notes` is optional
- **No soft deletes** on visits — hard delete is acceptable for MVP

### TDD Approach

#### Step 1: Write Test First (RED)

**File:** `tests/Feature/Socioeconomic/` ← No. New directory:
**File:** `tests/Feature/Visits/VisitMigrationTest.php`

```php
<?php

use Illuminate\Support\Facades\Schema;

test('visits table exists', function () {
    expect(Schema::hasTable('visits'))->toBeTrue();
});

test('visits table has required columns', function () {
    expect(Schema::hasColumns('visits', [
        'id',
        'patient_id',
        'doctor_id',
        'date',
        'notes',
        'created_at',
        'updated_at',
    ]))->toBeTrue();
});

test('visits table has correct column types', function () {
    $columns = Schema::getColumns('visits');
    $columnMap = collect($columns)->keyBy('name');

    expect($columnMap['patient_id']['type_name'])->toBe('bigint')
        ->and($columnMap['doctor_id']['type_name'])->toBe('bigint')
        ->and($columnMap['date']['type_name'])->toBe('date')
        ->and($columnMap['notes']['nullable'])->toBeTrue();
});
```

**Run test (should FAIL):**
```bash
php artisan test --filter=VisitMigration
```

#### Step 2: Create Migration (GREEN)

**Command:**
```bash
php artisan make:migration create_visits_table --no-interaction
```

**File:** `database/migrations/YYYY_MM_DD_xxxxxx_create_visits_table.php`

**Specification:**

```php
Schema::create('visits', function (Blueprint $table) {
    $table->id();
    $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
    $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
    $table->date('date');
    $table->text('notes')->nullable();
    $table->timestamps();
});
```

**Run migration:**
```bash
php artisan migrate --no-interaction
```

#### Step 3: Run Tests (should PASS)
```bash
php artisan test --filter=VisitMigration
```

### Why This Way?
- `cascadeOnDelete` on `patient_id` ensures visits are removed if a patient is force-deleted
- `doctor_id` references `users` directly (not `patients`) — any user with role `doktor` is a doctor
- `notes` is nullable — a visit can be logged with date only

---

## TASK 22: Visit Model & Relationships

### Goal
Create the `Visit` Eloquent model with relationships to `Patient` and `User` (doctor).

### Key Concepts
- **BelongsTo**: Visit belongs to one Patient, belongs to one User (doctor)
- **HasMany**: Visit has many VitalSigns, Labs, Recommendations (future tasks)
- **Date casting**: `date` column cast to Carbon for formatting

### TDD Approach

#### Step 1: Write Tests First (RED)

**File:** `tests/Feature/Visits/VisitModelTest.php`

```php
<?php

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;

test('visit can be created with required fields', function () {
    $patient = Patient::factory()->create();
    $doctor = User::factory()->create(['role' => 'doktor']);

    $visit = Visit::create([
        'patient_id' => $patient->id,
        'doctor_id'  => $doctor->id,
        'date'       => '2026-01-15',
    ]);

    expect($visit)->toBeInstanceOf(Visit::class)
        ->and($visit->patient_id)->toBe($patient->id)
        ->and($visit->doctor_id)->toBe($doctor->id);
});

test('visit belongs to a patient', function () {
    $visit = Visit::factory()->create();

    expect($visit->patient)->toBeInstanceOf(Patient::class);
});

test('visit belongs to a doctor', function () {
    $visit = Visit::factory()->create();

    expect($visit->doctor)->toBeInstanceOf(User::class);
});

test('visit date is cast to a date instance', function () {
    $visit = Visit::factory()->create(['date' => '2026-03-10']);

    expect($visit->date)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

test('patient has many visits', function () {
    $patient = Patient::factory()->create();
    Visit::factory()->count(3)->create(['patient_id' => $patient->id]);

    expect($patient->visits)->toHaveCount(3);
});
```

**Run tests (should FAIL):**
```bash
php artisan test --filter=VisitModel
```

#### Step 2: Create Visit Model (GREEN)

**Command:**
```bash
php artisan make:model Visit --no-interaction
```

**File:** `app/Models/Visit.php`

**Specification:**

1. **Traits:** `HasFactory`

2. **`$fillable`:**
   - `patient_id`, `doctor_id`, `date`, `notes`

3. **`casts()` method:**
   - `'date' => 'date'`

4. **Relationships:**

   **`patient()`** → `BelongsTo` → `Patient::class`

   **`doctor()`** → `BelongsTo` → `User::class`, foreign key `doctor_id`
   ```php
   return $this->belongsTo(User::class, 'doctor_id');
   ```

#### Step 3: Add `visits()` to Patient Model

**File:** `app/Models/Patient.php`

Add:
```php
public function visits(): HasMany
{
    return $this->hasMany(Visit::class)->latest('date');
}
```

Import `HasMany` at the top.

#### Step 4: Run Tests (should PASS)
```bash
php artisan test --filter=VisitModel
```

### Why This Way?
- `doctor()` uses explicit `doctor_id` foreign key — otherwise Eloquent would look for `user_id`
- `latest('date')` on the relationship returns visits newest-first by default
- Keeping `patient_id` out of `$fillable` is not needed here — it IS fillable as it's set on creation

---

## TASK 23: Visit Factory

### Goal
Create a factory to generate realistic visit test data with linked patient and doctor.

### Key Concepts
- **Related factories**: Creates Patient and doctor User automatically
- **Date ranges**: Visits should be in the past

### TDD Approach

#### Step 1: Write Tests First (RED)

**File:** `tests/Feature/Visits/VisitFactoryTest.php`

```php
<?php

use App\Models\Visit;
use App\Models\Patient;
use App\Models\User;

test('visit factory creates a valid visit', function () {
    $visit = Visit::factory()->create();

    expect($visit)->toBeInstanceOf(Visit::class)
        ->and($visit->patient_id)->not->toBeNull()
        ->and($visit->doctor_id)->not->toBeNull()
        ->and($visit->date)->not->toBeNull();
});

test('visit factory creates a doctor user', function () {
    $visit = Visit::factory()->create();

    expect($visit->doctor->role)->toBe('doktor');
});

test('visit factory creates a valid patient', function () {
    $visit = Visit::factory()->create();

    expect($visit->patient)->toBeInstanceOf(Patient::class);
});

test('visit factory accepts custom attributes', function () {
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    expect($visit->patient_id)->toBe($patient->id);
});
```

**Run tests (should FAIL):**
```bash
php artisan test --filter=VisitFactory
```

#### Step 2: Create VisitFactory (GREEN)

**Command:**
```bash
php artisan make:factory VisitFactory --model=Visit --no-interaction
```

**File:** `database/factories/VisitFactory.php`

**Specification:**

```php
public function definition(): array
{
    return [
        'patient_id' => Patient::factory(),
        'doctor_id'  => User::factory()->create(['role' => 'doktor'])->id,
        'date'       => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
        'notes'      => fake()->optional()->paragraph(),
    ];
}
```

#### Step 3: Run Tests (should PASS)
```bash
php artisan test --filter=VisitFactory
```

---

## TASK 24: Visit Policy (Authorization)

### Goal
Define authorization rules for who can create, view, edit, and delete visits.

### Authorization Rules
| Action | Admin | Doktor | Pacijent |
|--------|-------|--------|----------|
| viewAny (list) | ✅ | ✅ | ❌ (use patient profile) |
| view | ✅ | ✅ | ✅ own visits only |
| create | ✅ | ✅ | ❌ |
| update | ✅ | ✅ own visits only | ❌ |
| delete | ✅ | ❌ | ❌ |

### TDD Approach

#### Step 1: Write Tests First (RED)

**File:** `tests/Feature/Visits/VisitPolicyTest.php`

```php
<?php

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;

// viewAny
test('admin can view any visits', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    expect($admin->can('viewAny', Visit::class))->toBeTrue();
});

test('doktor can view any visits', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    expect($doktor->can('viewAny', Visit::class))->toBeTrue();
});

test('pacijent cannot view all visits', function () {
    $pacijent = User::factory()->create(['role' => 'pacijent']);
    expect($pacijent->can('viewAny', Visit::class))->toBeFalse();
});

// view
test('admin can view any visit', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $visit = Visit::factory()->create();
    expect($admin->can('view', $visit))->toBeTrue();
});

test('doktor can view any visit', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create();
    expect($doktor->can('view', $visit))->toBeTrue();
});

test('pacijent can view own visits', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);
    expect($user->can('view', $visit))->toBeTrue();
});

test('pacijent cannot view visits of another patient', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $visit = Visit::factory()->create(); // different patient
    expect($user->can('view', $visit))->toBeFalse();
});

// create
test('admin can create visits', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    expect($admin->can('create', Visit::class))->toBeTrue();
});

test('doktor can create visits', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    expect($doktor->can('create', Visit::class))->toBeTrue();
});

test('pacijent cannot create visits', function () {
    $pacijent = User::factory()->create(['role' => 'pacijent']);
    expect($pacijent->can('create', Visit::class))->toBeFalse();
});

// update
test('admin can update any visit', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $visit = Visit::factory()->create();
    expect($admin->can('update', $visit))->toBeTrue();
});

test('doktor can update own visits', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create(['doctor_id' => $doktor->id]);
    expect($doktor->can('update', $visit))->toBeTrue();
});

test('doktor cannot update visits by another doctor', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create(); // different doctor
    expect($doktor->can('update', $visit))->toBeFalse();
});

test('pacijent cannot update visits', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);
    expect($user->can('update', $visit))->toBeFalse();
});

// delete
test('admin can delete visits', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $visit = Visit::factory()->create();
    expect($admin->can('delete', $visit))->toBeTrue();
});

test('doktor cannot delete visits', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $visit = Visit::factory()->create(['doctor_id' => $doktor->id]);
    expect($doktor->can('delete', $visit))->toBeFalse();
});

test('pacijent cannot delete visits', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);
    expect($user->can('delete', $visit))->toBeFalse();
});
```

**Run tests (should FAIL):**
```bash
php artisan test --filter=VisitPolicy
```

#### Step 2: Create Policy (GREEN)

**Command:**
```bash
php artisan make:policy VisitPolicy --model=Visit --no-interaction
```

**File:** `app/Policies/VisitPolicy.php`

**Specification:**

```php
public function viewAny(User $user): bool
{
    return $user->isAdmin() || $user->isDoctor();
}

public function view(User $user, Visit $visit): bool
{
    if ($user->isAdmin() || $user->isDoctor()) {
        return true;
    }

    // Pacijent can view own visits
    return $user->isPatient() && $user->patient?->id === $visit->patient_id;
}

public function create(User $user): bool
{
    return $user->isAdmin() || $user->isDoctor();
}

public function update(User $user, Visit $visit): bool
{
    if ($user->isAdmin()) {
        return true;
    }

    // Doktor can only update visits they created
    return $user->isDoctor() && $user->id === $visit->doctor_id;
}

public function delete(User $user, Visit $visit): bool
{
    return $user->isAdmin();
}
```

**Register policy in** `app/Providers/AppServiceProvider.php`:
```php
Gate::policy(\App\Models\Visit::class, \App\Policies\VisitPolicy::class);
```

#### Step 3: Run Tests (should PASS)
```bash
php artisan test --filter=VisitPolicy
```

---

## TASK 25: Visit Routes

### Goal
Register web routes for visit CRUD operations nested under `/patients/{patient}/visits`.

### Route Map

| Method | URI | Name | Component |
|--------|-----|------|-----------|
| GET | `/patients/{patient}/visits` | `visits.index` | `VisitList` |
| GET | `/patients/{patient}/visits/create` | `visits.create` | `CreateVisit` |
| GET | `/patients/{patient}/visits/{visit}` | `visits.show` | `ViewVisit` |
| GET | `/patients/{patient}/visits/{visit}/edit` | `visits.edit` | `EditVisit` |
| GET | `/patients/{patient}/visits/{visit}/delete` | `visits.delete` | `DeleteVisit` |

### TDD Approach

#### Step 1: Write Tests First (RED)

**File:** `tests/Feature/Visits/VisitRoutesTest.php`

```php
<?php

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;

test('visit list route requires auth', function () {
    $patient = Patient::factory()->create();
    $this->get("/patients/{$patient->id}/visits")->assertRedirect('/login');
});

test('admin can access visit list for a patient', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/visits")
        ->assertOk();
});

test('doktor can access visit list for a patient', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    $this->actingAs($doktor)
        ->get("/patients/{$patient->id}/visits")
        ->assertOk();
});

test('pacijent cannot access visit list', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get("/patients/{$patient->id}/visits")
        ->assertForbidden();
});

test('create visit route requires admin or doktor', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create();

    $this->actingAs($user)
        ->get("/patients/{$patient->id}/visits/create")
        ->assertForbidden();
});

test('admin can access create visit route', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/visits/create")
        ->assertOk();
});

test('admin can access view visit route', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/visits/{$visit->id}")
        ->assertOk();
});

test('pacijent can view own visit detail', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    $this->actingAs($user)
        ->get("/patients/{$patient->id}/visits/{$visit->id}")
        ->assertOk();
});

test('pacijent cannot view another patients visit detail', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $otherPatient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $otherPatient->id]);

    $this->actingAs($user)
        ->get("/patients/{$otherPatient->id}/visits/{$visit->id}")
        ->assertForbidden();
});

test('edit visit route returns 404 for non-existent visit', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/visits/9999/edit")
        ->assertNotFound();
});
```

**Run tests (should FAIL):**
```bash
php artisan test --filter=VisitRoutes
```

#### Step 2: Add Routes (GREEN)

**File:** `routes/web.php`

Add inside the `auth` middleware group:

```php
// Visit routes - nested under patients
Route::prefix('/patients/{patient}/visits')->name('visits.')->group(function () {
    Route::get('/', \App\Livewire\Patient\Visit\VisitList::class)
        ->can('viewAny', \App\Models\Visit::class)
        ->name('index');

    Route::get('/create', \App\Livewire\Patient\Visit\CreateVisit::class)
        ->can('create', \App\Models\Visit::class)
        ->name('create');

    Route::get('/{visit}', \App\Livewire\Patient\Visit\ViewVisit::class)
        ->name('show');

    Route::get('/{visit}/edit', \App\Livewire\Patient\Visit\EditVisit::class)
        ->name('edit');

    Route::get('/{visit}/delete', \App\Livewire\Patient\Visit\DeleteVisit::class)
        ->name('delete');
});
```

#### Step 3: Run Tests (should PASS)
```bash
php artisan test --filter=VisitRoutes
```

---

## TASK 26: Visit List Component

### Goal
Livewire full-page component that displays all visits for a patient, sorted by date descending. Accessible to admin and doktor only.

### Specification
- URL: `/patients/{patient}/visits`
- Shows: visit date, doctor name, notes preview, action links (view, edit)
- Empty state: message + "Add Visit" button
- Authorization: `$this->authorize('viewAny', Visit::class)` in `mount()`

### TDD Approach

#### Step 1: Write Tests First (RED)

**File:** `tests/Feature/Visits/VisitListComponentTest.php`

```php
<?php

use App\Livewire\Patient\Visit\VisitList;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Livewire\Livewire;

test('admin can access visit list page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/visits")
        ->assertOk()
        ->assertSeeLivewire(VisitList::class);
});

test('doktor can access visit list page', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    $this->actingAs($doktor)
        ->get("/patients/{$patient->id}/visits")
        ->assertOk();
});

test('pacijent is forbidden from visit list page', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get("/patients/{$patient->id}/visits")
        ->assertForbidden();
});

test('component renders visits for the patient', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'date' => '2026-01-10']);

    Livewire::actingAs($admin)
        ->test(VisitList::class, ['patient' => $patient])
        ->assertSee('2026-01-10');
});

test('component shows empty state when no visits exist', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(VisitList::class, ['patient' => $patient])
        ->assertSee('No visits recorded');
});

test('component shows add visit button for admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(VisitList::class, ['patient' => $patient])
        ->assertSee('Add Visit');
});

test('component does not show visits of other patients', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $otherPatient = Patient::factory()->create();
    Visit::factory()->create(['patient_id' => $otherPatient->id, 'notes' => 'other-visit-notes-xyz']);

    Livewire::actingAs($admin)
        ->test(VisitList::class, ['patient' => $patient])
        ->assertDontSee('other-visit-notes-xyz');
});
```

**Run tests (should FAIL):**
```bash
php artisan test --filter=VisitListComponent
```

#### Step 2: Create Component (GREEN)

**Command:**
```bash
php artisan make:livewire Patient/Visit/VisitList --no-interaction
```

**Class:** `app/Livewire/Patient/Visit/VisitList.php`

**Specification:**
- Mount receives `Patient $patient` via route model binding
- Call `$this->authorize('viewAny', Visit::class)` in `mount()`
- Public property `$patient`
- `render()` passes `$visits = $this->patient->visits()->with('doctor')->get()` to view
- Layout: `layouts.app`

**View:** `resources/views/livewire/patient/visit/visit-list.blade.php`

**Specification:**
- Page heading: "Visits — {patient full name}"
- Table columns: Date, Doctor, Notes (truncated), Actions (View, Edit)
- Empty state: "No visits recorded yet." with Add Visit button
- "Add Visit" button links to `route('visits.create', $patient)` — visible to admin/doktor only via `@can`
- "Back to Patient" link: `route('patients.show', $patient)`

#### Step 3: Run Tests (should PASS)
```bash
php artisan test --filter=VisitListComponent
```

---

## TASK 27: Create Visit Component

### Goal
Livewire full-page component with a form to log a new visit for a patient.

### Specification
- URL: `/patients/{patient}/visits/create`
- Fields: `date` (required), `notes` (optional), `doctor_id` (pre-filled with current user if doktor)
- On success: redirect to `visits.show` for the new visit
- Authorization: `create` on `Visit::class`
- Form Request: `StoreVisitRequest`

### TDD Approach

#### Step 1: Write Tests First (RED)

**File:** `tests/Feature/Visits/CreateVisitComponentTest.php`

```php
<?php

use App\Livewire\Patient\Visit\CreateVisit;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Livewire\Livewire;

test('admin can access create visit page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/visits/create")
        ->assertOk()
        ->assertSeeLivewire(CreateVisit::class);
});

test('pacijent cannot access create visit page', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get("/patients/{$patient->id}/visits/create")
        ->assertForbidden();
});

test('doktor can submit a new visit', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($doktor)
        ->test(CreateVisit::class, ['patient' => $patient])
        ->set('form.date', '2026-03-01')
        ->set('form.notes', 'Initial consultation.')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('visits.show', [$patient, Visit::latest()->first()]));

    expect(Visit::where('patient_id', $patient->id)->exists())->toBeTrue();
});

test('date field is required', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(CreateVisit::class, ['patient' => $patient])
        ->set('form.date', '')
        ->call('save')
        ->assertHasErrors(['form.date' => 'required']);
});

test('date field must be a valid date', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($admin)
        ->test(CreateVisit::class, ['patient' => $patient])
        ->set('form.date', 'not-a-date')
        ->call('save')
        ->assertHasErrors(['form.date' => 'date']);
});

test('doctor id is set to current user when doktor submits', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();

    Livewire::actingAs($doktor)
        ->test(CreateVisit::class, ['patient' => $patient])
        ->set('form.date', '2026-03-01')
        ->call('save')
        ->assertHasNoErrors();

    $visit = Visit::where('patient_id', $patient->id)->latest()->first();
    expect($visit->doctor_id)->toBe($doktor->id);
});
```

**Run tests (should FAIL):**
```bash
php artisan test --filter=CreateVisitComponent
```

#### Step 2: Create Form Request (GREEN)

**Command:**
```bash
php artisan make:request StoreVisitRequest --no-interaction
```

**File:** `app/Http/Requests/StoreVisitRequest.php`

**Rules:**
```php
public function rules(): array
{
    return [
        'date'  => ['required', 'date'],
        'notes' => ['nullable', 'string', 'max:5000'],
    ];
}
```

#### Step 3: Create Component

**Command:**
```bash
php artisan make:livewire Patient/Visit/CreateVisit --no-interaction
```

**Specification:**
- `mount(Patient $patient)`: set `$this->patient`, call `$this->authorize('create', Visit::class)`
- Uses a `Form` object or inline properties — follow existing `ManageSocioeconomic` pattern
- `save()`: validate with `StoreVisitRequest` rules, create visit with `doctor_id = auth()->id()`, redirect to `visits.show`

**View:** Clean form with `flux:input` for date, `flux:textarea` for notes, submit button.

#### Step 4: Run Tests (should PASS)
```bash
php artisan test --filter=CreateVisitComponent
```

---

## TASK 28: View Visit Detail Component

### Goal
Livewire full-page component showing full visit information. This is the hub page — it will embed nested sections for Vitals, Labs, Medications, and Recommendations in future tasks.

### Specification
- URL: `/patients/{patient}/visits/{visit}`
- Shows: date, doctor name, notes, patient name
- Placeholder sections (empty callouts) for: Vital Signs, Lab Results, Medications, Recommendations
- Authorization: `view` on `$visit` in `mount()`

### TDD Approach

#### Step 1: Write Tests First (RED)

**File:** `tests/Feature/Visits/ViewVisitComponentTest.php`

```php
<?php

use App\Livewire\Patient\Visit\ViewVisit;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Livewire\Livewire;

test('admin can access view visit page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/visits/{$visit->id}")
        ->assertOk()
        ->assertSeeLivewire(ViewVisit::class);
});

test('pacijent can view own visit detail', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    $this->actingAs($user)
        ->get("/patients/{$patient->id}/visits/{$visit->id}")
        ->assertOk();
});

test('pacijent cannot view another patients visit', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $otherPatient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $otherPatient->id]);

    $this->actingAs($user)
        ->get("/patients/{$otherPatient->id}/visits/{$visit->id}")
        ->assertForbidden();
});

test('component renders visit date', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'date'       => '2026-01-20',
    ]);

    Livewire::actingAs($admin)
        ->test(ViewVisit::class, ['patient' => $patient, 'visit' => $visit])
        ->assertSee('2026-01-20');
});

test('component renders doctor name', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $doctor = User::factory()->create(['role' => 'doktor', 'first_name' => 'Ana', 'last_name' => 'Kovač']);
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id]);

    Livewire::actingAs($admin)
        ->test(ViewVisit::class, ['patient' => $patient, 'visit' => $visit])
        ->assertSee('Ana Kovač');
});

test('component renders notes when present', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'notes'      => 'Patient reports improvement.',
    ]);

    Livewire::actingAs($admin)
        ->test(ViewVisit::class, ['patient' => $patient, 'visit' => $visit])
        ->assertSee('Patient reports improvement.');
});

test('component shows edit button for admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(ViewVisit::class, ['patient' => $patient, 'visit' => $visit])
        ->assertSee('Edit');
});

test('component shows back to patient link', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(ViewVisit::class, ['patient' => $patient, 'visit' => $visit])
        ->assertSee('Back to Patient');
});
```

**Run tests (should FAIL):**
```bash
php artisan test --filter=ViewVisitComponent
```

#### Step 2: Create Component (GREEN)

**Command:**
```bash
php artisan make:livewire Patient/Visit/ViewVisit --no-interaction
```

**Specification:**
- `mount(Patient $patient, Visit $visit)`: set both properties, call `$this->authorize('view', $visit)`
- `render()`: eager-load `$visit->load('doctor')`, pass to view

**View:** `resources/views/livewire/patient/visit/view-visit.blade.php`
- Heading: "Visit — {date formatted}"
- Fields: Date, Doctor, Notes (or "No notes recorded.")
- Action buttons: Edit (`@can('update', $visit)`), Delete (`@can('delete', $visit)`)
- Placeholder `<flux:callout>` sections for: Vital Signs, Lab Results, Medications, Recommendations — each with "Not yet implemented" or "Coming soon" text
- "Back to Patient" link: `route('patients.show', $patient)`

#### Step 3: Run Tests (should PASS)
```bash
php artisan test --filter=ViewVisitComponent
```

---

## TASK 29: Edit Visit Component

### Goal
Livewire full-page component with a pre-filled form to update an existing visit.

### Specification
- URL: `/patients/{patient}/visits/{visit}/edit`
- Pre-fills `date` and `notes` from existing visit
- Authorization: `update` on `$visit` — doktors can only edit their own visits
- Form Request: `UpdateVisitRequest`
- On success: redirect to `visits.show`

### TDD Approach

#### Step 1: Write Tests First (RED)

**File:** `tests/Feature/Visits/EditVisitComponentTest.php`

```php
<?php

use App\Livewire\Patient\Visit\EditVisit;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Livewire\Livewire;

test('admin can access edit visit page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/visits/{$visit->id}/edit")
        ->assertOk()
        ->assertSeeLivewire(EditVisit::class);
});

test('doktor can edit own visits', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doktor->id]);

    $this->actingAs($doktor)
        ->get("/patients/{$patient->id}/visits/{$visit->id}/edit")
        ->assertOk();
});

test('doktor cannot edit visits by another doctor', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id]); // different doctor

    $this->actingAs($doktor)
        ->get("/patients/{$patient->id}/visits/{$visit->id}/edit")
        ->assertForbidden();
});

test('pacijent cannot access edit visit page', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    $this->actingAs($user)
        ->get("/patients/{$patient->id}/visits/{$visit->id}/edit")
        ->assertForbidden();
});

test('form is pre-filled with existing visit data', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create([
        'patient_id' => $patient->id,
        'date'       => '2026-01-15',
        'notes'      => 'Follow-up notes.',
    ]);

    Livewire::actingAs($admin)
        ->test(EditVisit::class, ['patient' => $patient, 'visit' => $visit])
        ->assertSet('form.date', '2026-01-15')
        ->assertSet('form.notes', 'Follow-up notes.');
});

test('admin can update a visit', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'date' => '2026-01-10']);

    Livewire::actingAs($admin)
        ->test(EditVisit::class, ['patient' => $patient, 'visit' => $visit])
        ->set('form.date', '2026-02-20')
        ->set('form.notes', 'Updated notes.')
        ->call('save')
        ->assertHasNoErrors();

    expect($visit->fresh()->date->format('Y-m-d'))->toBe('2026-02-20')
        ->and($visit->fresh()->notes)->toBe('Updated notes.');
});

test('date field is required on update', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(EditVisit::class, ['patient' => $patient, 'visit' => $visit])
        ->set('form.date', '')
        ->call('save')
        ->assertHasErrors(['form.date' => 'required']);
});
```

**Run tests (should FAIL):**
```bash
php artisan test --filter=EditVisitComponent
```

#### Step 2: Create Form Request (GREEN)

**Command:**
```bash
php artisan make:request UpdateVisitRequest --no-interaction
```

**Rules:** Same as `StoreVisitRequest`.

#### Step 3: Create Component

**Command:**
```bash
php artisan make:livewire Patient/Visit/EditVisit --no-interaction
```

**Specification:**
- `mount(Patient $patient, Visit $visit)`: authorize `update` on `$visit`, fill form properties from `$visit`
- `save()`: validate, `$this->visit->update(...)`, redirect to `visits.show`

#### Step 4: Run Tests (should PASS)
```bash
php artisan test --filter=EditVisitComponent
```

---

## TASK 30: Delete Visit Component

### Goal
Livewire full-page confirmation component for deleting a visit. Admin only.

### Specification
- URL: `/patients/{patient}/visits/{visit}/delete`
- Shows visit date and patient name for confirmation
- On confirm: delete the visit, redirect to `visits.index` for the patient
- Authorization: `delete` on `$visit` — admin only

### TDD Approach

#### Step 1: Write Tests First (RED)

**File:** `tests/Feature/Visits/DeleteVisitComponentTest.php`

```php
<?php

use App\Livewire\Patient\Visit\DeleteVisit;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Livewire\Livewire;

test('admin can access delete visit page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/visits/{$visit->id}/delete")
        ->assertOk()
        ->assertSeeLivewire(DeleteVisit::class);
});

test('doktor cannot access delete visit page', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doktor->id]);

    $this->actingAs($doktor)
        ->get("/patients/{$patient->id}/visits/{$visit->id}/delete")
        ->assertForbidden();
});

test('pacijent cannot access delete visit page', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    $this->actingAs($user)
        ->get("/patients/{$patient->id}/visits/{$visit->id}/delete")
        ->assertForbidden();
});

test('admin can delete a visit', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(DeleteVisit::class, ['patient' => $patient, 'visit' => $visit])
        ->call('delete')
        ->assertRedirect(route('visits.index', $patient));

    expect(Visit::find($visit->id))->toBeNull();
});

test('deleting removes the record from the database', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(DeleteVisit::class, ['patient' => $patient, 'visit' => $visit])
        ->call('delete');

    expect(Visit::where('id', $visit->id)->exists())->toBeFalse();
});

test('component renders confirmation heading', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $visit = Visit::factory()->create(['patient_id' => $patient->id]);

    Livewire::actingAs($admin)
        ->test(DeleteVisit::class, ['patient' => $patient, 'visit' => $visit])
        ->assertSee('Delete Visit');
});

test('non-existent visit returns 404', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();

    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/visits/9999/delete")
        ->assertNotFound();
});
```

**Run tests (should FAIL):**
```bash
php artisan test --filter=DeleteVisitComponent
```

#### Step 2: Create Component (GREEN)

**Command:**
```bash
php artisan make:livewire Patient/Visit/DeleteVisit --no-interaction
```

**Specification:**
- `mount(Patient $patient, Visit $visit)`: authorize `delete` on `$visit`
- `delete()`: call `$this->visit->delete()`, redirect to `visits.index` for patient with flash message

**View:** Confirmation page with visit date, "Delete Visit" heading, "Confirm Delete" button, "Cancel" link back to `visits.show`.

#### Step 3: Run Tests (should PASS)
```bash
php artisan test --filter=DeleteVisitComponent
```

---

## Checkpoint: Run All Visit Tests

```bash
php artisan test tests/Feature/Visits/
```

All tests green → Feature Group 3 complete → proceed to **Feature Group 4: Vital Signs**.
