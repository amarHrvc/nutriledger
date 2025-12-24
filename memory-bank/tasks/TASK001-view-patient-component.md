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

## Progress Log
### 2025-12-24
- Created TASK001 to track implementation of Patient Management Phase 1 Task 8 (View Patient Profile Component).
- Captured the vertical-slice/TDD expectations from `_docs/_CURRENT_STATE.md` and patient feature docs.
