# SE MVP Plan — nutri-ledger

**Scope:** Feature Groups 1, 2, 3 only (Users, Patients, Visits)
**Partner:** confirmed, React-comfortable
**Groups 4-11:** deferred — SE minimum is satisfied with 3 entities + full CRUD each

---

## M1 — Apr 5 2026 — Docs Only (no code)

All deliverables are documents placed in `_docs/SE_M1/`.

### User Stories (min 25 functional + 3 non-functional)

**Authentication & Roles (Group 1)**
1. As an admin, I can log in with email and password to access the system.
2. As a doctor, I can log in to view and manage my patients.
3. As a patient, I can log in to view my own medical records.
4. As an admin, I can register new user accounts with a specified role.
5. As an admin, I can deactivate (soft-delete) a user account without losing their data.
6. As an admin, I can restore a soft-deleted user account.
7. As an admin, I can permanently delete a user account.
8. As a user, I can update my own profile information.
9. As a user, I can change my password.

**Patient Management (Group 2)**
10. As an admin or doctor, I can register a new patient with their personal and medical details.
11. As an admin or doctor, I can view the full list of all patients.
12. As a doctor, I can view detailed information for a specific patient.
13. As a patient, I can view my own profile and medical information.
14. As an admin or doctor, I can update a patient's personal information.
15. As an admin or doctor, I can update a patient's medical metadata (blood type, allergies, notes).
16. As an admin, I can soft-delete a patient record.
17. As an admin, I can restore a soft-deleted patient record.
18. As an admin or doctor, I can view and update a patient's socioeconomic profile.
19. As an admin or doctor, I can filter or search for patients by name or other criteria.
20. As an admin, I can view the count of active and deleted patients.

**Visits & Encounters (Group 3)**
21. As a doctor, I can create a new visit record for a patient.
22. As an admin or doctor, I can view the full visit history for a specific patient.
23. As a doctor, I can view the details of a specific visit (date, notes).
24. As a doctor, I can edit the notes of a visit I conducted.
25. As an admin, I can delete a visit record.
26. As a patient, I can view my own visit history (read-only).
27. As a doctor, I can record the date and clinical notes for each visit.

**Non-functional**
28. All API responses must return within 500ms for standard CRUD operations.
29. All API endpoints must require authentication — unauthenticated requests receive 401.
30. The application UI must be usable on mobile viewports (min 375px width).

---

### UML Diagrams (3-5 Activity, 3-5 Sequence, 1 Class)

**Activity Diagrams** (patient/visit flows — login/register diagrams NOT accepted):
1. Patient Registration Flow (admin/doctor fills form → validation → patient + socioeconomic created)
2. Visit Creation Flow (doctor selects patient → fills visit form → visit saved → redirected to visit list)
3. Patient Profile View Flow (user logs in → role check → patient detail rendered with appropriate fields)
4. Patient Soft Delete & Restore Flow
5. Visit List Access Flow (role check → patient resolved → visits listed or access denied)

**Sequence Diagrams** (API request flows):
1. POST /api/login — client → API → Sanctum → token response
2. POST /api/patients — admin/doctor → API → policy → PatientService → DB → PatientResource
3. GET /api/patients/{id} — auth user → API → policy → patient loaded → PatientResource
4. PUT /api/patients/{id} — admin/doctor → API → FormRequest validation → PatientService → updated resource
5. POST /api/patients/{patient}/visits — doctor → API → VisitPolicy → VisitService → VisitResource

**Class Diagram:**
- `User` (id, name, email, password, role, soft_deletes) — 1:1 → `Patient`
- `Patient` (id, user_id FK, first_name, last_name, dob, gender, phone, ...) — 1:1 → `PatientSocioeconomic`
- `Patient` — 1:many → `Visit`
- `Visit` (id, patient_id FK, doctor_id FK→users, date, notes)
- Include all relationships with cardinalities

---

### Gantt Chart

| Phase | Tasks | From | To |
|---|---|---|---|
| M1 — Docs | User stories, UML, Gantt, ER diagram | Mar 10 | Apr 5 |
| M2 — Release 1 | Sanctum setup, User/Patient API, React FE | Apr 6 | May 3 |
| M2 — React | Login, patient list, patient CRUD pages | Apr 6 | May 3 |
| M3 — Release 2 | Visit API, React visit FE | May 4 | May 31 |
| M3 — Patterns | Service + Repository + Observer | May 15 | Jun 1 |
| M3 — Tests | 5 Pest HTTP tests | Jun 1 | Jun 5 |
| M3 — Deploy | Railway/Fly.io deployment | Jun 1 | Jun 7 |

---

## M2 — May 3 2026 — Release 1

### Backend (Laravel API — `feature/se-pivot` branch)

**Setup:**
- [ ] `composer require laravel/sanctum`
- [ ] `php artisan sanctum:install`
- [ ] Create `routes/api.php`, register in `bootstrap/app.php`
- [ ] Configure `config/cors.php` for React dev origin (`localhost:5173`)
- [ ] Add GitHub collaborators: `Ajla115`, `amilacausevic`

**Auth endpoints:**
- `POST /api/login` — returns token
- `POST /api/logout` — revokes token
- `GET /api/user` — returns authenticated user

**User endpoints (admin only):**
- `GET /api/users` — list all users
- `POST /api/users` — create user (admin only)
- `GET /api/users/{id}` — view user
- `PUT /api/users/{id}` — update user
- `DELETE /api/users/{id}` — soft delete

**Patient endpoints:**
- `GET /api/patients` — list (admin/doctor: all; patient: own only)
- `POST /api/patients` — create (admin/doctor)
- `GET /api/patients/{id}` — view (role-gated via PatientPolicy)
- `PUT /api/patients/{id}` — update (admin/doctor)
- `DELETE /api/patients/{id}` — soft delete (admin)

**Eloquent Resources:**
- `UserResource`
- `PatientResource` (includes PatientSocioeconomicResource when loaded)
- `PatientSocioeconomicResource`

**Architecture: Service Layer pattern** (introduced here, not M3)
- `UserService`, `PatientService`
- Controllers stay thin — all business logic in services

**Documentation deliverable:**
- ER diagram: users, patients, patient_socioeconomic, visits (4 entities, satisfies min 3)
- Project Structure section: folder layout of both repos

---

### Frontend (React — `/frontend` subfolder or separate repo)

- Login page (POST /api/login → store token)
- Patient list page (GET /api/patients)
- Create patient page (POST /api/patients)
- View patient page (GET /api/patients/{id})
- Edit patient page (PUT /api/patients/{id})

---

## M3 — Jun 7 2026 — Release 2

### Backend

**Visit endpoints:**
- `GET /api/patients/{patient}/visits` — list visits for a patient
- `POST /api/patients/{patient}/visits` — create visit (doctor/admin)
- `GET /api/visits/{id}` — view single visit
- `PUT /api/visits/{id}` — update visit (doctor/admin)
- `DELETE /api/visits/{id}` — delete visit (admin)

**VisitResource:** id, patient_id, doctor (UserResource), date, notes

**Visit scope note:** Visit detail = date + doctor + notes only. No nested vitals/meds/labs — those are Groups 4-6 scope and are out of SE MVP.

---

### Patterns (documented + implemented)

**Architectural pattern: Service Layer**
- `UserService`, `PatientService`, `VisitService`
- Encapsulates domain logic, keeps controllers thin
- Already introduced in M2 — document and formalize in M3

**Design pattern 1: Repository Pattern**
- `UserRepository`, `PatientRepository`, `VisitRepository`
- Abstracts DB access from service layer
- Each repository wraps Eloquent model queries

**Design pattern 2: Observer Pattern**
- `PatientObserver` — fires on `created`, `deleted` events
- Use case: log patient creation, notify admin on deletion
- Register in `AppServiceProvider`

---

### Tests (min 5 Pest HTTP tests)

```php
// 1. POST /api/login — valid credentials returns token
// 2. GET /api/patients — admin sees all patients
// 3. POST /api/patients — creates patient with valid data
// 4. GET /api/patients/{id} — patient can only see own record
// 5. POST /api/patients/{patient}/visits — doctor creates visit
```

---

### Deployment

- Platform: Railway or Fly.io (GitHub Education Pack if available)
- Both backend (Laravel) and frontend (React build) deployed
- Public URL submitted in M3 documentation

---

### Frontend (React)

- Visit list page per patient (`/patients/{id}/visits`)
- Create visit page
- View visit page (date, doctor, notes)

---

## Feature Restriction Resolution

| Feature | SE Scope | Post-SE |
|---|---|---|
| Visit detail | date + doctor + notes only | Add vitals/meds/labs in Groups 4-6 |
| Allergies | text field | Separate table (Group 4-5 scope) |
| `users.name` | keep single field | Split to first_name/last_name (migration risk, defer) |
| Groups 4-11 | not implemented | Continue as SD work after Jun 7 |
