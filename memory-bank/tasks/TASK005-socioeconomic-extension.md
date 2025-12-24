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

## Progress Log
### 2025-12-24
- Created TASK005 to track implementation of Patient Socioeconomic Extension (Phase 2) as a follow-up to Phase 1 core patient features.
- Captured the high-level steps and constraints from `_docs/_Feature/2_PATIENT_MANAGEMENT_FEATURE_TASKS.md`.
