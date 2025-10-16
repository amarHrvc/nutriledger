# Progress

**Last Updated:** 2025-12-24

## What Works
- Feature Group 1 (User Management) is fully implemented with CRUD, policies, UI components, and passing tests.
- Patient Management Phase 1 tasks 1–7 are implemented: patient migration, model/relationships, factory, policy, routes, patient list component (with pagination/search), and create-patient component.
- Test suite (over 120 tests plus a few skipped) is passing on this branch; core tooling (Boost, Larastan, Pint, Pest, Debugbar) is configured and in use.

## What's Left to Build
- Finish Patient Management Phase 1 (tasks 8–11): view, edit, delete, and navigation integration for patients.
- Implement the Phase 2 Socioeconomic extension (model, migration, factory, policy, components, routes, tests) as documented in the patient feature tasks.
- Implement the remaining MVP clinical feature groups (visits, vitals, labs, medications, recommendations, reminders, dashboards) and their supporting factories, seeders, and tests.

## Known Issues
- Minor code-quality issues in the CreatePatient component: the form calls `save` while the method is named `createPatient`, and there is no `mount()` authorization check yet.
- Memory Bank and `_docs/_CURRENT_STATE.md` must be updated together as features ship to avoid drift between documentation and actual behavior.
- Progress for non-patient feature groups (visits, labs, medications, recommendations, reminders, dashboards) is still conceptual in `_docs/dev_tasks.md` and not yet reflected in tests or code.
