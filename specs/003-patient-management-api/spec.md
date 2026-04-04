# Feature Specification: Patient Management API

**Feature Branch**: `003-patient-management-api`
**Created**: 2026-03-31
**Status**: Draft
**Input**: User description: "Build a comprehensive REST API for Patient Management in the NutriLedger application. Enables admins and doctors to manage patient records (personal, medical, socioeconomic), with role-based authorization, JSON:API responses, soft delete + restore, and dual-entity creation of socioeconomic records."

## User Scenarios & Testing (mandatory)

### User Story 1 - Manage patients (Priority: P1)
As an Admin or Doctor I can create, list, view, update, archive (soft-delete) and restore patient records so I can manage patient care and records.

Why this priority: Core clinician/administrator workflow; enables all other features.

Independent Test: Using authenticated Admin/Doctor credentials, perform full CRUD + restore via API and validate responses and persisted data.

Acceptance Scenarios:
1. Given an authenticated Admin, when they POST valid patient + socioeconomic data, then the system returns 201 and the created patient resource including socioeconomic relationship.
2. Given an authenticated Doctor, when they GET /patients, then they receive a paginated list of patients with relationships loaded.
3. Given an authenticated Admin, when they DELETE a patient, then the patient is soft-deleted and subsequent GET by non-admin omits it; restore returns it.

---

### User Story 2 - Patient self-view and update (Priority: P2)
As a Patient I can view and update my own profile and socioeconomic details so I can keep my information current.

Why this priority: Empowers patients to verify personal data; lower priority than clinician workflows.

Independent Test: Authenticate as a patient and GET/PUT your patient resource; ensure other patients are inaccessible.

Acceptance Scenarios:
1. Given an authenticated Patient, when they GET /patients/{id} for their id, then they receive their resource; requests for other ids return 403.
2. Given an authenticated Patient, when they PATCH their patient resource with valid fields, then changes persist and API returns updated resource.

---

### User Story 3 - Incremental socioeconomic data capture (Priority: P3)
As a clinician or patient I can add or update socioeconomic fields independently from patient core fields so data can be collected progressively.

Why this priority: Complements clinical data; not blocking core CRUD flows.

Independent Test: Update only socioeconomic subset and verify other patient fields remain unchanged; socioeconomic record is created if absent.

Acceptance Scenario:
1. Given an existing patient with no socioeconomic record, when clinician PATCHes socioeconomic data, then a socioeconomic record is created and linked.

---

### Edge Cases
- Requests with partial socioeconomic payloads should create/patch only provided fields.
- Attempts to change linked user_id after creation must fail (422).
- Restoration must reinstate associated socioeconomic record state as it was at deletion.
- Concurrent updates must preserve atomicity (last-write-wins acceptable if conflict resolution is not required).

## Requirements (mandatory)

## Clarifications

### Session 2026-03-31
- Q: JSON format contract — use strict JSON:API v1 structure? → A: Yes (Strict JSON:API v1 compliance)
- Q: Socioeconomic field types — enums or free-form? → A: Enums for key socio fields (maritalStatus, employmentStatus, incomeLevel, smokingStatus, alcoholConsumption, physicalActivityLevel, foodSecurityStatus)
- Q: Enum value sets — use spec prompt lists or shorter curated lists? → A: Use spec prompt lists (Recommended)

### Socioeconomic Enum Values (from spec prompt)
- maritalStatus: single, married, divorced, widowed, separated, other
- employmentStatus: employed_full_time, employed_part_time, self_employed, unemployed, retired, student, unable_to_work, other
- incomeLevel: low, lower_middle, middle, upper_middle, high
- smokingStatus: never, former, current_light, current_heavy
- alcoholConsumption: none, occasional, moderate, heavy
- physicalActivityLevel: sedentary, lightly_active, moderately_active, very_active
- foodSecurityStatus: food_secure, food_insecure, unsure

- Q: Soft-delete behavior for socioeconomic record when patient is soft-deleted? → A: Socioeconomic soft-deletes with patient; restored with patient. User hard-delete cascades to patient and socioeconomic.
- Q: Can Admin/Doctor list soft-deleted patients? → A: No listing of deleted patients needed; only Admins can see deleted users, and restoring the user restores the patient.

### Edge Cases
- Requests with partial socioeconomic payloads should create/patch only provided fields.
- Attempts to change linked user_id after creation must fail (422).
- Restoration must reinstate associated socioeconomic record state as it was at deletion.
- Concurrent updates must preserve atomicity (last-write-wins acceptable if conflict resolution is not required).
- Socioeconomic records are soft-deleted together with their Patient and restored on Patient restore; hard-deleting the linked User cascades and permanently removes Patient and Socioeconomic records.




### Functional Requirements
- FR-001: API MUST allow authorized Admins and Doctors to create a patient and its socioeconomic record in a single request.
- FR-002: API MUST return patient lists filtered by role (Admin/Doctor: all; Patient: own only).
- FR-003: API MUST allow retrieval of a single patient with related socioeconomic data and user relationship.
- FR-004: API MUST allow updating patient fields, socioeconomic fields, or both; updating user linkage (user_id) after create is forbidden.
- FR-005: API MUST support soft-delete (archive) and restore operations; soft-deleted patients are excluded from all patient queries (no trashed listing endpoint). Patient restoration occurs via User restore (Admin-only in User Management API).
- FR-006: API responses MUST use a stable, documented JSON envelope with camelCase attribute keys. Request body inputs MUST use snake_case keys (no conversion layer — see Constitution Principle VI).
- FR-007: API MUST validate inputs and return field-level errors for invalid data (422) and proper auth errors (401/403).
- FR-008: Authorization MUST enforce role rules: Admin/Doctor broad access; Patient limited to own record.
- FR-009: Creating or updating patient data MUST create or update socioeconomic record atomically.
- FR-010: API MUST provide pagination and metadata for list endpoints.

### Key Entities
- Patient: personal and medical profile (first_name, last_name, date_of_birth, gender, phone, address, blood_type, allergies, medical_notes, emergency_contact_name, emergency_contact_phone, etc.)
- PatientSocioeconomic: social determinants (marital_status, number_of_dependents, employment_status, income_level, has_health_insurance, smoking_status, alcohol_consumption, physical_activity_level, food_security_status, additional_notes)
- User (reference): authentication and role attribution (admin, doktor, pacijent)

## Success Criteria (mandatory)

### Measurable Outcomes
- SC-001: All CRUD endpoints + restore respond with expected HTTP codes (201/200/204/404/403) for valid and invalid requests in 100% of tested cases.
- SC-002: Role-based access is enforced: patients cannot access other patient records (0% leakage in tests).
- SC-003: 95% of standard CRUD responses return within 1 second in test environment under normal load. (Confirmed: 95%<1s)
- SC-004: At least 35 automated HTTP tests (Pest) cover permissions, validation, CRUD, soft-delete, restore, and resource format; all must pass before merge.
- SC-005: Patient resource responses conform to documented envelope and camelCase keys in 100% of sampled responses.

## Assumptions
- User accounts and role management already exist and are authoritative for authorization decisions.
- Data retention and privacy constraints follow project defaults unless specified elsewhere.
- Socioeconomic fields are optional and may be collected incrementally.
- Concurrency conflicts resolved by last-write-wins unless a separate conflict policy is requested.

## Dependencies & Preconditions
- Auth system available for API authentication and role extraction.
- Database migrations for patients and patient_socioeconomic exist.
- API-level pagination and error envelope conventions are defined elsewhere in project guidelines.

## Acceptance Criteria (testable)
- Given valid Admin credentials, POST /patients with patient + socio payload returns 201 and resource includes socioeconomic relationship.
- Given Doctor credentials, GET /patients returns paginated collection including relationships.
- Given Patient credentials, GET /patients returns only the requester’s patient.
- Given invalid payload, POST/PATCH return 422 with field-specific errors.
- DELETE sets deleted flag; patient restore handled via User restore endpoint (Admin-only, User Management API).

## Notes / Out of Scope
- Photo/document uploads, appointment scheduling, export, and audit logs are out of scope for this feature.
- Fine-grained validation rules (exact enum lists) will be provided by the implementation plan.

---

**Spec ready for planning**
