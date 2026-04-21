# NutriBase — Midterm Presentation Proposal
**Course**: Senior Design Project  
**Team**: 2 members  
**Date**: April 2026 (midterm)  
**Duration**: ~15–20 min + Q&A

---

## Slide Structure

### 1. Introduction
**Goal**: Present NutriBase — a web-based clinical nutrition information system designed to replace fragmented paper-based records in nutrition clinics.

**Problem statement** (1 slide):
- Nutrition clinics rely on paper records or generic software not designed for clinical workflows
- No role-based access, no structured patient profile, no visit audit trail
- Result: fragmented data, risk of loss, no traceability

**Objectives** (1 slide):
- Centralised platform for 3 user roles: **Admin**, **Doctor (Doktor)**, **Patient (Pacijent)**
- Strict role-based access control at every layer
- Capture both clinical data (blood type, allergies, visit notes) and socioeconomic context (employment, income, food security)
- Decoupled architecture: Laravel REST API + React SPA

---

### 2. Requirements

**System overview** (1 slide):
- Stateless RESTful API — Laravel 12, PHP 8.3, Sanctum token auth
- React TypeScript SPA — Next.js, TanStack Query, shadcn/ui
- MySQL (production) / SQLite in-memory (tests)
- Deployed on Railway / Fly.io

**Role matrix** (1 slide — table):

| Action | Admin | Doctor | Patient |
|--------|-------|--------|---------|
| Manage users (CRUD) | ✅ | ❌ | ❌ |
| Manage all patients | ✅ | ✅ | ❌ |
| View own profile only | — | — | ✅ |
| Create visits | ❌ | ✅ | ❌ |
| Edit own visits | ❌ | ✅ | ❌ |
| Edit any visit | ✅ | ❌ | ❌ |
| Delete visits | ✅ | ❌ | ❌ |
| View own visits | — | — | ✅ |

**Key functional requirements** (1–2 slides — condensed user stories):
- US1: Admin logs in, receives Sanctum token → accesses management dashboard
- US2: Doctor registers new patient with medical + socioeconomic profile
- US3: Doctor creates visit record for patient — date, notes, auto-assigned doctor
- US4: Patient views own profile and visit history (read-only)
- US5: Admin deactivates/restores/force-deletes user accounts
- US6: Doctor edits only their own visit records; admin edits any

**Non-functional requirements** (1 slide):
- Three-layer authorization: `auth:sanctum` middleware → FormRequest authorize() → Policy class
- 401 for unauthenticated, 403 for wrong role, 403 for cross-patient access
- Rate limiting on login (5 attempts/minute)
- API response envelope: `{ "data": {...}, "message": "...", "status": ... }`

---

### 3. Use Case & Class Diagrams

> Diagrams are ready — source files in `_docs_uni/diagrams/`.  
> Render with Mermaid (VS Code preview, mermaid.live, or embed in slides).

**Use Case Diagram** (1 slide):
- Source: `diagrams/use-case/domain.mmd` (or show all 3 per feature group)
- Groups: User Management, Patient Management, Visit Management
- Actors: Admin, Doktor, Pacijent
- Constraints annotated: "Doctor: only own visits", "Patient: only own record"

**Class Diagram** (1 slide):
- Source: `diagrams/class/domain.mmd`
- 4 core classes: `User`, `Patient`, `PatientSocioeconomic`, `Visit`
- Relationships:
  - `User 1 → 0..1 Patient`
  - `Patient 1 → 0..1 PatientSocioeconomic`
  - `Patient 1 → 0..* Visit`
  - `User 1 → 0..* Visit` (as doctor)

---

### 4. Demo

#### Backend — fully functional (show via Postman or Swagger/Scramble)

**Endpoints implemented** (19 total):

| Group | Endpoints |
|-------|-----------|
| Auth | `POST /api/login`, `POST /api/logout`, `GET /api/user` |
| Users (admin) | `GET/POST /api/users`, `GET/PUT/DELETE /api/users/{id}`, `POST /api/users/{id}/restore`, `DELETE /api/users/{id}/force` |
| Patients | `GET/POST /api/patients`, `GET/PUT/DELETE /api/patients/{id}` |
| Visits | `GET/POST /api/patients/{id}/visits`, `GET/PATCH/DELETE /api/patients/{id}/visits/{id}` |

**Demo flow suggestion**:
1. `POST /api/login` → get token
2. `POST /api/patients` → create patient (admin/doctor)
3. `GET /api/patients` → list with pagination
4. `POST /api/patients/{id}/visits` as doctor → create visit
5. `GET /api/patients/{id}/visits` → paginated visit history
6. `GET /api/patients/{id}/visits/{id}` as patient → own visit (then show 403 for another patient's visit)
7. `DELETE /api/patients/{id}/visits/{id}` as admin → 204 no content

**Test coverage** (1 slide — show terminal output):
- 35 test files, ~410 tests across Feature and Unit directories
- Visits group alone: 9 files, 75 tests
- All passing — zero failures
- Covers: all role combinations, validation failures, cross-patient 403, route scoping 404

#### Frontend — scaffold ready

**What's built**:
- Next.js app shell with MUI-based layout (vertical nav, horizontal nav, dark/light mode)
- Login page (`/login`) — UI complete
- Authenticated layout shell with sidebar navigation

**What's not yet built** (honest gap — see Section 5):
- Patient list, create patient, patient profile, edit patient pages
- Visit history, create visit pages
- API integration (Axios client, TanStack Query hooks)
- Route guards (redirect unauthenticated → login)

---

### 5. Conclusion — Plans & Remaining Work

**What's done** (1 slide):
- M1 (Apr 5) ✅ — documentation: user stories, UML, architecture
- G1 Auth & User Management ✅ — full admin CRUD, login/logout, token auth
- G2 Patient Management ✅ — patient + socioeconomic CRUD, soft-delete/restore
- G3 Visits & Encounters ✅ — full visit CRUD, role-based access, 76 tests

**What's remaining until M3 (Jun 7)** (1 slide):

| Item | Milestone | Priority |
|------|-----------|----------|
| React SPA: login + patient CRUD pages | M2 (May 3) | P1 |
| API integration (Axios + TanStack Query) | M2 (May 3) | P1 |
| Route guards + auth store | M2 (May 3) | P1 |
| Service Layer pattern (UserService, PatientService, VisitService) | M3 (Jun 7) | P1 |
| Repository Pattern (3 repositories) | M3 (Jun 7) | P1 |
| Observer Pattern (PatientObserver — log on create, notify on delete) | M3 (Jun 7) | P1 |
| Visit pages in React SPA | M3 (Jun 7) | P2 |
| Public deployment (Railway / Fly.io) | M3 (Jun 7) | P1 |
| Self-registration endpoint (`POST /api/register`) | M3 (Jun 7) | P3 |

**Analysis** (1 slide):
- Backend is ahead of schedule — all 3 feature groups complete 3 weeks before M2
- Frontend is the current bottleneck — no feature pages yet, shell only
- Design patterns are not yet applied — documented in spec, implementation deferred to M3
- Risk: 5-week M2→M3 window must cover FE feature pages + 3 patterns + deployment simultaneously
- Mitigation: FE and BE work can proceed in parallel (API contract already defined); patterns are additive (no breaking changes to existing API)

---

## Notes for Presenter

- Use Postman collection (`postman-nutri-ledger-collection.json` in repo root) for the live API demo
- Scramble API docs available at `/docs/api` when backend is running — alternative to Postman
- Diagrams: open `.mmd` files in VS Code with Mermaid Preview extension or paste into mermaid.live before the presentation
- For the frontend demo: run `pnpm dev` in `/frontend` — show the login page and layout shell, be upfront that feature pages are M2 work
- Test output: run `php artisan test` in `/backend` during demo for live pass confirmation
