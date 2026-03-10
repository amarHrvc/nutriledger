# Product Roadmap — nutri-ledger

**Project:** nutri-ledger — Clinic Nutrition Management System
**Team:** 2 members
**Course:** Software Engineering
**Period:** March 2026 – June 2026

---

## Vision

nutri-ledger is a clinic management system designed for nutritionists and general practitioners to track patient health, socioeconomic factors, and visit history. The system exposes a RESTful API (Laravel) consumed by a responsive React SPA, enabling role-based access for admins, doctors, and patients.

---

## Architecture

- **Backend:** Laravel 12 REST API, Sanctum token auth, Service Layer + Repository + Observer patterns
- **Frontend:** React + Vite + TypeScript, TanStack Query, React Router, shadcn/ui
- **Database:** MySQL — 4 core entities (Users, Patients, PatientSocioeconomic, Visits)
- **Deployment:** Railway or Fly.io (public URL)

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
- React: login page, patient list, create/view/edit patient pages
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

## Out of Scope (SE timeline)

Feature Groups 4-11 (Vital Signs, Medications, Labs, Reminders, Body Measurements, Food Preferences, Physical Activity, Reporting) are deferred post-June and continue as SD (Software Design) project work.

---

## Risk Register

| Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|
| Partner availability drops near deadline | Medium | High | Clear task split from M2 start; async collaboration via GitHub PRs |
| Deployment issues (CORS, env config) | Medium | Medium | Configure CORS early in M2; test deploy before M3 deadline |
| Scope creep (adding Group 4+ features) | Low | Medium | SE scope strictly Groups 1-3; document boundary explicitly |
| Schema migration breaks during pivot | Low | High | No destructive migrations during SE; tech debt deferred |
| React FE falls behind API | Medium | Medium | API-first, partner starts FE from M2 day 1 |
