# NutriLedger — Product Roadmap

**NutriBase** (SE project name) / **NutriLedger** (SD project name)
Clinical nutrition management platform — multi-role, multi-track development.

---

## Two-Track Strategy

| Track | Scope | Deadline | Status |
|---|---|---|---|
| **MVP — SE Track** | Feature Groups 1–3 | M3 Jun 7 2026 | In Progress |
| **Full Platform — SD Track** | Feature Groups 4–11 | Post-June 2026 | Planning |
| **AI & Automation Layer** | Post-Groups 1–6 | TBD | Planning |

Architecture: **Laravel 12 REST API** (shared backend) + **React SPA** (SE) / TBD (SD)

---

## MVP — SE Track (Feature Groups 1–3)

### Milestones

| Milestone | Deadline | Deliverable |
|---|---|---|
| M1 | Apr 5 2026 | Docs only: user stories (25+), UML, Gantt |
| M2 | May 3 2026 | Release 1: Sanctum + User/Patient API + React auth/patient CRUD |
| M3 | Jun 7 2026 | Release 2: Visit API+FE, patterns, 5 tests, deployed |

### Group 1 — Auth & User Management ✅ Complete
- Sanctum token auth (login/logout), admin user CRUD, soft-delete/restore
- BE: `AuthController`, `UserController`, `UserResource`, `UserService`
- FE: Login page

### Group 2 — Patient Management ✅ Complete
- Patient CRUD, medical metadata, socioeconomic profile, search/filter
- BE: `PatientController`, `PatientResource`, `PatientSocioeconomicResource`, `PatientService`
- FE: Patient list, Create, View, Edit

### Group 3 — Visits & Encounters 🚧 In Progress
- Visit CRUD per patient, doctor notes, patient read-only history
- BE: `VisitController`, `VisitResource`, `VisitService`
- FE: Visit list, Create visit, View visit
- BD epic: `nutri-ledger-9d3`

### M3 — Patterns, Tests & Deployment
- **Service Layer**: `UserService`, `PatientService`, `VisitService`
- **Repository Pattern**: `UserRepository`, `PatientRepository`, `VisitRepository`
- **Observer Pattern**: `PatientObserver` (logs on created, notifies on deleted)
- **Tests**: min 5 Pest HTTP tests — login token, admin patient list, create patient, 403 cross-access, doctor creates visit
- **Deployment**: Railway or Fly.io (both BE + FE publicly accessible)
- BD epic: `nutri-ledger-jb2`

---

## Full Platform — SD Track (Feature Groups 4–11)

> Blocked by MVP completion. Deferred post-June 2026.

### Group 4 — Vital Signs
Record vitals during a visit: blood pressure, pulse, temperature, weight, height.
Auto-calculate BMI, flag abnormal values (badge on patient list and profile), BMI trend chart per patient.
New table: `visit_vitals` — BD: `nutri-ledger-0cx`

### Group 5 — Medications
Add medication to patient (name, dose, frequency, duration, expiry date).
List medications grouped by status (active / discontinued), view medication detail.
New table: `medications` — BD: `nutri-ledger-cyj`

### Group 6 — Lab Results
Log lab result linked to patient and optionally a visit (parameter, value, unit, category, timestamps).
View chronologically with filters (date, type, parameter), attach labs to visit detail view.
New table: `lab_results` — BD: `nutri-ledger-b5j`
> Prerequisite for the AI lab interpretation feature.

### Group 7 — Recommendations
Add diet/lifestyle recommendation to a visit (diet plan, supplementation, lifestyle notes, validity date).
View and track active vs expired recommendations per patient, auto-expire based on `date_valid_to`.
New table: `recommendations` — BD: `nutri-ledger-i6l`

### Group 8 — Reminders
Schedule reminders for labs, check-ups, follow-ups linked to patient and optionally a visit.
Upcoming reminders view sorted by date — visible in patient UI and clinic dashboard.
New table: `reminders` — BD: `nutri-ledger-lry`

### Group 9 — Dashboards & Summaries
**Clinic dashboard**: total patients, upcoming reminders, active medications, recent labs.
**Patient mini-overview widget**: recent vitals, active medications, allergies, age, gender.
No new tables — aggregates existing data. BD: `nutri-ledger-jh6`

### Group 10 — Testing & Infrastructure
Validation rules, factories, and seeders for all Groups 4–9 entities.
QA and smoke tests for all core flows. BD: `nutri-ledger-4ou`

### Group 11 — Finalization & Staging
Route and role protection audit across all groups, schema index review (patient_id, visit_id, doctor_id FK/cascade),
staging environment configuration and deployment scripts. BD: `nutri-ledger-13m`

---

## AI & Automation Layer

> Blocked by MVP + Group 6. Stack: Laravel AI SDK, n8n, Laravel MCP, Claude API.
> BD: `nutri-ledger-d0f.3`

| Complexity | Feature |
|---|---|
| Low | Nutritional risk scoring (rule-based from socioeconomic data), BMI trend chart |
| Low–Med | Follow-up/inactivity alerts (n8n scheduled, notify doctor if patient inactive) |
| Medium | Dynamic menu suggestions v1 (rule-based service), pre-visit briefing (n8n + LLM), scheduled report digest (weekly email) |
| Medium | Doctor assistant Phase 1 — single-patient RAG (Laravel AI SDK + Claude, SSE streaming) |
| Med–High | Doctor assistant Phase 2 — cross-patient queries, menu suggestions v2 (LLM), PDF report generation |
| Medium | Voice-to-notes transcription (Whisper API + React mic) |
| High | Lab result interpretation assist (requires Group 6) |

---

## Domain Model

```
User (1:1) Patient (1:1) PatientSocioeconomic
Patient (1:many) Visit
Visit (1:1) VisitVitals        [Group 4]
Patient (1:many) Medications   [Group 5]
Patient (1:many) LabResults    [Group 6]
Visit (1:many) Recommendations [Group 7]
Patient (1:many) Reminders     [Group 8]
```

---

## BD Root Epic

Full roadmap tracked in BD under `nutri-ledger-d0f`.
Run `bd list` or `bd show nutri-ledger-d0f` for live status.
