# Feature Specification: Diet Plan Edit and Email Delivery

**Feature Branch**: `015-diet-plan-edit-email`
**Created**: 2026-05-24
**Status**: Draft

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Edit a Generated Diet Plan (Priority: P1)

A doctor opens a patient's diet plan history, selects a completed plan, and reviews it in full. If the AI-generated content needs correction — wrong meal, unsuitable ingredient, inaccurate calorie estimate — the doctor can edit any field (rationale, daily calorie target, macro goals, individual meals, warnings) directly in the UI. Once satisfied, the doctor saves the changes. The plan record is updated with the revised content and a note that it was manually edited.

**Why this priority**: Editing is the prerequisite for email delivery. A doctor must be able to review and correct a plan before sending it to a patient. Raw AI output may require clinical adjustment before it is appropriate to share.

**Independent Test**: Can be fully tested by selecting a completed plan, modifying a meal field, saving, and verifying the updated content is stored and visible on reload.

**Acceptance Scenarios**:

1. **Given** a doctor is viewing a completed diet plan, **When** they click "Edit Plan", **Then** all editable fields (rationale, daily calories, macro goals, each day's meals and snack, warnings) become editable in-place.
2. **Given** a doctor has made changes to one or more fields, **When** they click "Save Changes", **Then** the plan record is updated with the new content and marked as manually edited.
3. **Given** a doctor edits a plan and saves, **When** they navigate away and return to the plan, **Then** the saved changes persist and the plan shows an "Edited" indicator.
4. **Given** a doctor is editing a plan, **When** they click "Cancel", **Then** no changes are saved and the original content is restored.
5. **Given** a pending or failed plan, **When** the doctor views it, **Then** no "Edit Plan" option is available — editing is only permitted on completed plans.

---

### User Story 2 - Send Diet Plan to Patient via Email (Priority: P2)

After reviewing (and optionally editing) a diet plan, a doctor can send it directly to the patient's registered email address with a single action. The email presents the full plan in a readable format: rationale, daily calorie target, macro goals, 7-day meal grid, and any clinical warnings. The system records when the plan was sent and by whom.

**Why this priority**: Email delivery is the primary output action — it completes the clinical workflow by getting the plan into the patient's hands. It depends on having a reviewable (and optionally edited) completed plan.

**Independent Test**: Can be tested by sending a completed plan to a patient with a registered email and verifying the delivery record is created and the email content matches the plan data.

**Acceptance Scenarios**:

1. **Given** a doctor is viewing a completed diet plan, **When** they click "Send to Patient", **Then** an email containing the full diet plan is sent to the patient's registered email address.
2. **Given** the email is sent successfully, **When** the doctor views the plan, **Then** a "Sent" status indicator is shown along with the date and time the plan was last sent.
3. **Given** a plan has been edited and saved, **When** the doctor sends it, **Then** the email reflects the edited (not original AI-generated) content.
4. **Given** a doctor sends a plan that has already been sent before, **When** they confirm the action, **Then** a new delivery is recorded and the email is resent — previous delivery records are preserved.
5. **Given** a patient has no registered email address, **When** the doctor attempts to send the plan, **Then** the action is blocked with a clear message indicating the patient has no email on file.
6. **Given** the email delivery fails (e.g. mail service unavailable), **When** the failure occurs, **Then** the doctor sees a clear error and the delivery is recorded as failed; the plan remains sendable for retry.

---

### User Story 3 - Access Control for Edit and Send (Priority: P1)

Only doctors and admins can edit or send diet plans. Patients and unauthenticated users are denied these actions entirely.

**Why this priority**: Access control is a security requirement. A patient must not be able to alter their own medical records or trigger email communications on behalf of a doctor.

**Independent Test**: Can be tested by attempting to edit or send a diet plan as a patient or unauthenticated user and verifying 401/403 responses.

**Acceptance Scenarios**:

1. **Given** an unauthenticated request, **When** any edit or send endpoint is called, **Then** a 401 Unauthorized response is returned.
2. **Given** a logged-in patient, **When** they attempt to edit or send a diet plan, **Then** a 403 Forbidden response is returned.
3. **Given** a logged-in doctor or admin, **When** they edit or send a diet plan for any patient, **Then** the request is permitted.

---

### Edge Cases

- What happens when a doctor edits a plan and saves, then another doctor generates a new plan? → The edited plan remains in history unchanged; the new plan is a separate record.
- What happens if a required field (e.g. daily calories) is cleared during editing? → The save is blocked with an inline validation message; the plan is not updated until all required fields are filled.
- What happens if the patient's email bounces or is unreachable? → The delivery attempt is recorded as failed; the error is shown to the doctor; no silent failure.
- Can a failed or pending plan be sent via email? → No — only completed plans are eligible for sending.
- What if a plan is sent before it is edited? → Allowed — sending the unedited AI-generated plan is a valid workflow.
- What happens if the doctor leaves the edit form without saving? → A confirmation prompt warns of unsaved changes before navigating away; if dismissed, changes are discarded.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: Doctors and admins MUST be able to edit any completed diet plan for any patient.
- **FR-002**: The editable fields MUST include: rationale, daily calorie target, macro goals (protein, carbs, fat in grams), all seven days of meals (breakfast, lunch, dinner, snack per day), and the warnings list.
- **FR-003**: Editing MUST only be available for plans in `completed` status; pending and failed plans MUST NOT be editable.
- **FR-004**: When a doctor saves edits, the plan record MUST be updated with the revised content and flagged as manually edited — recording the editing user and the timestamp of the edit.
- **FR-005**: All edited fields MUST be validated before saving — required fields cannot be blank, daily calories must be a positive integer within a clinically reasonable range, and the plan must retain exactly 7 days of meals.
- **FR-006**: Doctors and admins MUST be able to send a completed diet plan to the patient's registered email address.
- **FR-007**: The email MUST contain the full plan: rationale, daily calorie target, macro goals, all 7 days of meals, and any warnings.
- **FR-008**: The system MUST record each send action as a delivery event — storing the sender, recipient email, timestamp, and delivery outcome (sent / failed) and any failure reason.
- **FR-009**: A plan MUST be sendable multiple times; each send creates a new delivery record; previous records are preserved.
- **FR-010**: If the patient has no registered email address, the send action MUST be blocked with a clear user-facing message.
- **FR-011**: If email delivery fails, the failure MUST be recorded and surfaced to the doctor; no silent failures.
- **FR-012**: Patients MUST NOT be able to access any edit or send endpoint.
- **FR-013**: The doctor MUST be prompted to confirm before navigating away from an unsaved edit form.

### Key Entities

- **DietPlan (extended)**: Gains `is_edited` flag, `edited_by` (the user who last edited it), and `edited_at` (timestamp of last edit) to distinguish AI-generated content from doctor-reviewed content.
- **DietPlanDelivery**: Represents a single email send attempt for a diet plan. Stores the sending doctor, the recipient email address, the timestamp, the delivery outcome (sent / failed), and any failure reason. A plan can have many delivery records.
- **Patient**: Source of the recipient email address. Already exists — no structural changes required.
- **User (Doctor/Admin)**: Actor who performs edits and initiates sends. Recorded on each edit and delivery event.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: A doctor can open a plan, make edits, and save within a single uninterrupted workflow — no page reloads required.
- **SC-002**: Saved edits are persisted and visible to any doctor viewing the same plan within 2 seconds of saving.
- **SC-003**: An email is delivered to the patient within 30 seconds of the doctor confirming the send action under normal conditions.
- **SC-004**: 100% of send attempts — successful or failed — are recorded as delivery events; no silent outcomes.
- **SC-005**: 100% of patient and unauthenticated access attempts to edit or send endpoints are rejected with the correct HTTP status code.
- **SC-006**: A plan's full history and all previous delivery records remain intact after any edit or resend action.

## Assumptions

- The patient's email address is the email field on the associated `User` record — no separate contact model is needed.
- Editing updates the plan record in-place (the record is the same row, content is overwritten); the AI-generated values are not separately archived once the doctor saves an edit. The `is_edited` flag distinguishes reviewed plans from raw AI output.
- A "resend" confirmation is shown in the UI when the plan has already been sent at least once, but the API does not block resending — the confirmation is a UX-level safeguard only.
- Email formatting is a readable plain-text or simple HTML template; rich PDF rendering is out of scope for this feature.
- This feature builds directly on the `patient_diet_plans` table and `DietPlan*` resources established in feature 014.
