# Feature Specification: AI Diet Plan Generator

**Feature Branch**: `014-ai-diet-plan`  
**Created**: 2026-05-17  
**Status**: Draft  

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Generate Diet Plan for Patient (Priority: P1)

A doctor opens a patient's profile page and clicks "Generate Diet Plan". The system immediately acknowledges the request and begins generating a personalised 7-day meal plan in the background using the patient's clinical and socioeconomic data. Once ready, the plan appears on the page with a daily calorie target, macro breakdown, a full week of meals, and any clinical warnings (e.g. allergen exclusions, food insecurity adaptations).

**Why this priority**: This is the core value of the feature. Without it, nothing else is usable.

**Independent Test**: Can be fully tested by triggering generation for a patient with complete profile data and verifying a completed plan appears with 7 days of meals and no allergens included.

**Acceptance Scenarios**:

1. **Given** a logged-in doctor viewing a patient's profile, **When** they click "Generate Diet Plan", **Then** the system returns an immediate acknowledgement and begins generating the plan in the background.
2. **Given** a plan generation is in progress, **When** the frontend polls for status, **Then** it receives a "pending" status until the plan is ready.
3. **Given** a plan generation completes successfully, **When** the frontend polls for status, **Then** the latest completed plan is returned with rationale, daily calorie target, macro goals, 7 days of meals (breakfast, lunch, dinner, snack), and a warnings list.
4. **Given** a patient has known allergies, **When** a diet plan is generated, **Then** no meal in the plan contains the allergen.
5. **Given** a patient has food-insecure or low-income status, **When** a diet plan is generated, **Then** the meals use affordable, accessible ingredients and the warnings list notes the income/food security constraint.

---

### User Story 2 - View Diet Plan History (Priority: P2)

A doctor returns to a patient's profile and wants to see all previously generated diet plans — not just the latest one. They can browse the history with each entry showing when it was generated and by which doctor.

**Why this priority**: History is the key differentiator from a single-column JSON approach. It gives clinical context over time and lets doctors compare plans.

**Independent Test**: Can be tested independently by generating two plans for the same patient and verifying both appear in the history list with correct metadata.

**Acceptance Scenarios**:

1. **Given** a patient has multiple generated diet plans, **When** a doctor views the diet plan section, **Then** a history list shows all plans ordered newest first, each displaying the generation date and the generating doctor's name.
2. **Given** a doctor clicks a historical plan entry, **Then** the full plan detail is displayed (rationale, macros, 7-day meal grid, warnings).
3. **Given** a new plan is generated, **When** the doctor views the history, **Then** the new plan appears at the top and previous plans remain intact.

---

### User Story 3 - Handle Generation Failure Gracefully (Priority: P3)

If the AI generation fails (e.g. invalid structured output after retry), the doctor is informed clearly and can try again without the system crashing or showing corrupt data.

**Why this priority**: Failure handling is essential for production trust but not blocking for initial value delivery.

**Independent Test**: Can be tested by simulating a generation failure and verifying the plan record shows "failed" status and the UI presents a retry option.

**Acceptance Scenarios**:

1. **Given** the AI generation fails to produce valid output after one retry, **When** the frontend polls for status, **Then** the plan record shows a "failed" status with a human-readable message.
2. **Given** a failed plan exists, **When** the doctor clicks "Regenerate", **Then** a new generation attempt is started and a new plan record is created (the failed record is preserved in history).

---

### User Story 4 - Access Control Enforcement (Priority: P1)

Only doctors and admins can generate or view diet plans. Patients and unauthenticated users are denied access.

**Why this priority**: Access control is a security requirement and must be enforced from the start.

**Independent Test**: Can be tested by attempting to access the diet plan endpoints as a patient or unauthenticated user and verifying 401/403 responses.

**Acceptance Scenarios**:

1. **Given** an unauthenticated request, **When** any diet plan endpoint is called, **Then** a 401 Unauthorized response is returned.
2. **Given** a logged-in patient, **When** they attempt to generate or view diet plans, **Then** a 403 Forbidden response is returned.
3. **Given** a logged-in doctor, **When** they generate or view a diet plan for any patient, **Then** the request is permitted.
4. **Given** a logged-in admin, **When** they generate or view a diet plan for any patient, **Then** the request is permitted.

---

### Edge Cases

- What happens when a patient has no socioeconomic profile recorded? → Generation proceeds with available patient data; warnings note the missing context.
- What happens when a patient has no known allergies or dietary restrictions? → Plan is generated without exclusion constraints; warnings list is empty or minimal.
- What happens when generation is triggered while a "pending" plan already exists? → A new plan record is created regardless; the previous pending record is superseded.
- What happens when the AI service is unavailable? → The job fails gracefully; the plan record is stored with "failed" status and a failure reason.
- What happens when `daily_calories` falls outside the valid range in the AI response? → Validation fails, a retry is attempted once; if still invalid, the record is stored as failed.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: Doctors and admins MUST be able to trigger diet plan generation for any patient from the patient profile.
- **FR-002**: The system MUST return an immediate acknowledgement when generation is triggered, without waiting for the AI response.
- **FR-003**: The system MUST generate a 7-day meal plan personalised using the patient's clinical profile (age, gender, blood type, allergies, medical notes) and socioeconomic data (food security, income level, dietary restrictions, activity level, smoking status, alcohol consumption).
- **FR-004**: Generated meal plans MUST never include ingredients that match the patient's known allergens.
- **FR-005**: Meal plans MUST be calibrated to the patient's food security and income level — food-insecure or low-income patients MUST NOT receive plans requiring expensive or inaccessible ingredients.
- **FR-006**: Each generated plan MUST include: an overall rationale, a daily calorie target, macro targets (protein, carbs, fat in grams), 7 daily meal sets (breakfast, lunch, dinner, snack), and a warnings list.
- **FR-007**: The system MUST validate all AI-generated output before storing it; if validation fails, one automatic retry MUST be attempted before marking the plan as failed.
- **FR-008**: Every generation attempt MUST be stored as a separate record; historical plans MUST NOT be overwritten or deleted on regeneration.
- **FR-009**: The system MUST expose an endpoint to retrieve the full paginated history of diet plans for a patient, ordered newest first.
- **FR-010**: The system MUST expose an endpoint to retrieve the full detail of a specific diet plan by ID.
- **FR-011**: When a plan is in "failed" status, the UI MUST present a human-readable failure message and a retry option.
- **FR-012**: Patients MUST NOT be able to access any diet plan endpoint (generate, list, or view detail).

### Key Entities

- **DietPlan**: Represents a single generation attempt for a patient. Tracks status (pending / completed / failed), the generating doctor, the full structured output (rationale, calorie target, macro goals, 7-day meal data, warnings), and any failure reason. Each patient can have many diet plans.
- **Patient**: The subject of the diet plan. Provides clinical attributes (blood type, allergies, medical notes, age, gender) used to personalise generation.
- **PatientSocioeconomic**: Provides lifestyle and socioeconomic context (food security, income level, dietary restrictions, activity level) that constrains meal affordability and appropriateness.
- **User (Doctor/Admin)**: The actor who triggers generation. Recorded on each plan as the generating doctor for auditability.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: A doctor can trigger diet plan generation and receive an acknowledgement within 2 seconds, regardless of how long the AI generation takes.
- **SC-002**: A completed diet plan is available for viewing within 30 seconds of generation being triggered under normal conditions.
- **SC-003**: 100% of generated plans that pass validation contain exactly 7 days of meals with no allergen violations.
- **SC-004**: All failed generation attempts are stored with a failure reason; no silent failures occur.
- **SC-005**: A patient's full diet plan history (all previous generations) remains accessible and unchanged after a new plan is generated.
- **SC-006**: 100% of patient and unauthenticated access attempts to diet plan endpoints are rejected with the correct HTTP status code.

## Assumptions

- The Laravel AI SDK is already available or will be added as a dependency for this feature.
- An Anthropic API key is available in the environment.
- The patient always has at least a basic profile (name, date of birth, gender); socioeconomic data may be absent for some patients.
- Export to PDF is out of scope for this feature; the "Export PDF" button is a UI placeholder only.
- Real-time push (WebSocket/Reverb) is out of scope for this POC; the frontend uses polling to check generation status.
- Diet plans are clinical decision-support tools only — the generated output carries no medical guarantee and doctors review before acting on it.
- This is an SD-track feature and is not required for the SE university milestone.
- The structured output validation pattern established in this feature will be the reusable baseline for all future AI agent features in the system.
