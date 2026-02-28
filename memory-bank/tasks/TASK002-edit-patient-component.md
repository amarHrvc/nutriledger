# TASK002 - Edit Patient Component

**Status:** Pending  
**Added:** 2025-12-24  
**Updated:** 2025-12-24

## Original Request
Implement **Task 9: Edit Patient Component** from `_docs/_Feature/2_PATIENT_MANAGEMENT_FEATURE_TASKS.md`, allowing authorized users to edit existing patient records with pre-filled data and validation.

## Thought Process
Per `_docs/_CURRENT_STATE.md`, Task 9 follows after the ViewPatient component and should reuse the same validation rules as patient creation, minus email changes.
The component must respect `PatientPolicy@update`, support only authorized users (admin/doktor/own profile), and keep the user email read-only.

## Implementation Plan
- Add Pest tests for EditPatient behavior (authorization, pre-filled fields, successful update, validation errors, redirect behavior).
- Implement `App\Livewire\Patient\EditPatient` with `mount(Patient $patient)` for pre-fill and authorization.
- Create/edit the Blade view (`resources/views/livewire/patient/edit-patient.blade.php`) based on the create form but with read-only email.
- Ensure the `patients.edit` route correctly points to the component and is covered by route tests.
- Update `_docs/_CURRENT_STATE.md` and Memory Bank progress once Task 9 is complete.

## Progress Tracking

**Overall Status:** Not Started - 0%

### Subtasks
| ID | Description | Status | Updated | Notes |
|----|-------------|--------|---------|-------|
| 2.1 | Add/edit Pest tests for EditPatient authorization and pre-filled data | Not Started | 2025-12-24 | Follow test patterns from `_docs` examples |
| 2.2 | Implement `EditPatient` Livewire component with mount pre-fill and rules | Not Started | 2025-12-24 | Mirror `CreatePatient` rules without email changes |
| 2.3 | Build/update Blade view for editing patients with read-only email | Not Started | 2025-12-24 | Reuse structure from create form |
| 2.4 | Verify routes and policies via tests, then update docs/Memory Bank | Not Started | 2025-12-24 | Keep `_docs/_CURRENT_STATE.md` in sync |

## 📝 Expanded Task Specification

### 🎯 Goal
Allow authorized users to edit existing patient records with pre-filled data, validation, and read-only email field.

### 📚 Key Concepts
- Livewire components
- Laravel authorization policies (PatientPolicy@update)
- Blade templating
- Route model binding
- TDD with Pest
- Form validation and error handling
- Read-only fields

### 📝 TDD Approach
#### Step 1: Write Tests First (RED)
**File:** `tests/Feature/EditPatientComponentTest.php`
**Command to create:**
```bash
php artisan make:test EditPatientComponentTest
```
**Example Tests (expand as needed):**
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
test('admin can edit patient profile', function () {
    $this->actingAs($this->admin)
        ->get("/patients/{$this->patient->id}/edit")
        ->assertOk()
        ->assertSeeLivewire('patient.edit-patient');
});
// TODO: Test doktor can edit patient profile
// TODO: Test pacijent can edit own profile
// TODO: Test pacijent cannot edit other patient profile
// === Component Tests ===
test('pre-fills patient data in form', function () {
    Livewire::actingAs($this->admin)
        ->test(\App\Livewire\Patient\EditPatient::class, ['patient' => $this->patient])
        ->assertSet('patient.first_name', $this->patient->first_name)
        ->assertSet('patient.last_name', $this->patient->last_name);
});
// TODO: Test email field is read-only
// TODO: Test validation errors for required fields
// TODO: Test successful update redirects to view page
```
**Run tests (should FAIL):**
```bash
php artisan test --filter=EditPatientComponent
```

#### Step 2: Implement Component (GREEN)
**Command:**
```bash
php artisan make:livewire Patient/EditPatient
```
**Component Specification:**
**File:** `app/Livewire/Patient/EditPatient.php`
**Properties:**
- `public Patient $patient` - Injected via route model binding
**Method: mount(Patient $patient)**
- Purpose: Initialize component, pre-fill data, and check authorization
- Logic:
  - Store patient in property
  - Call `$this->authorize('update', $patient)`
  - If unauthorized → 403 Forbidden
**Method: save()**
- Purpose: Validate and update patient data
- Logic:
  - Validate input (reuse create rules except email)
  - Save changes
  - Redirect to view page
**Method: render()**
- Purpose: Return view with patient data
- Logic: `return view('livewire.patient.edit-patient')`

#### Step 3: Create View (GREEN)
**File:** `resources/views/livewire/patient/edit-patient.blade.php`
**View Structure:**
```blade
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <form wire:submit.prevent="save">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Edit Patient Profile</h2>
            <div class="space-y-6">
                {{-- Personal Information Section --}}
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Personal Information</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <input type="text" wire:model="patient.full_name" class="form-input" />
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <input type="email" wire:model="patient.email" class="form-input" readonly />
                            </dd>
                        </div>
                        <!-- TODO: Add more fields and sections -->
                    </dl>
                </div>
                {{-- TODO: Add address, emergency, medical sections --}}
            </div>
            <div class="mt-6 flex justify-between">
                <a href="{{ route('patients.show', $patient) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Save Changes</button>
            </div>
        </form>
    </div>
</div>
```
**What to display and edit:**
- All patient fields except email (read-only)
- Validation errors inline
- Pre-filled data for all fields

#### Step 4: Run Tests (should PASS)
```bash
php artisan test --filter=EditPatientComponent
```

### 🧠 Why This Way?
- Authorization in mount(): Prevents unauthorized access before any data loads
- Pre-filled form: Improves UX and reduces errors
- Read-only email: Maintains data integrity
- Reuse validation: Consistency with create form

### ✅ Verification
```bash
# Run tests
php artisan test --filter=EditPatientComponent
# Manual test
# 1. Login as admin/doktor/pacijent
# 2. Visit: /patients/{id}/edit
# 3. Verify all fields are pre-filled
# 4. Verify email is read-only
# 5. Edit data, submit, and confirm changes
# 6. Verify unauthorized users cannot access edit page
```

## Progress Log
### 2025-12-24
- Created TASK002 to track implementation of Patient Management Phase 1 Task 9 (Edit Patient Component).
- Documented dependencies on Task 8 (ViewPatient) and existing create-patient behavior.
