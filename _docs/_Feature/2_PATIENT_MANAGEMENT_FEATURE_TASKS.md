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

## 🚧 UPCOMING TASKS (Not Yet Expanded)

- ⏳ TASK 6: List Patients Component
- ⏳ TASK 7: Create Patient Component
- ⏳ TASK 8: View Patient Component
- ⏳ TASK 9: Edit Patient Component
- ⏳ TASK 10: Delete Patient Component
- ⏳ TASK 11: Navigation Integration

---

*Tasks 6-11 will be expanded next. Complete Tasks 4-5 first!*
