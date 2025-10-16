# Active Context

**Last Updated:** 2025-12-24

## Current Focus
- Working on branch `feature/patient_management`, focusing on Feature Group 2 (Patient Management) Phase 1.
- Implementing and wiring up the remaining patient Livewire components (view, edit, delete, navigation) with policies and tests.

## Recent Changes
- Completed Feature Group 1 (User Management) including full CRUD, authorization policies, UI, and tests.
- Implemented Patient Phase 1 tasks 1–7: migration, model/relationships, factory, policy, routes, patient list component (pagination + search), and create-patient component.
- Documented detailed state and learning-mode guidelines in `_docs/_CURRENT_STATE.md` and `_docs/_Feature/2_PATIENT_MANAGEMENT_FEATURE_TASKS.md`.

## Next Steps
- Complete Patient Management Phase 1 tasks 8–11: ViewPatient component, EditPatient component, delete flow, and navigation integration.
- After Phase 1, implement the Socioeconomic extension (Phase 2) using the specs in `2_PATIENT_MANAGEMENT_FEATURE_TASKS.md`.
- Later, build the remaining clinical MVP feature groups (visits, vitals, labs, medications, recommendations, reminders, dashboards) once patient management is stable.

## Active Decisions
- Use a vertical-slice, TDD workflow (DB → model/factory → policy → routes → Livewire → view → tests) for each patient-related feature.
- Honor the learning-mode rules from `_docs/_CURRENT_STATE.md` and `_docs/LEARNING_MODE.md`: AI acts as a teacher/spec writer while the developer writes the main code and remaining tests.
- Keep documentation in `_docs` and the Memory Bank synchronized at the end of each working session.
