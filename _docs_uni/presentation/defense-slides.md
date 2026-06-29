# NutriBase — Bachelor Thesis Defense Presentation
## International Burch University | July 6, 2026
## Mentor: Assist. Prof. Dr. Dželila Mehanović

---

## SLIDE 1 — Title

**NutriBase**
*A Web-Based Clinical Nutrition Information System*

Bachelor Thesis Defense
Amar Hajrović

International Burch University
Faculty of Engineering, Natural and Medical Sciences
Department of Information Technology
July 6, 2026

---

## SLIDE 2 — The Problem

Nutrition clinics and independent advisors face a shared reality: patient data lives in spreadsheets, diet plans are drafted manually and emailed, and visit notes are kept in separate documents with no shared structure.

The consequences:
- No role-based access control — any staff member can see any record
- No structured patient profile — allergies, blood type, socioeconomic context are scattered
- No visit history — no audit trail of clinical encounters
- No mechanism for tracking vital signs or generating personalised dietary guidance

Generic office software was not designed for clinical workflows. The gap is not a lack of technology — it is a lack of a purpose-built tool that understands what clinicians actually need.

**NutriBase is that tool.**

---

## SLIDE 3 — What NutriBase Is

NutriBase is a web-based clinical information system built for nutrition clinics and small practices.

It provides a centralised platform for three user roles working around the same patient record:

- **Admin** — manages user accounts, has full system access, oversees all records
- **Doctor** — registers and manages patients, documents clinical visits, records vital signs, generates diet plans
- **Patient** — read-only access to their own profile, visit history, and vital sign records

The system is built as a **decoupled architecture**: a stateless REST API backend consumed by a Next.js frontend. The two layers communicate exclusively over HTTPS using JSON. Authorization is enforced at three independent layers on every protected endpoint — route middleware, form request authorization, and policy classes — ensuring no user can reach data outside their permitted scope.

Three software design patterns were applied throughout: **Service Layer** for business logic encapsulation, **Repository Pattern** for data access abstraction, and **Observer Pattern** for domain event handling.

> [IMAGE suggestion: high-level architecture diagram — Next.js SPA ↔ REST API ↔ PostgreSQL, three-role actor model]

---

## SLIDE 4 — Core Feature Groups

NutriBase covers five feature areas spanning 46 functional requirements and 31 API endpoints.

**Feature Group 1 — User and Authentication Management**
Sanctum token-based login across all three roles. Admins create accounts, assign roles, deactivate users — with immediate token revocation — restore them, or permanently delete them. Rate limiting blocks brute-force attacks.

**Feature Group 2 — Patient Record Management**
Doctors register patients with personal details, clinical data (blood type, allergies, medical notes), and a socioeconomic profile (income, employment, food security, dietary restrictions) — all in a single atomic database transaction. Search, paginated listing, full profile view, soft-delete and restore.

**Feature Group 3 — Visit and Encounter Tracking**
Doctors create visit records per patient with date, time, and clinical notes. Each doctor owns their own visits. A global schedule is available to admins and doctors. Patients can read their own history. Admins can delete any visit.

**Feature Group 4 — Vital Signs**
Per-visit vital sign recording: blood pressure, heart rate, temperature, weight, height, computed BMI. The system automatically evaluates each measurement against clinical reference ranges and flags out-of-range values. A chronological history view tracks trends with optional date-range filtering.

**Feature Group 5 — AI Diet Plan Generator**
Doctors trigger a 7-day AI-generated diet plan. The system returns 202 immediately, generates the plan in a background job via the OpenAI API, stores it as structured JSON, and surfaces it to the doctor as an editable draft requiring sign-off before it reaches the patient.

---

## SLIDE 5 — System Architecture and Technology Stack

**Backend — `backend/`**
- Laravel 12, PHP 8.4
- Laravel Sanctum — token-based auth, no OAuth overhead
- PostgreSQL — production; SQLite in-memory for all automated tests
- Pest 4, Larastan level 5, Laravel Pint

**Frontend — `frontend/`**
- Next.js 15 (App Router) — routing, rendering, and build in a single dependency
- React 18 + TypeScript — type-safe API contracts
- Vuexy admin template — consistent clinical UI
- Orval — generates a fully typed fetch client from the backend's OpenAPI spec; API contract changes surface as TypeScript compile errors, not silent runtime failures

**Deployment — Railway, 3 services**
- **API Service** — Laravel backend, auto-built by Nixpacks
- **Queue Worker** — same Laravel codebase, separate process running `php artisan queue:work`; handles AI generation and email delivery jobs without blocking HTTP requests
- **Frontend Service** — Next.js served via Node.js runner
- **PostgreSQL plugin** — jobs stored in the same database, no Redis needed; services communicate over Railway's private network

> [IMAGE suggestion: dashboard screenshot]

---

## SLIDE 6 — A Key Engineering Challenge: Atomic Patient Registration

The most complex data flow in the system is patient registration. Creating a new patient requires writing to **three tables atomically**:

1. `users` — the patient's login account
2. `patients` — personal and clinical data, linked to the user
3. `patient_socioeconomic` — socioeconomic profile, linked to the patient

If the third write fails after the first two succeed, the database is left in a permanently inconsistent state — a user account with a patient record but no socioeconomic profile attached.

**Solution: the Service Layer pattern.**
`PatientService` wraps the entire creation in a single database transaction. Any failure rolls everything back — zero partial records remain. The controller delegates entirely to the service and contains no transaction logic. The Repository Pattern separates the query layer from the service, making each independently testable.

`PatientServiceTest` verifies the rollback explicitly: when a required field is missing, the test asserts that **zero records** exist across all three tables after the failed request.

This is a concrete example of why patterns exist — not to follow a trend, but to make a genuinely complex invariant expressible as clean, testable code.

---

## SLIDE 7 — Testing: 605 Tests, 1612 Assertions

Testing was treated as a first-class deliverable throughout the project.

**605 automated tests across 51 test classes, 1612 assertions. Full suite runs in under 26 seconds.**

Coverage spans all five feature groups:
- Authentication, rate limiting, role middleware, response envelope contract
- User management — full lifecycle including deactivation with token revocation
- Patient management — CRUD, service transaction rollback, resource shape
- Socioeconomic data — model, factory, policy, migration schema assertions at column level
- Visits — creation, listing, show, update, delete, global schedule
- Vital signs — flag computation correctness, history filtering, policy for all roles
- Diet plans — agent structured output, controller, email delivery, edit flow

Every role combination is tested: guest (401), wrong role (403), cross-patient access (403), mismatched route scoping (404). The system is probed the way an attacker would probe it, not just the happy path.

A dedicated `VitalSignResourceTest` with 24 assertions verifies that clinical flag computation is correct for every combination of in-range and out-of-range values — because a flag that fires incorrectly in a medical context is a patient safety issue.

> [IMAGE suggestion: terminal output showing `Tests: 605 passed (1612 assertions) / Duration: 25.XXs`]

---

## SLIDE 8 — AI Diet Plan: The Technical Pipeline

**How a diet plan goes from button click to patient-ready recommendation:**

1. Doctor clicks "Generate Plan" → `POST /api/patients/{patient}/diet-plans`
2. System responds **immediately with HTTP 202** — the plan is queued, the HTTP cycle is not blocked
3. `GenerateDietPlanJob` runs on the Queue Worker service. It builds a structured prompt from the patient's clinical profile (blood type, allergies, medical notes) and socioeconomic profile (income, food security, dietary restrictions)
4. The job calls the **OpenAI API** requesting a structured JSON response with a defined schema: clinical rationale, daily calorie target, nutritional goals (protein/carbs/fat in grams), a full 7-day meal schedule (breakfast, lunch, dinner, snack per day), and clinical warnings
5. The response is validated against the schema. On success: plan status → `completed`, content stored in a JSON column. On failure after two attempts: plan status → `failed`, failure reason recorded
6. The frontend polls for status. Once `completed`, the plan renders as an editable card
7. The doctor reviews, adjusts if needed, then optionally triggers email delivery — dispatched asynchronously through a second job

**The non-negotiable design constraint:** the plan is always surfaced as a draft requiring doctor sign-off. The AI proposes; the clinician decides. This is enforced at the UI level and the policy level — patients cannot access diet plan endpoints at all.

> [IMAGE suggestion: patient profile page showing the Diet Plans tab with a completed plan card]

---

## SLIDE 9 — Speckit: Specification-Driven Development with AI

The AI diet plan feature introduced three things the codebase had not seen before: a new external SDK dependency, an asynchronous job architecture, and a new security boundary. Implementing it with a loose prompt and reviewing the output — "vibe coding" — would have worked for a demo but left undocumented decisions scattered across the codebase.

NutriBase uses a different approach: **speckit**, a structured specification workflow that enforces a strict four-phase sequence before a single line of implementation code is written.

**Phase 1 — `/speckit.specify`**
A natural language feature description becomes a formal `spec.md`: user stories with acceptance criteria in Given/When/Then form, functional requirements as testable "System MUST" statements, bounded unknowns that must be resolved before proceeding.

**Phase 2 — `/speckit.plan`**
Produces `research.md` (every technical decision with rationale and alternatives considered), `data-model.md` (table schema, model, agent, job, policy — all defined before any file is created), and `contracts/` (exact request/response shapes for every endpoint).

**Phase 3 — `/speckit.tasks`**
Decomposes the plan into atomic, dependency-ordered tasks. Backend tasks follow a strict TDD commit sequence: **RED** (test file only, all tests failing) → **GREEN** (implementation, all tests passing) → **REFACTOR** (Pint, Larastan, documentation). Each phase is a separate commit.

**Phase 4 — `/speckit.implement`**
The AI coding agent executes `tasks.md` task-by-task. It is executing a specification, not making design decisions. Every implementation choice traces back to a numbered requirement in `spec.md` or a research entry in `research.md`.

The result: every decision is documented, every requirement is testable, and the gap between what was planned and what was built is visible and auditable.

---

## SLIDE 10 — Conclusion and What Comes Next

**What was built**

NutriBase is a functional, deployed clinical information system:
- **31 API endpoints** across 5 feature groups
- **46 functional requirements** implemented
- **605 automated tests**, 1612 assertions, zero failures
- **3 Railway services** running in production: API, Queue Worker, Frontend
- One AI pipeline with async job queuing, structured output, retry logic, and email delivery
- A full development methodology — speckit — documented as a standalone thesis chapter

**Key lessons**

Designing the OpenAPI contract before writing any frontend code was the single best decision in the project. Orval generating a typed client from the spec meant API changes surfaced as compile errors, not runtime failures discovered by users.

On the AI side: small prompt wording changes produce meaningfully different output structures that break the JSON parser downstream. Locking the prompt behind a versioned constant and defining the response schema upfront proved essential.

**What comes next**

- Retrieval-augmented generation for diet plans — feeding the model the patient's full visit history and flagged vital sign anomalies, not just a point-in-time snapshot
- Appointment scheduling with conflict detection
- PDF export of patient progress summaries
- Multi-tenancy — data isolated per clinic, making the platform viable as a hosted SaaS product rather than a self-hosted installation

---

*[DEMO follows the presentation]*
