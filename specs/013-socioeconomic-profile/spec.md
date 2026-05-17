# Feature Specification: Patient Socioeconomic Profile

**Feature Branch**: `013-socioeconomic-profile`  
**Created**: 2026-05-17  
**Status**: Draft  
**Input**: Add a Socioeconomic profile tab and form section to the patient management feature in the NutriBase frontend.

## User Scenarios & Testing *(mandatory)*

### User Story 1 - View Socioeconomic Profile (Priority: P1)

A doctor or administrator opens a patient's record and navigates to the Socioeconomic tab to review the patient's background — employment status, living conditions, food security, lifestyle factors, and support systems — before or after a consultation. This context informs dietary recommendations and treatment planning.

**Why this priority**: Viewing is the prerequisite for all other stories. A read-only tab already has value on its own — clinical staff can immediately access data that is recorded in the system but currently invisible. This is the largest gap in the current UI.

**Independent Test**: Open any patient record, click the Socioeconomic tab, and verify all 17 fields across 5 sections are displayed with correct labels, human-readable values, and "—" for missing data.

**Acceptance Scenarios**:

1. **Given** a patient record has socioeconomic data recorded, **When** a doctor opens the patient detail page and clicks the Socioeconomic tab, **Then** all five sections (Demographics & Social, Economic, Lifestyle, Support Systems, Food Security) are displayed with correct labels and human-readable values for each field.
2. **Given** a patient record has no socioeconomic data yet, **When** any authorised user opens the Socioeconomic tab, **Then** all fields display "—" and an "Edit" button is visible to clinical staff.
3. **Given** a patient is viewing their own record, **When** they open the Socioeconomic tab, **Then** they can see their own data read-only with no Edit button visible.
4. **Given** a boolean field (e.g. has_health_insurance) is true, **When** displayed in the tab, **Then** it renders as a "Yes" indicator; when false, as "No".
5. **Given** an enum field (e.g. employment_status = "employed_full_time"), **When** displayed in the tab, **Then** it renders as a human-readable label ("Employed Full Time"), not the raw enum value.

---

### User Story 2 - Edit Socioeconomic Profile (Priority: P2)

A doctor or administrator reviews or updates a patient's socioeconomic profile from the Socioeconomic tab. They click "Edit", fill in or change fields across the five categories, and save. The tab immediately reflects the updated data.

**Why this priority**: Without editing, the data is forever locked to whatever was set at patient creation (or remains empty). Editing closes the lifecycle for maintaining an accurate clinical picture over time.

**Independent Test**: Open a patient's Socioeconomic tab, click "Edit", change at least one field per category, save, and verify the tab reflects all changes without a page reload.

**Acceptance Scenarios**:

1. **Given** a doctor is on the Socioeconomic tab, **When** they click "Edit", **Then** a form opens (in a dialog or panel) pre-populated with the patient's existing socioeconomic data.
2. **Given** the edit form is open, **When** the doctor changes values and submits, **Then** the changes are saved and the tab immediately shows the updated values.
3. **Given** all fields are optional, **When** the doctor submits the form with no fields filled, **Then** the save succeeds and all fields display "—".
4. **Given** a network or server error occurs on save, **When** the doctor submits the form, **Then** an error message is shown and the form remains open with the user's input preserved.
5. **Given** a patient (pacijent role) is viewing their own record, **When** they are on the Socioeconomic tab, **Then** no "Edit" button is visible and they cannot modify the data.

---

### User Story 3 - Include Socioeconomic Data at Patient Creation (Priority: P3)

When creating a new patient, a doctor or administrator can optionally expand a "Socioeconomic Information" section at the bottom of the patient creation form to record socioeconomic data at intake, without needing to return to the patient record afterward.

**Why this priority**: Capturing this at intake is operationally efficient but not blocking. A patient can be created without it and the data added via the Edit flow (Story 2). This story is additive convenience.

**Independent Test**: Open the Add New Patient form, expand the Socioeconomic Information section, fill in several fields, submit, open the new patient's Socioeconomic tab, and confirm the data is present.

**Acceptance Scenarios**:

1. **Given** a doctor opens the Add New Patient form, **When** they view the form, **Then** a collapsed "Socioeconomic Information" section is visible at the bottom, clearly marked as optional.
2. **Given** the section is collapsed, **When** the doctor submits the form without expanding it, **Then** the patient is created successfully with no socioeconomic data recorded.
3. **Given** the doctor expands the section and fills in fields, **When** they submit the form, **Then** the patient and their socioeconomic data are saved together in a single action.
4. **Given** a validation error occurs on the core patient fields, **When** the form is submitted, **Then** the socioeconomic section retains its expanded/collapsed state and all entered values.

---

### User Story 4 - Include Socioeconomic Data When Editing a Patient (Priority: P4)

When editing an existing patient's core details (name, contact info, medical fields), clinical staff can also update socioeconomic data in the same form via an optional collapsible section.

**Why this priority**: Convenience for combined edits; lower priority than Story 2 since the dedicated Socioeconomic tab already covers this need.

**Independent Test**: Open a patient's Edit form, expand the Socioeconomic section, change a field, save, and verify the change appears in the Socioeconomic tab.

**Acceptance Scenarios**:

1. **Given** a doctor opens the Edit Patient form, **When** they view the form, **Then** the Socioeconomic Information section is present, collapsed by default, and pre-populated with existing data when expanded.
2. **Given** the doctor edits only the socioeconomic section, **When** they save, **Then** only the socioeconomic data is updated and core patient data is unchanged.

---

### Edge Cases

- What happens when a patient has partial socioeconomic data (some fields filled, some null)? Each null field must render as "—" without affecting the display of adjacent filled fields.
- What happens if the socioeconomic record does not exist at all (no row in the database) vs. exists with all nulls? The UI must handle both states identically — all fields show "—" and the Edit button is available.
- What if a doctor saves the edit form with no changes? The save succeeds silently; no error or "nothing changed" warning is required.
- What if `number_of_dependents` is 0 (a valid integer)? It must display as "0", not as "—" (which would imply missing data).
- What if the socioeconomic tab is opened by an admin for a patient they don't directly manage? Admin has full read/write access to all patient socioeconomic data.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: The patient detail page MUST include a "Socioeconomic" tab accessible alongside the existing Medical, Visits, and Vitals tabs.
- **FR-002**: The Socioeconomic tab MUST display all 17 socioeconomic fields, grouped into five named sections: Demographics & Social, Economic, Lifestyle, Support Systems, and Food Security.
- **FR-003**: Each enum field MUST be displayed as a human-readable label rather than the raw stored value (e.g., "Employed Full Time" not "employed_full_time").
- **FR-004**: Boolean fields (has_health_insurance, has_family_support, has_caregiver) MUST be displayed with clear Yes/No indicators.
- **FR-005**: Fields with no recorded value MUST display "—" (em dash) as a placeholder.
- **FR-006**: The Socioeconomic tab MUST display an "Edit" button visible only to users with the admin or doctor (doktor) role.
- **FR-007**: Clicking "Edit" MUST open a form pre-populated with the patient's current socioeconomic data (or empty fields if none recorded).
- **FR-008**: The socioeconomic edit form MUST include controls for all 17 fields: dropdowns/selects for enum fields, toggles or checkboxes for boolean fields, and free-text inputs for text/integer fields.
- **FR-009**: All socioeconomic fields MUST be optional — the form MUST be submittable with zero fields filled.
- **FR-010**: On successful save, the Socioeconomic tab MUST reflect updated values without requiring a full page reload.
- **FR-011**: On save failure, the edit form MUST display a meaningful error message and preserve all user input.
- **FR-012**: The patient creation form MUST include a collapsed "Socioeconomic Information" section at the bottom, clearly labelled as optional.
- **FR-013**: Expanding the socioeconomic section in the creation form MUST reveal the same 17 fields as the edit form.
- **FR-014**: Submitting the patient creation form WITHOUT expanding the socioeconomic section MUST succeed and create the patient with no socioeconomic data.
- **FR-015**: The patient edit form MUST also include a collapsed "Socioeconomic Information" section that, when expanded, is pre-populated with existing data.
- **FR-016**: Users with the patient (pacijent) role MUST be able to view their own socioeconomic data in read-only mode; the "Edit" button MUST NOT be visible to them.
- **FR-017**: Users with the patient role MUST NOT be able to modify socioeconomic data through any form or UI action.

### Key Entities

- **PatientSocioeconomicProfile**: A record associated one-to-one with a Patient, capturing social, economic, and lifestyle context across 17 optional fields. A patient may have no profile (no record exists) or a partial profile (record exists with some null fields). The profile is updated as a single unit.
- **SocioeconomicField categories**:
  - *Demographics & Social*: marital status, number of dependents, living arrangement
  - *Economic*: employment status, occupation, income level, health insurance coverage
  - *Lifestyle*: education level, smoking status, alcohol consumption, physical activity level
  - *Support Systems*: family support availability, caregiver availability, transportation access
  - *Food Security*: food security status, cultural/dietary restrictions, additional notes

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: A doctor can navigate from the patient list to a patient's Socioeconomic tab and read all available socioeconomic data in under 30 seconds without training.
- **SC-002**: A doctor can open, edit, and save a patient's socioeconomic profile in under 3 minutes, including the time to expand and fill multiple fields.
- **SC-003**: 100% of the 17 socioeconomic fields are accessible for viewing and editing from the patient detail page — no field requires a separate page or external tool.
- **SC-004**: When creating a patient with socioeconomic data included, the combined form completes in a single submission with no additional steps.
- **SC-005**: Patients can view their own socioeconomic profile without any "access denied" or missing-tab experience.
- **SC-006**: No socioeconomic data is lost or reset when a form submission fails — all entered values are preserved in the form.

## Assumptions

- The backend API already stores and returns the full socioeconomic profile as part of the patient resource. No backend or API changes are required for this feature.
- The backend accepts partial socioeconomic updates — fields not included in a save payload are left unchanged (or null if explicitly cleared).
- Role enforcement (admin/doktor can edit, pacijent read-only) is enforced by both the UI (no Edit button shown) and the backend (API will reject unauthorised mutations). The FE applies the role check as a UX guard, not the sole security layer.
- `number_of_dependents = 0` is a meaningful clinical value (no dependents) and must not be treated as missing.
- The socioeconomic section in create/edit patient forms is collapsed by default and optional — existing patient creation flows are not broken if the user ignores it.
- Enum value sets are fixed and defined by the backend data model; the UI does not need to fetch them dynamically.

## Out of Scope

- No backend, API, or database changes.
- No new API routes or endpoints.
- No changes to the existing Medical, Visits, or Vitals tabs.
- No socioeconomic data displayed on the Patients list page.
- No bulk editing of socioeconomic data across multiple patients.
- No reporting, filtering, or searching of patients by socioeconomic attributes.
