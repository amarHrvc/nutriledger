# Feature Specification: Patient Role UI Restrictions

**Feature Branch**: `016-patient-role-ui`  
**Created**: 2026-05-26  
**Status**: Draft  
**Input**: User description: "at this moment when patient logs in it can see only dashboard page, patient can visit itsown page patients/{id}, it is preveneted to see other patient pages and that is ok. On visiting patient page standard full page is displayed. Generally content of full page is ok it shows all data relevant to patient. There are some buttons displayed which patient should not see like suspend, add visit on visits tab, Generate diet plan on Diet plans tab those should not be displayed. Upon login patient should be forwarded to its own patient page."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Patient Login Redirect (Priority: P1)

When a patient logs into the system, they are automatically forwarded to their own patient profile page instead of the generic dashboard. This ensures patients immediately see their own health data without requiring any manual navigation.

**Why this priority**: Directly addresses the primary post-login experience for patients. Without this, patients land on a page irrelevant to their daily use case.

**Independent Test**: Can be fully tested by logging in as a patient user and verifying the landing URL matches `/patients/{own-id}`.

**Acceptance Scenarios**:

1. **Given** a patient is not logged in, **When** they submit valid credentials, **Then** they are redirected to their own patient profile page (`/patients/{their-id}`)
2. **Given** a patient is already logged in and navigates to the root or dashboard, **When** the page loads, **Then** they are redirected to their own patient profile page
3. **Given** a doctor or admin logs in, **When** authentication succeeds, **Then** they are NOT redirected to a patient page (existing behavior is preserved)

---

### User Story 2 - Hide Action Buttons from Patients (Priority: P1)

When a patient views their own profile page, staff-only action controls are hidden. Specifically, the "Suspend" button, the "Add Visit" button on the Visits tab, and the "Generate Diet Plan" button on the Diet Plans tab are not visible to patients.

**Why this priority**: Equal priority to redirect — showing staff-only controls to patients creates confusion and potential accidental actions. Both stories together form the complete patient-role UX fix.

**Independent Test**: Can be fully tested by logging in as a patient, navigating to their own patient profile page, and verifying the three buttons are absent from the UI.

**Acceptance Scenarios**:

1. **Given** a logged-in patient is on their own profile page, **When** the page renders, **Then** the "Suspend" button is not visible anywhere on the page
2. **Given** a logged-in patient is on their own profile page and views the Visits tab, **When** the tab content renders, **Then** the "Add Visit" button is not present
3. **Given** a logged-in patient is on their own profile page and views the Diet Plans tab, **When** the tab content renders, **Then** the "Generate Diet Plan" button is not present
4. **Given** a logged-in doctor or admin is on a patient profile page, **When** the page renders, **Then** all three buttons remain visible (existing behavior preserved)

---

### Edge Cases

- What happens if a patient's own ID changes or the account is reassigned? The redirect should always resolve against the current authenticated patient's linked ID.
- What if a patient has no linked patient record? An inline error message is displayed ("Your account is not linked to a patient record. Please contact your administrator.") — no redirect, no logout, no crash.
- What if the patient directly navigates to another patient's page? Access should be denied (already enforced, confirmed in scope).
- Are there additional staff-only controls not mentioned that may be present? Only the three explicitly named buttons are in scope; any others are out of scope for this feature.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST automatically redirect an authenticated patient user to their own patient profile page immediately after login; this redirect is handled client-side by a frontend route guard once auth state is resolved
- **FR-002**: System MUST automatically redirect an authenticated patient user to their own patient profile page if they navigate to the root or dashboard while logged in; the route guard enforces this on every navigation attempt, not only at initial login
- **FR-003**: System MUST NOT redirect non-patient users (doctors, admins) after login — their existing post-login destination is preserved
- **FR-004**: The patient profile page MUST NOT display the "Suspend" button when the viewer is a patient role
- **FR-005**: The Visits tab on the patient profile page MUST NOT display the "Add Visit" button when the viewer is a patient role
- **FR-006**: The Diet Plans tab on the patient profile page MUST NOT display the "Generate Diet Plan" button when the viewer is a patient role
- **FR-007**: All other content on the patient profile page (health data, visit history, diet plan history) MUST remain fully visible to the patient
- **FR-008**: Button hiding MUST be enforced by the frontend based on the authenticated user's role — the controls should simply not be rendered, not merely disabled. This is a display-only concern; backend API restrictions for these actions are already in place and are not modified by this feature.
- **FR-009**: If a patient user account has no linked patient record, the system MUST display an inline error message ("Your account is not linked to a patient record. Please contact your administrator.") — no redirect, no logout, no blank page.

### Key Entities

- **Authenticated User**: The currently logged-in user; has a `role` attribute (`pacijent`, `doktor`, `admin`) that governs which UI elements are rendered
- **Patient Profile Page**: The detail view for a patient; contains multiple tabs (Overview, Visits, Diet Plans) and various action controls
- **Patient Record**: Linked to a user account with role `pacijent`; the patient's own record ID is used as the redirect target

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 100% of patient logins land on the patient's own profile page — zero patients land on the dashboard after a successful login
- **SC-002**: The "Suspend", "Add Visit", and "Generate Diet Plan" controls are absent from the rendered page for all patient-role sessions — verified across all three tabs
- **SC-003**: Doctor and admin login flows are unaffected — their post-login page and visible controls remain unchanged
- **SC-004**: A patient user with no linked patient record sees an inline error message directing them to contact their administrator — the page does not crash, redirect, or log them out

## Clarifications

### Session 2026-05-26

- Q: Are patients already blocked at the API level from creating visits, generating diet plans, and suspending accounts — making this a display-only change? → A: Yes — backend already enforces all three restrictions via policies. This feature is frontend display-only.
- Q: Where does the post-login redirect logic live? → A: Frontend route guard — client-side check on role; redirect happens in the browser after auth state is available.
- Q: What should the patient see when their user account has no linked patient record? → A: Inline error message ("Contact your administrator") displayed on the page — no redirect, no crash, no logout.

## Assumptions

- The frontend already knows the authenticated user's role (available in auth context/state) and the patient's own ID (available from the auth response or session) — no additional API calls are required to determine these values
- "Suspend" refers to a button that soft-deletes or deactivates the patient account; it appears on the main profile view (not inside a tab)
- The three named buttons are the only staff-only controls that need hiding; all other page content is already appropriate for patient viewing
- Access restriction to other patients' pages is already enforced and is out of scope for this feature
