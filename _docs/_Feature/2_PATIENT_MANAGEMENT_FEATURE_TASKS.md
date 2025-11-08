# 📋 Patient Management Feature - Development Tasks

> **Created:** 2025-11-07  
> **Updated:** 2025-11-08T12:41:04.022Z  
> **Feature Group:** 2 - Patient Registration & Management  
> **Status:** 🚀 IN PROGRESS  
> **Approach:** Feature-by-Feature with TDD  
> **Dependencies:** User Management (✅ Complete)

---

## 🎯 Feature Overview

**From dev_tasks.md - Feature Group 2:**
- Admin: Create & Manage Users (Doctors & Patients)
- Patient Registration (Admin/Doctor-Created)
- View & Edit Patient Profile
- Record Socio-Economic Data

**Implementation Strategy:**
- **Phase 1:** Complete Patient feature (DB → Model → Factory → Policy → Routes → CRUD GUI → Tests)
- **Phase 2:** Add Socioeconomic extension (DB → Model → Factory → Form → Integration)

## 🏗️ Development Approach: Feature-by-Feature with TDD

### ⚡ Implementation Mode Active
- AI implements code directly when prefixed with **"command:"**
- TDD workflow: Write test → Implement → Verify (Red → Green → Refactor)
- Complete Patient feature FULLY before starting Socioeconomic
- Run tests after each task to ensure green

### 📋 Task Organization

#### **PHASE 1: Patient Core Feature** (Complete Vertical Slice)
Build full CRUD for Patient management with authorization and tests.

#### **PHASE 2: Socioeconomic Extension** (Optional Data Add-on)
Extend Patient profile with socioeconomic data collection.

---

## 📊 PHASE 1: PATIENT CORE FEATURE - Database Schema

### Patient Table Structure

```sql
patients
├── id (primary key)
├── user_id (foreign key to users.id, unique, cascades on delete)
├── first_name (string, required)
├── last_name (string, required)
├── date_of_birth (date, required)
├── gender (enum: male, female, other)
├── phone (string, nullable)
├── address (text, nullable)
├── city (string, nullable)
├── postal_code (string, nullable)
├── emergency_contact_name (string, nullable)
├── emergency_contact_phone (string, nullable)
├── blood_type (enum: A+, A-, B+, B-, AB+, AB-, O+, O-, nullable)
├── allergies (text, nullable)
├── medical_notes (text, nullable)
├── created_at (timestamp)
├── updated_at (timestamp)
├── deleted_at (timestamp, soft delete)
```

---

## 📋 PHASE 1 TASK SUMMARY

### **Patient Core Feature Tasks** (Complete in Order)
1. ⏳ **Patient DB Migration** - Create patients table
2. ⏳ **Patient Model** - Eloquent model with User relationship
3. ⏳ **Patient Factory** - Test data generation
4. ⏳ **Patient Policy** - Authorization rules (viewAny, view, create, update, delete)
5. ⏳ **Patient Routes** - Register CRUD routes with middleware
6. ⏳ **List Patients** - Livewire component with search/filter + tests
7. ⏳ **Create Patient** - Livewire form component + validation + tests
8. ⏳ **View Patient** - Profile page + tests
9. ⏳ **Edit Patient** - Livewire edit form + tests
10. ⏳ **Delete Patient** - Soft delete with confirmation + tests
11. ⏳ **Navigation** - Add Patients link to main menu

**🎉 Checkpoint: Patient feature 100% complete and tested**

---

## 📖 PHASE 1 DETAILED TASK BREAKDOWN

---

## ✅ TASK 1: Patient Database Migration

**Goal:** Create patients table with proper constraints, indexes, and soft deletes.

**TDD Approach:** Verify migration creates correct schema structure.

### Implementation

**Command:**
```bash
php artisan make:migration create_patients_table
```

**File:** `database/migrations/YYYY_MM_DD_HHMMSS_create_patients_table.php`

**Schema Requirements:**
```php
public function up(): void
{
    Schema::create('patients', function (Blueprint $table) {
        $table->id();
        
        // Foreign key to users table (1-to-1 relationship)
        $table->foreignId('user_id')
            ->unique()
            ->constrained()
            ->onDelete('cascade');
        
        // Personal information
        $table->string('first_name');
        $table->string('last_name');
        $table->date('date_of_birth');
        $table->enum('gender', ['male', 'female', 'other']);
        $table->string('phone')->nullable();
        
        // Address information
        $table->text('address')->nullable();
        $table->string('city')->nullable();
        $table->string('postal_code')->nullable();
        
        // Emergency contact
        $table->string('emergency_contact_name')->nullable();
        $table->string('emergency_contact_phone')->nullable();
        
        // Medical information
        $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])
            ->nullable();
        $table->text('allergies')->nullable();
        $table->text('medical_notes')->nullable();
        
        $table->timestamps();
        $table->softDeletes();
        
        // Indexes for performance
        $table->index('date_of_birth');
        $table->index('phone');
    });
}

public function down(): void
{
    Schema::dropIfExists('patients');
}
```

**Run Migration:**
```bash
php artisan migrate
```

**Verification:**
```bash
php artisan tinker
Schema::hasTable('patients'); // true
Schema::hasColumn('patients', 'user_id'); // true
DB::table('patients')->count(); // 0
```

---

## ✅ TASK 2: Patient Model & Relationships

**Goal:** Create Patient Eloquent model with User relationship, accessors, and casts.

**TDD Approach:** Test relationships, accessors, and attribute casting.

### Implementation

**Command:**
```bash
php artisan make:model Patient
```

**File:** `app/Models/Patient.php`

**Model Requirements:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone',
        'address',
        'city',
        'postal_code',
        'emergency_contact_name',
        'emergency_contact_phone',
        'blood_type',
        'allergies',
        'medical_notes',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => "{$this->first_name} {$this->last_name}"
        );
    }

    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->date_of_birth?->age ?? 0
        );
    }
}
```

**Update User Model:**

Add to `app/Models/User.php`:
```php
use Illuminate\Database\Eloquent\Relations\HasOne;

public function patient(): HasOne
{
    return $this->hasOne(Patient::class);
}
```

**Verification:**
```bash
php artisan tinker
$patient = new App\Models\Patient();
$patient->user(); // BelongsTo instance
$user = App\Models\User::first();
$user->patient(); // HasOne instance
```

---

## ✅ TASK 3: Patient Factory

**Goal:** Create factory for generating realistic test patient data.

**TDD Approach:** Verify factory creates valid patients with required relationships.

### Implementation

**Command:**
```bash
php artisan make:factory PatientFactory
```

**File:** `database/factories/PatientFactory.php`

**Factory Requirements:**
```php
<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(['role' => 'pacijent'])->id,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'date_of_birth' => fake()->dateTimeBetween('-80 years', '-18 years'),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->phoneNumber(),
            'blood_type' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'allergies' => fake()->optional()->sentence(),
            'medical_notes' => fake()->optional()->paragraph(),
        ];
    }
}

---

## 📊 PHASE 2: SOCIOECONOMIC EXTENSION - Database Schema

### Socioeconomic Table Structure

```sql
patient_socioeconomic
├── id (primary key)
├── patient_id (foreign key to patients.id, unique, cascades on delete)
├── marital_status (enum: single, married, divorced, widowed, nullable)
├── occupation (string, nullable)
├── education_level (string, nullable)
├── income_level (enum: low, medium, high, nullable)
├── living_situation (enum: alone, family, assisted, other, nullable)
├── insurance_provider (string, nullable)
├── insurance_number (string, nullable)
├── lifestyle_notes (text, nullable)
├── created_at (timestamp)
├── updated_at (timestamp)
```

---

## 📋 PHASE 2 TASK SUMMARY

### **Socioeconomic Extension Tasks** (After Phase 1 Complete)
12. ⏳ **Socioeconomic Migration** - Create patient_socioeconomic table
13. ⏳ **Socioeconomic Model** - Model with Patient relationship  
14. ⏳ **Socioeconomic Factory** - Test data generation
15. ⏳ **Edit Socioeconomic Form** - Livewire component + tests
16. ⏳ **Integration** - Add to patient profile page
17. ⏳ **Final Verification** - End-to-end tests

**🎉 Checkpoint: Full Patient + Socioeconomic system complete**

---

## 📖 PHASE 2 DETAILED TASK BREAKDOWN

---

## ✅ TASK 12: Socioeconomic Database Migration

**Goal:** Create patient_socioeconomic table for optional patient data.

**TDD Approach:** Verify migration creates correct schema with patient relationship.

### Implementation

**Command:**
```bash
php artisan make:migration create_patient_socioeconomic_table
```

**File:** ```database/migrations/YYYY_MM_DD_HHMMSS_create_patient_socioeconomic_table.php```

**Schema Requirements:**
```php
public function up(): void
{
    Schema::create('patient_socioeconomic', function (Blueprint $	able) {
        $	able->id();
        
        // One-to-one with patients table
        $	able->foreignId('patient_id')
            ->unique()
            ->constrained()
            ->onDelete('cascade');
        
        // Socioeconomic factors (all optional)
        $	able->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])
            ->nullable();
        $	able->string('occupation')->nullable();
        $	able->string('education_level')->nullable();
        $	able->enum('income_level', ['low', 'medium', 'high'])->nullable();
        $	able->enum('living_situation', ['alone', 'family', 'assisted', 'other'])
            ->nullable();
        
        // Insurance information
        $	able->string('insurance_provider')->nullable();
        $	able->string('insurance_number')->nullable();
        
        // Additional notes
        $	able->text('lifestyle_notes')->nullable();
        
        $	able->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('patient_socioeconomic');
}
```

---

## ✅ TASK 13: Socioeconomic Model

**Goal:** Create PatientSocioeconomic model with Patient relationship.

**Implementation:**

**Command:**
```bash
php artisan make:model PatientSocioeconomic
```

**File:** ```app/Models/PatientSocioeconomic.php```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientSocioeconomic extends Model
{
    use HasFactory;

    protected $	able = 'patient_socioeconomic';

    protected $illable = [
        'patient_id',
        'marital_status',
        'occupation',
        'education_level',
        'income_level',
        'living_situation',
        'insurance_provider',
        'insurance_number',
        'lifestyle_notes',
    ];

    public function patient(): BelongsTo
    {
        return $	his->belongsTo(Patient::class);
    }
}
```

**Update Patient Model:** Add relationship (if not already added in Phase 1)

```php
use Illuminate\Database\Eloquent\Relations\HasOne;

public function socioeconomic(): HasOne
{
    return $	his->hasOne(PatientSocioeconomic::class);
}
```

---

## ✅ TASK 14: Socioeconomic Factory

**Goal:** Create factory for generating test socioeconomic data.

**Command:**
```bash
php artisan make:factory PatientSocioeconomicFactory
```

**File:** ```database/factories/PatientSocioeconomicFactory.php```

```php
<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientSocioeconomicFactory extends Factory
{
    protected $model = PatientSocioeconomic::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'marital_status' => fake()->randomElement(['single', 'married', 'divorced', 'widowed']),
            'occupation' => fake()->jobTitle(),
            'education_level' => fake()->randomElement(['High School', 'Bachelor', 'Master', 'PhD']),
            'income_level' => fake()->randomElement(['low', 'medium', 'high']),
            'living_situation' => fake()->randomElement(['alone', 'family', 'assisted', 'other']),
            'insurance_provider' => fake()->optional()->company(),
            'insurance_number' => fake()->optional()->numerify('INS-########'),
            'lifestyle_notes' => fake()->optional()->paragraph(),
        ];
    }
}
```

---

## ✅ TASK 15: Edit Socioeconomic Livewire Component

**Goal:** Create form component to edit patient socioeconomic data.

**TDD Approach:** Write tests for form validation, update logic, and authorization.

**Components to Create:**
- Livewire component: ```app/Livewire/Patient/EditSocioeconomic.php```
- Blade view: ```resources/views/livewire/patient/edit-socioeconomic.blade.php```
- Feature tests: ```tests/Feature/Patient/EditSocioeconomicTest.php```

---

## ✅ TASK 16: Integration with Patient Profile

**Goal:** Add socioeconomic section to patient profile page.

**Implementation:**
- Add tab/section in patient show view
- Link to edit socioeconomic form
- Display socioeconomic data if exists

---

## ✅ TASK 17: Final Verification

**Goal:** End-to-end testing of complete Patient + Socioeconomic system.

**Verification Checklist:**

```bash
# 1. All migrations ran
php artisan migrate:status

# 2. All tables exist
php artisan tinker
Patient::count()
PatientSocioeconomic::count()

# 3. Routes registered
php artisan route:list --path=patients

# 4. All tests pass
php artisan test

# 5. Manual UI testing:
# - Create patient
# - View patient profile
# - Add socioeconomic data
# - Edit socioeconomic data
# - Verify data persists
```

---

## 🎉 FEATURE COMPLETE

**Patient Management Feature Group 2 - 100% Complete**

✅ **Phase 1:** Full Patient CRUD with authorization  
✅ **Phase 2:** Socioeconomic data extension

**What's Built:**
- Patient registration and management
- User-Patient relationship (1-to-1)
- Patient profile with bio-data
- Socioeconomic data collection
- Role-based authorization (Admin, Doktor, Pacijent)
- Full test coverage
- Livewire components with Flux UI

**Next Feature:** Feature Group 3 - Visits & Encounters

---
