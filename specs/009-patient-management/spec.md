# Feature Specification: Patient Management

**Feature Branch**: `009-patient-management`  
**Created**: 2026-05-03  
**Status**: Draft  
**Input**: User description: "i want to impl patients management feature as it was done with users management"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Browse and Search Patients (Priority: P1)

An admin or doctor opens the patients section and sees a searchable, paginated list of all patients. Each row shows the patient's full name, date of birth, and active/deactivated status. Clicking a row navigates to the patient's detail page.

**Why this priority**: Without the list there is no entry point to any other patient operation. Everything else depends on it.

**Independent Test**: Navigate to `/dashboard/patients`, verify the list renders with patient rows, search by name filters results, clicking a row navigates to the detail page.

**Acceptance Scenarios**:

1. **Given** an admin or doctor is authenticated, **When** they navigate to the patients page, **Then** they see a paginated list of all patients with name, date of birth, and status columns.
2. **Given** the patient list is displayed, **When** the user types in the search field, **Then** the list filters to show only patients whose name matches the input.
3. **Given** a patient is listed, **When** the user clicks the row, **Then** they are navigated to that patient's detail page.
4. **Given** no patients match the search term, **When** search is applied, **Then** an inline "No patients found" message is shown (not an error).

---

### User Story 2 - View Patient Detail (Priority: P2)

An admin or doctor opens a patient's detail page. The left column shows the patient's identity card (avatar with initials, full name, status chip, contact details). The right column has tabs: Overview (personal and contact info), Medical (blood type, allergies, medical notes), Visits (list of past consultations).

**Why this priority**: The detail view is the primary workspace for clinical staff reviewing a patient before or after a consultation.

**Independent Test**: Navigate to `/dashboard/patients/{id}`, verify the left card renders patient identity, tabs switch content, all fields display the correct data.

**Acceptance Scenarios**:

1. **Given** a valid patient id, **When** the detail page loads, **Then** the left card shows initials avatar, full name, status chip, phone, and member-since date.
2. **Given** the detail page is loaded, **When** the user clicks the Medical tab, **Then** blood type, allergies, and medical notes are displayed.
3. **Given** the detail page is loaded, **When** the user clicks the Visits tab, **Then** a list of the patient's visit history is shown.
4. **Given** a patient id that does not exist or is inaccessible, **When** the page loads, **Then** an error alert is shown instead of a spinner.

---

### User Story 3 - Create Patient (Priority: P3)

An admin or doctor clicks "Add New Patient" on the list page. A dialog opens with a form for personal info (first name, last name, date of birth, gender, phone, address) and an optional medical section (blood type, allergies, medical notes). On submit the patient is created and the list refreshes automatically.

**Why this priority**: Creating patients is essential for the system to have data, but the list and detail views must work first to verify the result.

**Independent Test**: Open the create dialog, fill required fields, submit, verify the new patient appears in the list.

**Acceptance Scenarios**:

1. **Given** an admin or doctor is on the patients list, **When** they click "Add New Patient", **Then** a dialog opens with the patient creation form.
2. **Given** the form is submitted with valid data, **When** the server responds with success, **Then** the dialog closes, the list refreshes, and a success toast is shown.
3. **Given** the form is submitted with invalid data, **When** the server responds with 422, **Then** field-level error messages appear under the relevant inputs.
4. **Given** the server returns a non-validation error, **When** submit is clicked, **Then** a top-level error banner appears inside the form.

---

### User Story 4 - Edit Patient (Priority: P4)

An admin, doctor, or the patient themselves can edit patient information from the detail page. An "Edit" button opens a pre-filled form. On save the detail page refreshes with updated data.

**Why this priority**: After creation, patient records need to be kept current — contact info changes, medical notes are updated after consultations.

**Independent Test**: Open a patient detail, click Edit, change a field, save, verify the detail page reflects the change.

**Acceptance Scenarios**:

1. **Given** a patient detail page is open, **When** the user clicks Edit, **Then** a pre-filled form opens with all existing patient data.
2. **Given** the edit form is submitted with valid changes, **When** the server responds with success, **Then** the form closes and the detail page reflects the updated data.
3. **Given** a patient user views their own profile, **When** they click Edit, **Then** they can edit their own information.
4. **Given** a patient user, **When** they attempt to edit another patient's record, **Then** they are denied access.

---

### User Story 5 - Deactivate, Restore, and Permanently Delete Patient (Priority: P5)

An admin or doctor can deactivate a patient (soft delete), restore a deactivated patient, or permanently delete a patient. All destructive actions require confirmation via a dialog. After permanent deletion the user is redirected to the patient list.

**Why this priority**: Lifecycle management is important for clinical data hygiene but is less urgent than viewing and editing.

**Independent Test**: Deactivate a patient, verify status changes to Deactivated. Restore them, verify status returns to Active. Force delete, verify redirect to list and patient is gone.

**Acceptance Scenarios**:

1. **Given** an active patient's detail page, **When** the user clicks Suspend and confirms, **Then** the patient status changes to Deactivated and the detail page refreshes.
2. **Given** a deactivated patient's detail page, **When** the user clicks Restore and confirms, **Then** the patient status changes to Active.
3. **Given** a deactivated patient's detail page, **When** the user clicks Force Delete and confirms, **Then** the patient is permanently removed and the user is redirected to the patients list.
4. **Given** any destructive action dialog is open, **When** the user clicks Cancel, **Then** no action is taken.
5. **Given** any action returns an API error, **When** the user confirms, **Then** an error toast is shown and no navigation occurs.

---

### Edge Cases

- What happens when a patient's linked user account is also deactivated?
- How does the system handle a patient with no visits when the Visits tab is opened?
- What if the patient record is deleted by another session while the detail page is open?
- What fields are required vs optional in the create/edit form?

---

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST display a paginated, searchable list of patients to admins and doctors.
- **FR-002**: System MUST restrict patients to viewing only their own record in the list and detail views.
- **FR-003**: System MUST provide a patient detail page with identity, medical, and visits sections.
- **FR-004**: System MUST allow admins and doctors to create new patient records.
- **FR-005**: System MUST allow admins, doctors, and the patient themselves to edit patient personal and medical information.
- **FR-006**: System MUST allow admins and doctors to deactivate (soft delete) a patient.
- **FR-007**: System MUST allow admins and doctors to restore a deactivated patient.
- **FR-008**: System MUST allow admins and doctors to permanently delete a patient after explicit confirmation.
- **FR-009**: System MUST show field-level validation errors returned by the server on the create/edit form.
- **FR-010**: System MUST refresh the patient list automatically after any mutation (create, edit, deactivate, restore).
- **FR-011**: System MUST navigate away from the detail page after a patient is permanently deleted.
- **FR-012**: System MUST show an error state (not an infinite spinner) when a patient record fails to load.
- **FR-013**: All destructive actions MUST require explicit confirmation before executing.
- **FR-014**: The patient creation form MUST collect both user account fields (name, email, password) and patient fields (date of birth, phone, etc.) and create the linked user account and patient record together in a single operation.

### Key Entities

- **Patient**: Clinical record linked one-to-one with a User account. Holds personal info (first name, last name, date of birth, gender, phone, address, city, postal code), emergency contact, and medical metadata (blood type, allergies, medical notes). Supports soft deletion.
- **User**: The authentication identity linked to a patient. Provides email, role, and account status.
- **Visit**: A clinical encounter linked to a patient and a doctor. Displayed read-only in the patient's Visits tab within this feature scope.
- **PatientSocioeconomic**: Optional supplementary record linked to a patient. Displayed in the Overview tab; editing it is out of scope for this feature.

---

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: An admin or doctor can locate any patient by name and open their detail page in under 30 seconds.
- **SC-002**: A patient creation form can be completed and submitted in under 2 minutes.
- **SC-003**: All mutations (create, edit, deactivate, restore) are reflected in the UI without requiring a manual page refresh.
- **SC-004**: Destructive actions always require a confirmation step — zero accidental deletions from a single click.
- **SC-005**: An invalid form submission always surfaces at least one field-level or banner error message — no silent failures.
- **SC-006**: Navigating to a non-existent or inaccessible patient never results in an infinite loading state.

---

## Assumptions

- The backend API for patients (`GET/POST /api/patients`, `GET/PATCH/DELETE /api/patients/{id}`, restore, force-delete) is already implemented.
- Orval-generated client code for the patients API exists or will be regenerated before implementation begins.
- The `ConfirmDialog` shared component from user management is reused directly — no new shared primitive needed.
- The Visits tab on the patient detail page is read-only in this feature — creating or editing visits is out of scope.
- PatientSocioeconomic data is displayed in the Overview tab but editing it is out of scope.
- The implementation follows the user management blueprint (`_docs/blueprints/user-management.md`) for folder structure, data flow, refresh mechanism, action pattern, error/loading states, and form pattern.
