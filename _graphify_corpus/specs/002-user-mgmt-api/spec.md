# Feature Specification: Auth & User Management API

**Feature Branch**: `002-user-mgmt-api`
**Created**: 2026-03-19
**Status**: Draft
**Input**: Group 1 — Auth & User Management API (NutriLedger SE track)

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Admin Authenticates and Views Own Profile (Priority: P1)

A system administrator logs in with their credentials and receives an access token. While logged in, they can view their own profile at any time. When they are done, they log out and the token is invalidated.

**Why this priority**: Authentication is the gateway to all other operations. Without a working login/logout/profile flow, no other user management work can proceed.

**Independent Test**: Can be tested by registering an admin, logging in, fetching own profile, and logging out — all without needing any other user management feature.

**Acceptance Scenarios**:

1. **Given** valid credentials, **When** a user submits a login request, **Then** they receive an access token and their profile data
2. **Given** an authenticated user, **When** they request their own profile, **Then** they receive their current account details excluding password
3. **Given** an authenticated user with a valid token, **When** they submit a logout request, **Then** the token is revoked and cannot be reused
4. **Given** invalid credentials, **When** a user submits a login request, **Then** they receive an authentication failure response
5. **Given** no token or an invalid token, **When** a request reaches a protected endpoint, **Then** access is denied
6. **Given** valid credentials belonging to a deactivated account, **When** a user submits a login request, **Then** they receive an authentication failure response

---

### User Story 2 - Admin Lists and Views All User Accounts (Priority: P1)

An administrator needs to see all user accounts in the system, including those that have been deactivated, to maintain oversight of who has access.

**Why this priority**: Listing and viewing users is the most fundamental read operation for administration, required before any create/update/delete workflows.

**Independent Test**: Can be tested by seeding users with different roles and states, then verifying the admin can retrieve the complete list and individual profiles.

**Acceptance Scenarios**:

1. **Given** an authenticated admin, **When** they request the user list, **Then** they receive a paginated list of all users including deactivated ones
2. **Given** an authenticated admin, **When** they request a specific user by their identifier, **Then** they receive that user's full profile excluding sensitive fields
3. **Given** an authenticated non-admin (doctor or patient), **When** they request the user list, **Then** access is denied
4. **Given** an identifier that does not correspond to any user, **When** an admin requests that user, **Then** they receive a not-found response

---

### User Story 3 - Admin Creates a New User Account (Priority: P1)

An administrator creates a new user account, assigning a role and providing credentials. The new user can immediately log in after the account is created.

**Why this priority**: User creation is a core administrative action enabling clinical staff onboarding; it must work before update and delete flows can be meaningfully tested.

**Independent Test**: Can be tested by having an admin create a user, then verifying the new account exists and can authenticate successfully.

**Acceptance Scenarios**:

1. **Given** an authenticated admin with valid user data, **When** they submit a create request, **Then** a new user account is created and the account details are returned
2. **Given** an authenticated admin with a duplicate email address, **When** they submit a create request, **Then** a validation error is returned identifying the conflicting field
3. **Given** an authenticated admin with missing required fields, **When** they submit a create request, **Then** a validation error identifies all missing fields
4. **Given** an authenticated non-admin, **When** they attempt to create a user, **Then** access is denied

---

### User Story 4 - Admin Updates a User Account (Priority: P2)

An administrator updates an existing user's name, email, password, or role. Changes take effect immediately.

**Why this priority**: Update is secondary to create; a user account must exist before it can be updated.

**Independent Test**: Can be tested by creating a user, updating individual fields, and confirming the changes are reflected on the next profile fetch.

**Acceptance Scenarios**:

1. **Given** an authenticated admin and an existing user, **When** they submit a partial or full update, **Then** only the provided fields are changed and the rest remain unchanged
2. **Given** an admin updating a user's email to one already in use by another account, **When** they submit the update, **Then** a validation error is returned
3. **Given** an admin updating a user's password, **When** the update succeeds, **Then** the new password is stored and the old one no longer works for authentication
4. **Given** an authenticated non-admin, **When** they attempt to update a user, **Then** access is denied

---

### User Story 5 - Admin Deactivates and Restores User Accounts (Priority: P2)

An administrator deactivates a user account to revoke access without permanently removing the data. The account can later be restored if access needs to be reinstated.

**Why this priority**: Soft-delete is a data-safety requirement that must be validated independently before the irreversible force-delete operation is introduced.

**Independent Test**: Can be tested by creating a user, deactivating them, verifying they still appear in admin lists with their data intact, then restoring and confirming the account is active again.

**Acceptance Scenarios**:

1. **Given** an active user account, **When** an admin deactivates it, **Then** the account is marked inactive but its data is preserved and visible to admins
2. **Given** a deactivated user, **When** an admin restores it, **Then** the account becomes active again
3. **Given** an admin's own account, **When** the admin attempts to deactivate it, **Then** the request is rejected with an appropriate error
4. **Given** an authenticated non-admin, **When** they attempt to deactivate any user, **Then** access is denied

---

### User Story 6 - Admin Permanently Deletes a User Account (Priority: P3)

An administrator permanently removes a user account from the system when it is no longer needed and data retention is not required.

**Why this priority**: Permanent deletion is an irreversible destructive action and lower priority than the recoverable soft-delete and restore flow.

**Independent Test**: Can be tested independently by creating a user, permanently deleting them, and confirming the record no longer exists in any listing.

**Acceptance Scenarios**:

1. **Given** an existing user (active or deactivated), **When** an admin permanently deletes them, **Then** the account is removed and can no longer be found
2. **Given** an authenticated non-admin, **When** they attempt to permanently delete a user, **Then** access is denied

---

### User Story 7 - New User Self-Registration (Priority: P2)

A new user registers for the platform by providing their name, email, and password. They receive a token on successful registration and can immediately use the system.

**Why this priority**: Self-registration enables onboarding without requiring an admin to manually create accounts, expanding system accessibility.

**Independent Test**: Can be tested by submitting registration data and verifying the user account is created and a valid token is returned.

**Acceptance Scenarios**:

1. **Given** a valid name, email, and password, **When** a user submits a registration request, **Then** a new account is created and a token is returned
2. **Given** an email already in use, **When** a user attempts to register, **Then** a validation error identifies the conflict
3. **Given** a password confirmation that does not match the password, **When** a user submits registration, **Then** a validation error is returned
4. **Given** missing required fields, **When** a user submits registration, **Then** validation errors identify each missing field

---

### Edge Cases

- What happens when an admin attempts to deactivate their own account?
- What happens when a deactivated user attempts to log in? → Login is rejected with an authentication failure; deactivation blocks all access.
- What happens when a client requests a user that has been soft-deleted (deactivated)?
- How does the system respond when a revoked, tampered, or expired token is used? → Request is rejected with an authentication failure response.
- What happens when an update request changes only the role, leaving all other fields unchanged?
- How does the system handle a registration where the optional role field contains an unrecognized value?
- What happens when a permanently deleted user's identifier is requested?
- What happens when a new user attempts to register with the email of a permanently deleted account? → The email is available and registration succeeds.
- What happens when the user list is requested and no users exist?
- What happens when a source exceeds the failed login attempt threshold? → The request is throttled and a rate-limit response is returned; applies to the login endpoint only.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST allow a user to authenticate with email and password and receive a session token
- **FR-002**: System MUST allow an authenticated user to invalidate their own session token
- **FR-003**: System MUST allow any authenticated user to retrieve their own account profile excluding sensitive fields
- **FR-004**: System MUST allow new users to register an account with name, email, password, and optional role
- **FR-005**: System MUST allow only users with the admin role to list all user accounts
- **FR-006**: System MUST include deactivated accounts in admin user listings
- **FR-007**: System MUST allow only users with the admin role to view any individual user account
- **FR-008**: System MUST allow only users with the admin role to create new user accounts with an assigned role
- **FR-009**: System MUST allow only users with the admin role to update any user account's name, email, password, or role
- **FR-010**: System MUST allow only users with the admin role to deactivate a user account without destroying its data
- **FR-011**: System MUST prevent an admin from deactivating their own account
- **FR-012**: System MUST preserve all data for deactivated accounts
- **FR-013**: System MUST allow only users with the admin role to restore a deactivated user account to active status
- **FR-014**: System MUST allow only users with the admin role to permanently remove a user account
- **FR-015**: System MUST paginate user listing results
- **FR-016**: System MUST return consistent structured responses with a message, status code, and data payload for all operations
- **FR-017**: System MUST never include password values in any response payload
- **FR-018**: System MUST return descriptive validation errors identifying specific fields when input is invalid
- **FR-019**: System MUST return an authentication failure response for requests with missing, invalid, or revoked tokens on protected operations
- **FR-020**: System MUST return an authorization failure response when a non-admin attempts any user management operation
- **FR-021**: System MUST reject login attempts from deactivated user accounts with an authentication failure response
- **FR-022**: System MUST expire authentication tokens after a fixed absolute duration; expired tokens MUST be rejected on any protected endpoint
- **FR-023**: System MUST throttle repeated failed login attempts from the same source and return a rate-limit response when the threshold is exceeded
- **FR-024**: System MUST release an email address for re-registration once the account it belongs to has been permanently deleted
- **FR-025**: System MUST log security-relevant events (failed login attempts, account deactivations, token revocations, permanent deletions) with event type and outcome; log entries MUST NOT include passwords or other sensitive credential values

### Key Entities

- **User Account**: A registered system identity with a display name, unique email address, role, and credentials. Supports deactivation (data-preserving suspension) and permanent removal. Linked to a patient record when the role is patient.
- **User Role**: Determines the access scope of an account. Three values: admin (full system access), doctor (clinical access), patient (self-access only).
- **Authentication Token**: A credential issued at login or registration, tied to a specific user session. Expires after a fixed absolute duration. Becomes invalid upon logout or expiry. Future: sliding idle-period expiry.
- **User Profile**: The non-sensitive view of a user account — display name, email, role, account timestamps, and linked patient reference when applicable.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: All 11 user management operations (register, login, logout, view own profile, list users, create user, view user, update user, deactivate user, restore user, permanently delete user) return expected results for authorized users
- **SC-002**: 100% of user management requests from non-admin authenticated users are rejected with an access denial response
- **SC-003**: Deactivated users remain present in admin listings with all data intact after deactivation
- **SC-004**: An admin's own account cannot be deactivated — the attempt is rejected in every case
- **SC-005**: No response payload from any endpoint includes a password value
- **SC-006**: All validation failures identify the specific field(s) that failed and the reason, without leaking system internals
- **SC-007**: A minimum of 25 automated tests cover happy paths, authorization failures, validation failures, and edge cases — all pass in a clean environment
- **SC-008**: All responses share a consistent envelope structure with message, status, and data fields
- **SC-009**: A user who registers receives a working authentication token in the same response

## Assumptions

- Registration is a public endpoint requiring no prior authentication
- If role is not provided during registration, the account defaults to the patient role
- Deactivated users are visible in admin user listings alongside active users
- User listing returns paginated results with a standard page size
- Email addresses are treated as unique identifiers (case-insensitive matching assumed)
- Passwords must meet a minimum length requirement; no additional complexity rules are enforced in this version
- The admin performing a deactivation cannot target themselves; no restriction is placed on one admin deleting another

## Dependencies

**This feature depends on:**
- Token-based authentication infrastructure being installed and configured
- Role-based access control middleware being registered and functional
- User model with soft-delete support being in place
- Consistent API response envelope (message, status, data) being available to all controllers

**This feature blocks:**
- Frontend user management UI — cannot be built without working API endpoints
- Patient management API — patient accounts are linked to user accounts created here
- Visit management API — visits are performed by doctors whose accounts are managed here

## Out of Scope

- Self-service profile updates (a user updating their own name, email, or password) — separate feature
- Password reset via email — handled by platform authentication infrastructure
- Email verification — handled by platform authentication infrastructure
- Two-factor authentication — handled by platform authentication infrastructure
- User avatar or profile image uploads — future enhancement
- Structured queryable audit log for all user management operations — planned future enhancement; initial implementation covers minimal security-event logging only
- API versioning — not required for current project phase
- Sliding idle-period token expiry (activity-based) — planned future enhancement; initial implementation uses fixed absolute duration only

## Clarifications

### Session 2026-03-19

- Q: Can a deactivated user still authenticate? → A: No — login attempt returns an authentication failure
- Q: Do authentication tokens expire automatically? → A: Yes — fixed absolute duration initially; sliding idle-period expiry planned as future enhancement
- Q: Is rate limiting required for login? → A: Yes — throttle repeated failed attempts from the same source on the login endpoint only
- Q: Can a permanently deleted account's email be re-registered? → A: Yes — permanent deletion fully releases the email address
- Q: What observability is required? → A: Minimal security-event logging (failed login, deactivation, token revocation, permanent deletion) with no PII; structured audit log deferred to a later feature
