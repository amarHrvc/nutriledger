# 📋 Patient Management Feature - Development Tasks

> **Created:** 2025-11-07  
> **Updated:** 2025-11-08T14:54:00.000Z  
> **Feature Group:** 2 - Patient Registration & Management  
> **Status:** 🚀 IN PROGRESS  
> **Approach:** Feature-by-Feature with TDD (Patient first, Socioeconomic later)

---

## 🎯 Feature Overview

Build a complete Patient Management system where:
- Each **Patient** is linked to a **User** account (1-to-1 relationship)
- Doctors and Admins can register and manage patient profiles
- Complete Patient feature fully before adding Socioeconomic data

---

## 📋 ALL PATIENT FEATURE TASKS (Phase 1)

1. ⏳ Patient Database Migration
2. ⏳ Patient Model & Relationships
3. ⏳ Patient Factory
4. ⏳ Patient Policy (Authorization)
5. ⏳ Patient Routes
6. ⏳ List Patients Component
7. ⏳ Create Patient Component
8. ⏳ View Patient Profile Component
9. ⏳ Edit Patient Component
10. ⏳ Delete Patient Component
11. ⏳ Navigation Integration

**🎉 Phase 1 Checkpoint:** Patient feature 100% complete and tested

---

## ✅ COMPLETED TASKS

- ✅ TASK 1: Patient Database Migration (created, pending run)
- ✅ Patient Model & Relationships
- ✅ Patient Factory
- ✅ Patient Policy (Authorization)
- ✅  Patient Routes

---

## 📚 EXPANDED TASKS (Ready to Implement)

Below are the detailed task expansions following TDD approach with example tests and specifications.

---

## 📝 TASK 2: Patient Model & Relationships

### 🎯 Goal
Create Eloquent models with relationships and computed properties for Patient and PatientSocioeconomic data.

### 📚 Key Concepts
- **Eloquent Models**: ORM representation of database tables
- **Relationships**: `BelongsTo`, `HasOne` (1-to-1 relationships)
- **Mass Assignment**: `$fillable` array for security
- **Type Casting**: `casts()` method for automatic type conversion
- **Accessors**: Computed properties using `Attribute::make()`
- **Soft Deletes**: `SoftDeletes` trait for trash/restore

### 📝 TDD Approach

#### Step 1: Write Tests First (RED)

**File:** `tests/Feature/PatientModelTest.php`

Create this file with example tests:

```php
<?php

use App\Models\Patient;
use App\Models\User;

// === Basic Model Tests ===

test('patient can be created with required fields', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    
    $patient = Patient::create([
        'user_id' => $user->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'date_of_birth' => '1990-01-15',
        'gender' => 'M',
    ]);
    
    expect($patient)->toBeInstanceOf(Patient::class)
        ->and($patient->first_name)->toBe('John')
        ->and($patient->last_name)->toBe('Doe');
});

// TODO: Write test for patient belongs to user relationship

// === Accessor Tests ===

test('patient full_name accessor returns combined name', function () {
    $patient = Patient::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
    ]);
    
    expect($patient->full_name)->toBe('Jane Smith');
});

// TODO: Write test for age accessor calculates correctly from date_of_birth

// === Soft Delete Tests ===

test('patient can be soft deleted', function () {
    $patient = Patient::factory()->create();
    
    $patient->delete();
    
    expect($patient->trashed())->toBeTrue();
});

// TODO: Write test for patient can be restored after soft delete
```

**Run tests (should FAIL - models don't exist yet):**
```bash
php artisan test --filter=PatientModel
```

#### Step 2: Create Patient Model (GREEN)

**Command:**
```bash
php artisan make:model Patient
```

**File:** `app/Models/Patient.php`

**Specification - What your Patient model needs:**

1. **Namespace & Imports:**
   - Use namespace `App\Models`
   - Import: `HasFactory`, `Model`, `SoftDeletes`, `BelongsTo`, `Attribute`

2. **Traits:**
   - Use `HasFactory` (for testing with factories)
   - Use `SoftDeletes` (for trash/restore functionality)

3. **Properties:**
   - `$fillable` array with ALL fields from migration EXCEPT `id`, timestamps
   - Fields: `user_id`, `first_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `address`, `city`, `postal_code`, `emergency_contact_name`, `emergency_contact_phone`, `blood_type`, `allergies`, `medical_notes`

4. **Casting Method:**
   ```php
   protected function casts(): array
   ```
   - Cast `date_of_birth` to `'date'` type (converts string to Carbon object)

5. **Relationship:**
   
   **Method: `user()`**
   - Return type: `BelongsTo`
   - Purpose: Patient belongs to one User
   - Logic: `return $this->belongsTo(User::class);`

6. **Accessors (Computed Properties):**
   
   **Method: `fullName()`**
   - Return type: `Attribute`
   - Purpose: Get full name as "FirstName LastName"
   - Logic: Use `Attribute::make(get: fn() => "{$this->first_name} {$this->last_name}")`
   
   **Method: `age()`**
   - Return type: `Attribute`
   - Purpose: Calculate age in years from date_of_birth
   - Logic: Use `Attribute::make(get: fn() => $this->date_of_birth?->age ?? 0)`
   - Note: The `?->` safely handles null dates, `age` is a Carbon property

---

#### Step 3: Update User Model

**File:** `app/Models/User.php`

**Add this relationship method:**

**Method: `patient()`**
- Return type: `HasOne`
- Purpose: User has one optional Patient profile
- Logic: `return $this->hasOne(Patient::class);`
- Note: Not all users are patients (some are admin/doktor)

**Add import at top:** `use Illuminate\Database\Eloquent\Relations\HasOne;`

---

#### Step 4: Run Tests (should PASS now)

```bash
php artisan test --filter=PatientModel
```

### 🧠 Why This Way?
- **Relationships**: Enable navigation like `$user->patient->full_name`
- **Fillable**: Protects against mass assignment vulnerabilities
- **Accessors**: Cleaner code - use `$patient->age` instead of calculating everywhere
- **Soft Deletes**: Can restore accidentally deleted patients

### ✅ Verification
```bash
# Run tests
php artisan test --filter=PatientModel

# Test in Tinker
php artisan tinker
$patient = App\Models\Patient::factory()->create();
$patient->full_name; // Should show "FirstName LastName"
$patient->age;       // Should show age in years
$patient->user;      // Should show related User
```

---

## 📝 TASK 3: Patient Factory

### 🎯 Goal
Create factory class to generate fake patient data for testing and seeding.

### 📚 Key Concepts
- **Factories**: Generate test data with realistic fake values
- **Faker**: PHP library for generating fake data
- **Relationships in Factories**: Using `User::factory()` to create related records
- **Optional Values**: Using `fake()->optional()` for nullable fields

### 📝 TDD Approach

#### Step 1: Write Tests First (RED)

**File:** `tests/Feature/PatientFactoryTest.php`

```php
<?php

use App\Models\Patient;
use App\Models\User;

// === Factory Creation Tests ===

test('patient factory creates patient with valid data', function () {
    $patient = Patient::factory()->create();
    
    expect($patient)->toBeInstanceOf(Patient::class)
        ->and($patient->user_id)->not->toBeNull()
        ->and($patient->first_name)->not->toBeNull()
        ->and($patient->last_name)->not->toBeNull()
        ->and($patient->date_of_birth)->not->toBeNull();
});

// TODO: Write test for patient factory creates user with pacijent role
// TODO: Write test for patient factory respects provided attributes

// === Gender Enum Tests ===

test('patient factory uses correct gender values', function () {
    $patient = Patient::factory()->create();
    
    expect($patient->gender)->toBeIn(['M', 'F']);
});

// TODO: Write test for blood type uses correct enum values
```

**Run tests (should FAIL):**
```bash
php artisan test --filter=PatientFactory
```

#### Step 2: Create PatientFactory (GREEN)

**Command:**
```bash
php artisan make:factory PatientFactory
```

**File:** `database/factories/PatientFactory.php`

**Specification:**

1. **Set Model:**
   ```php
   protected $model = Patient::class;
   ```

2. **Definition Method** - Return array with these keys:

   - `user_id`: Create a User with role 'pacijent' → `User::factory()->create(['role' => 'pacijent'])->id`
   - `first_name`: Use `fake()->firstName()`
   - `last_name`: Use `fake()->lastName()`
   - `date_of_birth`: Random date between 80 years ago and 18 years ago → `fake()->dateTimeBetween('-80 years', '-18 years')`
   - `gender`: Random from array → `fake()->randomElement(['M', 'F'])` (matches migration enum)
   - `phone`: Use `fake()->phoneNumber()`
   - `address`: Optional street address → `fake()->optional()->streetAddress()`
   - `city`: Optional city → `fake()->optional()->city()`
   - `postal_code`: Optional postcode → `fake()->optional()->postcode()`
   - `emergency_contact_name`: Use `fake()->name()`
   - `emergency_contact_phone`: Use `fake()->phoneNumber()`
   - `blood_type`: Optional, random from array → `fake()->optional()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])`
   - `allergies`: Optional text → `fake()->optional()->sentence()`
   - `medical_notes`: Optional paragraph → `fake()->optional()->paragraph()`

---

#### Step 3: Run Tests (should PASS)

```bash
php artisan test --filter=PatientFactory
```

### 🧠 Why This Way?
- **Realistic data**: Fake data looks real for demos/testing
- **Relationships handled**: Factory creates related User automatically
- **Optional fields**: Some data nullable, matches real-world scenarios (not everyone has address)
- **Reusable**: Use in tests, seeders, and development

### ✅ Verification
```bash
# Run tests
php artisan test --filter=PatientFactory

# Test in Tinker
php artisan tinker
$patient = App\Models\Patient::factory()->create();
$patient; // See generated data

# Create 5 patients
App\Models\Patient::factory()->count(5)->create();

# Check user relationship
$patient->user; // Should show related User with role 'pacijent'
```

---

## 🚧 UPCOMING TASKS (Not Yet Expanded)

- ⏳ TASK 4: Patient Policy (Authorization)
- ⏳ TASK 5: Patient Routes
- ⏳ TASK 6: List Patients Component
- ⏳ TASK 7: Create Patient Component
- ⏳ TASK 8: View Patient Profile Component
- ⏳ TASK 9: Edit Patient Component
- ⏳ TASK 10: Delete Patient Component
- ⏳ TASK 11: Navigation Integration

---

*Complete Tasks 1-3 first, then request expansion of Tasks 4-5!*

### 🎯 Goal
Create authorization rules to control who can view, create, edit, and delete patient records.

### 📚 Key Concepts
- **Laravel Policies**: Centralized authorization logic per model
- **Policy Methods**: `viewAny()`, `view()`, `create()`, `update()`, `delete()`
- **Role-Based Access**: Different permissions for Admin, Doktor, Pacijent
- **Self-Access**: Patients can view/edit their own profile only

### 📝 TDD Approach

#### Step 1: Write Test First (RED)
**File:** `tests/Feature/PatientPolicyTest.php`

```php
<?php

use App\Models\Patient;
use App\Models\User;

test('admin can view any patients', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    expect($admin->can('viewAny', Patient::class))->toBeTrue();
});

test('doktor can view any patients', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    
    expect($doktor->can('viewAny', Patient::class))->toBeTrue();
});

test('pacijent cannot view all patients', function () {
    $pacijent = User::factory()->create(['role' => 'pacijent']);
    
    expect($pacijent->can('viewAny', Patient::class))->toBeFalse();
});

test('pacijent can view own patient profile', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    
    expect($user->can('view', $patient))->toBeTrue();
});

test('pacijent cannot view other patient profiles', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $otherPatient = Patient::factory()->create();
    
    expect($user->can('view', $otherPatient))->toBeFalse();
});

test('admin and doktor can create patients', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $doktor = User::factory()->create(['role' => 'doktor']);
    
    expect($admin->can('create', Patient::class))->toBeTrue();
    expect($doktor->can('create', Patient::class))->toBeTrue();
});

test('admin and doktor can update any patient', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    
    expect($admin->can('update', $patient))->toBeTrue();
});

test('pacijent can update own profile', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    
    expect($user->can('update', $patient))->toBeTrue();
});

test('admin and doktor can delete patients', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    
    expect($admin->can('delete', $patient))->toBeTrue();
});

test('pacijent cannot delete patients', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    
    expect($user->can('delete', $patient))->toBeFalse();
});
```

**Run test (should FAIL):**
```bash
php artisan test --filter=PatientPolicy
```

#### Step 2: Create Policy (GREEN)
**Command:**
```bash
php artisan make:policy PatientPolicy --model=Patient
```

**File:** `app/Policies/PatientPolicy.php`

```php
<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    /**
     * Admin and Doktor can view patient list
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }

    /**
     * Admin/Doktor can view any patient
     * Pacijent can view only their own profile
     */
    public function view(User $user, Patient $patient): bool
    {
        return $user->isAdmin()
            || $user->isDoctor()
            || $user->id === $patient->user_id;
    }

    /**
     * Only Admin and Doktor can create patients
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }

    /**
     * Admin/Doktor can update any patient
     * Pacijent can update only their own profile
     */
    public function update(User $user, Patient $patient): bool
    {
        return $user->isAdmin()
            || $user->isDoctor()
            || $user->id === $patient->user_id;
    }

    /**
     * Only Admin and Doktor can delete patients
     */
    public function delete(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }
}
```

**Run test again (should PASS):**
```bash
php artisan test --filter=PatientPolicy
```

### 🧠 Why This Way?
- **Centralized logic**: All authorization rules in one place
- **Reusable**: Used in routes, controllers, Livewire components, and Blade views
- **Testable**: Clear test cases for each permission scenario
- **Follows User Policy pattern**: Same structure as existing UserPolicy

### ✅ Verification
```bash
# Run policy tests
php artisan test --filter=PatientPolicy

# Test in Tinker
php artisan tinker
$admin = User::where('role', 'admin')->first();
$patient = Patient::first();
$admin->can('view', $patient); // should return true
```

---

## 📝 TASK 5: Patient Routes

### 🎯 Goal
Register web routes for all patient CRUD operations with proper middleware protection.

### 📚 Key Concepts
- **Route Model Binding**: Automatic Patient model injection from URL parameter
- **Route Groups**: Shared middleware for related routes
- **Named Routes**: Easy URL generation with `route('patients.index')`
- **Livewire Route Registration**: Point routes to Livewire components

### 📝 TDD Approach

#### Step 1: Write Test First (RED)
**File:** `tests/Feature/PatientRoutesTest.php`

```php
<?php

use App\Models\Patient;
use App\Models\User;

test('patients index route exists and requires auth', function () {
    $response = $this->get('/patients');
    $response->assertRedirect('/login');
});

test('admin can access patients list', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $this->actingAs($admin)
        ->get('/patients')
        ->assertOk();
});

test('doktor can access patients list', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    
    $this->actingAs($doktor)
        ->get('/patients')
        ->assertOk();
});

test('pacijent cannot access patients list', function () {
    $pacijent = User::factory()->create(['role' => 'pacijent']);
    
    $this->actingAs($pacijent)
        ->get('/patients')
        ->assertForbidden();
});

test('create patient route exists', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $this->actingAs($admin)
        ->get('/patients/create')
        ->assertOk();
});

test('view patient route exists', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    
    $this->actingAs($admin)
        ->get("/patients/{$patient->id}")
        ->assertOk();
});

test('edit patient route exists', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    
    $this->actingAs($admin)
        ->get("/patients/{$patient->id}/edit")
        ->assertOk();
});

test('pacijent can view own profile', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    
    $this->actingAs($user)
        ->get("/patients/{$patient->id}")
        ->assertOk();
});
```

**Run test (should FAIL):**
```bash
php artisan test --filter=PatientRoutes
```

#### Step 2: Add Routes (GREEN)
**File:** `routes/web.php`

**Add inside the authenticated middleware group:**

```php
// Patient Management Routes
Route::middleware(['auth'])->group(function () {
    
    // List all patients (Admin & Doktor only)
    Route::get('/patients', App\Livewire\Patient\PatientList::class)
        ->can('viewAny', App\Models\Patient::class)
        ->name('patients.index');

    // Create new patient (Admin & Doktor only)
    Route::get('/patients/create', App\Livewire\Patient\CreatePatient::class)
        ->can('create', App\Models\Patient::class)
        ->name('patients.create');

    // View patient profile (authorized via component)
    Route::get('/patients/{patient}', App\Livewire\Patient\ViewPatient::class)
        ->name('patients.show');

    // Edit patient profile (authorized via component)
    Route::get('/patients/{patient}/edit', App\Livewire\Patient\EditPatient::class)
        ->name('patients.edit');
});
```

**Note:** Since components don't exist yet, tests will fail. We'll create placeholder components next.

#### Step 3: Create Placeholder Components

Create empty components so routes resolve:

```bash
php artisan make:livewire Patient/PatientList
php artisan make:livewire Patient/CreatePatient
php artisan make:livewire Patient/ViewPatient
php artisan make:livewire Patient/EditPatient
```

Each component should have basic structure:

```php
<?php

namespace App\Livewire\Patient;

use Livewire\Component;

class PatientList extends Component
{
    public function render()
    {
        return view('livewire.patient.patient-list');
    }
}
```

And basic Blade view:

```blade
<div>
    <h1>Patient List (TODO)</h1>
</div>
```

**Run test again (should PASS):**
```bash
php artisan test --filter=PatientRoutes
```

### 🧠 Why This Way?
- **Test routes before components**: Ensures routing layer works independently
- **Policy-based protection**: Using `->can()` on routes for cleaner code
- **Named routes**: Makes URL generation easier in components and views
- **Placeholder components**: Allow route tests to pass while we build real components later

### ✅ Verification
```bash
# Run route tests
php artisan test --filter=PatientRoutes

# Check routes registered
php artisan route:list --path=patients

# Manual browser test (as admin)
# Visit: http://your-app.test/patients
```

---

## 📝 TASK 6: List Patients Component

### 🎯 Goal
Build a Livewire component that displays a paginated, searchable table of all patients with actions for view/edit/delete.

### 📚 Key Concepts
- **Livewire Component**: Full-page component for listing patients
- **Pagination**: Built-in Livewire pagination with `WithPagination` trait
- **Search**: Real-time search filtering without page reload
- **Authorization**: Show/hide actions based on user permissions
- **Table Layout**: Responsive table with patient data

### 📝 TDD Approach

#### Step 1: Write Tests First (RED)

**File:** `tests/Feature/PatientListComponentTest.php`

```php
<?php

use App\Models\Patient;
use App\Models\User;
use Livewire\Livewire;

test('admin can see patient list component', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $this->actingAs($admin)
        ->get('/patients')
        ->assertOk()
        ->assertSeeLivewire('patient.patient-list');
});

test('doctor can see patient list component', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    
    $this->actingAs($doktor)
        ->get('/patients')
        ->assertOk()
        ->assertSeeLivewire('patient.patient-list');
});

test('patient list displays all patients', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patients = Patient::factory()->count(3)->create();
    
    Livewire::actingAs($admin)
        ->test(\App\Livewire\Patient\PatientList::class)
        ->assertSee($patients[0]->first_name)
        ->assertSee($patients[1]->first_name)
        ->assertSee($patients[2]->first_name);
});

test('patient list can search by name', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $john = Patient::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);
    $jane = Patient::factory()->create(['first_name' => 'Jane', 'last_name' => 'Smith']);
    
    Livewire::actingAs($admin)
        ->test(\App\Livewire\Patient\PatientList::class)
        ->set('search', 'John')
        ->assertSee('John')
        ->assertDontSee('Jane');
});

test('patient list pagination works', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Patient::factory()->count(15)->create();
    
    Livewire::actingAs($admin)
        ->test(\App\Livewire\Patient\PatientList::class)
        ->assertSee('Next')
        ->assertSee('Previous');
});

test('patient list shows create button for admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    Livewire::actingAs($admin)
        ->test(\App\Livewire\Patient\PatientList::class)
        ->assertSee('Add Patient');
});

test('patient cannot access patient list', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    
    $this->actingAs($user)
        ->get('/patients')
        ->assertForbidden();
});
```

**Run tests (should FAIL):**
```bash
php artisan test --filter=PatientListComponent
```

#### Step 2: Implement PatientList Component (GREEN)

**File:** `app/Livewire/Patient/PatientList.php`

**Specification:**

```php
<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class PatientList extends Component
{
    use AuthorizesRequests, WithPagination;

    public string $search = '';

    // Reset to page 1 when search changes
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $this->authorize('viewAny', Patient::class);

        $patients = Patient::query()
            ->with('user')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.patient.patient-list', [
            'patients' => $patients,
        ]);
    }
}
```

#### Step 3: Create Blade View

**File:** `resources/views/livewire/patient/patient-list.blade.php`

**Specification:**

```blade
<div class="p-6">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Patients</h1>
        
        @can('create', \App\Models\Patient::class)
            <a href="{{ route('patients.create') }}" 
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add Patient
            </a>
        @endcan
    </div>

    {{-- Search Bar --}}
    <div class="mb-4">
        <input 
            type="text" 
            wire:model.live="search" 
            placeholder="Search by name or phone..." 
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
    </div>

    {{-- Patients Table --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($patients as $patient)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $patient->full_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $patient->gender === 'M' ? 'Male' : 'Female' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $patient->age }} years</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $patient->phone ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @can('view', $patient)
                                <a href="{{ route('patients.show', $patient) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                            @endcan
                            
                            @can('update', $patient)
                                <a href="{{ route('patients.edit', $patient) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            @endcan
                            
                            @can('delete', $patient)
                                <button 
                                    wire:click="$dispatch('delete-patient', { id: {{ $patient->id }} })"
                                    class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No patients found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $patients->links() }}
    </div>
</div>
```

#### Step 4: Run Tests (should PASS)

```bash
php artisan test --filter=PatientListComponent
```

### 🧠 Why This Way?
- **WithPagination trait**: Automatic pagination handling
- **Live search**: `wire:model.live` updates results as you type
- **Policy-based actions**: Show/hide buttons based on user permissions
- **Eager loading**: `->with('user')` prevents N+1 query problem
- **Responsive design**: Tailwind CSS for mobile-friendly layout

### ✅ Verification
```bash
# Run tests
php artisan test --filter=PatientListComponent

# Manual browser test (as admin)
# Visit: http://your-app.test/patients
# - Should see list of patients
# - Search should filter results
# - Pagination should work
# - "Add Patient" button visible
```

---

## 📝 TASK 7: Create Patient Component

### 🎯 Goal
Build a Livewire component with a form to create new patient records, including user account creation.

### 📚 Key Concepts
- **Livewire Forms**: Two-way data binding with wire:model
- **Validation**: Real-time validation with Livewire rules
- **User Creation**: Create both User and Patient records
- **Flash Messages**: Success/error notifications
- **Form Components**: Reusable input fields

### 📝 TDD Approach

#### Step 1: Write Tests First (RED)

**File:** `tests/Feature/CreatePatientComponentTest.php`

```php
<?php

use App\Models\Patient;
use App\Models\User;
use Livewire\Livewire;

test('admin can see create patient form', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    $this->actingAs($admin)
        ->get('/patients/create')
        ->assertOk()
        ->assertSeeLivewire('patient.create-patient');
});

test('doctor can see create patient form', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    
    $this->actingAs($doktor)
        ->get('/patients/create')
        ->assertOk()
        ->assertSeeLivewire('patient.create-patient');
});

test('patient cannot access create patient form', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    
    $this->actingAs($user)
        ->get('/patients/create')
        ->assertForbidden();
});

test('can create patient with valid data', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    Livewire::actingAs($admin)
        ->test(\App\Livewire\Patient\CreatePatient::class)
        ->set('email', 'john.doe@example.com')
        ->set('first_name', 'John')
        ->set('last_name', 'Doe')
        ->set('date_of_birth', '1990-01-15')
        ->set('gender', 'M')
        ->set('phone', '123-456-7890')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('patients.index'));
    
    $this->assertDatabaseHas('users', [
        'email' => 'john.doe@example.com',
        'role' => 'pacijent',
    ]);
    
    $this->assertDatabaseHas('patients', [
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
});

test('email is required', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    Livewire::actingAs($admin)
        ->test(\App\Livewire\Patient\CreatePatient::class)
        ->set('email', '')
        ->call('save')
        ->assertHasErrors(['email' => 'required']);
});

test('email must be unique', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->create(['email' => 'existing@example.com']);
    
    Livewire::actingAs($admin)
        ->test(\App\Livewire\Patient\CreatePatient::class)
        ->set('email', 'existing@example.com')
        ->set('first_name', 'John')
        ->set('last_name', 'Doe')
        ->call('save')
        ->assertHasErrors(['email' => 'unique']);
});

test('first name is required', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    Livewire::actingAs($admin)
        ->test(\App\Livewire\Patient\CreatePatient::class)
        ->set('first_name', '')
        ->call('save')
        ->assertHasErrors(['first_name' => 'required']);
});

test('date of birth is required', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    Livewire::actingAs($admin)
        ->test(\App\Livewire\Patient\CreatePatient::class)
        ->set('date_of_birth', '')
        ->call('save')
        ->assertHasErrors(['date_of_birth' => 'required']);
});

test('gender must be valid', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    Livewire::actingAs($admin)
        ->test(\App\Livewire\Patient\CreatePatient::class)
        ->set('gender', 'X')
        ->call('save')
        ->assertHasErrors(['gender']);
});
```

**Run tests (should FAIL):**
```bash
php artisan test --filter=CreatePatientComponent
```

#### Step 2: Implement CreatePatient Component (GREEN)

**File:** `app/Livewire/Patient/CreatePatient.php`

**Specification:**

```php
<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class CreatePatient extends Component
{
    use AuthorizesRequests;

    // User fields
    public string $email = '';

    // Patient fields
    public string $first_name = '';
    public string $last_name = '';
    public string $date_of_birth = '';
    public string $gender = '';
    public string $phone = '';
    public string $address = '';
    public string $city = '';
    public string $postal_code = '';
    public string $emergency_contact_name = '';
    public string $emergency_contact_phone = '';
    public string $blood_type = '';
    public string $allergies = '';
    public string $medical_notes = '';

    public function mount()
    {
        $this->authorize('create', Patient::class);
    }

    protected function rules()
    {
        return [
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:M,F',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string',
            'medical_notes' => 'nullable|string',
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        DB::transaction(function () use ($validated) {
            // Create user account
            $user = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make(Str::random(16)), // Random password
                'role' => 'pacijent',
            ]);

            // Create patient profile
            Patient::create([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'emergency_contact_name' => $validated['emergency_contact_name'],
                'emergency_contact_phone' => $validated['emergency_contact_phone'],
                'blood_type' => $validated['blood_type'],
                'allergies' => $validated['allergies'],
                'medical_notes' => $validated['medical_notes'],
            ]);
        });

        session()->flash('success', 'Patient created successfully.');

        return $this->redirect(route('patients.index'));
    }

    public function render()
    {
        return view('livewire.patient.create-patient');
    }
}
```

#### Step 3: Create Blade View

**File:** `resources/views/livewire/patient/create-patient.blade.php`

**Specification:**

```blade
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold">Create New Patient</h1>
            <p class="text-gray-600">Register a new patient and create their user account</p>
        </div>

        {{-- Form --}}
        <form wire:submit="save" class="space-y-6 bg-white p-6 rounded-lg shadow">
            
            {{-- User Account Section --}}
            <div>
                <h2 class="text-lg font-semibold mb-4 text-gray-900">Account Information</h2>
                
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                    <input type="email" id="email" wire:model="email" 
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Personal Information Section --}}
            <div>
                <h2 class="text-lg font-semibold mb-4 text-gray-900">Personal Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                        <input type="text" id="first_name" wire:model="first_name" 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('first_name') border-red-500 @enderror">
                        @error('first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                        <input type="text" id="last_name" wire:model="last_name" 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('last_name') border-red-500 @enderror">
                        @error('last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth *</label>
                        <input type="date" id="date_of_birth" wire:model="date_of_birth" 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('date_of_birth') border-red-500 @enderror">
                        @error('date_of_birth') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                        <select id="gender" wire:model="gender" 
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('gender') border-red-500 @enderror">
                            <option value="">Select Gender</option>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                        </select>
                        @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" id="phone" wire:model="phone" 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="blood_type" class="block text-sm font-medium text-gray-700 mb-2">Blood Type</label>
                        <select id="blood_type" wire:model="blood_type" 
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Blood Type</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                        @error('blood_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Address Information --}}
            <div>
                <h2 class="text-lg font-semibold mb-4 text-gray-900">Address Information</h2>
                
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Street Address</label>
                        <input type="text" id="address" wire:model="address" 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                            <input type="text" id="city" wire:model="city" 
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                            <input type="text" id="postal_code" wire:model="postal_code" 
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('postal_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Emergency Contact --}}
            <div>
                <h2 class="text-lg font-semibold mb-4 text-gray-900">Emergency Contact</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-2">Contact Name</label>
                        <input type="text" id="emergency_contact_name" wire:model="emergency_contact_name" 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('emergency_contact_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                        <input type="text" id="emergency_contact_phone" wire:model="emergency_contact_phone" 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('emergency_contact_phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Medical Information --}}
            <div>
                <h2 class="text-lg font-semibold mb-4 text-gray-900">Medical Information</h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">Allergies</label>
                        <textarea id="allergies" wire:model="allergies" rows="2"
                                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        @error('allergies') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="medical_notes" class="block text-sm font-medium text-gray-700 mb-2">Medical Notes</label>
                        <textarea id="medical_notes" wire:model="medical_notes" rows="3"
                                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        @error('medical_notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex justify-end space-x-3">
                <a href="{{ route('patients.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Create Patient
                </button>
            </div>
        </form>
    </div>
</div>
```

#### Step 4: Run Tests (should PASS)

```bash
php artisan test --filter=CreatePatientComponent
```

### 🧠 Why This Way?
- **Transaction safety**: User + Patient created atomically with `DB::transaction()`
- **Random password**: Generated securely, patient can reset via email
- **Real-time validation**: Errors shown immediately with `wire:model`
- **Organized form**: Sections for different data categories
- **Flash messages**: Success notification after creation
- **Redirect**: Returns to patient list after success

### ✅ Verification
```bash
# Run tests
php artisan test --filter=CreatePatientComponent

# Manual browser test (as admin)
# Visit: http://your-app.test/patients/create
# - Fill form with valid data
# - Submit and verify redirect to list
# - Check database for user and patient records
```

---

## 🚧 UPCOMING TASKS (Not Yet Expanded)

- ⏳ TASK 8: View Patient Profile Component
- ⏳ TASK 9: Edit Patient Component
- ⏳ TASK 10: Delete Patient Component
- ⏳ TASK 11: Navigation Integration

---

*Complete Tasks 6-7 before expanding Tasks 8-11!*

---
---

# 📋 PHASE 2: SOCIOECONOMIC DATA FEATURE

> **Target Developer:** Junior Developer (Independent Assignment)  
> **Prerequisites:** Phase 1 Tasks 1-2 Complete (Patient Migration + Model)  
> **Status:** 📝 READY TO START  
> **Approach:** Feature-by-Feature with TDD  
> **Updated:** 2025-11-18T20:59:47.876Z

---

## 🎯 Phase 2 Overview

Build a complete Socioeconomic Data tracking system where:
- Each **Patient** has optional **Socioeconomic Data** (1-to-1 relationship)
- Doctors and Admins can record and update socioeconomic information
- Data includes: marital status, occupation, lifestyle, income, support systems
- Separate tab/section in patient profile for easy management

---

## 📋 ALL SOCIOECONOMIC TASKS (Phase 2)

12. ⏳ Socioeconomic Database Migration
13. ⏳ PatientSocioeconomic Model & Relationships
14. ⏳ PatientSocioeconomic Factory
15. ⏳ PatientSocioeconomic Policy (Authorization)
16. ⏳ Socioeconomic Routes
17. ⏳ View Socioeconomic Data Component
18. ⏳ Create/Edit Socioeconomic Form Component
19. ⏳ Delete Socioeconomic Data Component
20. ⏳ Integration with Patient Profile

**🎉 Phase 2 Checkpoint:** Socioeconomic data feature 100% complete and tested

---

## 📚 SOCIOECONOMIC DATA FIELDS

Based on typical nutrition clinic needs:

### **Demographics & Social**
- `marital_status` - ENUM: 'single', 'married', 'divorced', 'widowed', 'partnered'
- `number_of_dependents` - INT (how many people depend on patient)
- `living_arrangement` - ENUM: 'alone', 'with_family', 'with_partner', 'shared', 'institution'

### **Economic**
- `employment_status` - ENUM: 'employed_full_time', 'employed_part_time', 'self_employed', 'unemployed', 'retired', 'student', 'disabled'
- `occupation` - VARCHAR (job title/description)
- `income_level` - ENUM: 'low', 'middle', 'high', 'prefer_not_to_say'
- `has_health_insurance` - BOOLEAN

### **Lifestyle**
- `education_level` - ENUM: 'primary', 'secondary', 'vocational', 'bachelor', 'master', 'doctorate', 'other'
- `smoking_status` - ENUM: 'never', 'former', 'current'
- `alcohol_consumption` - ENUM: 'none', 'occasional', 'moderate', 'heavy'
- `physical_activity_level` - ENUM: 'sedentary', 'light', 'moderate', 'active', 'very_active'

### **Support Systems**
- `has_family_support` - BOOLEAN
- `has_caregiver` - BOOLEAN
- `transportation_access` - ENUM: 'own_vehicle', 'public_transport', 'family', 'limited', 'none'

### **Food Security**
- `food_security_status` - ENUM: 'secure', 'at_risk', 'insecure'
- `dietary_restrictions_cultural` - TEXT (cultural/religious dietary needs)

### **Notes**
- `additional_notes` - TEXT (any other relevant information)

---

## 📝 TASK 12: Socioeconomic Database Migration

### 🎯 Goal
Create database table to store socioeconomic data linked to patients via 1-to-1 relationship.

### 📚 Key Concepts
- **Foreign Key Relationship**: Links to `patients` table
- **ENUM Types**: Predefined valid values for structured data
- **Optional Data**: Most fields nullable (not all patients provide this data)
- **Cascade Delete**: Remove socioeconomic data when patient is deleted
- **Timestamps**: Track when data was created/updated

### 📝 TDD Approach

#### Step 1: Create Migration

**Command:**
```bash
php artisan make:migration create_patient_socioeconomic_table
```

**File:** `database/migrations/YYYY_MM_DD_HHMMSS_create_patient_socioeconomic_table.php`

**Specification:**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_socioeconomic', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to patients table (1-to-1 relationship)
            $table->foreignId('patient_id')
                ->unique() // Ensures 1-to-1 relationship
                ->constrained('patients')
                ->onDelete('cascade'); // Delete socioeconomic data when patient is deleted
            
            // Demographics & Social
            $table->enum('marital_status', [
                'single', 'married', 'divorced', 'widowed', 'partnered'
            ])->nullable();
            $table->integer('number_of_dependents')->nullable();
            $table->enum('living_arrangement', [
                'alone', 'with_family', 'with_partner', 'shared', 'institution'
            ])->nullable();
            
            // Economic
            $table->enum('employment_status', [
                'employed_full_time', 'employed_part_time', 'self_employed', 
                'unemployed', 'retired', 'student', 'disabled'
            ])->nullable();
            $table->string('occupation')->nullable();
            $table->enum('income_level', [
                'low', 'middle', 'high', 'prefer_not_to_say'
            ])->nullable();
            $table->boolean('has_health_insurance')->default(false);
            
            // Lifestyle
            $table->enum('education_level', [
                'primary', 'secondary', 'vocational', 'bachelor', 
                'master', 'doctorate', 'other'
            ])->nullable();
            $table->enum('smoking_status', [
                'never', 'former', 'current'
            ])->nullable();
            $table->enum('alcohol_consumption', [
                'none', 'occasional', 'moderate', 'heavy'
            ])->nullable();
            $table->enum('physical_activity_level', [
                'sedentary', 'light', 'moderate', 'active', 'very_active'
            ])->nullable();
            
            // Support Systems
            $table->boolean('has_family_support')->default(false);
            $table->boolean('has_caregiver')->default(false);
            $table->enum('transportation_access', [
                'own_vehicle', 'public_transport', 'family', 'limited', 'none'
            ])->nullable();
            
            // Food Security
            $table->enum('food_security_status', [
                'secure', 'at_risk', 'insecure'
            ])->nullable();
            $table->text('dietary_restrictions_cultural')->nullable();
            
            // Additional Notes
            $table->text('additional_notes')->nullable();
            
            $table->timestamps();
            
            // Index for faster lookups
            $table->index('patient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_socioeconomic');
    }
};
```

#### Step 2: Run Migration

```bash
php artisan migrate
```

#### Step 3: Verify Migration

```bash
# Check migration status
php artisan migrate:status

# View table structure
php artisan db:show --database=sqlite
```

### 🧠 Why This Way?
- **Separate table**: Keeps patient data clean, optional socioeconomic data separate
- **Unique constraint on patient_id**: Enforces 1-to-1 relationship (one socioeconomic record per patient)
- **Cascade delete**: Maintains referential integrity
- **ENUMs**: Provides data consistency and validation at database level
- **Most fields nullable**: Not all patients will provide all information
- **Booleans with defaults**: Simplifies form handling

### ✅ Verification
```bash
# Check table created
php artisan migrate:status

# Test in Tinker (after Task 13)
php artisan tinker
Schema::hasTable('patient_socioeconomic'); // Should return true
```

---

## 📝 TASK 13: PatientSocioeconomic Model & Relationships

### 🎯 Goal
Create Eloquent model for socioeconomic data with proper relationships and type casting.

### 📚 Key Concepts
- **Eloquent Models**: ORM representation of `patient_socioeconomic` table
- **BelongsTo Relationship**: Each socioeconomic record belongs to one Patient
- **HasOne Relationship**: Each Patient has one optional socioeconomic record
- **Type Casting**: Automatic conversion of boolean and enum values
- **Mass Assignment Protection**: `$fillable` for security

### 📝 TDD Approach

#### Step 1: Write Tests First (RED)

**File:** `tests/Feature/PatientSocioeconomicModelTest.php`

```php
<?php

use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use App\Models\User;

// === Basic Model Tests ===

test('socioeconomic data can be created for patient', function () {
    $patient = Patient::factory()->create();
    
    $socioeconomic = PatientSocioeconomic::create([
        'patient_id' => $patient->id,
        'marital_status' => 'married',
        'employment_status' => 'employed_full_time',
        'income_level' => 'middle',
    ]);
    
    expect($socioeconomic)->toBeInstanceOf(PatientSocioeconomic::class)
        ->and($socioeconomic->marital_status)->toBe('married')
        ->and($socioeconomic->patient_id)->toBe($patient->id);
});

// TODO: Write test for socioeconomic belongs to patient relationship
// TODO: Write test for patient has one socioeconomic relationship

// === Relationship Tests ===

test('socioeconomic data belongs to patient', function () {
    $patient = Patient::factory()->create();
    $socioeconomic = PatientSocioeconomic::factory()->create([
        'patient_id' => $patient->id
    ]);
    
    expect($socioeconomic->patient)->toBeInstanceOf(Patient::class)
        ->and($socioeconomic->patient->id)->toBe($patient->id);
});

test('patient can have socioeconomic data', function () {
    $patient = Patient::factory()->create();
    $socioeconomic = PatientSocioeconomic::factory()->create([
        'patient_id' => $patient->id
    ]);
    
    expect($patient->socioeconomic)->toBeInstanceOf(PatientSocioeconomic::class)
        ->and($patient->socioeconomic->id)->toBe($socioeconomic->id);
});

// === Unique Constraint Test ===

test('patient can only have one socioeconomic record', function () {
    $patient = Patient::factory()->create();
    
    PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);
    
    // Attempting to create second record should fail
    expect(fn() => PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]))
        ->toThrow(\Exception::class);
});

// === Cascade Delete Test ===

test('socioeconomic data is deleted when patient is deleted', function () {
    $patient = Patient::factory()->create();
    $socioeconomic = PatientSocioeconomic::factory()->create([
        'patient_id' => $patient->id
    ]);
    
    $socioeconomicId = $socioeconomic->id;
    
    $patient->delete();
    
    expect(PatientSocioeconomic::find($socioeconomicId))->toBeNull();
});
```

**Run tests (should FAIL):**
```bash
php artisan test --filter=PatientSocioeconomicModel
```

#### Step 2: Create PatientSocioeconomic Model (GREEN)

**Command:**
```bash
php artisan make:model PatientSocioeconomic
```

**File:** `app/Models/PatientSocioeconomic.php`

**Specification:**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientSocioeconomic extends Model
{
    use HasFactory;

    /**
     * Table name
     */
    protected $table = 'patient_socioeconomic';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'patient_id',
        'marital_status',
        'number_of_dependents',
        'living_arrangement',
        'employment_status',
        'occupation',
        'income_level',
        'has_health_insurance',
        'education_level',
        'smoking_status',
        'alcohol_consumption',
        'physical_activity_level',
        'has_family_support',
        'has_caregiver',
        'transportation_access',
        'food_security_status',
        'dietary_restrictions_cultural',
        'additional_notes',
    ];

    /**
     * Type casting for attributes
     */
    protected function casts(): array
    {
        return [
            'has_health_insurance' => 'boolean',
            'has_family_support' => 'boolean',
            'has_caregiver' => 'boolean',
            'number_of_dependents' => 'integer',
        ];
    }

    /**
     * Belongs to one Patient
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
```

#### Step 3: Update Patient Model

**File:** `app/Models/Patient.php`

**Add this relationship method:**

```php
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Has one optional Socioeconomic record
 */
public function socioeconomic(): HasOne
{
    return $this->hasOne(PatientSocioeconomic::class);
}
```

**Add import at top of file:**
```php
use Illuminate\Database\Eloquent\Relations\HasOne;
```

#### Step 4: Run Tests (should PASS)

```bash
php artisan test --filter=PatientSocioeconomicModel
```

### 🧠 Why This Way?
- **Explicit table name**: Laravel would pluralize to `patient_socioeconomics` (wrong)
- **BelongsTo/HasOne**: Establishes bidirectional relationship
- **Type casting**: Booleans and integers handled automatically
- **All fields fillable**: Safe since this is internal medical data

### ✅ Verification
```bash
# Run tests
php artisan test --filter=PatientSocioeconomicModel

# Test in Tinker
php artisan tinker
$patient = App\Models\Patient::first();
$patient->socioeconomic; // Should return null or socioeconomic data
```

---

## 📝 TASK 14: PatientSocioeconomic Factory

### 🎯 Goal
Create factory class to generate fake socioeconomic data for testing and seeding.

### 📚 Key Concepts
- **Factories**: Generate realistic test data
- **Faker**: PHP library for generating fake data
- **Optional Values**: Using `fake()->optional()` for nullable fields
- **ENUM Values**: Using `fake()->randomElement()` for predefined choices

### 📝 TDD Approach

#### Step 1: Write Tests First (RED)

**File:** `tests/Feature/PatientSocioeconomicFactoryTest.php`

```php
<?php

use App\Models\PatientSocioeconomic;
use App\Models\Patient;

test('socioeconomic factory creates valid data', function () {
    $socioeconomic = PatientSocioeconomic::factory()->create();
    
    expect($socioeconomic)->toBeInstanceOf(PatientSocioeconomic::class)
        ->and($socioeconomic->patient_id)->not->toBeNull();
});

// TODO: Write test for factory creates patient automatically
// TODO: Write test for factory respects provided attributes
// TODO: Write test for factory uses valid enum values

test('socioeconomic factory uses valid marital status', function () {
    $socioeconomic = PatientSocioeconomic::factory()->create();
    
    $validStatuses = ['single', 'married', 'divorced', 'widowed', 'partnered', null];
    expect($validStatuses)->toContain($socioeconomic->marital_status);
});

test('socioeconomic factory can be created for existing patient', function () {
    $patient = Patient::factory()->create();
    
    $socioeconomic = PatientSocioeconomic::factory()->create([
        'patient_id' => $patient->id
    ]);
    
    expect($socioeconomic->patient_id)->toBe($patient->id);
});
```

**Run tests (should FAIL):**
```bash
php artisan test --filter=PatientSocioeconomicFactory
```

#### Step 2: Create Factory (GREEN)

**Command:**
```bash
php artisan make:factory PatientSocioeconomicFactory
```

**File:** `database/factories/PatientSocioeconomicFactory.php`

**Specification:**

```php
<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PatientSocioeconomic>
 */
class PatientSocioeconomicFactory extends Factory
{
    protected $model = PatientSocioeconomic::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            
            // Demographics & Social
            'marital_status' => fake()->optional()->randomElement([
                'single', 'married', 'divorced', 'widowed', 'partnered'
            ]),
            'number_of_dependents' => fake()->optional()->numberBetween(0, 6),
            'living_arrangement' => fake()->optional()->randomElement([
                'alone', 'with_family', 'with_partner', 'shared', 'institution'
            ]),
            
            // Economic
            'employment_status' => fake()->optional()->randomElement([
                'employed_full_time', 'employed_part_time', 'self_employed',
                'unemployed', 'retired', 'student', 'disabled'
            ]),
            'occupation' => fake()->optional()->jobTitle(),
            'income_level' => fake()->optional()->randomElement([
                'low', 'middle', 'high', 'prefer_not_to_say'
            ]),
            'has_health_insurance' => fake()->boolean(70), // 70% have insurance
            
            // Lifestyle
            'education_level' => fake()->optional()->randomElement([
                'primary', 'secondary', 'vocational', 'bachelor',
                'master', 'doctorate', 'other'
            ]),
            'smoking_status' => fake()->optional()->randomElement([
                'never', 'former', 'current'
            ]),
            'alcohol_consumption' => fake()->optional()->randomElement([
                'none', 'occasional', 'moderate', 'heavy'
            ]),
            'physical_activity_level' => fake()->optional()->randomElement([
                'sedentary', 'light', 'moderate', 'active', 'very_active'
            ]),
            
            // Support Systems
            'has_family_support' => fake()->boolean(75), // 75% have family support
            'has_caregiver' => fake()->boolean(20), // 20% have caregiver
            'transportation_access' => fake()->optional()->randomElement([
                'own_vehicle', 'public_transport', 'family', 'limited', 'none'
            ]),
            
            // Food Security
            'food_security_status' => fake()->optional()->randomElement([
                'secure', 'at_risk', 'insecure'
            ]),
            'dietary_restrictions_cultural' => fake()->optional()->sentence(),
            
            // Notes
            'additional_notes' => fake()->optional()->paragraph(),
        ];
    }
}
```

#### Step 3: Run Tests (should PASS)

```bash
php artisan test --filter=PatientSocioeconomicFactory
```

### 🧠 Why This Way?
- **Realistic data**: Uses appropriate Faker methods for each field type
- **Optional values**: Most fields use `optional()` matching database nullable columns
- **Weighted probabilities**: Some booleans have custom probabilities (e.g., 70% have insurance)
- **Auto-creates patient**: Factory creates Patient if not provided
- **Reusable**: Use in tests, seeders, and development

### ✅ Verification
```bash
# Run tests
php artisan test --filter=PatientSocioeconomicFactory

# Test in Tinker
php artisan tinker
$socio = App\Models\PatientSocioeconomic::factory()->create();
$socio->patient; // Should show related Patient
$socio->toArray(); // See all generated data
```

---

## 📝 TASK 15: PatientSocioeconomic Policy (Authorization)

### 🎯 Goal
Create authorization rules to control who can view, create, update, and delete socioeconomic data.

### 📚 Key Concepts
- **Laravel Policies**: Centralized authorization logic
- **Policy Methods**: `viewAny()`, `view()`, `create()`, `update()`, `delete()`
- **Role-Based Access**: Different permissions for Admin, Doktor, Pacijent
- **Patient Privacy**: Patients can view their own socioeconomic data only

### 📝 TDD Approach

#### Step 1: Write Tests First (RED)

**File:** `tests/Feature/PatientSocioeconomicPolicyTest.php`

```php
<?php

use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use App\Models\User;

// === viewAny() Tests ===

test('admin can view any socioeconomic data', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    expect($admin->can('viewAny', PatientSocioeconomic::class))->toBeTrue();
});

// TODO: Write test for doktor can view any socioeconomic data
// TODO: Write test for pacijent cannot view all socioeconomic data

// === view() Tests ===

test('doktor can view any patient socioeconomic data', function () {
    $doktor = User::factory()->create(['role' => 'doktor']);
    $patient = Patient::factory()->create();
    $socioeconomic = PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);
    
    expect($doktor->can('view', $socioeconomic))->toBeTrue();
});

test('pacijent can view own socioeconomic data', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $socioeconomic = PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);
    
    expect($user->can('view', $socioeconomic))->toBeTrue();
});

test('pacijent cannot view other patient socioeconomic data', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    Patient::factory()->create(['user_id' => $user->id]);
    
    $otherPatient = Patient::factory()->create();
    $otherSocioeconomic = PatientSocioeconomic::factory()->create(['patient_id' => $otherPatient->id]);
    
    expect($user->can('view', $otherSocioeconomic))->toBeFalse();
});

// === create() Tests ===

test('admin and doktor can create socioeconomic data', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $doktor = User::factory()->create(['role' => 'doktor']);
    
    expect($admin->can('create', PatientSocioeconomic::class))->toBeTrue();
    expect($doktor->can('create', PatientSocioeconomic::class))->toBeTrue();
});

test('pacijent cannot create socioeconomic data', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    
    expect($user->can('create', PatientSocioeconomic::class))->toBeFalse();
});

// === update() Tests ===

test('admin and doktor can update socioeconomic data', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $socioeconomic = PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);
    
    expect($admin->can('update', $socioeconomic))->toBeTrue();
});

test('pacijent cannot update socioeconomic data', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $socioeconomic = PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);
    
    expect($user->can('update', $socioeconomic))->toBeFalse();
});

// === delete() Tests ===

test('admin and doktor can delete socioeconomic data', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $patient = Patient::factory()->create();
    $socioeconomic = PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);
    
    expect($admin->can('delete', $socioeconomic))->toBeTrue();
});

test('pacijent cannot delete socioeconomic data', function () {
    $user = User::factory()->create(['role' => 'pacijent']);
    $patient = Patient::factory()->create(['user_id' => $user->id]);
    $socioeconomic = PatientSocioeconomic::factory()->create(['patient_id' => $patient->id]);
    
    expect($user->can('delete', $socioeconomic))->toBeFalse();
});
```

**Run tests (should FAIL):**
```bash
php artisan test --filter=PatientSocioeconomicPolicy
```

#### Step 2: Create Policy (GREEN)

**Command:**
```bash
php artisan make:policy PatientSocioeconomicPolicy --model=PatientSocioeconomic
```

**File:** `app/Policies/PatientSocioeconomicPolicy.php`

**Specification:**

```php
<?php

namespace App\Policies;

use App\Models\PatientSocioeconomic;
use App\Models\User;

class PatientSocioeconomicPolicy
{
    /**
     * Determine if user can view any socioeconomic data
     * Only Admin and Doktor can see list of all socioeconomic records
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }

    /**
     * Determine if user can view specific socioeconomic data
     * Admin/Doktor can view any
     * Pacijent can only view their own
     */
    public function view(User $user, PatientSocioeconomic $socioeconomic): bool
    {
        // Admin and Doktor can view any socioeconomic data
        if ($user->isAdmin() || $user->isDoctor()) {
            return true;
        }

        // Pacijent can only view their own socioeconomic data
        return $user->id === $socioeconomic->patient->user_id;
    }

    /**
     * Determine if user can create socioeconomic data
     * Only Admin and Doktor can create
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }

    /**
     * Determine if user can update socioeconomic data
     * Only Admin and Doktor can update
     * Patients cannot update their own socioeconomic data (must be done by medical staff)
     */
    public function update(User $user, PatientSocioeconomic $socioeconomic): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }

    /**
     * Determine if user can delete socioeconomic data
     * Only Admin and Doktor can delete
     */
    public function delete(User $user, PatientSocioeconomic $socioeconomic): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }
}
```

#### Step 3: Run Tests (should PASS)

```bash
php artisan test --filter=PatientSocioeconomicPolicy
```

### 🧠 Why This Way?
- **Stricter than Patient Policy**: Patients can view but NOT edit their socioeconomic data
- **Medical Staff Control**: Only doctors/admins can record accurate socioeconomic information
- **Patient Privacy**: Patients can see their own data but not others'
- **Centralized Logic**: All authorization rules in one place
- **Follows User/Patient Policy pattern**: Consistent structure across the application

### ✅ Verification
```bash
# Run policy tests
php artisan test --filter=PatientSocioeconomicPolicy

# Test in Tinker
php artisan tinker
$admin = User::where('role', 'admin')->first();
$socio = App\Models\PatientSocioeconomic::first();
$admin->can('view', $socio); // should return true
```

---

## 📝 TASKS 16-20: Quick Task Summaries

### TASK 16: Socioeconomic Routes
- Create routes for viewing/creating/editing socioeconomic data
- Routes should be under `/patients/{patient}/socioeconomic`
- Apply policy middleware for authorization
- Example: `/patients/{patient}/socioeconomic/create`

### TASK 17: View Socioeconomic Data Component
- Display socioeconomic data in readable format
- Show as tab in patient profile
- Handle case when no socioeconomic data exists
- Authorization: Admin/Doktor/Own profile

### TASK 18: Create/Edit Socioeconomic Form Component
- Form with all socioeconomic fields
- Organized into sections (Demographics, Economic, Lifestyle, etc.)
- Dropdowns for ENUM fields
- Validation rules matching database constraints
- Save/Update functionality

### TASK 19: Delete Socioeconomic Data Component
- Soft delete or hard delete (decide based on requirements)
- Confirmation dialog
- Only Admin/Doktor can delete
- Show success message

### TASK 20: Integration with Patient Profile
- Add "Socioeconomic Data" tab to patient profile (Task 8)
- Show indicator if socioeconomic data exists/missing
- "Add Socioeconomic Data" button if none exists
- Seamless navigation between tabs

---

## 🎯 Phase 2 Completion Checklist

- [ ] Task 12: Socioeconomic migration created and run
- [ ] Task 13: PatientSocioeconomic model with relationships
- [ ] Task 14: Factory for generating test data
- [ ] Task 15: Authorization policy with full test coverage
- [ ] Task 16: Routes registered with middleware
- [ ] Task 17: View component displays data correctly
- [ ] Task 18: Create/Edit form works with validation
- [ ] Task 19: Delete functionality with confirmation
- [ ] Task 20: Integrated into patient profile seamlessly
- [ ] All tests passing
- [ ] Code formatted with Pint
- [ ] Documentation updated

---

## 📚 Resources for Junior Developer

### Laravel Documentation
- Models & Relationships: https://laravel.com/docs/eloquent-relationships
- Policies: https://laravel.com/docs/authorization#creating-policies
- Migrations: https://laravel.com/docs/migrations
- Validation: https://laravel.com/docs/validation

### Project-Specific
- Follow existing Patient model patterns
- Reuse Patient components as templates
- Check `CLAUDE.md` for coding conventions
- Follow TDD approach (write tests first)

### Getting Help
- Review completed Phase 1 tasks (1-11) as examples
- Ask questions when stuck
- Run tests frequently: `php artisan test --filter=PatientSocioeconomic`
- Use Tinker for quick testing: `php artisan tinker`

---

**🎉 Good luck with Phase 2! Follow the TDD approach and tests will guide you to success!**
