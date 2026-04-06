# 2. SYSTEM ANALYSIS

## 2.1. System Overview

### 2.1.1. Product Perspective

NutriBase is a web-based clinical information system designed to support multi-role workflows in nutrition clinics and general practice settings. The system exposes a stateless RESTful API as the backend layer, which is consumed by a React TypeScript single-page application. The API and SPA communicate exclusively over HTTPS using JSON request and response bodies. Authentication is token-based: on login, the server issues an opaque Sanctum token that the client includes as a `Bearer` header on all subsequent requests. The system does not use session cookies, making it suitable for deployment across separate origins.

The platform manages four core entities: user accounts (with role assignments), patient profiles, patient socioeconomic profiles, and visit records. Role-based access control is enforced at three layers — route middleware, form request authorization, and policy classes — ensuring that each user type can only access and modify data within their permitted scope.

### 2.1.2. Target Audience

- **Admin** — The system administrator. Admins have full access to all resources: they create, edit, deactivate, restore, and permanently delete user accounts; they manage all patient records including soft-delete and restore operations; and they can delete visit records. Admins benefit from a centralised control point for system access and data governance.

- **Doctor (Doktor)** — Clinical staff responsible for patient care. Doctors can register and manage patient profiles, view all active patients, and create visit records documenting their clinical encounters. They can edit only the visit records they personally created. Doctors benefit from a structured, searchable patient record that includes socioeconomic context alongside clinical data.

- **Patient (Pacijent)** — The end user receiving care. Patients have read-only access restricted to their own profile and their own visit history. They cannot view other patients' records, cannot create or modify any data, and cannot access user management functions. Patients benefit from transparent access to their own medical information.

### 2.1.3. Project Constraints and Risks

**Timeline constraint.** The project is structured around three university milestones: M1 (April 5 2026, documentation only), M2 (May 3 2026, Release 1: auth and patient management), and M3 (June 7 2026, Release 2: visits, patterns, tests, deployment). The interval between M2 and M3 is five weeks, during which the visit API, design patterns, test coverage, and deployment must all be completed. A delay in M2 delivery directly compresses the time available for M3.

**Team coordination risk.** The project is developed by a team of two, with backend (Laravel API) and frontend (React SPA) work proceeding in parallel. A divergence in the agreed API contract — response shapes, field names, HTTP status codes — between the two tracks would require rework on both sides. This risk is mitigated by defining the response envelope structure and endpoint contracts before implementation begins.

**Dual-entity creation complexity.** Registering a new patient requires atomically creating both a `Patient` record and an associated `PatientSocioeconomic` record in a single database transaction. If either insert fails, both must be rolled back. This is the most complex data flow in the system and introduces the risk of partial data corruption if not handled correctly.

**CORS configuration risk.** The React SPA and the Laravel API are deployed to separate origins. Misconfigured CORS headers — particularly the allowed origin, allowed headers, or preflight response — would silently block all API requests from the frontend without obvious error messages in the application UI.

**Cross-patient data leakage.** If the patient policy is not correctly enforced, an authenticated patient could request another patient's profile or visit records by guessing their ID. This is a critical security risk in a medical context. It is mitigated by explicit policy checks in every controller action that touches patient-scoped data, and by explicit test cases that verify 403 responses for cross-patient access attempts.

### 2.1.4. Success Criteria

The following measurable criteria define a successful delivery:

- All 19 API endpoints (`/api/login`, `/api/logout`, `/api/user`, 7 user endpoints, 7 patient endpoints, and 3 visit endpoints) return the correct HTTP status codes for all role combinations.
- Three-layer authorization is in place for every protected endpoint: `auth:sanctum` middleware, `authorize()` in the form request, and a named policy method.
- Unauthenticated requests to protected endpoints return HTTP 401. Requests from unauthorized roles return HTTP 403. Patients attempting to access other patients' records return HTTP 403.
- A minimum of 5 Pest HTTP feature tests pass, covering authentication, role-gated access, and at least one validation failure case.
- The application is accessible at a publicly reachable URL on Railway or Fly.io.
- The React SPA renders the login page, patient list, patient profile, and visit history without JavaScript errors.

---

## 2.2. Requirements Analysis

### 2.2.1. Functional Requirements

---

**1. Admin Login**
As an **admin**, I want to log in with my email and password so that I can access the management dashboard.

**Acceptance Criteria:**
1. The user navigates to the login page of the React SPA.
2. The user enters a valid admin email address and password and submits the form.
3. The SPA sends a `POST /api/login` request with `{ "email": "...", "password": "..." }`.
4. The server validates the credentials against the `users` table and confirms the role is `admin`.
5. The server returns HTTP 200 with a JSON body containing the Sanctum token and user details wrapped in the API envelope.
6. The SPA stores the token in the auth store and redirects the user to the admin dashboard.
7. If the email does not exist, the server returns HTTP 422 with a validation error message.
8. If the password is incorrect, the server returns HTTP 422 with a credential mismatch error.
9. If the user submits the form with an empty email or password field, the server returns HTTP 422 with field-level validation errors.
10. After three consecutive failed attempts within one minute, the login endpoint returns HTTP 429 (Too Many Requests).

---

**2. Doctor Login**
As a **doctor**, I want to log in to the system so that I can manage my assigned patients and visits.

**Acceptance Criteria:**
1. The user navigates to the login page.
2. The user enters a valid doctor email and password and submits the form.
3. The SPA sends `POST /api/login` with valid credentials.
4. The server confirms the credentials and the role is `doktor`.
5. The server returns HTTP 200 with a token and user object.
6. The SPA stores the token and redirects to the doctor's patient list view.
7. If the credentials belong to an `admin` or `pacijent` account, the login still succeeds — role enforcement happens at the resource level, not at login.
8. If credentials are invalid, the server returns HTTP 422.

---

**3. Patient Login**
As a **patient**, I want to log in to the system so that I can view my own medical records.

**Acceptance Criteria:**
1. The user navigates to the login page.
2. The user enters valid patient credentials and submits.
3. The SPA sends `POST /api/login`.
4. The server returns HTTP 200 with token and user object.
5. The SPA redirects to the patient's own profile page.
6. The patient cannot navigate to the user list, patient list, or any other patient's profile — these routes are guarded in the SPA and return HTTP 403 from the API.
7. Invalid credentials return HTTP 422.

---

**4. Admin Creates User Account**
As an **admin**, I want to create new user accounts and assign them a role so that staff and patients can access the system.

**Acceptance Criteria:**
1. The authenticated admin sends `POST /api/users` with `{ "name": "...", "email": "...", "password": "...", "role": "doktor" }`.
2. The request passes the `auth:sanctum` middleware and the `StoreUserRequest` authorization check, which confirms the authenticated user is an admin.
3. The server validates that `email` is unique, `role` is one of `admin`, `doktor`, `pacijent`, and `password` meets the minimum length requirement.
4. The server creates the user record and returns HTTP 201 with the new user wrapped in `UserResource`.
5. If the email is already taken, the server returns HTTP 422 with `errors.email`.
6. If the role value is not one of the allowed strings, the server returns HTTP 422 with `errors.role`.
7. If required fields are missing, the server returns HTTP 422 with field-level errors.
8. If the request is made by a doctor or patient, the server returns HTTP 403.
9. If the request is unauthenticated, the server returns HTTP 401.

---

**5. Admin Views User List**
As an **admin**, I want to view a list of all registered users so that I can manage system access.

**Acceptance Criteria:**
1. The authenticated admin sends `GET /api/users`.
2. The request passes the `auth:sanctum` middleware and the `UserPolicy::viewAny()` check.
3. The server returns HTTP 200 with a paginated list of users (15 per page), each serialised through `UserResource`.
4. Soft-deleted users are excluded from the response by default.
5. The response includes pagination metadata: current page, total count, last page, and links.
6. If the request is made by a doctor or patient, the server returns HTTP 403.
7. If the request is unauthenticated, the server returns HTTP 401.

---

**6. Admin Edits User Profile**
As an **admin**, I want to edit a user's profile information so that records stay up to date.

**Acceptance Criteria:**
1. The authenticated admin sends `PUT /api/users/{id}` with the fields to update.
2. The request passes `auth:sanctum` middleware and `UpdateUserRequest` authorization.
3. The server applies only the provided fields (partial update supported).
4. The server returns HTTP 200 with the updated user through `UserResource`.
5. If the new email is already taken by another user, the server returns HTTP 422 with `errors.email`.
6. If `{id}` does not correspond to an existing, non-deleted user, the server returns HTTP 404.
7. If the request is made by a doctor or patient, the server returns HTTP 403.
8. If the request is unauthenticated, the server returns HTTP 401.

---

**7. Admin Deactivates User Account**
As an **admin**, I want to deactivate (soft-delete) a user account so that access is revoked without losing historical data.

**Acceptance Criteria:**
1. The authenticated admin sends `DELETE /api/users/{id}`.
2. The request passes `auth:sanctum` middleware and `UserPolicy::delete()`.
3. The server sets `deleted_at` on the user record (soft delete) and revokes all Sanctum tokens for that user.
4. The server returns HTTP 204 with no response body.
5. Subsequent requests authenticated with the deactivated user's token return HTTP 401.
6. The user record remains in the database and can be restored.
7. If `{id}` does not correspond to an active user, the server returns HTTP 404.
8. If the request is made by a non-admin, the server returns HTTP 403.

---

**8. Admin Restores Soft-Deleted User**
As an **admin**, I want to restore a soft-deleted user account so that access can be reinstated when needed.

**Acceptance Criteria:**
1. The authenticated admin sends `POST /api/users/{id}/restore`.
2. The request passes `auth:sanctum` middleware and `UserPolicy::restore()`.
3. The server clears `deleted_at` on the user record.
4. The server returns HTTP 200 with the restored user through `UserResource`.
5. The restored user can log in and receive a new Sanctum token.
6. If `{id}` does not correspond to a soft-deleted user, the server returns HTTP 404.
7. If the request is made by a non-admin, the server returns HTTP 403.

---

**9. Admin Permanently Deletes User**
As an **admin**, I want to permanently delete a user account so that their data is fully removed from the system.

**Acceptance Criteria:**
1. The authenticated admin sends `DELETE /api/users/{id}/force`.
2. The request passes `auth:sanctum` middleware and `UserPolicy::forceDelete()`.
3. The server permanently removes the user record and all associated Sanctum tokens from the database.
4. The server returns HTTP 204.
5. Any subsequent request to `GET /api/users/{id}` returns HTTP 404.
6. This action is irreversible — no restore endpoint exists for force-deleted records.
7. If `{id}` does not exist (including soft-deleted), the server returns HTTP 404.
8. If the request is made by a non-admin, the server returns HTTP 403.

---

**10. Authenticated User Updates Own Profile**
As any **authenticated user**, I want to update my own profile information so that my account details are current.

**Acceptance Criteria:**
1. Any authenticated user sends `PUT /api/users/{id}` where `{id}` matches their own user ID.
2. The `UpdateUserRequest` authorization confirms that `$request->user()->id === $user->id` or the requester is an admin.
3. The server applies the update and returns HTTP 200 with the updated user through `UserResource`.
4. The user cannot change their own role — the `role` field is ignored if present in the request body.
5. If the user attempts to update another user's profile without admin role, the server returns HTTP 403.
6. If the new email is already taken, the server returns HTTP 422.
7. If the request is unauthenticated, the server returns HTTP 401.

---

**11. Register New Patient — Personal Details**
As an **admin or doctor**, I want to register a new patient with their personal details so that they become part of the system.

**Acceptance Criteria:**
1. The authenticated admin or doctor sends `POST /api/patients` with personal fields: `first_name`, `last_name`, `date_of_birth`, `gender`, `phone`, `address`.
2. The request passes `auth:sanctum` middleware and `StorePatientRequest` authorization, which confirms the role is `admin` or `doktor`.
3. The `PatientService` wraps the creation in a database transaction: it creates a `User` account for the patient (role `pacijent`), a `Patient` record, and a blank `PatientSocioeconomic` record atomically.
4. The server returns HTTP 201 with the new patient through `PatientResource`, including the nested user and socioeconomic data.
5. If required personal fields are missing, the server returns HTTP 422 with field-level errors.
6. If the patient's email (used for the linked user account) is already taken, the server returns HTTP 422.
7. If a patient role user sends the request, the server returns HTTP 403.
8. If the transaction fails partway through, all created records are rolled back and the server returns HTTP 500.

---

**12. Register New Patient — Medical Metadata**
As an **admin or doctor**, I want to enter a patient's medical metadata during registration so that clinically relevant information is captured upfront.

**Acceptance Criteria:**
1. The `POST /api/patients` request body may include optional medical fields: `blood_type`, `allergies`, `medical_notes`.
2. If provided, these fields are validated and stored on the `patients` table.
3. `blood_type` must be one of the valid values (A+, A-, B+, B-, AB+, AB-, O+, O-) or null.
4. `allergies` is stored as free text; no format validation is applied.
5. `medical_notes` is stored as free text; maximum length is enforced.
6. If `blood_type` contains an invalid value, the server returns HTTP 422 with `errors.blood_type`.
7. The fields are returned in the `PatientResource` response under the `attributes` object.
8. Medical metadata can be updated independently via the patient update endpoint after initial registration.

---

**13. Admin or Doctor Views Patient List**
As an **admin or doctor**, I want to view the full list of all active patients so that I can quickly find and access any patient record.

**Acceptance Criteria:**
1. The authenticated admin or doctor sends `GET /api/patients`.
2. The request passes `auth:sanctum` middleware and `PatientPolicy::viewAny()`.
3. The server returns HTTP 200 with a paginated list of active (non-deleted) patients, each serialised through `PatientResource`.
4. Each item includes the patient's full name, date of birth, and primary contact number.
5. The response includes pagination metadata.
6. Soft-deleted patients are excluded from the list.
7. If a patient role user sends the request, the server returns HTTP 403.
8. If the request is unauthenticated, the server returns HTTP 401.

---

**14. Admin or Doctor Views Full Patient Profile**
As an **admin or doctor**, I want to view the full profile of a specific patient, including their personal, medical, and socioeconomic data.

**Acceptance Criteria:**
1. The authenticated admin or doctor sends `GET /api/patients/{id}`.
2. The request passes `auth:sanctum` middleware and `PatientPolicy::view()`.
3. The server eager-loads the patient's user account and socioeconomic profile to prevent N+1 queries.
4. The server returns HTTP 200 with the full patient through `PatientResource`, including nested `user`, `socioeconomic`, and computed `fullName` fields.
5. If `{id}` does not correspond to an active patient, the server returns HTTP 404.
6. If a patient role user sends the request for a different patient's profile, the server returns HTTP 403.
7. If the request is unauthenticated, the server returns HTTP 401.

---

**15. Patient Views Own Profile**
As a **patient**, I want to view my own profile and medical information so that I am informed about my records.

**Acceptance Criteria:**
1. The authenticated patient sends `GET /api/patients/{id}` where `{id}` is their own patient ID.
2. `PatientPolicy::view()` confirms that `$user->patient->id === $patient->id`.
3. The server returns HTTP 200 with the patient's own full profile.
4. If the patient attempts `GET /api/patients/{id}` for a different patient's ID, the server returns HTTP 403.
5. The patient cannot see other patients in the patient list — `GET /api/patients` returns HTTP 403 for patient role users.
6. If the request is unauthenticated, the server returns HTTP 401.

---

**16. Admin or Doctor Updates Patient Personal and Contact Information**
As an **admin or doctor**, I want to update a patient's personal and contact information so that records remain accurate.

**Acceptance Criteria:**
1. The authenticated admin or doctor sends `PUT /api/patients/{id}` with personal fields to update.
2. The request passes `auth:sanctum` middleware and `UpdatePatientRequest` authorization.
3. The `PatientService` applies the update to the `patients` table, supporting partial updates.
4. The server returns HTTP 200 with the updated patient through `PatientResource`.
5. If `{id}` does not exist, the server returns HTTP 404.
6. If a field fails validation (e.g., invalid date format for `date_of_birth`), the server returns HTTP 422.
7. If a patient role user sends the request, the server returns HTTP 403.

---

**17. Admin or Doctor Updates Medical Metadata**
As an **admin or doctor**, I want to update a patient's medical metadata so that the clinical record reflects the current state.

**Acceptance Criteria:**
1. The authenticated admin or doctor sends `PUT /api/patients/{id}` including medical metadata fields: `blood_type`, `allergies`, `medical_notes`.
2. The request is handled by the same update endpoint as personal data — the `PatientService` separates fields by their target table.
3. Only provided fields are updated; omitted fields retain their current values.
4. `blood_type` is validated against the allowed enumeration if provided.
5. The server returns HTTP 200 with the updated patient resource.
6. If `blood_type` is invalid, the server returns HTTP 422 with `errors.blood_type`.
7. If a patient role user sends the request, the server returns HTTP 403.

---

**18. Admin or Doctor Views and Updates Socioeconomic Profile**
As an **admin or doctor**, I want to view and update a patient's socioeconomic profile so that the care plan accounts for social determinants.

**Acceptance Criteria:**
1. The socioeconomic data is included in the `PatientResource` response under a `socioeconomic` key whenever `GET /api/patients/{id}` is called.
2. The authenticated admin or doctor sends `PUT /api/patients/{id}` with socioeconomic fields: `marital_status`, `employment_status`, `monthly_income`, `health_insurance`, `smoking_status`, `alcohol_use`, `food_security_score`, `dietary_restrictions`.
3. The `PatientService` routes these fields to the `patient_socioeconomic` table, creating the record if it does not yet exist.
4. The server returns HTTP 200 with the updated patient resource including the updated socioeconomic data.
5. Enum fields (`marital_status`, `employment_status`, `health_insurance`, `smoking_status`) are validated against their allowed value sets.
6. If a field fails validation, the server returns HTTP 422 with field-level errors.
7. If a patient role user sends the request, the server returns HTTP 403.

---

**19. Admin Soft-Deletes Patient Record**
As an **admin**, I want to soft-delete a patient record so that it is removed from the active list without losing data.

**Acceptance Criteria:**
1. The authenticated admin sends `DELETE /api/patients/{id}`.
2. The request passes `auth:sanctum` middleware and `PatientPolicy::delete()`, which requires the admin role.
3. The server sets `deleted_at` on the `patients` record.
4. The server returns HTTP 204.
5. The patient no longer appears in `GET /api/patients` responses.
6. Direct access via `GET /api/patients/{id}` returns HTTP 404 for the soft-deleted record.
7. The patient's visit history and associated data remain in the database.
8. If a doctor or patient role user sends the request, the server returns HTTP 403.

---

**20. Admin Restores Soft-Deleted Patient**
As an **admin**, I want to restore a soft-deleted patient record so that the patient can be reactivated.

**Acceptance Criteria:**
1. The authenticated admin sends `POST /api/patients/{id}/restore`.
2. The request passes `auth:sanctum` middleware and `PatientPolicy::restore()`.
3. The server clears `deleted_at` on the patient record.
4. The server returns HTTP 200 with the restored patient through `PatientResource`.
5. The patient reappears in `GET /api/patients` responses.
6. If `{id}` does not correspond to a soft-deleted patient, the server returns HTTP 404.
7. If a doctor or patient role user sends the request, the server returns HTTP 403.

---

**21. Admin or Doctor Searches Patients by Name**
As an **admin or doctor**, I want to search or filter patients by name so that I can quickly locate specific records in a large list.

**Acceptance Criteria:**
1. The authenticated admin or doctor sends `GET /api/patients?search=smith`.
2. The server applies a case-insensitive `LIKE` filter on the `first_name` and `last_name` columns.
3. The server returns HTTP 200 with the filtered, paginated list of matching patients.
4. If no patients match the search term, the server returns HTTP 200 with an empty `data` array.
5. The search parameter is optional — omitting it returns all active patients.
6. The search term is sanitised to prevent SQL injection.
7. If a patient role user sends the request, the server returns HTTP 403.

---

**22. Doctor Creates Visit Record**
As a **doctor**, I want to create a new visit record for a patient so that each encounter is documented.

**Acceptance Criteria:**
1. The authenticated doctor sends `POST /api/patients/{patient}/visits` with `{ "date": "2026-03-15", "notes": "..." }`.
2. The request passes `auth:sanctum` middleware and `StoreVisitRequest` authorization, which confirms the role is `doktor`.
3. The server auto-assigns `doctor_id` from the authenticated user — this field is never accepted from the request body.
4. The server creates the visit and returns HTTP 201 with the new visit through `VisitResource`.
5. If `{patient}` does not correspond to an active patient, the server returns HTTP 404.
6. If `date` is missing or not a valid date format, the server returns HTTP 422.
7. If `notes` exceeds the maximum length, the server returns HTTP 422.
8. If an admin or patient role user sends the request, the server returns HTTP 403.

---

**23. Admin or Doctor Views Patient Visit History**
As an **admin or doctor**, I want to view the full visit history for a specific patient so that I have a chronological overview of their encounters.

**Acceptance Criteria:**
1. The authenticated admin or doctor sends `GET /api/patients/{patient}/visits`.
2. The request passes `auth:sanctum` middleware and `VisitPolicy::viewAny()`.
3. The server returns HTTP 200 with the list of visits ordered by `date` descending (newest first).
4. Each visit item includes the visit date, the attending doctor's name, and a summary of the notes.
5. If `{patient}` does not correspond to an active patient, the server returns HTTP 404.
6. If a patient role user sends the request for a different patient's history, the server returns HTTP 403.
7. If the request is unauthenticated, the server returns HTTP 401.

---

**24. Doctor Views Visit Details**
As a **doctor**, I want to view the details of a specific visit so that I can review what was recorded.

**Acceptance Criteria:**
1. The authenticated doctor sends `GET /api/patients/{patient}/visits/{visit}`.
2. The server confirms that the `visit.patient_id` matches `{patient}` — a visit cannot be accessed via a mismatched patient route.
3. The server returns HTTP 200 with the full visit through `VisitResource`, including date, notes, and doctor details.
4. If `{visit}` does not belong to `{patient}`, the server returns HTTP 404.
5. If `{patient}` does not exist, the server returns HTTP 404.
6. A patient role user can access `GET /api/patients/{patient}/visits/{visit}` for their own patient ID — `VisitPolicy::view()` allows this.
7. A patient role user attempting to access a visit belonging to a different patient returns HTTP 403.
8. If the request is unauthenticated, the server returns HTTP 401.

---

**25. Doctor Edits Visit Notes**
As a **doctor**, I want to edit the notes of a visit I conducted so that I can correct or supplement the documentation.

**Acceptance Criteria:**
1. The authenticated doctor sends `PUT /api/patients/{patient}/visits/{visit}` with `{ "notes": "..." }` or `{ "date": "..." }` or both.
2. `VisitPolicy::update()` confirms that `$visit->doctor_id === $user->id` — doctors can only edit visits they created.
3. The server applies the update and returns HTTP 200 with the updated visit through `VisitResource`.
4. `doctor_id` cannot be changed via this endpoint — it is ignored if present in the request body.
5. If the authenticated doctor did not create this visit, the server returns HTTP 403.
6. If `{visit}` does not belong to `{patient}`, the server returns HTTP 404.
7. If `date` is provided but is not a valid date format, the server returns HTTP 422.
8. Admin role users can update any visit regardless of `doctor_id`.

---

**26. Admin Deletes Visit Record**
As an **admin**, I want to delete a visit record so that erroneous entries can be removed.

**Acceptance Criteria:**
1. The authenticated admin sends `DELETE /api/patients/{patient}/visits/{visit}`.
2. `VisitPolicy::delete()` confirms the role is `admin`.
3. The server permanently deletes the visit record (hard delete — no soft delete on visits).
4. The server returns HTTP 204.
5. Subsequent requests to `GET /api/patients/{patient}/visits/{visit}` return HTTP 404.
6. The deletion does not affect the patient record or other visit records.
7. If `{visit}` does not belong to `{patient}`, the server returns HTTP 404.
8. If a doctor or patient role user sends the request, the server returns HTTP 403.

---

**27. Patient Views Own Visit History**
As a **patient**, I want to view my own visit history (read-only) so that I can see when and why I attended the clinic.

**Acceptance Criteria:**
1. The authenticated patient sends `GET /api/patients/{patient}/visits` where `{patient}` is their own patient ID.
2. `VisitPolicy::viewAny()` confirms that `$user->patient->id === $patient->id`.
3. The server returns HTTP 200 with the patient's own visit history, ordered by date descending.
4. If the patient sends the request using a different patient's ID, the server returns HTTP 403.
5. The patient cannot create, update, or delete any visit records — those endpoints return HTTP 403.
6. If the request is unauthenticated, the server returns HTTP 401.

---

### 2.2.2. Non-Functional Requirements

**Security**
- All API routes are protected by the `auth:sanctum` middleware, ensuring unauthenticated requests are rejected at the routing layer with HTTP 401 before reaching any application logic.
- Authorization is enforced at three independent layers: route middleware, `authorize()` in form request classes, and dedicated policy methods. A failure at any layer returns HTTP 403.
- Cross-patient data access is a critical security violation. `PatientPolicy` and `VisitPolicy` explicitly verify that patient role users can only access records associated with their own user ID. Test cases explicitly cover these cross-access scenarios.
- All communication between the SPA and the API occurs over HTTPS in production. HTTP requests are redirected to HTTPS at the infrastructure level.

**Performance**
- API responses for single-resource endpoints (show, store, update) should complete in under 500ms under normal load.
- List endpoints (index) use Laravel's built-in pagination with a default page size of 15 records to limit response payload size and database query cost.
- Eloquent eager loading (`with()`) is applied on all endpoints that return nested relationships (e.g., `PatientResource` includes the user and socioeconomic records). This prevents N+1 query patterns that would degrade performance as the patient count grows.

**Usability**
- The React SPA presents a consistent navigation structure with role-appropriate menu items — patients see only their own profile and visit history, while admins and doctors see the full management interface.
- Validation errors from the API are surfaced to the user as inline form field errors, not generic alert messages.
- All form submissions provide visual feedback (loading state on the submit button) while the API request is in progress.

**Scalability**
- The Laravel API is stateless — no server-side session is maintained between requests. All state is carried in the Sanctum token. This allows multiple API instances to be run behind a load balancer without session affinity requirements.
- The database schema uses indexed foreign keys (`user_id` on `patients`, `patient_id` and `doctor_id` on `visits`) to maintain query performance as record counts grow.

**Compliance**
- Patient data — including medical metadata (blood type, allergies) and socioeconomic data (income, employment, health insurance status) — is sensitive personal health information. Access is restricted by role and record ownership as described in the authorization model. No patient data is exposed in error messages or logs.
- The system enforces the principle of least privilege: each role has access only to the minimum set of resources required to perform their clinical function.
