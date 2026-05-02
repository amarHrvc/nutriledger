# tasks.md — Admin User Management

## Overview
Feature: Admin User Management (specs/008-admin-user-management)
MVP: User Story 1 (Browse and Search Users)

---

Phase 1 — Setup

- [X] T001 Initialize frontend page entry at frontend/src/app/(dashboard)/dashboard/users/page.tsx
- [X] T002 Create BFF route folder at frontend/src/app/api/users/ and add route.ts (GET list, POST create)
- [X] T003 Create BFF route folder at frontend/src/app/api/users/[id]/ and add route.ts, restore/route.ts, force/route.ts (GET show, PATCH update, DELETE soft-delete, POST restore, DELETE force-delete)

Phase 2 — Foundational

- [X] T004 [P] Add Orval typed client import and helper at frontend/src/api/generated/user/user.ts (ensure client used by BFF routes)
- [X] T005 [P] Add shared UI components: frontend/src/views/users/shared/ConfirmDialog.tsx and UserStatusChip.tsx
- [X] T006 [P] Add reusable view orchestrator: frontend/src/views/users/index.tsx (client orchestrator entry)

Phase 3 — US1: Browse and Search Users (Priority: P1)
Independent test: Open Users page as admin; list (including deactivated) shows; search filters by name/email; pagination works.

- [X] T007 [US1] Implement UserList component at frontend/src/views/users/UserList.tsx with search input and pagination wired to BFF GET /api/users
- [X] T008 [US1] Implement server page at frontend/src/app/(dashboard)/dashboard/users/page.tsx to render views/users/index.tsx and pass server-side data via BFF
- [X] T009 [US1] Wire BFF GET list to Orval client in frontend/src/app/api/users/route.ts, accept query params (page, search)
- [X] T010 [US1] Add empty-state UX and loading indicators in frontend/src/views/users/UserList.tsx

Phase 4 — US2: View User Detail (Priority: P2)
Independent test: Click a user row → detail view shows attributes, patient link for pacijent role, deactivated state shows Restore action.

- [ ] T011 [US2] Implement UserDetail component at frontend/src/views/users/UserDetail.tsx (render name, email, role, status, timestamps)
- [ ] T012 [US2] Wire BFF GET show in frontend/src/app/api/users/[id]/route.ts to return full user payload to the detail view

Phase 5 — US3: Create New User (Priority: P3)
Independent test: Submit create form with valid data → new user appears in list; invalid inputs show inline errors.

- [ ] T013 [US3] Create UserForm component at frontend/src/views/users/UserForm.tsx (create mode) with validation and inline error display
- [ ] T014 [US3] Wire BFF POST create in frontend/src/app/api/users/route.ts to call backend create endpoint via Orval client
- [ ] T015 [US3] On success, refresh UserList from frontend/src/views/users/index.tsx and show success feedback/toast

Phase 6 — US4: Edit User (Priority: P3)
Independent test: Open edit for a user, change role or fields, save → list and detail reflect updates.

- [ ] T016 [US4] Extend UserForm component (frontend/src/views/users/UserForm.tsx) to support edit mode with pre-filled values
- [ ] T017 [US4] Wire BFF PATCH update in frontend/src/app/api/users/[id]/route.ts to call backend update via Orval client
- [ ] T018 [US4] Ensure UI updates (list + detail) and shows inline validation errors for conflicts (duplicate email)

Phase 7 — US5: Deactivate and Restore Users (Priority: P4)
Independent test: Deactivate user → remains visible with deactivated status; Restore returns to active.

- [ ] T019 [US5] Implement deactivate action in UserDetail (button + confirmation dialog) at frontend/src/views/users/UserDetail.tsx
- [ ] T020 [US5] Wire BFF DELETE soft-delete in frontend/src/app/api/users/[id]/route.ts to call backend soft-delete endpoint
- [ ] T021 [US5] Implement restore action UI and wire frontend/src/app/api/users/[id]/restore/route.ts to call backend restore endpoint

Phase 8 — US6: Permanently Delete User (Priority: P5)
Independent test: Permanently delete a deactivated user → user removed from lists; option unavailable for active users.

- [ ] T022 [US6] Add Force Delete button in UserDetail (visible only for deactivated users) with ConfirmDialog at frontend/src/views/users/UserDetail.tsx
- [ ] T023 [US6] Wire BFF DELETE force-delete in frontend/src/app/api/users/[id]/force/route.ts to call backend force-delete endpoint

Final Phase — Polish & Cross-Cutting Concerns

- [ ] T024 [P] Add toasts/notifications for success/error feedback (frontend/src/components/Toast or existing notifications integration)
- [ ] T025 [P] Add accessibility checks and minor UI polish: keyboard focus, aria labels for search, confirm dialogs (frontend/src/views/users/**)
- [ ] T026 [P] Update specs/008-admin-user-management/quickstart.md with developer steps and manual verification checklist

---

Dependencies (story completion order):
1. Phase1 Setup (T001-T003) -> Phase2 Foundational (T004-T006) -> US1 (T007-T010) -> US2 (T011-T012) -> US3/US4 (T013-T018) -> US5 (T019-T021) -> US6 (T022-T023) -> Polish (T024-T026)

Parallel opportunities:
- T004, T005, T006 are parallelizable during Foundational phase [P]
- UI component creation (UserStatusChip, ConfirmDialog) can be done in parallel with BFF route scaffolding [P]
- Toasts, accessibility polish (T024-T025) can be parallelized across files [P]

Independent test criteria (per story):
- US1: Paginated list loads; search filters by name/email; empty-state shown; pagination controls work.
- US2: Detail view shows all fields; patient link appears for role `pacijent`; restore available for deactivated.
- US3: Create form validates and creates user; duplicates/missing fields show inline errors.
- US4: Edit persists changed fields only; email conflict shows inline error.
- US5: Deactivate keeps user visible with status; restore returns active state.
- US6: Force delete removes deactivated user and is not visible for active users.

Suggested MVP scope: Implement only US1 (T007-T010) plus Setup/Foundation tasks (T001-T006). This yields a usable admin Users list quickly.

Format validation: All tasks follow the required checklist format with TaskID, optional [P], story labels for story tasks, and explicit file paths.

---

Generated-by: speckit.tasks

