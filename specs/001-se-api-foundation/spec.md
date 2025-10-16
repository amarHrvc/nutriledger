# Feature Specification: SE API Foundation

**Feature Branch**: `001-se-api-foundation`
**Created**: 2026-03-15
**Status**: Draft
**Type**: BE/FE
**Input**: User description: "SE API Foundation — foundational BE/FE infrastructure layer for NutriLedger pivot to Laravel REST API + React SPA."

## User Scenarios & Testing *(mandatory)*

> **Note**: This feature is infrastructure-facing. The "users" are API consumers (the React
> SPA and developers). Stories describe the observable contracts each consumer depends on.

### User Story 1 - Token Authentication (Priority: P1)

A client application needs to establish an authenticated session. The user submits credentials,
receives an access token, and uses it on all subsequent requests. When they are done, they
revoke the token. The entire lifecycle must work before any domain endpoint can be built.

**Why this priority**: Authentication is the gateway to every protected endpoint. No child
feature (users, patients, visits) can be safely tested or demonstrated without it.

**Independent Test**: Send login, identity-fetch, and logout requests in sequence and verify
the token lifecycle end-to-end — no domain endpoints required.

**Acceptance Scenarios**:

1. **Given** valid credentials are submitted, **When** a login request is made, **Then** a token is returned and the authenticated user's identity can be retrieved with it.
2. **Given** invalid credentials are submitted, **When** a login request is made, **Then** the response indicates authentication failure and no token is issued.
3. **Given** an active token, **When** a logout request is made, **Then** the token is invalidated and any subsequent request using it is rejected as unauthenticated.
4. **Given** a request to a protected endpoint with no token, **When** the server processes it, **Then** the response signals unauthenticated — never a redirect or HTML page.

---

### User Story 2 - Role-Gated Access Control (Priority: P2)

Authenticated clients must be restricted to only the endpoints their role permits. Admins,
doctors, and patients have different permission levels. Attempts to cross role boundaries
are rejected with a clear, consistent signal — not a redirect or ambiguous error.

**Why this priority**: Role enforcement at the routing level is required before any domain
resource can be safely exposed. Without it, every child feature would define its own
access control baseline independently.

**Independent Test**: Make requests to role-protected endpoints using tokens for each role
(admin, doktor, pacijent) and verify the correct permit/reject outcome for each.

**Acceptance Scenarios**:

1. **Given** an authenticated user with the correct role, **When** they access a role-restricted endpoint, **Then** the request is permitted and a valid response is returned.
2. **Given** an authenticated user with an insufficient role, **When** they access a role-restricted endpoint, **Then** the response signals forbidden — never a redirect.
3. **Given** any request to a role-restricted endpoint without authentication, **When** the server processes it, **Then** the response signals unauthenticated — not forbidden.

---

### User Story 3 - Consistent Response Contracts (Priority: P3)

A client developer handling API responses needs to trust a predictable response shape
regardless of which endpoint they call. Successes, validation errors, authorization
failures, and not-found responses all share the same JSON structure so client-side
error handling can be written once and applied everywhere.

**Why this priority**: Without a shared contract, each child feature invents its own format,
making client-side error handling brittle and inconsistent across the SPA.

**Independent Test**: Trigger each response type (success, validation failure, unauthorized,
forbidden, not found) and verify all conform to the same envelope structure.

**Acceptance Scenarios**:

1. **Given** any successful request, **When** the server responds, **Then** the response contains a `data` field with the result and a `message` field.
2. **Given** a request with invalid input, **When** the server responds, **Then** the response contains field-level errors under an `errors` field in the same envelope as all other responses.
3. **Given** a request that fails authorization, **When** the server responds, **Then** the response uses the same envelope structure — not a plain string or different shape.
4. **Given** a request for a resource that does not exist, **When** the server responds, **Then** the response uses the same envelope with an appropriate message.

---

### User Story 4 - Cross-Origin SPA Connectivity (Priority: P4)

The React SPA running in a browser at a different origin must be able to make requests to
the API without being blocked by browser security policies. A developer running the SPA
locally can verify the connection works using a health-check endpoint before any feature
work begins.

**Why this priority**: Cross-origin misconfiguration silently breaks the entire SPA in
browsers. Verifying it with a live browser request before child feature work prevents
wasted debugging time later.

**Independent Test**: Run the SPA scaffold locally, open a browser, and confirm the
health-check request succeeds with no cross-origin errors in the console.

**Acceptance Scenarios**:

1. **Given** the SPA is running locally and the API is running locally, **When** the SPA makes a request to the health-check endpoint, **Then** the response arrives with no cross-origin browser errors.
2. **Given** the API receives a preflight request from the SPA's local origin, **When** it processes it, **Then** it responds with headers that permit the cross-origin request.
3. **Given** the SPA scaffold is cloned and dependencies installed, **When** the development server is started, **Then** it starts without errors and the health-check request succeeds.

---

### Edge Cases

- What happens when an expired or tampered token is used? The response must be identical to a missing-token response — no leakage about why the token was rejected.
- What happens when the API is unreachable from the SPA? The SPA scaffold must not crash — a connection error should be logged without breaking the page.
- What happens when a role-protected route receives a valid token for an unexpected role? The response must be forbidden, not a server error.
- What happens when the same token is used after logout? It must be rejected as unauthenticated — revocation must be immediate.
- What happens when login is attempted with a missing field? The response must signal validation failure with field-level errors, not an authentication failure.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST accept email and password credentials and return a revocable access token on successful authentication.
- **FR-002**: System MUST reject invalid credentials with a machine-readable unauthenticated response containing no token.
- **FR-003**: System MUST revoke an active token immediately when a logout request is made by its owner.
- **FR-004**: System MUST return the authenticated user's identity (id, name, email, role) in the standard response envelope when queried with a valid token.
- **FR-005**: System MUST reject requests to protected endpoints that carry no valid token with a machine-readable unauthenticated response — never an HTML page or redirect.
- **FR-006**: System MUST reject requests to role-restricted endpoints from users without the required role with a machine-readable forbidden response.
- **FR-007**: System MUST return all responses — success and all error types — in a consistent JSON envelope containing `data`, `message`, and `errors` fields.
- **FR-008**: System MUST return a validation-failure response with field-level errors when required input fields are missing or invalid.
- **FR-009**: System MUST expose a public health-check endpoint that returns a success response without requiring authentication.
- **FR-010**: System MUST respond to cross-origin preflight requests from the SPA's configured development origin with appropriate permission headers.
- **FR-011**: A SPA scaffold MUST be provided that makes a request to the health-check endpoint from a real browser and confirms cross-origin connectivity.
- **FR-012**: The SPA scaffold MUST include environment-based API URL configuration and an HTTP client pre-configured to attach Bearer token credentials.

### Key Entities

- **Access Token**: Represents a single authenticated session for one user. Issued on login, revoked on logout. Used as a credential on protected requests. Must be invalidated immediately on revocation.
- **User** *(pre-existing)*: Identified by email, password, and role (admin, doktor, pacijent). Already implemented in the domain layer — this feature consumes it, does not create it.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: A client can complete the full token lifecycle (login → authenticated request → logout → rejected reuse) with correct responses at every step and no errors.
- **SC-002**: Every API response across all endpoints and all error types conforms to the `{ data, message, errors }` envelope with no exceptions.
- **SC-003**: Requests to protected endpoints without a valid token receive a machine-readable rejection response — never a redirect or HTML content.
- **SC-004**: A browser running the SPA scaffold can reach the API health-check endpoint from a different local port without any cross-origin errors.
- **SC-005**: The SPA scaffold development server starts without errors, makes a successful health-check request, and displays or logs the response.
- **SC-006**: All three automated quality gates pass on every changed file: code style, static analysis, and the affected test suite.

## Assumptions

- The existing domain layer (User model, PatientPolicy, VisitPolicy, RoleMiddleware, form
  requests) is fully functional and all existing tests pass before this feature begins.
- All existing Livewire routes and components remain in place and are not modified.
- The SPA scaffold lives in a `/frontend` folder at the repository root.
- Local development: API on port 8000, SPA dev server on port 5173.
- The SPA uses JavaScript (not TypeScript) per constitution v2.0.1.
- No auth state management is in scope for this feature — the SPA scaffold only proves
  connectivity. Auth state (login form, token storage, protected routes) belongs to the
  next feature (002-users FE track).
