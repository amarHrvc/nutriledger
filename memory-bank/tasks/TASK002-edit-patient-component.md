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

## Progress Log
### 2025-12-24
- Created TASK002 to track implementation of Patient Management Phase 1 Task 9 (Edit Patient Component).
- Documented dependencies on Task 8 (ViewPatient) and existing create-patient behavior.
