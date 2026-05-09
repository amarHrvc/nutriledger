# Feature Specification: Visit Detail Page

**Feature Branch**: `011-visit-detail-page`  
**Created**: 2026-05-09  
**Status**: Draft  
**Input**: User description: "visits are currently displayed in a list format, there is no possibility to view specific visit in its own detail page, visit detail page should be implemented"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - View Full Visit Details (Priority: P1)

A doctor or admin sees a list of visits and wants to inspect a specific visit in full. They click on a visit row and are taken to a dedicated detail page that shows all information about that visit: date, time, the attending doctor, the patient name, and clinical notes.

**Why this priority**: This is the core value of the feature — without a detail page, users have no way to read full visit notes or confirm all recorded fields. Everything else builds on this.

**Independent Test**: Can be fully tested by navigating to a visit detail URL and confirming all visit fields are rendered correctly, delivering a readable record for a single visit.

**Acceptance Scenarios**:

1. **Given** a doctor is logged in and viewing the visits list, **When** they click on a visit entry, **Then** they are taken to a dedicated page displaying all details for that visit (date, time, doctor name, patient name, notes).
2. **Given** an admin is logged in, **When** they navigate to a visit detail URL, **Then** they see the full visit record including all fields.
3. **Given** a visit has no notes recorded, **When** a doctor views the detail page, **Then** an appropriate empty state is shown for the notes section (not a blank space or error).

---

### User Story 2 - Patient Views Own Visit Detail (Priority: P2)

A patient navigates to their own visit history and wants to review a past visit. They click a visit entry and are taken to its detail page showing the date, time, attending doctor, and any notes recorded for that visit.

**Why this priority**: Patients need read-only access to their own visit records. This is a secondary flow but important for patient-facing completeness.

**Independent Test**: Can be tested by logging in as a patient, clicking a visit in their history, and verifying the detail page renders with correct data and no edit/delete controls.

**Acceptance Scenarios**:

1. **Given** a patient is logged in, **When** they click on one of their own visits in the list, **Then** they see the visit detail page with date, time, doctor name, and notes — with no ability to edit or delete.
2. **Given** a patient tries to access another patient's visit detail URL directly, **When** the page loads, **Then** access is denied and an appropriate message is shown.

---

### User Story 3 - Actions from Visit Detail Page (Priority: P3)

Authorized users (admin or doctor) can perform visit management actions directly from the detail page — editing the visit record or, for admins, deleting it — without returning to the list first.

**Why this priority**: Reduces friction for common management workflows; but editing and deletion already work from the list, so this is an enhancement rather than a blocker.

**Independent Test**: Can be tested in isolation by verifying edit and delete controls appear on the detail page for the correct roles and function correctly.

**Acceptance Scenarios**:

1. **Given** a doctor is viewing a visit detail that is still editable (not older than 1 day), **When** they click Edit, **Then** they can modify the visit fields and save the changes.
2. **Given** an admin is viewing any visit detail, **When** they click Delete, **Then** the visit is removed and they are redirected to the visits list.
3. **Given** a doctor views a visit detail for a visit older than 1 day, **When** they view the page, **Then** the Edit action is not available.
4. **Given** a doctor views a visit they did not personally record, **When** they view the page, **Then** the Edit action is not available.

---

### Edge Cases

- What happens when a visit ID in the URL does not exist? The user should see a clear "not found" state.
- What happens when a patient navigates directly to another patient's visit URL? Access must be denied with an appropriate message.
- What happens when a visit has extremely long notes? The detail page must handle long text gracefully without breaking the layout.
- What happens when the user navigates directly to the detail URL without a referrer? The back-navigation must still work (e.g., link to the main visits list).

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST provide a unique, bookmarkable URL for each visit's detail page.
- **FR-002**: The detail page MUST display all recorded visit fields: date, time, attending doctor name, patient name, and clinical notes.
- **FR-003**: The detail page MUST be reachable via a clickable link from every visit entry in the visits list view and from the patient profile visits tab.
- **FR-004**: The detail page MUST show an appropriate empty/placeholder state when clinical notes are absent.
- **FR-005**: Authorized editors (admin or doctor who recorded the visit, within the editable time window) MUST be able to initiate an edit action directly from the detail page.
- **FR-006**: Admin users MUST be able to delete the visit directly from the detail page.
- **FR-007**: The detail page MUST include a navigation control (e.g., back link or breadcrumb) that returns the user to the visits list.
- **FR-008**: Patients MUST only be able to view detail pages for their own visits; attempts to access another patient's visit must be denied.
- **FR-009**: Guest users and unauthorized roles MUST be prevented from accessing any visit detail page.
- **FR-010**: The detail page MUST reflect real-time data — if a visit was just edited, navigating to the detail page shows the updated values.

### Key Entities

- **Visit**: Represents a single medical appointment. Key attributes: date, time, clinical notes, editability status (whether it can still be modified), unique identifier.
- **Patient**: The subject of the visit. Displayed on the detail page; determines access scope for patients.
- **Doctor**: The user who recorded the visit. Displayed on the detail page; determines edit permissions for doctor-role users.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Users can reach a specific visit's full detail in at most 2 clicks from the visits list.
- **SC-002**: All visit fields are visible on the detail page without requiring additional interaction (no collapsed sections by default).
- **SC-003**: Navigation back to the visits list is always accessible from the detail page without using the browser's back button.
- **SC-004**: 100% of unauthorized access attempts (wrong patient, guest, wrong role) are blocked and receive appropriate feedback.
- **SC-005**: The detail page loads and displays complete visit data within the standard application response time users experience for other pages.

## Assumptions

- The existing API endpoint for retrieving a single visit (`GET /api/patients/{patient}/visits/{visit}`) is already implemented and will be consumed by the new detail page.
- "Editable" visits are defined by existing business logic: a visit can be edited only within 1 day of its date and only by the doctor who recorded it (or any admin).
- Deleting a visit from the detail page redirects the user to the visits list; no confirmation step is defined here beyond the existing pattern used elsewhere in the application.
- The visits list entry (both in the global visits view and in the patient profile visits tab) will be updated to include a clickable link to the detail page; no other changes to the list views are in scope for this feature.
- Mobile responsiveness follows the existing application conventions and is not separately specified.
