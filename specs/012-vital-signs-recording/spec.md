# Feature Specification: Vital Signs Recording & History

**Feature Branch**: `012-vital-signs-recording`
**Created**: 2026-05-10
**Status**: Draft

## Overview

Doctors record one set of vital signs measurements per clinical visit — blood pressure, heart rate, temperature, weight, and height. The system automatically computes BMI when both weight and height are provided and stores it alongside the measurements for historical consistency. Abnormal values are flagged for the doctor's attention without blocking data entry. Patients can view their own vitals history. The data structure is explicitly designed to support future AI-powered risk scoring, visit narrative generation, and dietary plan effectiveness evaluation.

---

## User Scenarios & Testing *(mandatory)*

### User Story 1 — Doctor Records Vitals on a Visit (Priority: VSR1)

After conducting a clinical visit, a doctor opens the Visit Detail page and records the patient's vital signs. The doctor fills in whichever measurements were taken — not all fields are required. The system immediately shows a BMI calculation if both weight and height are provided. Abnormal values are highlighted so the doctor can decide whether to add a note.

**Why this priority**: Recording vitals is the core data-collection action of this feature. Without it, no history can exist and no downstream AI analysis is possible. It is the first independently deliverable slice.

**Independent Test**: Can be tested end-to-end by logging in as a doctor, opening any visit, recording a partial set of vitals, and verifying the record is saved and displayed on the visit page with correct BMI and flags.

**Acceptance Scenarios**:

1. **Given** a doctor is viewing a visit with no vitals recorded, **When** they click "Record vital signs", **Then** a form opens with fields for BP (systolic/diastolic), heart rate, temperature, weight, and height — all optional.
2. **Given** the form is open, **When** the doctor enters weight and height, **Then** a BMI value is shown as a live preview before saving.
3. **Given** the doctor submits the form with only weight and height filled in, **Then** the vitals record is saved, BMI is computed and stored, and the visit page displays the card with those values.
4. **Given** the doctor enters a systolic BP of 155, **When** the record is saved, **Then** the vitals card displays the value with an abnormal flag — but the save is not blocked.
5. **Given** a vitals record already exists for a visit, **When** the doctor clicks Edit, **Then** the existing values are pre-filled and can be partially updated.

---

### User Story 2 — Doctor Views Vitals History on Patient Profile (Priority: VSR2)

A doctor opens a patient's profile and navigates to the Vitals tab to review all vital signs recorded across all visits, ordered from most recent to oldest. The table shows a BMI trend indicator per row (up, down, or stable vs. the previous visit). The patient's current weight-status category (underweight, overweight, obese) is visible on the overview card.

**Why this priority**: Trend review is the primary clinical value of recording vitals over time. Without historical view, each vitals record is isolated and loses medical context.

**Independent Test**: Can be tested independently with at least two visits with vitals recorded for the same patient. Navigate to the patient profile Vitals tab and verify the table shows both records in date-descending order with a BMI delta chip on the second row.

**Acceptance Scenarios**:

1. **Given** a patient has vitals on three visits, **When** the doctor opens the patient's Vitals tab, **Then** all three records appear in a table ordered by visit date descending, each row showing date, BP, heart rate, temperature, weight, height, BMI, and any flags.
2. **Given** the patient's BMI decreased from 28.2 to 26.1 between visits, **When** the table renders, **Then** the most recent row shows a downward-trend chip alongside BMI 26.1.
3. **Given** the patient's BMI is 31.0, **When** the patient overview card is displayed, **Then** an "Obese" status badge is visible on the card.
4. **Given** no vitals have been recorded for the patient, **When** the Vitals tab is opened, **Then** an empty state is shown with guidance to record vitals on a visit.

---

### User Story 3 — Patient Views Own Vitals (Priority: VSR3)

A patient logs into the system and can view their own vital signs from past visits. They can see each measurement with its value and unit, and whether any value was flagged as outside the normal range. They cannot create, edit, or delete any vitals record.

**Why this priority**: Patient read access is a data-privacy and transparency requirement, but it does not affect the core clinical workflow. Delivered after doctor-facing recording is stable.

**Independent Test**: Log in as a patient, navigate to vitals history, verify all own vitals are visible and all create/edit/delete controls are absent.

**Acceptance Scenarios**:

1. **Given** a patient is logged in, **When** they view their vitals history, **Then** they see only records from their own visits.
2. **Given** a patient attempts to access another patient's vitals URL directly, **Then** the system returns an access-denied response.
3. **Given** a patient views their vitals, **Then** no "Record", "Edit", or "Delete" controls are visible.

---

### User Story 4 — Admin Manages Vitals (Priority: VSR4)

An administrator has full access to all vitals records across all patients. An admin can record vitals on any visit, edit any existing record, and permanently delete a vitals record when required (e.g., data entry error correction). Deletion is a destructive action and requires explicit confirmation.

**Why this priority**: Admin oversight is a system integrity requirement. Admin delete is needed for error correction but is rare and delivered last.

**Independent Test**: Log in as admin, open any visit belonging to any patient, record vitals, then delete the record, and verify it is removed.

**Acceptance Scenarios**:

1. **Given** an admin opens a visit belonging to any doctor's patient, **When** no vitals exist, **Then** the "Record vital signs" button is visible and functional.
2. **Given** vitals exist on a visit, **When** an admin clicks Delete and confirms, **Then** the vitals record is permanently removed and the visit detail page returns to the empty state.
3. **Given** an admin deletes a vitals record, **Then** the patient's vitals history table no longer includes that row.

---

### Edge Cases

- What happens when a doctor tries to record vitals on a visit they did not conduct? The system denies the action with a clear permission message.
- What happens when only weight is provided (no height)? BMI is left blank — no partial BMI is computed or stored.
- What happens when a doctor submits the form with all fields empty? The system rejects the submission — at least one measurement must be provided.
- What happens when a patient has only one vitals record? The BMI trend chip shows a neutral/stable indicator (no previous record to compare).
- What happens when vitals already exist for a visit and the doctor tries to create a second set? The system rejects the creation — only one vitals record per visit is allowed; the doctor must edit the existing one.
- What happens when height is updated on an existing record and BMI changes significantly? The stored BMI reflects the value at time of update — historical records before that update are not retroactively changed.

---

## Requirements *(mandatory)*

### Functional Requirements

**Recording & Editing**

- **FR-001**: A doctor MUST be able to record vital signs on any visit they conducted, including partial records where only some fields are filled.
- **FR-002**: An admin MUST be able to record, edit, and delete vital signs on any visit regardless of which doctor conducted it.
- **FR-003**: The system MUST prevent more than one vitals record per visit — recording is blocked if a record already exists; the doctor must edit the existing one.
- **FR-004**: The system MUST compute and store BMI automatically when both weight and height are provided, using the formula: weight (kg) ÷ height (m)².
- **FR-005**: The system MUST NOT compute or store BMI when either weight or height is missing.
- **FR-006**: The system MUST display a live BMI preview during data entry as soon as both weight and height are entered, before the form is submitted.
- **FR-007**: A patient MUST NOT be able to create, edit, or delete any vitals record.
- **FR-008**: A doctor MUST NOT be able to record or edit vitals for a visit they did not conduct.

**Abnormal Value Flagging**

- **FR-009**: The system MUST flag abnormal values at the point of display (not at the point of entry) using the following thresholds — without blocking submission:
  - Systolic BP outside 90–140 mmHg
  - Diastolic BP outside 60–90 mmHg
  - Heart rate outside 50–100 bpm
  - Temperature outside 36.0–37.5 °C
  - BMI below 18.5 (underweight), 25–29.9 (overweight), or 30+ (obese)
- **FR-010**: The abnormal flags MUST be included in every vitals response so that any client (UI or AI service) can act on them without recomputing thresholds.

**History & Access**

- **FR-011**: The system MUST provide a complete vitals history for a patient across all visits, ordered by visit date descending, accessible by doctors, admins, and the patient themselves.
- **FR-012**: The vitals history MUST be filterable by date range so future AI services can isolate the period during which a specific dietary plan was active.
- **FR-013**: A patient MUST only be able to view vitals records from their own visits — accessing another patient's vitals MUST result in an access-denied response.
- **FR-014**: Each record in the vitals history MUST include the visit date, patient identifier, all measured values with their units, computed BMI, and abnormal flags — sufficient for trend analysis without additional data lookups.

**Data Design for AI Readiness**

- **FR-015**: The vitals data record MUST be structured so a future risk-scoring service can retrieve a patient's full vitals history in a single paginated request and determine: current BMI category, BMI trend direction (improving/plateau/worsening) across the last N visits, weight trajectory (kg per week), and which BP readings exceeded thresholds.
- **FR-016**: The vitals record MUST expose previous-visit values or deltas alongside current values so a visit-level AI narrative can be generated without a second data request (e.g., "weight down 2.3 kg since last visit").

### Key Entities

- **Vital Signs Record**: A set of clinical measurements taken at a single visit. Belongs to exactly one visit. Fields: systolic BP (mmHg), diastolic BP (mmHg), heart rate (bpm), body temperature (°C), weight (kg), height (cm), BMI (computed, stored). All measurement fields are optional — at least one must be present.
- **Visit**: The clinical encounter to which vitals belong. A visit has at most one vitals record. Linked to a patient and a doctor.
- **Patient**: The individual whose health is being measured. Can view but not modify their own vitals.
- **Doctor (Doktor)**: The clinician who records and edits vitals for visits they conducted.
- **Admin**: Has full read/write/delete access to all vitals records across all patients and visits.

---

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: A doctor can open a visit with no prior vitals and complete recording a partial set of measurements in under 60 seconds.
- **SC-002**: 100% of abnormal values are flagged on display — no measurement outside the defined thresholds is presented to the user without a visual indicator.
- **SC-003**: BMI is computed and stored on every record where both weight and height are provided — zero records with both values present have a missing BMI.
- **SC-004**: A patient can view their own vitals history and sees zero vitals records belonging to other patients.
- **SC-005**: The vitals history endpoint returns records in visit-date-descending order for 100% of responses — no out-of-order rows.
- **SC-006**: The vitals history response for a single patient call contains all data required for AI trend analysis (visit date, all measurements, BMI, flags) — no secondary request is needed to compute trends.
- **SC-007**: All role-based access rules are enforced — patients cannot write, doctors cannot access visits they did not conduct, and all unauthorized attempts result in an access-denied response (not a data leak).

---

## Assumptions

- A "partial" vitals record requires at least one measurement to be non-null — a completely empty form submission is rejected.
- BMI is stored as computed at the time of the last save. Editing height or weight recalculates and overwrites the stored BMI but does not retroactively update older records.
- Abnormal thresholds are fixed system-wide — there is no per-patient or per-age-group customisation in this feature.
- The "previous visit" for BMI delta calculation in the history table is the immediately preceding visit in date order that has a vitals record — visits without vitals are skipped.
- Date-range filtering on the history endpoint uses the visit date (not the record creation date).
- Vitals records are never soft-deleted — admin delete is permanent.

---

## AI Integration Touchpoints *(future — not implemented in this feature)*

These use cases are documented here so the data model and history endpoint are designed to support them without future breaking changes.

### 1. Nutritional Risk Scoring

A risk-scoring service will consume the vitals history to produce a per-patient risk level (low / moderate / high / critical). Inputs: current BMI, BMI trend across last N visits, BP threshold exceedances, weight trajectory per week. The history endpoint must return sufficient data for this computation in one paginated call.

### 2. Contextual Visit Insights

When a doctor queries the AI assistant about a patient or opens a visit, the AI generates a plain-language "since last visit" summary using vitals deltas. Example: "Patient lost 2.3 kg since Jan 15. BP improved from 145/92 to 132/84. BMI now 26.1, down from 27.4 — trend is positive." The vitals record must expose both current and previous-visit values (or computed deltas) so the AI can generate this narrative without an additional data call.

### 3. Dietary Plan Effectiveness Scoring

Weight and BMI trajectory are the primary objective signal for evaluating whether a prescribed dietary plan is producing results. If weight plateaus or worsens across three or more consecutive visits while on the same plan, the AI surfaces a refinement prompt to the doctor. The history endpoint's date-range filter is required so the AI can isolate the period a specific plan was active.

---

## Out of Scope

- Vitals trend charts or graphs (deferred to a later UI enhancement)
- AI tool implementation — touchpoints are documented, not built
- Dietary plan management (a separate future feature)
- Laboratory results, medications, and clinical recommendations (Groups 5 and beyond)
- Per-patient or age-adjusted abnormal thresholds
