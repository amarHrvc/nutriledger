# 📋 Patient Management Feature - Development Tasks

> **Created:** 2025-11-07  
> **Feature Group:** 2 - Patient Registration & Management  
> **Status:** Not Started  
> **Dependencies:** User Management (✅ Complete)

---

## 🎯 Feature Overview

Build a complete Patient Management system where:
- Each **Patient** is linked to a **User** account (1-to-1 relationship)
- Doctors and Admins can register and manage patient profiles
- Patient profiles contain bio-data and socio-economic information
- Separate from User Management (users are accounts, patients are medical records)

---

## 📊 Database Schema

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

### Socio-Economic Table Structure

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

## ✅ TASK 1: Database Setup

### 1.1 Create Patient Migration

**File:** `database/migrations/YYYY_MM_DD_HHMMSS_create_patients_table.php`

**Instructions:**
```php
- Create patients table with all fields listed above
- Add foreign key constraint: user_id references users(id) onDelete('cascade')
- Add unique index on user_id (one patient per user)
- Add soft deletes
- Add indexes on: user_id, date_of_birth, phone
```

**Command:**
```bash
php artisan make:migration create_patients_table
```

---

### 1.2 Create Socio-Economic Migration

**File:** `database/migrations/YYYY_MM_DD_HHMMSS_create_patient_socioeconomic_table.php`

**Instructions:**
```php
- Create patient_socioeconomic table with all fields listed above
- Add foreign key constraint: patient_id references patients(id) onDelete('cascade')
- Add unique index on patient_id (one socioeconomic record per patient)
```

**Command:**
```bash
php artisan make:migration create_patient_socioeconomic_table
```

---

### 1.3 Run Migrations

**Command:**
```bash
php artisan migrate
```

**Verification:**
```bash
php artisan tinker
DB::table('patients')->count();  // Should return 0
DB::table('patient_socioeconomic')->count();  // Should return 0
```

---

## ✅ TASK 2: Models & Relationships

### 2.1 Create Patient Model

**File:** `app/Models/Patient.php`

**Instructions:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // Relationship: Patient belongs to User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship: Patient has one SocioEconomic record
    public function socioeconomic(): HasOne
    {
        return $this->hasOne(PatientSocioeconomic::class);
    }

    // Helper: Get full name
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Helper: Get age
    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }
}
```

**Command:**
```bash
php artisan make:model Patient
```

---

### 2.2 Create PatientSocioeconomic Model

**File:** `app/Models/PatientSocioeconomic.php`

**Instructions:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientSocioeconomic extends Model
{
    use HasFactory;

    protected $table = 'patient_socioeconomic';

    protected $fillable = [
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

    // Relationship: Socioeconomic belongs to Patient
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
```

**Command:**
```bash
php artisan make:model PatientSocioeconomic
```

---

### 2.3 Update User Model Relationship

**File:** `app/Models/User.php`

**Add this method:**
```php
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Relationship: User has one Patient
 */
public function patient(): HasOne
{
    return $this->hasOne(Patient::class);
}
```

---

## ✅ TASK 3: Factories for Testing

### 3.1 Create Patient Factory

**File:** `database/factories/PatientFactory.php`

**Command:**
```bash
php artisan make:factory PatientFactory
```

**Instructions:**
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
```

---

### 3.2 Create PatientSocioeconomic Factory

**File:** `database/factories/PatientSocioeconomicFactory.php`

**Command:**
```bash
php artisan make:factory PatientSocioeconomicFactory
```

**Instructions:**
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
            'education_level' => fake()->randomElement(['Primary', 'Secondary', 'Bachelor', 'Master', 'PhD']),
            'income_level' => fake()->randomElement(['low', 'medium', 'high']),
            'living_situation' => fake()->randomElement(['alone', 'family', 'assisted', 'other']),
            'insurance_provider' => fake()->optional()->company(),
            'insurance_number' => fake()->optional()->numerify('INS-######'),
            'lifestyle_notes' => fake()->optional()->paragraph(),
        ];
    }
}
```

---

## ✅ TASK 4: Patient Policy (Authorization)

### 4.1 Create PatientPolicy

**File:** `app/Policies/PatientPolicy.php`

**Command:**
```bash
php artisan make:policy PatientPolicy --model=Patient
```

**Instructions:**
```php
<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    /**
     * Admin and Doktor can view any patients
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }

    /**
     * Admin and Doktor can view a patient
     * Patient can view their own profile
     */
    public function view(User $user, Patient $patient): bool
    {
        return $user->isAdmin() 
            || $user->isDoctor() 
            || $user->id === $patient->user_id;
    }

    /**
     * Admin and Doktor can create patients
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }

    /**
     * Admin and Doktor can update patients
     * Patient can update their own profile (limited fields)
     */
    public function update(User $user, Patient $patient): bool
    {
        return $user->isAdmin() 
            || $user->isDoctor() 
            || $user->id === $patient->user_id;
    }

    /**
     * Admin and Doktor can delete patients
     */
    public function delete(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }
}
```

---

## ✅ TASK 5: Routes Setup

### 5.1 Add Patient Routes

**File:** `routes/web.php`

**Add these routes inside the authenticated middleware group:**

```php
// Patient Management Routes
Route::middleware(['auth'])->group(function () {
    // List all patients (Admin & Doktor only)
    Route::get('/patients', \App\Livewire\Patient\PatientList::class)
        ->middleware('role:admin,doktor')
        ->name('patients.index');
    
    // Create new patient (Admin & Doktor only)
    Route::get('/patients/create', \App\Livewire\Patient\CreatePatient::class)
        ->middleware('role:admin,doktor')
        ->name('patients.create');
    
    // View patient profile (Admin, Doktor, or own profile)
    Route::get('/patients/{patient}', \App\Livewire\Patient\ViewPatient::class)
        ->name('patients.show');
    
    // Edit patient profile (Admin, Doktor, or own profile)
    Route::get('/patients/{patient}/edit', \App\Livewire\Patient\EditPatient::class)
        ->name('patients.edit');
    
    // Edit socio-economic data (Admin & Doktor only)
    Route::get('/patients/{patient}/socioeconomic', \App\Livewire\Patient\EditSocioeconomic::class)
        ->middleware('role:admin,doktor')
        ->name('patients.socioeconomic');
});
```

**Note:** You'll need to create a `role` middleware or use authorization in components.

---

## ✅ TASK 6: Livewire Components - Patient List

### 6.1 Create PatientList Component

**File:** `app/Livewire/Patient/PatientList.php`

**Command:**
```bash
php artisan make:livewire Patient/PatientList
```

**Instructions:**
```php
<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Component;
use Livewire\WithPagination;

class PatientList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $genderFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = ['search', 'genderFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingGenderFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
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
            ->when($this->genderFilter, function ($query) {
                $query->where('gender', $this->genderFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        return view('livewire.patient.patient-list', [
            'patients' => $patients,
        ]);
    }
}
```

---

### 6.2 Create PatientList Blade View

**File:** `resources/views/livewire/patient/patient-list.blade.php`

**Instructions:**
```blade
<div>
    <flux:header>
        <flux:heading size="xl">Patients</flux:heading>
        
        <flux:spacer />

        <flux:button href="{{ route('patients.create') }}" icon="plus" variant="primary">
            Add New Patient
        </flux:button>
    </flux:header>

    <flux:card class="mt-6">
        {{-- Search and Filters --}}
        <div class="mb-4 flex gap-4">
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search by name or phone..." 
                icon="magnifying-glass"
                class="flex-1"
            />

            <flux:select wire:model.live="genderFilter" placeholder="All Genders">
                <option value="">All Genders</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </flux:select>
        </div>

        {{-- Patient Table --}}
        <flux:table>
            <flux:columns>
                <flux:column>Name</flux:column>
                <flux:column>Date of Birth</flux:column>
                <flux:column>Age</flux:column>
                <flux:column>Gender</flux:column>
                <flux:column>Phone</flux:column>
                <flux:column>Email</flux:column>
                <flux:column>Actions</flux:column>
            </flux:columns>

            <flux:rows>
                @forelse ($patients as $patient)
                    <flux:row>
                        <flux:cell>
                            <a href="{{ route('patients.show', $patient) }}" 
                               class="font-medium text-blue-600 hover:underline">
                                {{ $patient->full_name }}
                            </a>
                        </flux:cell>
                        <flux:cell>{{ $patient->date_of_birth->format('M d, Y') }}</flux:cell>
                        <flux:cell>{{ $patient->age }} years</flux:cell>
                        <flux:cell>{{ ucfirst($patient->gender) }}</flux:cell>
                        <flux:cell>{{ $patient->phone ?? 'N/A' }}</flux:cell>
                        <flux:cell>{{ $patient->user->email }}</flux:cell>
                        <flux:cell>
                            <div class="flex gap-2">
                                <flux:button 
                                    href="{{ route('patients.show', $patient) }}" 
                                    size="sm" 
                                    variant="ghost"
                                    icon="eye"
                                >
                                    View
                                </flux:button>
                                
                                @can('update', $patient)
                                    <flux:button 
                                        href="{{ route('patients.edit', $patient) }}" 
                                        size="sm" 
                                        variant="ghost"
                                        icon="pencil"
                                    >
                                        Edit
                                    </flux:button>
                                @endcan
                            </div>
                        </flux:cell>
                    </flux:row>
                @empty
                    <flux:row>
                        <flux:cell colspan="7" class="text-center text-gray-500">
                            No patients found.
                        </flux:cell>
                    </flux:row>
                @endforelse
            </flux:rows>
        </flux:table>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $patients->links() }}
        </div>
    </flux:card>
</div>
```

---

## ✅ TASK 7: Livewire Components - Create Patient

### 7.1 Create CreatePatient Component

**File:** `app/Livewire/Patient/CreatePatient.php`

**Command:**
```bash
php artisan make:livewire Patient/CreatePatient
```

**Instructions:**
```php
<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class CreatePatient extends Component
{
    // User fields
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Patient fields
    public string $first_name = '';
    public string $last_name = '';
    public string $date_of_birth = '';
    public string $gender = 'male';
    public string $phone = '';
    public string $address = '';
    public string $city = '';
    public string $postal_code = '';
    public string $emergency_contact_name = '';
    public string $emergency_contact_phone = '';
    public string $blood_type = '';
    public string $allergies = '';
    public string $medical_notes = '';

    protected array $rules = [
        'email' => ['required', 'email', 'unique:users,email'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'first_name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'date_of_birth' => ['required', 'date', 'before:today'],
        'gender' => ['required', 'in:male,female,other'],
        'phone' => ['nullable', 'string', 'max:20'],
        'address' => ['nullable', 'string'],
        'city' => ['nullable', 'string', 'max:255'],
        'postal_code' => ['nullable', 'string', 'max:20'],
        'emergency_contact_name' => ['nullable', 'string', 'max:255'],
        'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
        'blood_type' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
        'allergies' => ['nullable', 'string'],
        'medical_notes' => ['nullable', 'string'],
    ];

    public function save()
    {
        $this->authorize('create', Patient::class);
        
        $this->validate();

        DB::transaction(function () {
            // Create user account
            $user = User::create([
                'name' => "{$this->first_name} {$this->last_name}",
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => 'pacijent',
            ]);

            // Create patient profile
            Patient::create([
                'user_id' => $user->id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'date_of_birth' => $this->date_of_birth,
                'gender' => $this->gender,
                'phone' => $this->phone,
                'address' => $this->address,
                'city' => $this->city,
                'postal_code' => $this->postal_code,
                'emergency_contact_name' => $this->emergency_contact_name,
                'emergency_contact_phone' => $this->emergency_contact_phone,
                'blood_type' => $this->blood_type ?: null,
                'allergies' => $this->allergies,
                'medical_notes' => $this->medical_notes,
            ]);
        });

        session()->flash('success', 'Patient registered successfully.');

        return $this->redirect(route('patients.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.patient.create-patient');
    }
}
```

---

### 7.2 Create CreatePatient Blade View

**File:** `resources/views/livewire/patient/create-patient.blade.php`

**Instructions:**
```blade
<div>
    <flux:header>
        <flux:heading size="xl">Register New Patient</flux:heading>
    </flux:header>

    <form wire:submit="save" class="mt-6 space-y-6">
        {{-- Account Information Section --}}
        <flux:card>
            <flux:heading size="lg">Account Information</flux:heading>
            <flux:subheading>Create login credentials for the patient</flux:subheading>

            <div class="mt-6 grid gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>Email Address</flux:label>
                    <flux:input wire:model="email" type="email" required />
                    <flux:error name="email" />
                </flux:field>

                <div></div>

                <flux:field>
                    <flux:label>Password</flux:label>
                    <flux:input wire:model="password" type="password" required />
                    <flux:error name="password" />
                </flux:field>

                <flux:field>
                    <flux:label>Confirm Password</flux:label>
                    <flux:input wire:model="password_confirmation" type="password" required />
                    <flux:error name="password_confirmation" />
                </flux:field>
            </div>
        </flux:card>

        {{-- Personal Information Section --}}
        <flux:card>
            <flux:heading size="lg">Personal Information</flux:heading>

            <div class="mt-6 grid gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>First Name</flux:label>
                    <flux:input wire:model="first_name" required />
                    <flux:error name="first_name" />
                </flux:field>

                <flux:field>
                    <flux:label>Last Name</flux:label>
                    <flux:input wire:model="last_name" required />
                    <flux:error name="last_name" />
                </flux:field>

                <flux:field>
                    <flux:label>Date of Birth</flux:label>
                    <flux:input wire:model="date_of_birth" type="date" required />
                    <flux:error name="date_of_birth" />
                </flux:field>

                <flux:field>
                    <flux:label>Gender</flux:label>
                    <flux:select wire:model="gender" required>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </flux:select>
                    <flux:error name="gender" />
                </flux:field>

                <flux:field>
                    <flux:label>Phone Number</flux:label>
                    <flux:input wire:model="phone" type="tel" />
                    <flux:error name="phone" />
                </flux:field>

                <flux:field>
                    <flux:label>Blood Type</flux:label>
                    <flux:select wire:model="blood_type">
                        <option value="">Select Blood Type</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </flux:select>
                    <flux:error name="blood_type" />
                </flux:field>
            </div>
        </flux:card>

        {{-- Address Information Section --}}
        <flux:card>
            <flux:heading size="lg">Address Information</flux:heading>

            <div class="mt-6 grid gap-6 sm:grid-cols-2">
                <flux:field class="sm:col-span-2">
                    <flux:label>Street Address</flux:label>
                    <flux:input wire:model="address" />
                    <flux:error name="address" />
                </flux:field>

                <flux:field>
                    <flux:label>City</flux:label>
                    <flux:input wire:model="city" />
                    <flux:error name="city" />
                </flux:field>

                <flux:field>
                    <flux:label>Postal Code</flux:label>
                    <flux:input wire:model="postal_code" />
                    <flux:error name="postal_code" />
                </flux:field>
            </div>
        </flux:card>

        {{-- Emergency Contact Section --}}
        <flux:card>
            <flux:heading size="lg">Emergency Contact</flux:heading>

            <div class="mt-6 grid gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>Contact Name</flux:label>
                    <flux:input wire:model="emergency_contact_name" />
                    <flux:error name="emergency_contact_name" />
                </flux:field>

                <flux:field>
                    <flux:label>Contact Phone</flux:label>
                    <flux:input wire:model="emergency_contact_phone" type="tel" />
                    <flux:error name="emergency_contact_phone" />
                </flux:field>
            </div>
        </flux:card>

        {{-- Medical Information Section --}}
        <flux:card>
            <flux:heading size="lg">Medical Information</flux:heading>

            <div class="mt-6 space-y-6">
                <flux:field>
                    <flux:label>Known Allergies</flux:label>
                    <flux:textarea wire:model="allergies" rows="3" />
                    <flux:error name="allergies" />
                </flux:field>

                <flux:field>
                    <flux:label>Medical Notes</flux:label>
                    <flux:textarea wire:model="medical_notes" rows="4" />
                    <flux:error name="medical_notes" />
                </flux:field>
            </div>
        </flux:card>

        {{-- Form Actions --}}
        <div class="flex justify-end gap-3">
            <flux:button href="{{ route('patients.index') }}" variant="ghost">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary">
                Register Patient
            </flux:button>
        </div>
    </form>
</div>
```

---

## ✅ TASK 8: Livewire Components - View Patient

### 8.1 Create ViewPatient Component

**File:** `app/Livewire/Patient/ViewPatient.php`

**Command:**
```bash
php artisan make:livewire Patient/ViewPatient
```

**Instructions:**
```php
<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Component;

class ViewPatient extends Component
{
    public Patient $patient;

    public function mount(Patient $patient)
    {
        $this->authorize('view', $patient);
        $this->patient = $patient->load(['user', 'socioeconomic']);
    }

    public function render()
    {
        return view('livewire.patient.view-patient');
    }
}
```

---

### 8.2 Create ViewPatient Blade View

**File:** `resources/views/livewire/patient/view-patient.blade.php`

**Instructions:**
```blade
<div>
    <flux:header>
        <div>
            <flux:heading size="xl">{{ $patient->full_name }}</flux:heading>
            <flux:subheading>Patient Profile</flux:subheading>
        </div>
        
        <flux:spacer />

        @can('update', $patient)
            <flux:button href="{{ route('patients.edit', $patient) }}" icon="pencil" variant="primary">
                Edit Profile
            </flux:button>
        @endcan
    </flux:header>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        {{-- Personal Information --}}
        <flux:card>
            <flux:heading size="lg">Personal Information</flux:heading>
            
            <dl class="mt-6 space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->full_name }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $patient->date_of_birth->format('F d, Y') }} 
                        <span class="text-gray-500">({{ $patient->age }} years old)</span>
                    </dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Gender</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($patient->gender) }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Blood Type</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->blood_type ?? 'Not specified' }}</dd>
                </div>
            </dl>
        </flux:card>

        {{-- Contact Information --}}
        <flux:card>
            <flux:heading size="lg">Contact Information</flux:heading>
            
            <dl class="mt-6 space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->user->email }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $patient->phone ?? 'Not provided' }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Address</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($patient->address)
                            {{ $patient->address }}<br>
                            {{ $patient->city }} {{ $patient->postal_code }}
                        @else
                            Not provided
                        @endif
                    </dd>
                </div>
            </dl>
        </flux:card>

        {{-- Emergency Contact --}}
        <flux:card>
            <flux:heading size="lg">Emergency Contact</flux:heading>
            
            <dl class="mt-6 space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Contact Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $patient->emergency_contact_name ?? 'Not provided' }}
                    </dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Contact Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $patient->emergency_contact_phone ?? 'Not provided' }}
                    </dd>
                </div>
            </dl>
        </flux:card>

        {{-- Medical Information --}}
        <flux:card>
            <flux:heading size="lg">Medical Information</flux:heading>
            
            <dl class="mt-6 space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Known Allergies</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $patient->allergies ?? 'None reported' }}
                    </dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Medical Notes</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $patient->medical_notes ?? 'No notes' }}
                    </dd>
                </div>
            </dl>
        </flux:card>

        {{-- Socio-Economic Information --}}
        @if($patient->socioeconomic)
            <flux:card class="lg:col-span-2">
                <flux:heading size="lg">Socio-Economic Information</flux:heading>
                
                <div class="mt-6 grid gap-6 sm:grid-cols-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Marital Status</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $patient->socioeconomic->marital_status ? ucfirst($patient->socioeconomic->marital_status) : 'Not specified' }}
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Occupation</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $patient->socioeconomic->occupation ?? 'Not specified' }}
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Living Situation</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $patient->socioeconomic->living_situation ? ucfirst($patient->socioeconomic->living_situation) : 'Not specified' }}
                        </dd>
                    </div>
                </div>
            </flux:card>
        @endif
    </div>
</div>
```

---

## ✅ TASK 9: Livewire Components - Edit Patient

### 9.1 Create EditPatient Component

**File:** `app/Livewire/Patient/EditPatient.php`

**Command:**
```bash
php artisan make:livewire Patient/EditPatient
```

**Instructions:**
```php
<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Component;

class EditPatient extends Component
{
    public Patient $patient;
    
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

    protected array $rules = [
        'first_name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'date_of_birth' => ['required', 'date', 'before:today'],
        'gender' => ['required', 'in:male,female,other'],
        'phone' => ['nullable', 'string', 'max:20'],
        'address' => ['nullable', 'string'],
        'city' => ['nullable', 'string', 'max:255'],
        'postal_code' => ['nullable', 'string', 'max:20'],
        'emergency_contact_name' => ['nullable', 'string', 'max:255'],
        'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
        'blood_type' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
        'allergies' => ['nullable', 'string'],
        'medical_notes' => ['nullable', 'string'],
    ];

    public function mount(Patient $patient)
    {
        $this->authorize('update', $patient);
        
        $this->patient = $patient;
        $this->first_name = $patient->first_name;
        $this->last_name = $patient->last_name;
        $this->date_of_birth = $patient->date_of_birth->format('Y-m-d');
        $this->gender = $patient->gender;
        $this->phone = $patient->phone ?? '';
        $this->address = $patient->address ?? '';
        $this->city = $patient->city ?? '';
        $this->postal_code = $patient->postal_code ?? '';
        $this->emergency_contact_name = $patient->emergency_contact_name ?? '';
        $this->emergency_contact_phone = $patient->emergency_contact_phone ?? '';
        $this->blood_type = $patient->blood_type ?? '';
        $this->allergies = $patient->allergies ?? '';
        $this->medical_notes = $patient->medical_notes ?? '';
    }

    public function save()
    {
        $this->authorize('update', $this->patient);
        
        $this->validate();

        $this->patient->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'blood_type' => $this->blood_type ?: null,
            'allergies' => $this->allergies,
            'medical_notes' => $this->medical_notes,
        ]);

        session()->flash('success', 'Patient profile updated successfully.');

        return $this->redirect(route('patients.show', $this->patient), navigate: true);
    }

    public function render()
    {
        return view('livewire.patient.edit-patient');
    }
}
```

---

### 9.2 Create EditPatient Blade View

**File:** `resources/views/livewire/patient/edit-patient.blade.php`

**Instructions:**
```blade
{{-- Copy the same structure as create-patient.blade.php but: --}}
{{-- 1. Remove account information section (email/password) --}}
{{-- 2. Change heading to "Edit Patient Profile" --}}
{{-- 3. Change button text to "Update Patient" --}}
{{-- 4. Use wire:model instead of wire:model for all fields --}}
{{-- 5. Pre-populate all fields with existing data --}}

<div>
    <flux:header>
        <flux:heading size="xl">Edit Patient Profile</flux:heading>
    </flux:header>

    <form wire:submit="save" class="mt-6 space-y-6">
        {{-- Same sections as create but without account info --}}
        {{-- Personal Information Section --}}
        {{-- Address Information Section --}}
        {{-- Emergency Contact Section --}}
        {{-- Medical Information Section --}}

        {{-- Form Actions --}}
        <div class="flex justify-end gap-3">
            <flux:button href="{{ route('patients.show', $patient) }}" variant="ghost">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary">
                Update Patient
            </flux:button>
        </div>
    </form>
</div>
```

**Note:** Follow the same structure as `create-patient.blade.php` but remove the account section.

---

## ✅ TASK 10: Livewire Components - Edit Socioeconomic Data

### 10.1 Create EditSocioeconomic Component

**File:** `app/Livewire/Patient/EditSocioeconomic.php`

**Command:**
```bash
php artisan make:livewire Patient/EditSocioeconomic
```

**Instructions:**
```php
<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use App\Models\PatientSocioeconomic;
use Livewire\Component;

class EditSocioeconomic extends Component
{
    public Patient $patient;
    
    public string $marital_status = '';
    public string $occupation = '';
    public string $education_level = '';
    public string $income_level = '';
    public string $living_situation = '';
    public string $insurance_provider = '';
    public string $insurance_number = '';
    public string $lifestyle_notes = '';

    protected array $rules = [
        'marital_status' => ['nullable', 'in:single,married,divorced,widowed'],
        'occupation' => ['nullable', 'string', 'max:255'],
        'education_level' => ['nullable', 'string', 'max:255'],
        'income_level' => ['nullable', 'in:low,medium,high'],
        'living_situation' => ['nullable', 'in:alone,family,assisted,other'],
        'insurance_provider' => ['nullable', 'string', 'max:255'],
        'insurance_number' => ['nullable', 'string', 'max:255'],
        'lifestyle_notes' => ['nullable', 'string'],
    ];

    public function mount(Patient $patient)
    {
        $this->authorize('update', $patient);
        
        $this->patient = $patient->load('socioeconomic');
        
        $socio = $patient->socioeconomic;
        if ($socio) {
            $this->marital_status = $socio->marital_status ?? '';
            $this->occupation = $socio->occupation ?? '';
            $this->education_level = $socio->education_level ?? '';
            $this->income_level = $socio->income_level ?? '';
            $this->living_situation = $socio->living_situation ?? '';
            $this->insurance_provider = $socio->insurance_provider ?? '';
            $this->insurance_number = $socio->insurance_number ?? '';
            $this->lifestyle_notes = $socio->lifestyle_notes ?? '';
        }
    }

    public function save()
    {
        $this->authorize('update', $this->patient);
        
        $this->validate();

        $this->patient->socioeconomic()->updateOrCreate(
            ['patient_id' => $this->patient->id],
            [
                'marital_status' => $this->marital_status ?: null,
                'occupation' => $this->occupation,
                'education_level' => $this->education_level,
                'income_level' => $this->income_level ?: null,
                'living_situation' => $this->living_situation ?: null,
                'insurance_provider' => $this->insurance_provider,
                'insurance_number' => $this->insurance_number,
                'lifestyle_notes' => $this->lifestyle_notes,
            ]
        );

        session()->flash('success', 'Socio-economic data updated successfully.');

        return $this->redirect(route('patients.show', $this->patient), navigate: true);
    }

    public function render()
    {
        return view('livewire.patient.edit-socioeconomic');
    }
}
```

---

### 10.2 Create EditSocioeconomic Blade View

**File:** `resources/views/livewire/patient/edit-socioeconomic.blade.php`

**Instructions:**
```blade
<div>
    <flux:header>
        <div>
            <flux:heading size="xl">Socio-Economic Information</flux:heading>
            <flux:subheading>{{ $patient->full_name }}</flux:subheading>
        </div>
    </flux:header>

    <form wire:submit="save" class="mt-6">
        <flux:card>
            <div class="grid gap-6 sm:grid-cols-2">
                <flux:field>
                    <flux:label>Marital Status</flux:label>
                    <flux:select wire:model="marital_status">
                        <option value="">Select Status</option>
                        <option value="single">Single</option>
                        <option value="married">Married</option>
                        <option value="divorced">Divorced</option>
                        <option value="widowed">Widowed</option>
                    </flux:select>
                    <flux:error name="marital_status" />
                </flux:field>

                <flux:field>
                    <flux:label>Occupation</flux:label>
                    <flux:input wire:model="occupation" />
                    <flux:error name="occupation" />
                </flux:field>

                <flux:field>
                    <flux:label>Education Level</flux:label>
                    <flux:input wire:model="education_level" />
                    <flux:error name="education_level" />
                </flux:field>

                <flux:field>
                    <flux:label>Income Level</flux:label>
                    <flux:select wire:model="income_level">
                        <option value="">Select Level</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </flux:select>
                    <flux:error name="income_level" />
                </flux:field>

                <flux:field>
                    <flux:label>Living Situation</flux:label>
                    <flux:select wire:model="living_situation">
                        <option value="">Select Situation</option>
                        <option value="alone">Alone</option>
                        <option value="family">With Family</option>
                        <option value="assisted">Assisted Living</option>
                        <option value="other">Other</option>
                    </flux:select>
                    <flux:error name="living_situation" />
                </flux:field>

                <flux:field>
                    <flux:label>Insurance Provider</flux:label>
                    <flux:input wire:model="insurance_provider" />
                    <flux:error name="insurance_provider" />
                </flux:field>

                <flux:field class="sm:col-span-2">
                    <flux:label>Insurance Number</flux:label>
                    <flux:input wire:model="insurance_number" />
                    <flux:error name="insurance_number" />
                </flux:field>

                <flux:field class="sm:col-span-2">
                    <flux:label>Lifestyle Notes</flux:label>
                    <flux:textarea wire:model="lifestyle_notes" rows="4" />
                    <flux:error name="lifestyle_notes" />
                </flux:field>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <flux:button href="{{ route('patients.show', $patient) }}" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Save Changes
                </flux:button>
            </div>
        </flux:card>
    </form>
</div>
```

---

## ✅ TASK 11: Testing

### 11.1 Create PatientManagementTest

**File:** `tests/Feature/Patient/PatientManagementTest.php`

**Command:**
```bash
php artisan make:test Patient/PatientManagementTest
```

**Instructions:**
```php
<?php

use App\Models\Patient;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->doktor = User::factory()->create(['role' => 'doktor']);
    $this->pacijent = User::factory()->create(['role' => 'pacijent']);
});

// Access Control Tests
test('admin can access patient list', function () {
    $this->actingAs($this->admin)
        ->get('/patients')
        ->assertStatus(200);
});

test('doktor can access patient list', function () {
    $this->actingAs($this->doktor)
        ->get('/patients')
        ->assertStatus(200);
});

test('pacijent cannot access patient list', function () {
    $this->actingAs($this->pacijent)
        ->get('/patients')
        ->assertStatus(403);
});

// Create Patient Tests
test('admin can create a new patient with full profile', function () {
    $this->actingAs($this->admin);

    Livewire::test(\App\Livewire\Patient\CreatePatient::class)
        ->set('email', 'newpatient@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('first_name', 'John')
        ->set('last_name', 'Doe')
        ->set('date_of_birth', '1990-01-01')
        ->set('gender', 'male')
        ->set('phone', '+123456789')
        ->call('save')
        ->assertRedirect(route('patients.index'));

    $this->assertDatabaseHas('users', [
        'email' => 'newpatient@example.com',
        'role' => 'pacijent',
    ]);

    $this->assertDatabaseHas('patients', [
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
});

test('doktor can create a new patient', function () {
    $this->actingAs($this->doktor);

    Livewire::test(\App\Livewire\Patient\CreatePatient::class)
        ->set('email', 'patient@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('first_name', 'Jane')
        ->set('last_name', 'Smith')
        ->set('date_of_birth', '1985-06-15')
        ->set('gender', 'female')
        ->call('save')
        ->assertRedirect(route('patients.index'));
});

// View Patient Tests
test('admin can view patient profile', function () {
    $patient = Patient::factory()->create();

    $this->actingAs($this->admin)
        ->get(route('patients.show', $patient))
        ->assertStatus(200)
        ->assertSee($patient->full_name);
});

test('patient can view their own profile', function () {
    $patient = Patient::factory()->create(['user_id' => $this->pacijent->id]);

    $this->actingAs($this->pacijent)
        ->get(route('patients.show', $patient))
        ->assertStatus(200);
});

// Edit Patient Tests
test('admin can edit patient profile', function () {
    $patient = Patient::factory()->create();

    $this->actingAs($this->admin);

    Livewire::test(\App\Livewire\Patient\EditPatient::class, ['patient' => $patient])
        ->set('first_name', 'Updated')
        ->set('last_name', 'Name')
        ->call('save')
        ->assertRedirect(route('patients.show', $patient));

    $this->assertDatabaseHas('patients', [
        'id' => $patient->id,
        'first_name' => 'Updated',
        'last_name' => 'Name',
    ]);
});

// Socioeconomic Tests
test('admin can update patient socioeconomic data', function () {
    $patient = Patient::factory()->create();

    $this->actingAs($this->admin);

    Livewire::test(\App\Livewire\Patient\EditSocioeconomic::class, ['patient' => $patient])
        ->set('marital_status', 'married')
        ->set('occupation', 'Engineer')
        ->call('save')
        ->assertRedirect(route('patients.show', $patient));

    $this->assertDatabaseHas('patient_socioeconomic', [
        'patient_id' => $patient->id,
        'marital_status' => 'married',
        'occupation' => 'Engineer',
    ]);
});
```

---

### 11.2 Run Tests

**Commands:**
```bash
# Run all patient tests
php artisan test --filter=Patient

# Run specific test
php artisan test --filter="admin can create a new patient"
```

---

## ✅ TASK 12: Navigation & UI Integration

### 12.1 Add Patients Link to Navigation

**File:** Update your main navigation layout (likely `resources/views/components/layout.blade.php` or similar)

**Add this link to the navigation menu:**
```blade
@can('viewAny', \App\Models\Patient::class)
    <flux:navlist.item 
        icon="users" 
        href="{{ route('patients.index') }}"
        :current="request()->routeIs('patients.*')"
    >
        Patients
    </flux:navlist.item>
@endcan
```

---

### 12.2 Update Dashboard (Optional)

Add a patient count widget to the dashboard showing total patients registered.

---

## ✅ TASK 13: Final Verification

### 13.1 Checklist

Run through this checklist:

```bash
# 1. Migrations ran successfully
php artisan migrate:status

# 2. Models exist
php artisan tinker
Patient::count()
PatientSocioeconomic::count()

# 3. Routes are registered
php artisan route:list --path=patients

# 4. All tests pass
php artisan test

# 5. Can create patient via UI
# - Login as admin or doktor
# - Navigate to /patients
# - Click "Add New Patient"
# - Fill form and submit
# - Verify patient appears in list

# 6. Can view patient profile
# - Click on patient name
# - Verify all data displays correctly

# 7. Can edit patient
# - Click "Edit Profile"
# - Update fields
# - Save and verify changes

# 8. Can edit socioeconomic data
# - From patient profile, navigate to socioeconomic form
# - Fill and save
# - Verify data persists
```

---

## 📊 Summary

This task list implements **Feature Group 2: Patient Registration & Management** with:

✅ Separate Patient entity (not just a user role)  
✅ Complete CRUD operations  
✅ Bio-data and socio-economic information  
✅ Role-based authorization (Admin & Doktor can manage, Patient can view own)  
✅ Full test coverage  
✅ Livewire components with Flux UI  
✅ Factories for testing  
✅ Proper relationships (User ← Patient ← Socioeconomic)

---

**Estimated Time:** 8-12 hours for a mid-level developer

**Next Feature:** Feature Group 3 - Visits & Encounters
