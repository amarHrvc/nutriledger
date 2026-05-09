# Feature Specification: Dashboard with Login & Logout (Sanctum Token Auth)

**Feature Branch**: `006-fe-auth-dashboard`  
**Created**: 2026-04-29  
**Status**: Draft  
**Input**: User description: "in frontend i have simple nextjs application based on vuexy template. You have graph db where full application is documented as well as frontend/_knowledge index of full app, i want to create dashboard for app with login and logout screens, which will use sanctum token for auth. There is also a poc of api call in current FE. Do recommended nextjs approach for this"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - User Logs In and Reaches Dashboard (Priority: P1)

A visitor arrives at the login screen, enters their email and password, and is granted access to the dashboard. All subsequent actions in the application use this authenticated session without requiring re-entry of credentials.

**Why this priority**: The entire application is gated behind authentication — nothing else works until this flow is solid.

**Independent Test**: Open the login page, submit valid credentials, verify the dashboard renders and the user's name/role is visible. The login flow can be fully demoed in isolation.

**Acceptance Scenarios**:

1. **Given** a visitor is on the login screen, **When** they submit a valid email and password, **Then** they are redirected to the main dashboard and their name is displayed.
2. **Given** a visitor submits an incorrect password, **When** the form is submitted, **Then** an error message is shown on the login screen and the user remains there.
3. **Given** a visitor submits the form with an empty email or password, **When** the form is submitted, **Then** inline validation errors appear and no network request is made.
4. **Given** a visitor's account is deactivated, **When** they attempt to log in, **Then** a clear message explains that the account is not accessible.

---

### User Story 2 - Protected Dashboard Is Inaccessible Without Login (Priority: P2)

Any attempt to navigate to the dashboard or any protected page while not logged in results in an automatic redirect to the login screen — no content is ever shown to unauthenticated users.

**Why this priority**: Without route protection, the entire security model fails. This must be verified before building any dashboard content.

**Independent Test**: Clear all session data, navigate directly to `/dashboard` or any sub-page, and confirm the browser lands on the login screen with no dashboard content leaked.

**Acceptance Scenarios**:

1. **Given** a user has no active session, **When** they navigate to any protected route, **Then** they are immediately redirected to the login screen.
2. **Given** a user's session token expires mid-session, **When** they attempt to load new data, **Then** they are redirected to login without seeing a raw error.

---

### User Story 3 - Authenticated User Sees Dashboard with Navigation (Priority: P2)

After logging in, the user lands on a dashboard home page that displays their profile summary (name, role) and a navigation menu. The menu items reflect the application's sections so the user can orient themselves.

**Why this priority**: The dashboard is the first thing users see after login — it anchors the rest of the feature groups built on top.

**Independent Test**: Log in as each role (admin, doctor, patient), verify the dashboard loads and the correct navigation items are visible for that role.

**Acceptance Scenarios**:

1. **Given** an authenticated user is on the dashboard, **When** the page loads, **Then** their name and role are visible in the header or sidebar.
2. **Given** a logged-in admin, **When** they view the navigation, **Then** admin-accessible sections appear in the menu.
3. **Given** a logged-in doctor, **When** they view the navigation, **Then** doctor-specific sections appear and admin-only sections are absent.
4. **Given** a logged-in patient, **When** they view the navigation, **Then** only patient-relevant sections appear.

---

### User Story 4 - User Logs Out (Priority: P3)

An authenticated user can log out from any page. After logging out their session is terminated, they are redirected to the login screen, and navigating back does not restore access to the dashboard.

**Why this priority**: Logout is required for shared-device scenarios and is a basic security control, but the application is still usable without it for demo purposes.

**Independent Test**: Log in, click the logout action, confirm redirect to login, press the browser back button, confirm the dashboard does not reload.

**Acceptance Scenarios**:

1. **Given** an authenticated user clicks the logout control, **When** the action completes, **Then** they are redirected to the login screen.
2. **Given** a user who has logged out, **When** they press the browser's back button and navigate to a protected URL, **Then** they are redirected to login rather than seeing dashboard content.
3. **Given** a logout request fails due to a network error, **When** the error is received, **Then** the local session is still cleared and the user is taken to the login screen.

---

### User Story 5 - Already Logged-In Users Are Redirected Away from Login (Priority: P3)

An authenticated user who navigates to the login screen is automatically sent to the dashboard — the login form is never shown to someone who already has a valid session.

**Why this priority**: Prevents confusing UX for users who bookmark the login URL or use the back button after logging in.

**Independent Test**: Log in, manually navigate to `/login`, confirm immediate redirect to dashboard.

**Acceptance Scenarios**:

1. **Given** an authenticated user navigates to the login screen, **When** the page loads, **Then** they are redirected to the dashboard without seeing the login form.

---

### Edge Cases

- What happens when the API is unreachable during login? → A user-friendly error is displayed; no crash or blank screen.
- What happens if the session token is corrupted (malformed but present)? → The user is treated as unauthenticated and redirected to login.
- What happens during a page refresh on a protected route? → The session is validated before rendering; the page does not flash content before redirecting.
- What happens when login credentials contain leading/trailing whitespace? → Whitespace is trimmed before submission.
- What happens if the user has multiple browser tabs open and logs out in one? → Other tabs detect the missing session on next navigation and redirect to login.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST allow users to authenticate using an email address and password.
- **FR-002**: System MUST store a valid session credential upon successful authentication for use across all subsequent requests in that session.
- **FR-003**: System MUST redirect users to the dashboard immediately after successful login.
- **FR-004**: System MUST protect all dashboard routes — any unauthenticated request to a protected route redirects to the login screen.
- **FR-005**: System MUST display the authenticated user's name and role on the dashboard.
- **FR-006**: System MUST render a role-aware navigation menu — menu items reflect the sections available to the user's role.
- **FR-007**: System MUST provide a logout control accessible from any authenticated page.
- **FR-008**: System MUST terminate the session and redirect to the login screen upon logout, regardless of whether the server-side termination succeeds.
- **FR-009**: System MUST redirect authenticated users away from the login screen to the dashboard.
- **FR-010**: System MUST display a clear, human-readable error message when login fails (invalid credentials, deactivated account, or server error).
- **FR-011**: System MUST validate login form inputs client-side before submitting (email format, non-empty password).
- **FR-012**: System MUST preserve the originally requested URL after a successful login so the user lands on the page they tried to access.
- **FR-013**: System MUST maintain the session across page refreshes without requiring the user to log in again.

### Key Entities

- **Session Token**: A credential issued by the server upon successful login. Attached to every API request that requires authentication. Invalidated on logout.
- **Authenticated User**: A user with a valid session token. Has attributes: name, email, role (`admin` / `doktor` / `pacijent`). Determines which parts of the application are accessible.
- **Dashboard Home**: The landing page after login. Shows personal profile summary and role-appropriate navigation links.
- **Navigation Menu**: A persistent menu structure whose items are filtered by the user's role. Provides access to all application sections available to that role.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: A user with valid credentials can complete the full login flow — from the login screen to the dashboard — in under 30 seconds under normal conditions.
- **SC-002**: 100% of direct URL accesses to protected routes by unauthenticated users result in a redirect to the login screen; no protected content is ever rendered.
- **SC-003**: Login error messages appear within 2 seconds of a failed submission under normal network conditions.
- **SC-004**: The logout action completes (session cleared, redirect executed) within 1 second of user interaction.
- **SC-005**: The dashboard correctly displays the user's name and role for all three roles (admin, doctor, patient) without requiring any manual configuration.
- **SC-006**: After logout, browser back navigation to a protected URL redirects to login — 0% bypass rate.
- **SC-007**: A logged-in user who refreshes the browser on any protected page continues to see that page without being sent to login.

## Assumptions

- The backend's `/api/login` endpoint returns a Bearer token on success. No additional setup (e.g., CSRF handshake) is required before submitting credentials.
- Session persistence applies within a single browser session by default. Extended persistence across browser closes ("remember me") is out of scope for this feature.
- The dashboard home page does not display live clinical data (patients, visits). It serves as a navigation hub and profile summary. Data-heavy pages are built in subsequent feature groups.
- Navigation menu structure is static per role for this feature. Dynamic permission-based filtering is a future concern.
- All three roles (admin, doctor, patient) use the same login screen. There is no role-specific login URL.
- Social login options visible in the current login UI template are out of scope and should be removed or hidden.
