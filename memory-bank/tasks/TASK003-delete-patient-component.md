# TASK003 - Delete Patient Component / Flow

**Status:** Pending  
**Added:** 2025-12-24  
**Updated:** 2025-12-24

## Original Request
Implement **Task 10: Delete Patient Component** from `_docs/_Feature/2_PATIENT_MANAGEMENT_FEATURE_TASKS.md`, adding a soft-delete flow with confirmation modal to the patient list.

## Thought Process
The `_docs` spec defines a soft-delete behavior using Livewire events and a confirmation modal, consistent with the existing user delete flow.
Deletion must respect `PatientPolicy@delete`, soft-delete the patient (not hard delete), and provide user feedback and test coverage.

## Implementation Plan
- Add Pest tests for authorized/unauthorized delete attempts, soft-delete behavior, and list refresh.
- Extend `App\Livewire\Patient\PatientList` with a `deletePatient` action that authorizes and soft-deletes the record.
- Add a confirmation modal (Alpine + Livewire events) to the patient list Blade view.
- Ensure flash messages and UI updates are tested and documented.
- Sync `_docs/_CURRENT_STATE.md` and Memory Bank when the delete flow is stable.

## Progress Tracking

**Overall Status:** Not Started - 0%

### Subtasks
| ID | Description | Status | Updated | Notes |
|----|-------------|--------|---------|-------|
| 3.1 | Write Pest tests for delete authorization and soft-delete behavior | Not Started | 2025-12-24 | Mirror patterns from user delete tests |
| 3.2 | Implement `deletePatient` method in `PatientList` with policy checks | Not Started | 2025-12-24 | Use `Patient::findOrFail` + `authorize('delete', $patient)` |
| 3.3 | Add confirmation modal and delete button wiring in Blade view | Not Started | 2025-12-24 | Use Livewire events + Alpine as in `_docs` spec |
| 3.4 | Verify UX (flash message, list refresh) and update documentation | Not Started | 2025-12-24 | Confirm behavior for admin and doktor roles |

## Progress Log
### 2025-12-24
- Created TASK003 to track implementation of Patient Management Phase 1 Task 10 (Delete Patient Component / flow).
- Captured the requirement for soft-delete and confirmation modal consistent with user deletion.
