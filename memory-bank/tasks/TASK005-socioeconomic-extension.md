# TASK005 - Patient Socioeconomic Extension (Phase 2)

**Status:** Pending  
**Added:** 2025-12-24  
**Updated:** 2025-12-24

## Original Request
Implement **Phase 2: Socioeconomic Extension** for patients as described in `_docs/_Feature/2_PATIENT_MANAGEMENT_FEATURE_TASKS.md`, including migration, model, factory, policy, routes, components, and integration with patient profiles.

## Thought Process
Phase 2 builds on the completed Patient Phase 1 by adding a 1-to-1 socioeconomic profile per patient, with stricter authorization (patients can view but not edit) and a richer data model (demographics, economic status, lifestyle, support systems, food security).
The `_docs` file already provides detailed TDD specs and example tests; this task tracks implementing that entire feature group once Phase 1 is stable.

## Implementation Plan
- Implement the socioeconomic database migration and Eloquent model with 1–1 relationship to Patient.
- Add a factory, policy, and routes for viewing/managing socioeconomic data.
- Build Livewire components and views for viewing and creating/editing socioeconomic information, integrated into the patient profile UI.
- Add comprehensive Pest tests following the patterns and examples in the `_docs` specs.
- Update `_docs/_CURRENT_STATE.md`, Memory Bank, and any dashboards once Phase 2 is complete.

## Progress Tracking

**Overall Status:** Not Started - 0%

### Subtasks
| ID | Description | Status | Updated | Notes |
|----|-------------|--------|---------|-------|
| 5.1 | Implement migration, model, and relationships for socioeconomic data | Not Started | 2025-12-24 | Follow field list and constraints in `_docs` |
| 5.2 | Add factory and policy with appropriate authorization rules | Not Started | 2025-12-24 | Patients view-only; admin/doktor manage |
| 5.3 | Create Livewire components and views and integrate with patient profile | Not Started | 2025-12-24 | Reuse layout patterns from Patient components |
| 5.4 | Add full test coverage and update docs/Memory Bank | Not Started | 2025-12-24 | Use TDD examples from `_docs` |

## 📝 Expanded Task Specification

### 🎯 Goal
Add a 1-to-1 socioeconomic profile for each patient, with rich data fields, strict authorization, and seamless integration into patient management.

### 📚 Key Concepts
- Eloquent relationships (1-to-1)
- Socioeconomic data modeling
- Authorization (view-only for patients, manage for admin/doktor)
- Livewire components
- Blade templating
- TDD with Pest
- Migration, factory, policy, routes

### 📝 TDD Approach
#### Step 1: Write Tests First (RED)
**File:** `tests/Feature/SocioeconomicExtensionTest.php`
**Command to create:**
```bash
php artisan make:test SocioeconomicExtensionTest
```
**Example Tests:**
```php
<?php
use App\Models\Patient;
use App\Models\User;
use App\Models\SocioeconomicProfile;
beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->doktor = User::factory()->create(['role' => 'doktor']);
    $this->patientUser = User::factory()->create(['role' => 'pacijent']);
    $this->patient = Patient::factory()->create(['user_id' => $this->patientUser->id]);
});
test('admin can create socioeconomic profile', function () {
    $this->actingAs($this->admin)
        ->post("/patients/{$this->patient->id}/socioeconomic", [
            // TODO: Add socioeconomic fields
        ])
        ->assertRedirect()
        ->assertSessionHas('status', 'Socioeconomic profile created');
});
// TODO: Test doktor can manage profile
// TODO: Test patient can view but not edit
// TODO: Test validation and required fields
// TODO: Test integration with patient profile view
```
**Run tests (should FAIL):**
```bash
php artisan test --filter=SocioeconomicExtension
```

#### Step 2: Implement Feature (GREEN)
- Migration: database/migrations/create_socioeconomic_profiles_table.php
- Model: app/Models/SocioeconomicProfile.php
- Factory: database/factories/SocioeconomicProfileFactory.php
- Policy: app/Policies/SocioeconomicProfilePolicy.php
- Routes: patients.socioeconomic.*
- Livewire: app/Livewire/Patient/SocioeconomicProfile.php
- Blade: resources/views/livewire/patient/socioeconomic-profile.blade.php

#### Step 3: Run and Refactor (REFACTOR)
- Run: php artisan test --filter=SocioeconomicExtension
- Manual: Create/view/edit socioeconomic profile, check integration and authorization

### 🧠 Why This Way?
- 1-to-1 relationship keeps data organized
- Strict authorization protects sensitive info
- TDD ensures reliability and coverage
- Integration with patient profile improves UX

### ✅ Verification
```bash
# Run tests
php artisan test --filter=SocioeconomicExtension
# Manual test
# 1. Login as admin/doktor/pacijent
# 2. Create/view/edit socioeconomic profile
# 3. Confirm patients can only view
# 4. Confirm admin/doktor can manage
# 5. Check integration with patient profile
```

## Progress Log
### 2025-12-24
- Created TASK005 to track implementation of Patient Socioeconomic Extension (Phase 2) as a follow-up to Phase 1 core patient features.
- Captured the high-level steps and constraints from `_docs/_Feature/2_PATIENT_MANAGEMENT_FEATURE_TASKS.md`.
