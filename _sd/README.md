# NutriBase — SD Project

Clinical nutrition management platform. Laravel 12 REST API + Next.js 15 SPA.

## Repository Structure

```
├── backend/    — Laravel 12 REST API (Sanctum auth, role-based access, Pest tests)
└── frontend/   — Next.js 15 SPA (React + TypeScript + Vuexy template)
```

## Backend Stack

- Laravel 12, PHP 8.4, PostgreSQL
- Laravel Sanctum — token-based authentication
- Three roles: `admin`, `doktor` (doctor), `pacijent` (patient)
- Pest 4 — 605 tests, 1612 assertions
- Larastan level 5, Laravel Pint

## Frontend Stack

- Next.js 15 (App Router), React 18, TypeScript
- Vuexy admin template
- Orval — typed fetch client generated from OpenAPI spec

## Implemented Features

### 001 — API Foundation
Laravel REST API scaffold. Sanctum installed, role middleware registered, `ApiController`
base class with `ApiResponses` trait (JSON envelope), global exception handling, CORS config,
Larastan and Pint configured. Full test suite passing.

### 002 — User Management API
Full CRUD for user administration. `UserController` (index, show, store, update, destroy,
restore, forceDelete), `UserResource` with JSON:API envelope, `UserPolicy` with 7 authorization
rules, `StoreUserRequest` / `UpdateUserRequest` validation, token revocation on deactivation,
rate limiting on auth endpoints. TDD cycle with Pest feature and unit tests.

### 003 — Patient Management API
Full CRUD for patient records with socioeconomic data. `PatientController` (index, show, store,
update, destroy), `PatientService` with transactional create/update, `PatientResource` and
`PatientSocioeconomicResource` with JSON:API envelope and camelCase attributes, `PatientPolicy`
with role-based authorization (admin/doktor full access, pacijent self-only), soft deletes with
cascade to socioeconomic record.

### 004 — API Docs & Orval Code Generation
Scramble OpenAPI spec generation. Orval configured to generate a fully typed fetch client from
the spec. Frontend API client regenerated via `pnpm run api:generate`.

### 005 — Visit Management
`VisitController` (index, show, store, update, destroy) with nested patient routing. Global
visit schedule across all patients. `VisitPolicy` — doctors own their visits, admins can edit
any. `VisitResource`. Full TDD cycle.

### 006 — Vital Signs
`VitalSignController` (store, show, update, destroy) and `VitalSignHistoryController` for
chronological patient history with date-range filtering. Automatic clinical flag computation
evaluated against standard reference ranges (blood pressure, heart rate, temperature, BMI).
`VitalSignPolicy`. `VitalSignResource` with flag output.

### 007 — AI Diet Plan Generator
Asynchronous diet plan generation via OpenAI API. `DietPlanController` (store, index, show,
update), `DietPlanAgent` with structured JSON output schema (rationale, calorie target,
nutritional goals, 7-day meal schedule, clinical warnings), `GenerateDietPlanJob` with manual
retry logic (max 2 attempts), `SendDietPlanEmailJob` for async email delivery, `DietPlanPolicy`
(admin/doktor only — patients excluded). Plans surface as editable drafts requiring doctor
sign-off before delivery.

## Running Locally

### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan test
```

### Frontend

```bash
cd frontend
pnpm install
pnpm run api:generate   # regenerate typed API client from OpenAPI spec
pnpm run dev
```

## Deployment

Deployed on Railway with three services:

- **API Service** — Laravel backend (Nixpacks, PHP, `php artisan serve`)
- **Queue Worker** — same codebase, separate process (`php artisan queue:work --sleep=3 --tries=3`), handles `GenerateDietPlanJob` and `SendDietPlanEmailJob`
- **Frontend Service** — Next.js 15 (Node.js runner)

PostgreSQL provided via Railway plugin. Jobs stored in the `jobs` table — no Redis required.
All services communicate over Railway's private network.
