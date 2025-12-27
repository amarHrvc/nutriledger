# TASK001 - View Patient Profile Component

**Status:** In Progress  
**Added:** 2025-12-24  
**Updated:** 2025-12-24

## Original Request
Implement **Task 8: View Patient Profile Component** from `_docs/_Feature/2_PATIENT_MANAGEMENT_FEATURE_TASKS.md`, displaying full patient information in a read-only Livewire view with proper authorization.

## Thought Process
The `_docs` folder defines Patient Management Phase 1 as 11 tasks, with tasks 1–7 complete and Task 8 as the current focus.
Task 8 should follow the established vertical slice and TDD pattern: write tests, build a Livewire component, design a Blade view, and wire everything through existing routes and policies.
This task is the immediate priority on branch `feature/patient_management` and unblocks Tasks 9–11.

## Implementation Plan
- Define Pest feature tests for viewing a patient profile (authorization, content rendering, edge cases).
- Implement the `App\Livewire\Patient\ViewPatient` component with `mount()` authorization and route model binding.
- Build a structured Blade view (`resources/views/livewire/patient/view-patient.blade.php`) with sections for personal, contact, address, emergency, and medical info.
- Ensure policies (`PatientPolicy@view`) and routes (`patients.show`) are used correctly and covered by tests.
- Update `_docs/_CURRENT_STATE.md` and the Memory Bank when the component and tests are complete.

## Progress Tracking

**Overall Status:** In Progress - 10%

### Subtasks
| ID | Description | Status | Updated | Notes |
|----|-------------|--------|---------|-------|
| 1.1 | Write initial Pest tests for ViewPatient component (auth + basic rendering) | Not Started | 2025-12-24 | Follow examples in `_docs/_Feature/2_PATIENT_MANAGEMENT_FEATURE_TASKS.md` |
| 1.2 | Implement `ViewPatient` Livewire component with `mount()` authorization | Not Started | 2025-12-24 | Use `authorize('view', $patient)` and route model binding |
| 1.3 | Build Blade view with all patient sections and null-safe display | Not Started | 2025-12-24 | Use same grouping as create/edit forms |
| 1.4 | Run and stabilize tests, then update docs/Memory Bank | Not Started | 2025-12-24 | Sync `_docs/_CURRENT_STATE.md` and Memory Bank after completion |

## 📝 Expanded Task Specification

### 🎯 Goal
Display complete patient information in a clean, organized, read-only format with proper authorization.

### 📚 Key Concepts
- Route Model Binding: Automatic Patient model injection from URL
- Authorization in mount(): Check permissions before rendering
- Read-only Display: Organized sections showing all patient data
- Conditional Rendering: Show/hide edit button based on permissions
- Null Handling: Display "N/A" or "-" for empty fields

### 📝 TDD Approach
#### Step 1: Write Tests First (RED)
**File:** `tests/Feature/ViewPatientComponentTest.php`
**Command to create:**
```bash
php artisan make:test ViewPatientComponentTest
```
**Example Tests (you write remaining):**
```php
<?php
use App\Models\Patient;
use App\Models\User;
use Livewire\Livewire;
beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->doktor = User::factory()->create(['role' => 'doktor']);
    $this->patientUser = User::factory()->create(['role' => 'pacijent']);
    $this->patient = Patient::factory()->create(['user_id' => $this->patientUser->id]);
});
// === Authorization Tests ===
test('admin can view patient profile', function () {
    $this->actingAs($this->admin)
        ->get("/patients/{$this->patient->id}")
        ->assertOk()
        ->assertSeeLivewire('patient.view-patient');
});
// TODO: Test doktor can view patient profile
// TODO: Test pacijent can view own profile
// TODO: Test pacijent cannot view other patient profile
// === Component Tests ===
test('displays patient personal information', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Patient\ViewPatient::class, ['patient' => $this->patient])
        ->assertSee($this->patient->first_name)
        ->assertSee($this->patient->last_name)
        ->assertSee($this->patient->full_name);
});
// TODO: Test displays patient contact information
// TODO: Test displays patient medical information
// TODO: Test displays patient emergency contact
// TODO: Test shows edit button for authorized users
// TODO: Test hides edit button for unauthorized users
// TODO: Test shows 'N/A' for empty optional fields
```
**Run tests (should FAIL):**
```bash
php artisan test --filter=ViewPatientComponent
```

#### Step 2: Implement Component (GREEN)
**Command:**
```bash
php artisan make:livewire Patient/ViewPatient
```
**Component Specification:**
**File:** `app/Livewire/Patient/ViewPatient.php`
**Properties:**
- `public Patient $patient` - Injected via route model binding
**Method: mount(Patient $patient)**
- Purpose: Initialize component and check authorization
- Logic:
  - Store patient in property
  - Call `$this->authorize('view', $patient)`
  - If unauthorized → 403 Forbidden
**Method: render()**
- Purpose: Return view with patient data
- Logic: `return view('livewire.patient.view-patient')`

#### Step 3: Create View (GREEN)
**File:** `resources/views/livewire/patient/view-patient.blade.php`
**View Structure:**
```blade
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        {{-- Header with Edit Button --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">
                    {{ $patient->full_name }}
                </h2>
                <p class="text-gray-600">Patient Profile</p>
            </div>
            @can('update', $patient)
                <a href="{{ route('patients.edit', $patient) }}" 
                   class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Edit Profile
                </a>
            @endcan
        </div>
        {{-- Information Cards --}}
        <div class="space-y-6">
            {{-- Personal Information Section --}}
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4 text-gray-900">Personal Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $patient->full_name }}</dd>
                    </div>
                    <!-- TODO: Add more fields: Gender, Date of Birth, Age, Phone -->
                </dl>
            </div>
            {{-- TODO: Add sections for: --}}
            {{-- Address Information --}}
            {{-- Emergency Contact --}}
            {{-- Medical Information --}}
        </div>
        {{-- Back Button --}}
        <div class="mt-6">
            <a href="{{ route('patients.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Back to List
            </a>
        </div>
    </div>
</div>
```
**What to display in each section:**
**Personal Information:**
- Full Name, Gender (M → Male, F → Female), Date of Birth, Age, Phone, Blood Type
**Address Information:**
- Street Address, City, Postal Code (or "No address provided")
**Emergency Contact:**
- Contact Name, Contact Phone (or "No emergency contact")
**Medical Information:**
- Allergies (or "None reported"), Medical Notes (or "No notes")

#### Step 4: Run Tests (should PASS)
```bash
php artisan test --filter=ViewPatientComponent
```

### 🧠 Why This Way?
- Authorization in mount(): Prevents unauthorized access before any data loads
- Route model binding: Cleaner URLs and automatic 404 if patient not found
- Organized sections: Easy to read, matches create/edit form structure
- @can directive: Shows edit button only to authorized users
- Null handling: Professional display when data is missing

### ✅ Verification
```bash
# Run tests
php artisan test --filter=ViewPatientComponent
# Manual test
# 1. Login as admin
# 2. Visit: /patients
# 3. Click on a patient name
# 4. Verify all information displays correctly
# 5. Verify edit button shows for admin/doktor
```

## Progress Log
### 2025-12-24
- Created TASK001 to track implementation of Patient Management Phase 1 Task 8 (View Patient Profile Component).
- Captured the vertical-slice/TDD expectations from `_docs/_CURRENT_STATE.md` and patient feature docs.
