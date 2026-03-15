# Product Roadmap — nutri-ledger

**Project:** Clinic Nutrition Management platform for tracking patient records, medical histories, and dietary consultations across a multi-role clinical workflow (admin,
doctor, patient).

**Team:** 2 members

**Course:** Software Engineering


---

## Vision

A clinic management system designed for nutritionists and general practitioners to track patient health, socioeconomic factors, and visit history. The system exposes a RESTful API (Laravel) consumed by a responsive React SPA, enabling role-based access for admins, doctors, and patients.

---

## Architecture

- **Backend:** Laravel 12 REST API, Sanctum token auth, Service Layer + Repository + Observer patterns
- **Frontend:** React + Vite + TypeScript, TanStack Query, React Router, shadcn/ui **still to be discusssed** 
- **Database:** MySQL/PosstqreSql — 4 core entities (Users, Patients, PatientSocioeconomic, Visits)
- **Deployment:** **to be decided**

---

## Milestones

### M1 — Documentation (Apr 5 2026)
**Goal:** Define scope, domain model, and user requirements before writing any code.

Deliverables:
- Product roadmap + release plan (this document)
- 30 user stories (27 functional + 3 non-functional)
- UML diagrams: 5 activity, 5 sequence, 1 class
- Submitted as PDF via LMS

---

### M2 — Release 1 (May 3 2026)
**Goal:** Working auth + patient management API with React frontend.

Scope:
- Feature Group 1: Auth & User Management
- Feature Group 2: Patient & Socioeconomic Management

Key deliverables:
- Laravel Sanctum token auth (login, logout, /api/user)
- User CRUD API (admin only)
- Patient CRUD API (role-gated via policies)
- Eloquent Resources: UserResource, PatientResource, PatientSocioeconomicResource
- Service Layer pattern implemented (UserService, PatientService)
- React: login page, 
- React: patient list, create/view/edit patient pages
- GitHub repo with collaborators (Ajla115, amilacausevic)
- ER diagram (4 entities)
- Project structure documentation
- First release merged to main

---

### M3 — Release 2 (Jun 7 2026)
**Goal:** Visit management, design patterns, test coverage, and public deployment.

Scope:
- Feature Group 3: Visits & Encounters

Key deliverables:
- Visit CRUD API (scoped per patient, role-gated)
- VisitResource
- React: visit list per patient, create/view visit pages
- Design patterns: Repository Pattern + Observer Pattern (PatientObserver)
- Minimum 5 Pest HTTP tests
- Public deployment (Railway or Fly.io)
- Full documentation
- Second release merged to main

---
