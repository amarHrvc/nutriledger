# SC:PM Orchestration Prompt — SE Pivot

Copy-paste this into a new session to start the SE pivot with orchestrated implementation.

---

```
/sc:pm

You are orchestrating the SE pivot for nutri-ledger — a Laravel 12 clinic management app pivoting from a Livewire monolith to a Laravel REST API + React SPA to satisfy a university Software Engineering requirement (separate FE+BE via REST).

## Project Context
- Laravel 12, PHP 8.4, Pest 4, Larastan level 5
- Current branch: feature/3_visits
- All new pivot work goes on: feature/se-pivot (create from master or feature/3_visits)
- Groups 1+2 fully implemented (Livewire), Group 3 in progress
- The pivot replaces all Livewire UI with a REST JSON API — no Livewire components needed going forward
- SE partner confirmed (React-comfortable). Partner handles React FE, you handle Laravel API.
- SE deadline: M1 Apr 5 / M2 May 3 / M3 Jun 7 2026

## MCP Memory Entities (search nutri-ledger for full context)
- schema-mvp-original
- schema-actual-implementation
- schema-divergence-analysis
- se-project-requirements
- project-pivot-plan
- frontend-decision

## Plan Source
All specifications live in _docs/_shift/:
- OVERVIEW.md — pivot summary (what changes, what stays, what drops)
- SE_MVP_PLAN.md — full milestone breakdown with concrete deliverables
- DB_SCHEMA_FINAL.md — reconciled schema matching actual migrations

## What Stays (do not remove or rewrite)
- app/Models/ — User, Patient, PatientSocioeconomic, Visit
- database/migrations/ — source of truth for schema
- app/Policies/ — PatientPolicy, VisitPolicy, UserPolicy
- database/factories/ — reuse in HTTP tests
- app/Http/Requests/ — reuse in API controllers
- app/Http/Middleware/RoleMiddleware.php — already JSON-aware, plug into API route groups

## Immediate Task List (M2 — Release 1)

### 1. Branch setup
- Create branch feature/se-pivot from master
- Add GitHub collaborators: Ajla115, amilacausevic

### 2. Install Sanctum
- composer require laravel/sanctum
- php artisan sanctum:install
- Configure config/cors.php for React dev origin (http://localhost:5173)
- Add HasApiTokens to User model (if not already present)

### 3. Create routes/api.php
Register the file in bootstrap/app.php. Define all auth, user, and patient endpoint groups with:
- auth:sanctum middleware for protected routes
- role:admin middleware for admin-only routes
- RoleMiddleware for doctor/patient role gates

### 4. Auth endpoints
- POST /api/login — issue Sanctum token
- POST /api/logout — revoke current token
- GET /api/user — return authenticated user as UserResource

### 5. API Controllers (app/Http/Controllers/Api/)
- AuthController — login, logout
- UserController — index, store, show, update, destroy
- PatientController — index, store, show, update, destroy
- VisitController — index (scoped to patient), store, show, update, destroy

### 6. Service Layer (Architectural pattern — document + implement)
- app/Services/UserService.php
- app/Services/PatientService.php
- app/Services/VisitService.php
Controllers delegate business logic to services. Services use Eloquent directly for M2.

### 7. Eloquent Resources (app/Http/Resources/)
- UserResource
- PatientResource (conditionally includes PatientSocioeconomicResource)
- PatientSocioeconomicResource
- VisitResource (includes doctor as UserResource)

### 8. HTTP Pest tests (replace Livewire component tests)
Min 5 tests covering:
1. POST /api/login with valid credentials returns token
2. GET /api/patients as admin returns all patients
3. POST /api/patients creates patient with valid data
4. GET /api/patients/{id} as patient returns only own record (others: 403)
5. POST /api/patients/{patient}/visits as doctor creates visit

### M3 additions (after M2 is stable)
- Visit API endpoints (nested under /api/patients/{patient}/visits + /api/visits/{id})
- Repository Pattern: UserRepository, PatientRepository, VisitRepository
- Observer Pattern: PatientObserver (log + notify on created/deleted)
- Deploy to Railway or Fly.io

## Constraints
- Visit detail scope: date + doctor + notes only — no nested vitals/meds/labs (Groups 4-6)
- patients.allergies stays as text field (deferred)
- users.name stays as single field (deferred — migration risk)
- Role enum values (doktor/pacijent) — document in API responses, keep as-is in DB
- Do not modify existing migrations
- Do not remove existing Livewire components or tests — they may resume for SD (non-SE) work
- Run vendor/bin/pint --dirty after every file change
- Run php artisan test --filter= for the affected tests after each implementation step
```
