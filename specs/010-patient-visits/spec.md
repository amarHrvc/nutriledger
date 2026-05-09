# Feature Specification: Patient Visits Management

**Feature Branch**: `010-patient-visits`  
**Created**: 2026-05-05  
**Status**: Draft  
**Input**: User description: "i want to add visits feature to patients. For each patient i should be able to see its own visits, and from patient profile i should be able to add new visit. Visits area should show all visits of doctors patients or all visits in system for admin. Visit can be edited only for now. Visit in past -1 day should not be edited. From visits area doctor should be able to create visit for any of the patients. Admin has broader access he should be able to add visit for specific doctor and patient. Doctor can define visit at some date and time in future. Upcoming visits should be clearly visible on visits page"

---

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Doctor Views and Manages Visits (Priority: P1)

A doctor navigates to the Visits page and sees a chronological list of all visits where they are the assigned doctor. Upcoming visits are visually distinguished from past visits. The doctor can create a new visit for any patient in the system, scheduling it for a future date and time. They can also edit a visit if it is no more than 1 day old; visits older than that are read-only.

**Why this priority**: This is the core workflow for the primary user role. Without this, the visits feature has no value for the clinical workflow.

**Independent Test**: Can be fully tested by logging in as a doctor, opening the Visits page, verifying the list, creating a visit, confirming upcoming highlighting, and verifying the edit lock on old visits.

**Acceptance Scenarios**:

1. **Given** a doctor is logged in, **When** they open the Visits page, **Then** they see only visits where they are the assigned doctor, sorted with upcoming visits first and past visits below.
2. **Given** a doctor is on the Visits page, **When** they create a new visit by selecting a patient and a future date/time, **Then** the visit is saved and appears in the list with the doctor pre-filled as themselves.
3. **Given** a doctor has a visit scheduled for today or yesterday, **When** they click Edit on that visit, **Then** the edit form opens and changes can be saved.
4. **Given** a doctor has a visit that occurred more than 1 calendar day ago, **When** they view that visit, **Then** the Edit action is disabled/unavailable.
5. **Given** a doctor is on the Visits page, **When** upcoming visits exist, **Then** those visits are clearly visually distinguished (e.g. badge, section header, or colour highlight) from past visits.

---

### User Story 2 - Admin Views and Manages All Visits (Priority: P2)

An admin navigates to the Visits page and sees every visit in the system across all doctors and patients. The admin can create a new visit by selecting both a patient and a specific doctor. Upcoming visits are highlighted the same way as for doctors. The admin can edit any visit within the 1-day window.

**Why this priority**: Admin oversight of all visits is required for system management, but depends on the core visit model established in P1.

**Independent Test**: Can be tested by logging in as admin, verifying the full cross-doctor visit list, creating a visit with an explicitly chosen doctor and patient, and confirming the edit lock matches doctor behaviour.

**Acceptance Scenarios**:

1. **Given** an admin is logged in, **When** they open the Visits page, **Then** they see all visits in the system regardless of doctor or patient.
2. **Given** an admin is on the Visits page, **When** they open the create visit form, **Then** both patient and doctor are required fields with no pre-filled doctor.
3. **Given** an admin creates a visit assigning Doctor A to Patient B, **When** Doctor A opens the Visits page, **Then** that visit appears in Doctor A's list.
4. **Given** an admin views a visit older than 1 calendar day, **When** they look for the Edit action, **Then** it is disabled/unavailable — identical to the doctor constraint.

---

### User Story 3 - View and Add Visits from Patient Profile (Priority: P3)

From a patient's profile page, any authorised user can see that patient's full visit history in a dedicated section. From the same view, they can add a new visit for that patient without navigating to the global Visits page. The newly created visit appears in both the patient profile section and the global Visits page.

**Why this priority**: Contextual access from the patient profile is a convenience improvement on top of the global Visits page. It adds no new data model requirements — only a secondary entry point.

**Independent Test**: Can be tested by opening any patient profile, verifying the visits section shows only that patient's visits, adding a visit via the profile, then confirming it appears on the global Visits page.

**Acceptance Scenarios**:

1. **Given** a user opens a patient profile, **When** they view the Visits section, **Then** only visits belonging to that patient are shown.
2. **Given** a doctor opens a patient profile, **When** they add a new visit, **Then** the doctor is pre-filled as the assigned doctor and a future date/time must be selected.
3. **Given** an admin opens a patient profile, **When** they add a new visit, **Then** they must select a doctor (no pre-fill) and provide a date/time.
4. **Given** a visit is created from a patient profile, **When** the user navigates to the global Visits page, **Then** the new visit appears there as well.

---

### Edge Cases

- What happens when a doctor has no visits yet — an empty state is shown with a prompt to create the first visit.
- What happens when the visits page loads and some upcoming visits have just passed — temporal status is evaluated at page load time.
- How is the 1-day edit boundary calculated — by calendar date (yesterday = editable, day before yesterday = locked), not a rolling 24-hour window.
- What if a doctor tries to submit a visit creation form without selecting a patient — form prevents submission with a validation message.
- What if the patient list in the visit creation form is very long — a searchable dropdown is used.
- What if an admin tries to edit a visit older than 1 day — edit is locked, same as for doctors.

---

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST provide a dedicated Visits page accessible from the main navigation for authenticated doctors and admins.
- **FR-002**: For doctors, the Visits page MUST show only visits where that doctor is the assigned doctor.
- **FR-003**: For admins, the Visits page MUST show all visits in the system across all doctors and patients.
- **FR-004**: The Visits page MUST visually distinguish upcoming visits (scheduled date/time in the future or today) from past visits in a clearly noticeable way.
- **FR-005**: Doctors MUST be able to create a new visit from the Visits page; the assigned doctor is automatically set to the logged-in doctor and cannot be changed.
- **FR-006**: Admins MUST be able to create a new visit from the Visits page by selecting any patient and any doctor.
- **FR-007**: Visit creation MUST require: patient, doctor, date, and time. Notes are optional.
- **FR-008**: Visits MUST only be schedulable for future dates and times.
- **FR-009**: A visit MUST be editable only if its scheduled date is today or within the past 1 calendar day; visits older than 1 calendar day MUST have the edit action permanently disabled.
- **FR-010**: Any authorised user MUST be able to view a patient's visit history from the patient profile page.
- **FR-011**: Authorised users MUST be able to create a new visit directly from the patient profile page.
- **FR-012**: A visit created from a patient profile MUST appear on the global Visits page immediately.
- **FR-013**: Unauthenticated users MUST NOT be able to access visit data or perform any visit actions.
- **FR-014**: Doctors MUST NOT be able to view or access visits assigned to other doctors.

### Key Entities

- **Visit**: A scheduled or completed clinical encounter. Attributes: assigned patient, assigned doctor, scheduled date, scheduled time, optional notes. Temporal status (upcoming vs past) is derived from scheduled date/time relative to current date/time.
- **Patient**: The subject of the visit. Referenced from existing patient management.
- **Doctor**: The clinician assigned to the visit. A user with the doctor role. Referenced from existing user management.

---

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: A doctor can create a new visit for a patient in under 60 seconds from the Visits page.
- **SC-002**: Upcoming visits are immediately visible without scrolling or applying filters on page load.
- **SC-003**: The edit lock on visits older than 1 calendar day is enforced for 100% of locked visits — no out-of-window visit can be modified via any UI path.
- **SC-004**: A visit created from a patient profile is visible on the global Visits page within the same session without requiring a full page reload.
- **SC-005**: Doctors see only their own visits; no UI path exposes another doctor's visit list to a different doctor.
- **SC-006**: An admin can create a visit assigned to any doctor-patient combination from a single form in under 90 seconds.

---

## Assumptions

- "A doctor's visits" means visits where that doctor is the assigned doctor — there is no separate doctor-patient assignment model; visit ownership determines visibility.
- The 1-day edit window is calculated by calendar date: a visit from yesterday is editable; a visit from the day before yesterday is not.
- Visit scheduling is future-only; recording visits in the past is out of scope for this feature.
- Notes are a free-text optional field.
- No visit deletion is in scope for this feature.
- Patient-role users are not in scope — they cannot create, edit, or view visits through this feature.
- Upcoming visits are those with a scheduled date/time strictly in the future; today's scheduled visits are also considered upcoming until their time passes.
