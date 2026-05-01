# Feature Specification: Admin User Management

**Feature Branch**: `008-admin-user-management`  
**Created**: 2026-05-01  
**Status**: Draft  
**Input**: User description: "I want to build users for admin account it should have searchable list of users end users detail as user It should support all planned user operations from the REST endpoint API"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Browse and Search Users (Priority: P1)

An administrator opens the Users section and sees a paginated list of all users in the system, including soft-deleted (deactivated) accounts. They can type in a search box to filter users by name or email without leaving the page. Each row shows the user's name, email, role, and account status (active / deactivated).

**Why this priority**: Without a working list, no other operation (view, edit, delete) is reachable. This is the entry point to all user management.

**Independent Test**: Open the Users page as an admin and verify: all users appear (including deactivated ones), typing a name or email filters the list in real time, and pagination controls work.

**Acceptance Scenarios**:

1. **Given** an admin is on the Users page, **When** the page loads, **Then** a paginated list of all users (active and deactivated) is displayed with name, email, role, and status columns.
2. **Given** an admin types a search term, **When** the search input changes, **Then** the list filters to only rows whose name or email contains the term.
3. **Given** a search returns no results, **When** the list renders, **Then** an empty-state message is shown instead of a blank table.
4. **Given** more users exist than fit on one page, **When** the admin uses pagination controls, **Then** the correct page of results is shown.

---

### User Story 2 - View User Detail (Priority: P2)

An administrator clicks a user row and sees a full detail view: personal information (name, email, role, status), account timestamps, and a link to the associated patient record if the user is a patient.

**Why this priority**: Detail view is required before any mutating action (edit, delete, restore) is meaningful. It also surfaces patient linkage without navigating away.

**Independent Test**: Click any user in the list and confirm all their attributes render correctly, including deactivated status and the patient record link for patient-role users.

**Acceptance Scenarios**:

1. **Given** an admin clicks a user, **When** the detail view opens, **Then** name, email, role, account status, created date, and deleted date (if any) are shown.
2. **Given** the user has role `pacijent`, **When** the detail view is shown, **Then** a link or section for the associated patient record is visible.
3. **Given** the user is soft-deleted, **When** the detail view is shown, **Then** the deactivated status is clearly indicated and a Restore action is available.

---

### User Story 3 - Create New User (Priority: P3)

An administrator creates a new user by providing name, email, password, password confirmation, and role. On success the new user appears in the list. Validation errors are shown inline if any field is invalid.

**Why this priority**: User creation is an administrative capability; the system already has seed users for development, so it is less urgent than read and edit operations.

**Independent Test**: Submit the create form with valid data and confirm the new user appears in the list. Submit with missing or duplicate email and confirm inline error messages appear.

**Acceptance Scenarios**:

1. **Given** an admin submits valid name, email, password, confirmation, and role, **When** the form is submitted, **Then** the user is created and the list refreshes to include the new entry.
2. **Given** an admin submits an email already in use, **When** the form is submitted, **Then** an inline error on the email field states the email is already taken.
3. **Given** an admin submits mismatched passwords, **When** the form is submitted, **Then** an inline error on the confirmation field states the passwords do not match.
4. **Given** any required field is left blank, **When** the form is submitted, **Then** inline validation errors appear for each missing field.

---

### User Story 4 - Edit User (Priority: P3)

An administrator edits an existing user's name, email, role, or password. Fields are pre-filled with current values. Only changed fields are submitted. On success the detail view reflects the updated values.

**Why this priority**: Tied with Create in importance; both cover the write operations for active accounts.

**Independent Test**: Change the role of an existing user, save, and confirm the role column in the list and the detail view both reflect the change.

**Acceptance Scenarios**:

1. **Given** an admin opens the edit form for a user, **When** the form appears, **Then** all current values (name, email, role) are pre-filled.
2. **Given** an admin changes only the role and submits, **When** the update completes, **Then** only the role is changed; name and email are unchanged.
3. **Given** an admin enters an email already used by another user, **When** the form is submitted, **Then** an inline error on the email field is shown.

---

### User Story 5 - Deactivate and Restore Users (Priority: P4)

An administrator soft-deletes (deactivates) an active user. The user remains visible in the list with a deactivated status. An administrator can restore a deactivated user, returning them to active status.

**Why this priority**: Deactivation is safer than permanent deletion and covers the most common account lifecycle event. Restore is its logical complement.

**Independent Test**: Deactivate an active user and confirm they remain in the list with deactivated status. Then restore them and confirm they return to active status.

**Acceptance Scenarios**:

1. **Given** an admin deactivates an active user, **When** the action is confirmed, **Then** the user's status changes to deactivated and they remain visible in the list.
2. **Given** an admin views a deactivated user's detail, **When** the Restore action is triggered, **Then** the user's status returns to active.
3. **Given** a deactivate action is triggered, **When** a confirmation prompt is shown, **Then** the action only proceeds after the admin confirms.

---

### User Story 6 - Permanently Delete User (Priority: P5)

An administrator permanently deletes a deactivated user. This action is irreversible. A confirmation step is required before proceeding.

**Why this priority**: Permanent deletion is a destructive, low-frequency action. It is lower priority than all other operations and must be protected from accidental use.

**Independent Test**: Attempt to permanently delete a deactivated user, complete the confirmation step, and verify the user no longer appears in any list view.

**Acceptance Scenarios**:

1. **Given** an admin triggers permanent deletion on a deactivated user, **When** the confirmation step is presented, **Then** the action only proceeds after the admin explicitly confirms.
2. **Given** the permanent deletion completes, **When** the admin returns to the list, **Then** the user is no longer present in any list view.
3. **Given** a user is still active (not soft-deleted), **When** the admin views their detail, **Then** the permanent delete option is not available.

---

### Edge Cases

- What happens when search returns zero results?
- How does the list behave when all existing users are deactivated?
- What if an admin attempts to deactivate their own account?
- What if the API returns an error during a mutating action (network failure, permission denied)?
- What if a `pacijent`-role user has no associated patient record yet?
- What happens if an admin tries to permanently delete a user who is not yet deactivated?

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The system MUST display a paginated list of all users (including deactivated) visible only to administrators.
- **FR-002**: The system MUST allow filtering the user list by name or email via a search input.
- **FR-003**: The system MUST display a user detail view showing: name, email, role, account status, created date, and deactivation date if applicable.
- **FR-004**: The system MUST show a link or section for the associated patient record when the user's role is `pacijent`.
- **FR-005**: The system MUST allow an administrator to create a new user by providing name, email, password, password confirmation, and role (`admin`, `doktor`, or `pacijent`).
- **FR-006**: The system MUST display inline validation errors for: duplicate email, mismatched passwords, and missing required fields.
- **FR-007**: The system MUST allow an administrator to update a user's name, email, role, and/or password.
- **FR-008**: The system MUST allow an administrator to soft-delete (deactivate) an active user after confirmation.
- **FR-009**: The system MUST allow an administrator to restore a deactivated user.
- **FR-010**: The system MUST allow an administrator to permanently delete a deactivated user after a confirmation step.
- **FR-011**: The permanent delete action MUST NOT be available for active (non-deactivated) users.
- **FR-012**: Deactivated users MUST remain visible in the list with a clear status indicator rather than being hidden.
- **FR-013**: All mutating actions (create, update, deactivate, restore, force delete) MUST show success or error feedback to the administrator.

### Key Entities

- **User**: Represents a system account with name, email, role (`admin` / `doktor` / `pacijent`), account status (active / deactivated), created date, and optional deactivation date.
- **Patient**: One-to-one companion record for users with role `pacijent`; surfaces on the user detail view as a navigable link.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: An administrator can locate any user by name or email within 10 seconds using the search input.
- **SC-002**: All seven user operations (list, view, create, edit, deactivate, restore, force delete) are reachable from the admin Users section without navigating outside it.
- **SC-003**: Creating or updating a user produces visible confirmation feedback within 2 seconds of form submission.
- **SC-004**: Attempting a destructive action (deactivate, force delete) without completing the required confirmation step never results in data loss.
- **SC-005**: The user list correctly reflects state changes (status, role) within one interaction cycle — no full page reload required.

## Assumptions

- Only administrators can access this section; doctors and patients have no access.
- Search filters the current page result set; a dedicated server-side search endpoint is not required for MVP.
- The confirmation for permanent deletion is a modal with an explicit confirm button.
- Pagination page size follows the server default (15 per page).
- Role change for a `pacijent`-role user does not automatically create or destroy the associated patient record; that is out of scope for this feature.
