# Feature Specification: Visits & Encounters REST API

**Feature Branch**: `005-visits-encounters-api`
**Created**: 2026-04-10
**Status**: Draft
**Input**: User description: "Visits & Encounters REST API"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Doctor Records a Patient Visit (Priority: P1)

A doctor logs a clinical encounter for a patient by submitting the visit date and optional clinical notes. The system automatically attributes the visit to the doctor who submitted it — the doctor cannot claim to be someone else.

**Why this priority**: Recording visits is the core capability of this feature. Without it, no visit history can exist. All other stories depend on visits being created first.

**Independent Test**: Can be fully tested by having an authenticated doctor submit a new visit for an existing patient and verifying the record appears in the patient's history attributed to that doctor.

**Acceptance Scenarios**:

1. **Given** a doctor is authenticated and a patient exists, **When** the doctor submits a visit with a valid date, **Then** the system creates the visit record, attributes it to the submitting doctor, and returns the newly created visit details.
2. **Given** a doctor submits a visit with no notes, **When** the request is processed, **Then** the visit is created successfully — notes are not required.
3. **Given** a doctor submits a visit with a future date, **When** the request is processed, **Then** the system rejects the submission with a validation error indicating that future dates are not allowed.
4. **Given** a doctor submits notes exceeding 10,000 characters, **When** the request is processed, **Then** the system rejects the submission with a validation error.
5. **Given** an admin is authenticated, **When** the admin attempts to create a visit, **Then** the system rejects the request — only doctors may record visits.
6. **Given** a patient is authenticated, **When** the patient attempts to create a visit, **Then** the system rejects the request — patients cannot create visit records.

---

### User Story 2 - View Visit History for a Patient (Priority: P2)

Doctors and admins can retrieve the complete chronological visit history for any patient. The list is ordered by date with the most recent visit shown first, and supports pagination for patients with extensive records.

**Why this priority**: Visit history is the primary read operation. Doctors need to review past visits before conducting a new one.

**Independent Test**: Can be fully tested by creating several visits for a patient and verifying the list is returned newest-first with correct pagination metadata.

**Acceptance Scenarios**:

1. **Given** a doctor is authenticated and a patient has visits, **When** the doctor requests the patient's visit history, **Then** the system returns visits in reverse chronological order with pagination.
2. **Given** an admin is authenticated, **When** the admin requests any patient's visit history, **Then** the system returns the full history.
3. **Given** a patient is authenticated, **When** the patient requests their own visit history, **Then** the system returns their history.
4. **Given** a patient is authenticated, **When** the patient requests another patient's visit history, **Then** the system rejects the request.
5. **Given** a patient has no visits, **When** a doctor requests that patient's visit history, **Then** the system returns an empty list (not an error).
6. **Given** a patient has more visits than fit on one page, **When** a doctor requests the history, **Then** the response includes pagination information indicating total records and available pages.

---

### User Story 3 - View Individual Visit Details (Priority: P3)

Any authorized user can retrieve the full details of a specific visit, including the conducting doctor's information and all recorded clinical notes.

**Why this priority**: After seeing a list, users need to drill into specific visits to read full clinical notes.

**Independent Test**: Can be fully tested by requesting a specific visit and verifying it returns the full record including doctor details.

**Acceptance Scenarios**:

1. **Given** a doctor is authenticated, **When** the doctor requests a specific visit for a patient, **Then** the full visit record is returned including the conducting doctor's information.
2. **Given** a patient is authenticated, **When** the patient requests a visit that belongs to them, **Then** the full visit details are returned.
3. **Given** a patient is authenticated, **When** the patient requests a visit that belongs to a different patient, **Then** the system rejects the request.
4. **Given** a visit ID that does not exist, **When** any user requests it, **Then** the system returns a not-found response.
5. **Given** a valid visit ID is provided but the visit belongs to a different patient than the one in the request context, **When** the request is processed, **Then** the system returns a not-found response (not a permission error).

---

### User Story 4 - Doctor Edits Their Own Visit Notes (Priority: P4)

A doctor can update the date or clinical notes on a visit they personally conducted. A doctor cannot modify another doctor's visit notes.

**Why this priority**: Clinical notes may need correction or additions after the visit concludes. The ownership constraint protects note integrity.

**Independent Test**: Can be fully tested by having a doctor update one of their visits and verifying a different doctor cannot update the same visit.

**Acceptance Scenarios**:

1. **Given** a doctor is authenticated and the visit was conducted by that doctor, **When** the doctor submits updated notes, **Then** the visit record is updated and the updated details are returned.
2. **Given** a doctor is authenticated and the visit was conducted by a different doctor, **When** the first doctor attempts to update the visit, **Then** the system rejects the request.
3. **Given** an admin is authenticated, **When** the admin updates any visit, **Then** the update is accepted regardless of which doctor conducted it.
4. **Given** a patient is authenticated, **When** the patient attempts to update any visit, **Then** the system rejects the request.
5. **Given** a doctor submits a partial update with only notes (no date), **When** the request is processed, **Then** only the notes are updated and the date remains unchanged.

---

### User Story 5 - Admin Permanently Deletes a Visit (Priority: P5)

Admins can permanently remove a visit record from the system. Deleted records cannot be recovered.

**Why this priority**: Data governance requires that incorrect or erroneous records can be removed, but this is an infrequent administrative action.

**Independent Test**: Can be fully tested by having an admin delete a visit and verifying the record no longer appears in any queries.

**Acceptance Scenarios**:

1. **Given** an admin is authenticated, **When** the admin deletes a visit, **Then** the system permanently removes the record and returns a success response with no content.
2. **Given** a doctor is authenticated, **When** the doctor attempts to delete any visit, **Then** the system rejects the request — only admins may delete visits.
3. **Given** a patient is authenticated, **When** the patient attempts to delete any visit, **Then** the system rejects the request.
4. **Given** an admin deletes a visit, **When** any user later queries that visit, **Then** the system returns a not-found response — the record is fully gone.

---

### Edge Cases

- What happens when a visit's date matches today's date? The system must accept it (today is valid, tomorrow is not).
- What happens when notes are submitted as an empty string vs. omitted entirely? Both must be accepted as "no notes."
- How does the system handle a visit URL where the visit belongs to a different patient than the one in the URL? Returns not-found, not a permission error (prevents information leakage).
- What happens when a patient or doctor account is deleted? All associated visits are removed along with the account.
- What happens when a doctor has 0 visits in the history? Returns an empty list, not an error.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST allow authenticated doctors to create visit records for patients, associating the visit with the patient specified in the request context.
- **FR-002**: System MUST automatically assign the submitting doctor as the visit's conducting doctor — this attribution cannot be overridden by request data.
- **FR-003**: System MUST prevent admins from creating visit records; only doctors may record clinical encounters.
- **FR-004**: System MUST prevent patients from creating, editing, or deleting visit records.
- **FR-005**: System MUST require a visit date and reject dates set in the future.
- **FR-006**: System MUST accept visit records submitted without clinical notes — notes are optional.
- **FR-007**: System MUST enforce a maximum length of 10,000 characters for clinical notes.
- **FR-008**: System MUST allow doctors and admins to view the complete visit history for any patient.
- **FR-009**: System MUST restrict patients to viewing only their own visit history.
- **FR-010**: System MUST return visit history ordered by date, with the most recent visit first.
- **FR-011**: System MUST paginate visit history results to support patients with extensive records.
- **FR-012**: System MUST include the conducting doctor's name and identifying information in each visit record returned to authorized users.
- **FR-013**: System MUST allow a doctor to update the date or notes of a visit only if they were the conducting doctor.
- **FR-014**: System MUST allow admins to update any visit regardless of which doctor conducted it.
- **FR-015**: System MUST allow partial updates — only the submitted fields are changed, others remain untouched.
- **FR-016**: System MUST restrict deletion of visit records to admins only; doctors and patients cannot delete visits.
- **FR-017**: System MUST permanently remove a deleted visit record — it must not be recoverable.
- **FR-018**: System MUST always scope visit access to a specific patient — there is no global visit list.
- **FR-019**: System MUST reject access to a visit that does not belong to the patient specified in the request, returning a not-found response.
- **FR-020**: System MUST reject unauthenticated requests to all visit endpoints.

### Key Entities

- **Visit**: A clinical encounter record that captures the date a patient was seen, optional clinical notes written by the doctor, and a reference to both the patient and the conducting doctor. Visits are permanently deleted when removed — no recovery state exists.
- **Patient**: The individual who is the subject of the visit. One patient may have many visits over time. Visit access is always scoped to a specific patient.
- **Doctor**: A system user with the doctor role who conducts and records visits. Doctors own the visits they create and may only edit their own records.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: A doctor can record a new patient visit (date + notes) in under 30 seconds from submission to confirmation.
- **SC-002**: Visit history loads without errors for patients with 100 or more historical records, with pagination intact.
- **SC-003**: All unauthorized access attempts — including wrong role, wrong patient, or mismatched visit context — are consistently rejected and do not expose data from other patients or doctors.
- **SC-004**: Visit lists always appear in reverse chronological order with no exceptions.
- **SC-005**: A deleted visit is completely absent from all subsequent queries — no partial state or recovery path remains.
- **SC-006**: The conducting doctor attribution is always accurate — no visit can be recorded under a different doctor than the one who submitted it.
- **SC-007**: All 30+ automated behavioral tests covering creation, retrieval, editing, deletion, authorization, and validation pass without failures.

## Assumptions

- Authentication and user roles (doctor, patient, admin) are provided by Group 1 (Auth & User Management). This feature assumes authenticated sessions are already functional.
- Patient records must exist before visits can be created. This feature depends on Group 2 (Patient Management) being complete.
- Pagination default is 15 records per page, consistent with the existing patient list API.
- Visit date granularity is day-level only. Recording the time of a visit is out of scope for this release.
- When a patient account or doctor account is deleted, all associated visit records are also removed — this is the expected cascade behavior agreed upon at the data model level.

## Out of Scope

- Visit templates or pre-filled note formats
- File or image attachments to visits
- Visit time tracking (arrival, start, end times)
- Searching or filtering visits by date range, diagnosis, or doctor
- Exporting visits to PDF or other formats
- Visit billing or medical coding (ICD-10, CPT)
- Soft deletes or visit recovery after deletion
- Vital signs, lab results, or medication records (future extensions)
- Telemedicine or visit-type classification (in-person vs. remote)
