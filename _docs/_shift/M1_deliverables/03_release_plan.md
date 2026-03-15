# Release Plan
Clinical nutrition management platform for tracking patient records, medical histories, and dietary consultations across a multi-role clinical workflow (admin,
doctor, patient).

**Two releases** aligned to M2 and M3 milestones. Each release has its own branch merged to main.

---

## Release 1 — `release/1.0` (merged by May 3 2026)

### Scope
Feature Groups 1 (Auth/Users) + 2 (Patients)

### Backend (Laravel API)
| Component | Details |
|---|---|
| Sanctum setup | Install, configure, add `HasApiTokens` to User |
| CORS config | Allow React SPA origin (`localhost:5173` dev, production domain) |
| Auth endpoints | `POST /api/login`, `POST /api/logout`, `GET /api/user` |
| User endpoints | `GET/POST /api/users`, `GET/PUT/DELETE /api/users/{id}` — admin only |
| Patient endpoints | `GET/POST /api/patients`, `GET/PUT/DELETE /api/patients/{id}` — role-gated |
| Resources | `UserResource`, `PatientResource`, `PatientSocioeconomicResource` |
| Service Layer | `UserService`, `PatientService` |
| Authorization | `PatientPolicy`, `UserPolicy` plugged into API controllers |

### Frontend (React)
| Page | Route |
|---|---|
| Login | `/login` |
| Patient list | `/patients` |
| Create patient | `/patients/create` |
| View patient | `/patients/:id` |
| Edit patient | `/patients/:id/edit` |

### Documentation
- ER diagram (users, patients, patient_socioeconomic, visits)
- Project structure section (folder layout of both repos)

---

## Release 2 — `release/2.0` (merged by Jun 7 2026)

### Scope
Feature Group 3 (Visits) + patterns + tests + deployment

### Backend (Laravel API)
| Component | Details |
|---|---|
| Visit endpoints | `GET/POST /api/patients/{patient}/visits`, `GET/PUT/DELETE /api/visits/{id}` |
| VisitResource | id, date, notes, doctor (UserResource inline) |
| Repository Pattern | `UserRepository`, `PatientRepository`, `VisitRepository` |
| Observer Pattern | `PatientObserver` — logs on `created`, notifies on `deleted` |

### Frontend (React)
| Page | Route |
|---|---|
| Visit list (per patient) | `/patients/:id/visits` |
| Create visit | `/patients/:id/visits/create` |
| View visit | `/visits/:id` |

### Tests (min 5 Pest HTTP)
1. `POST /api/login` with valid credentials returns token
2. `GET /api/patients` as admin returns all patients
3. `POST /api/patients` creates patient with valid data
4. `GET /api/patients/{id}` as patient returns 403 for other patient's record
5. `POST /api/patients/{patient}/visits` as doctor creates visit

### Deployment
- Platform: Railway or Fly.io
- Both backend (Laravel) and frontend (React build) deployed publicly
- Public URL included in M3 documentation

### Documentation
- Full application documentation (per professor's template)
- Pattern descriptions (what problem each solves, how implemented)
- Test report

---

## Branch Strategy

```
main
├── release/1.0   ← merged at M2 (May 3)
└── release/2.0   ← merged at M3 (Jun 7)

feature/se-pivot  ← active development branch
```
