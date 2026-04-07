# Feature Specification: Setup Scramble API Documentation

**Feature Branch**: `004-scramble-api-docs`
**Created**: 2026-04-07
**Status**: Draft
**Input**: User description: "we have discussed possibility of API documentation so i want to setup Scramble so i can have live docs for future usage with Orval for FE"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Browse Live API Documentation (Priority: P1)

A developer (you or the SE partner) opens a browser and navigates to the live API docs endpoint. They see all available endpoints grouped by resource (patients, users, auth), with request schemas, response shapes, required fields, and authentication requirements — all inferred automatically from existing routes and code. No manual annotation was needed.

**Why this priority**: This is the core deliverable. Everything else depends on the spec being live and accurate.

**Independent Test**: Start the backend, hit the docs URL in a browser — interactive Swagger UI loads with all API endpoints visible and documented.

**Acceptance Scenarios**:

1. **Given** the backend is running, **When** a developer navigates to the API docs URL, **Then** an interactive documentation page loads showing all API endpoints
2. **Given** the docs page is open, **When** the developer inspects any endpoint, **Then** they see the correct request body schema (from Form Request rules), response shape (from Resource), and required authentication
3. **Given** the docs page is open, **When** the developer inspects any authenticated endpoint, **Then** it shows Bearer token as the required auth scheme

---

### User Story 2 - Consume Live Spec for Frontend Code Generation (Priority: P2)

A developer runs the frontend code generation tool pointed at the live spec URL. It reads the spec and generates fully typed API client code (hooks, interfaces) matching every current backend endpoint — without manually writing or maintaining any client code.

**Why this priority**: This is the downstream value of the docs. Once the spec is live and accurate, the frontend client can be generated from it at any time.

**Independent Test**: With backend running, run the code generation tool from the frontend directory — it completes without errors and produces typed output files for each resource group.

**Acceptance Scenarios**:

1. **Given** the backend is running with a valid spec endpoint, **When** the frontend code generation tool is run, **Then** it exits successfully and produces client files in the designated output directory
2. **Given** generated client files exist, **When** a developer inspects them, **Then** each resource (patients, users, auth) has its own file with typed interfaces matching the backend response shapes
3. **Given** a backend route or resource changes, **When** the code generation tool is re-run, **Then** the generated files reflect the change and the TypeScript compiler surfaces any breaking changes in consuming components

---

### User Story 3 - Export Static Spec File (Priority: P3)

A developer exports the OpenAPI spec to a static file for offline use, sharing with external stakeholders, or as input to tools that cannot consume a live URL.

**Why this priority**: Useful but not required for the core workflow. The live URL covers all primary use cases.

**Independent Test**: Run the export command — a valid OpenAPI JSON file is produced in the expected output path.

**Acceptance Scenarios**:

1. **Given** the backend is set up, **When** the export command is run, **Then** a valid OpenAPI 3.1 JSON file is produced at the designated path
2. **Given** the exported file exists, **When** it is loaded into a compatible tool, **Then** all endpoints are visible and correctly documented

---

### Edge Cases

- Routes without a corresponding Form Request: Scramble emits an empty or minimal request body schema for that endpoint. No annotation is required; the absence of a Form Request is reflected as-is.
- Docs endpoints in production: The `/docs/api` and `/docs/api.json` endpoints are not registered in production (`App::isProduction()` gate). Requests return 404. This is intentional.
- Frontend code generation tool run while backend is offline: Orval exits with a network error. The fix is to start the backend first before running `pnpm run api:generate`.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The backend MUST expose a live OpenAPI 3.1 spec at a documented URL while running
- **FR-002**: The spec MUST include all API endpoints registered under the authenticated API route group
- **FR-003**: The spec MUST correctly reflect request body schemas derived from existing Form Request validation rules
- **FR-004**: The spec MUST correctly reflect response shapes derived from existing Eloquent Resources
- **FR-005**: The spec MUST document Bearer token as the required authentication scheme for protected endpoints
- **FR-006**: The backend MUST expose an interactive documentation UI at a documented URL
- **FR-007**: The live spec URL MUST be usable directly as input to the frontend code generation tool without exporting to a file first
- **FR-008**: The backend MUST support exporting the spec to a static file on demand
- **FR-009**: The docs and spec endpoints MUST be excluded from the spec itself (no self-referential documentation)
- **FR-010**: The setup MUST require zero annotations on existing controllers, resources, or routes

### Key Entities

- **OpenAPI Spec**: Machine-readable description of all API endpoints, inferred from routes, Form Requests, and Resources. Served live at runtime.
- **Interactive Docs UI**: Browser-based interface for exploring and manually testing endpoints. Derived from the live spec.
- **Generated Frontend Client**: Typed API client code (interfaces + hooks) produced by running the code generation tool against the live spec.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: All existing API endpoints are visible in the docs UI without adding any annotations to existing code; verified by asserting that `/docs/api.json` returns HTTP 200 and that specific API paths (`/api/patients`, `/api/users`, `/api/login`) are present in the JSON response
- **SC-002**: Bearer token authentication is correctly documented on all protected endpoints
- **SC-003**: The frontend code generation tool runs successfully against the live spec URL and produces output files with no manual intervention
- **SC-004**: Re-running code generation after a backend change produces updated client files; TypeScript compilation surfaces breaking changes in consuming components
- **SC-005**: Docs setup requires no changes to existing controllers, resources, form requests, or route files

## Clarifications

### Session 2026-04-07

- Q: What test depth should verify SC-001 (all endpoints visible in docs)? → A: Structural — assert HTTP 200 on `/docs/api.json` plus assert specific API paths (`/api/patients`, `/api/users`, `/api/login`) are present in the JSON response body
- Q: Should `frontend/src/api/generated/` be committed to git? → A: No — git-ignore the directory; regenerate locally from the live spec

## Assumptions

- Docs and spec endpoints will be accessible in local development only; production access is out of scope for this feature
- The frontend code generation tool configuration file is part of this feature's scope to create as a starting point
- `frontend/src/api/generated/` is git-ignored; generated output is never committed — developers regenerate from the live spec
- Existing routes, Form Requests, and Resources are structured consistently enough for automatic inference to work without gaps
- The spec will not cover archived Livewire monolith routes — only current REST API routes
