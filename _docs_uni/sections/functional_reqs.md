2.3. Functional Requirements
Functional requirements define the specific behaviors, services, and tasks that a software system must provide to satisfy user needs. They outline "what" the system is supposed to do under specific conditions.
Sommerville, I. (2015). Software Engineering (10th ed.). Pearson.

Authentication
As an admin, I can log in with my email and password so that I can access the management dashboard.
Acceptance criteria:
`POST /api/login` with a valid email and password returns HTTP 200 with a token and user object.
The response body contains a `token` (Sanctum personal access token) and a `user` object with role, name, and email.
An inactive or soft-deleted account returns HTTP 401 with the message "Account is deactivated".
As a doctor, I can log in with my email and password so that I can manage my assigned patients and visits.
Acceptance criteria:
`POST /api/login` with a valid email and password returns HTTP 200 with a token and user object.
The response body contains a `token` (Sanctum personal access token) and a `user` object with role, name, and email.  
An inactive or soft-deleted account returns HTTP 401 with the message "Account is deactivated".
As a patient, I can log in with my email and password so that I can view my own medical records.
Acceptance criteria:
`POST /api/login` with a valid email and password returns HTTP 200 with a token and user object.
The response body contains a `token` (Sanctum personal access token) and a `user` object with role, name, and email.
An inactive or soft-deleted account returns HTTP 401 with the message "Account is deactivated".
As any user, I receive a unique access token upon successful login so that my session is securely identified.
Acceptance Criteria:
The token returned on login is a Sanctum plaintext personal access token unique per login event.
A second login from the same user issues a new, distinct token without invalidating the previous one.
As any authenticated user, I can log out so that my session token is immediately invalidated and my account is secured.
Acceptance Criteria:
`POST /api/logout` (requires valid token) returns HTTP 204.
After logout, the same token is rejected with HTTP 401 on any subsequent request.
As any user, I receive a clear error message when I provide an incorrect email or password so that I know my login attempt failed.
Acceptance Criteria:
Invalid credentials return HTTP 401 with
`{ "message": "Invalid credentials" }`.
A deactivated account returns HTTP 401 with
`{ "message": "Account is deactivated" }`.
No information is leaked about whether the email exists.
As for the system, I temporarily block login attempts from a requester after a configurable number of consecutive failures so that brute-force attacks are mitigated.
Acceptance Criteria:
After 5 login attempts from the same IP within one minute, the next attempt returns HTTP 429.
The limit is enforced per IP address, not per user account.
The limit resets after 60 seconds.
User Management
As an admin, I can create a new user account by providing a name, email, password, and role so that staff and patients can access the system.
Acceptance Criteria:
`POST /api/users` with `name`, `email`, `password`, `password_confirmation`, and `role` returns HTTP 201 with the created user.
`role` must be one of `admin`, `doktor`, `pacijent`; any other value returns HTTP 422.
`email` must be unique; a duplicate returns HTTP 422.
A non-admin authenticated request returns HTTP 403.
An unauthenticated request returns HTTP 401.
As an admin, I can view a paginated list of all active user accounts so that I can oversee and manage system access.
Acceptance Criteria:
`GET /api/users` as admin returns HTTP 200 with a paginated collection including soft-deleted users.
`GET /api/users` as doctor returns HTTP 200 with only active (non-deleted) users.
`GET /api/users` as patient returns HTTP 403.
Response includes `data`, `meta`, and `links` pagination keys.

As an admin, I can update the profile information of any user account so that I can keep user data accurate.
Acceptance Criteria:
`PATCH /api/users/{id}` as admin with any combination of `name`, `email`, `password`, `role` returns HTTP 200 with the updated user.
- Fields not included in the request body are not modified.
- A non-admin request returns HTTP 403.
  As an authenticated user, I can update my own profile information so that I can keep my details current.
  Acceptance Criteria:
  `PATCH /api/users/{own-id}` as the owning user (non-admin) returns HTTP 200 with the updated user.
  Fields not included in the request are not modified.
  As any user, I am prevented from changing my own role assignment so that privilege escalation is blocked.
  Acceptance Criteria:
  `PATCH /api/users/{own-id}` including a `role` field returns HTTP 422 or ignores the `role` field for the requesting user.
  This restriction applies to all roles including admin (self-demotion / self-reassignment blocked).
  As an admin, I can deactivate a user account so that the user loses access while their data is retained in the system.
  Acceptance Criteria:
  `DELETE /api/users/{id}` as admin returns HTTP 204 and soft-deletes the user.
  An admin cannot deactivate their own account (returns HTTP 403).
  A non-admin request returns HTTP 403.
  As the system, all active access tokens of a user are revoked the moment their account is deactivated so that deactivated users cannot continue active sessions.
  Acceptance Criteria:
  When `DELETE /api/users/{id}` is called, all Sanctum tokens belonging to that user are deleted.
  Any in-flight request from the deactivated user using a previously valid token is rejected with HTTP 401.
  As an admin, I can restore a previously deactivated user account so that the user can authenticate again.
  Acceptance Criteria:
  `POST /api/users/{id}/restore` as admin returns HTTP 200 with the restored user.
  The user can log in again after restoration.
  A non-admin request returns HTTP 403.
  As an admin, I can permanently delete a user account and all associated data so that the system can be fully cleaned up when required.
  Acceptance Criteria:
  `DELETE /api/users/{id}/force` as admin returns HTTP 204.
  The user record and all related data are removed from the database.
  A non-admin request returns HTTP 403.
  Patient Management
  As an admin or doctor, I can register a new patient by providing their first name, last name, date of birth, gender, phone number, and address so that their record exists in the system.
  Acceptance Criteria:
  `POST /api/patients` with required personal fields returns HTTP 201 with the created patient.
  Missing required fields return HTTP 422.
  A patient-role request returns HTTP 403.
  An unauthenticated request returns HTTP 401.
  As an admin or doctor, I can record optional medical metadata for a patient — including blood type, allergies, and general medical notes — so that clinically relevant information is stored alongside their profile.
  Acceptance Criteria:
  `POST /api/patients` with an optional `socioeconomic` nested object persists socioeconomic data.
  `blood_type`, `allergies`, and `medical_notes` are accepted as optional fields on the patient payload.
  Omitting any optional field does not cause validation failure.


As the system, registering a new patient is treated as an atomic operation so that either all associated records are created successfully or none are persisted, preventing partial data.
Acceptance Criteria:
If socioeconomic record creation fails after the patient record is created, the entire operation is rolled back.
No patient record exists in the database after a failed registration attempt.
As an admin or doctor, I can view a paginated list of all active patient records so that I can efficiently browse and manage patients.
Acceptance Criteria:
`GET /api/patients` as admin or doctor returns HTTP 200 with a paginated list of active patients.
Response includes `data`, `meta`, and `links` pagination keys.
A patient-role request returns only the requesting user's own record.
As an admin or doctor, I can filter the patient list by searching on first or last name (case-insensitively) so that I can quickly locate specific patients.
Acceptance Criteria:
`GET /api/patients?search=john` returns only patients whose first or last name contains "john" (case-insensitive).
An empty search or absent `search` parameter returns the full paginated list.
As an admin or doctor, I can view the complete profile of any active patient — including personal details, medical metadata, and socioeconomic data — so that I have a full picture when needed.
Acceptance Criteria:
`GET /api/patients/{id}` as admin or doctor returns HTTP 200 with patient, user, and socioeconomic data.
A non-existent patient returns HTTP 404.


As a patient, I can view only my own profile so that my privacy is protected and I cannot access other patients' data.
Acceptance Criteria:
`GET /api/patients/{own-id}` as a patient returns HTTP 200.
`GET /api/patients/{other-id}` as a patient returns HTTP 403.
As an admin or doctor, I can update a patient's personal information, medical metadata, and socioeconomic profile, where only the fields included in my request are modified, so that partial updates do not overwrite unrelated data.
Acceptance Criteria:
`PATCH /api/patients/{id}` with a subset of fields returns HTTP 200; untouched fields retain their previous values.
If a `socioeconomic` object is included, it is upserted without affecting other patient fields.
Wrapped in a DB transaction; failure during socioeconomic update rolls back the patient update too.
A patient-role request returns HTTP 403.
As an admin, I can deactivate a patient record so that it is removed from the active list while the patient's visit history is preserved.
Acceptance Criteria:
`DELETE /api/patients/{id}` as admin or doctor returns HTTP 204 and soft-deletes the patient.
The patient's visit records remain in the database.
GET /api/patients` no longer returns the soft-deleted patient.
As an admin, I can restore a previously deactivated patient record so that it becomes active and visible again.
Acceptance Criteria: 
`POST /api/patients/{id}/restore` as admin returns HTTP 200 with the restored patient.
The patient appears again in `GET /api/patients`.
The associated `PatientSocioeconomic` record is also restored.
A non-admin request returns HTTP 403.
Visit Management
As a doctor, I can create a visit record for an active patient by specifying the visit date and clinical notes so that the encounter is formally documented.
Acceptance Criteria:
POST /api/patients/{patient}/visits as doctor with date (≤ today) and optional notes returns HTTP 201.
date is required and must not be in the future; a future date returns HTTP 422.
A patient-role request returns HTTP 403.
An unauthenticated request returns HTTP 401.
As the system, each visit record is automatically associated with the doctor who created it and this association cannot be overridden by the requester so that authorship integrity is maintained.
Acceptance Criteria:
The created visit's doctor_id equals the authenticated user's ID regardless of any doctor_id field in the request body.
A request body containing doctor_id does not affect the stored value.
As an admin or doctor, I can retrieve the complete visit history of a patient ordered from most recent to oldest so that I can review the patient's clinical timeline.
Acceptance Criteria:
GET /api/patients/{patient}/visits as admin or doctor returns HTTP 200 with a paginated list.
Results are ordered by date descending, then created_at descending.
As a patient, I can view only my own visit history so that my medical records remain private and inaccessible to others.
Acceptance Criteria:
GET /api/patients/{own-patient-id}/visits as the owning patient returns HTTP 200.
GET /api/patients/{other-patient-id}/visits as a patient returns HTTP 403.
As a doctor, I can edit the date and clinical notes of a visit record that I personally created so that I can correct or supplement my own documentation.
Acceptance Criteria:
PATCH /api/patients/{patient}/visits/{visit} as the visit's doctor returns HTTP 200 with the updated visit.
A doctor attempting to update a visit created by another doctor returns HTTP 403.
date must be ≤ today if provided; a future date returns HTTP 422.
Fields not included in the request are not modified.
As an admin, I can edit any visit record regardless of which doctor created it so that administrative corrections can be made when necessary.
Acceptance Criteria:
PATCH /api/patients/{patient}/visits/{visit} as admin always returns HTTP 200.
Admin can edit visits created by any doctor.
As an admin, I can permanently delete a visit record from the system without affecting the associated patient record or any other visits so that erroneous entries can be removed cleanly.
Acceptance Criteria:
DELETE /api/patients/{patient}/visits/{visit} as admin returns HTTP 204.
The associated patient record remains intact and unchanged.
Other visits for the same patient are unaffected.
A visit ID that belongs to a different patient returns HTTP 404 (route scoping enforced).
A doctor or patient request returns HTTP 403.

Access Control
As the system, I deny access to all protected resources for any request that does not carry a valid access token so that unauthenticated access is universally blocked.
Acceptance Criteria:
Any request to a protected endpoint without an Authorization: Bearer <token> header returns HTTP 401.
An expired or deleted token returns HTTP 401.
POST /api/login and GET /api/ping are the only endpoints accessible without a token.
As the system, I enforce role-based access control so that each user role can only perform the operations explicitly permitted to that role.
Acceptance Criteria:
Admin: full access to user CRUD, patient CRUD, visit CRUD, and all restore/force-delete operations.
Doctor: can create/read/update patients and visits; cannot delete users; cannot delete visits.
Patient: can only read own patient profile and own visit history; all write operations return HTTP 403.
Requests violating role constraints return HTTP 403.
As the system, I deny a patient access to any resource belonging to another patient — even if they have guessed a valid record identifier — so that patient data isolation is strictly enforced.
Acceptance Criteria:
GET /api/patients/{other-patient-id} as a patient returns HTTP 403.
GET /api/patients/{other-patient-id}/visits as a patient returns HTTP 403.
GET /api/patients/{other-patient-id}/visits/{visit-id} as a patient returns HTTP 403.
The response does not reveal whether the record exists (no information leak via 404 vs 403 distinction).
