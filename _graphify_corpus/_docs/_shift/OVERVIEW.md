# SE Pivot Overview: Livewire Monolith → Laravel REST API + React SPA

## Why the Pivot

The SE university requirement mandates a **separate frontend and backend communicating via REST or GraphQL**. The current implementation (Laravel + Livewire) is a coupled monolith — Livewire renders views server-side and there is no REST boundary. This disqualifies the current architecture for SE grading.

---

## What Changes

| Layer | Before | After |
|---|---|---|
| Auth | Fortify session-based | Sanctum token-based (SPA) |
| Routes | `web.php` → Livewire full-page | `api.php` → JSON API controllers |
| UI | Blade + Livewire + Flux | React + Vite + TypeScript (separate repo/folder) |
| Response | HTML | JSON (Eloquent API Resources) |
| Tests | Livewire component tests | HTTP endpoint tests (Pest) |

---

## What Stays (salvageable)

- All **models** (`User`, `Patient`, `PatientSocioeconomic`, `Visit`)
- All **migrations** — schema is the source of truth
- All **policies** (`PatientPolicy`, `VisitPolicy`, `UserPolicy`)
- All **factories** and **seeders**
- All **form request classes** (`StorePatientRequest`, etc.) — reused in API controllers
- `RoleMiddleware` — already JSON-aware, plug into `api` middleware group

---

## What Drops from the Backend

- `livewire/livewire` and `livewire/volt` — no longer needed for server-rendering
- `livewire/flux` — Flux UI is a Blade component library, irrelevant in API context
- All files under `app/Livewire/` and `resources/views/livewire/` — not deleted, just superseded
- `routes/web.php` Livewire routes for patient/visit pages

---

## What's Added to the Backend

| Addition | Purpose |
|---|---|
| `laravel/sanctum` | Token-based auth for SPA |
| `routes/api.php` | All REST endpoints |
| `app/Http/Controllers/Api/` | API controllers (Auth, User, Patient, Visit) |
| `app/Http/Resources/` | Eloquent API Resources |
| Sanctum CORS config | Allow React SPA origin in `config/cors.php` |
| HTTP Pest tests | Replace Livewire component tests for API coverage |

---

## Frontend (React SPA)

- **Stack**: React + Vite + TypeScript
- **Location**: `/frontend` subfolder or separate repo (TBD with partner)
- **Likely libs**: React Router v6, TanStack Query, shadcn/ui or MUI
- **Auth flow**: Sanctum SPA token — POST `/api/login` → store token → send as `Bearer` header
- Partner handles: React components, routing, forms, state management
- You handle: Laravel API, auth, resources, deployment

---

## SE Timeline Summary

| Milestone | Date | Deliverable |
|---|---|---|
| M1 | Apr 5 2026 | Docs only: user stories, UML diagrams, Gantt |
| M2 | May 3 2026 | Release 1: Auth + User + Patient API, React FE |
| M3 | Jun 7 2026 | Release 2: Visit API, React visit FE, patterns, tests, deployed |

Full milestone breakdown: `SE_MVP_PLAN.md`
Schema reference: `DB_SCHEMA_FINAL.md`
Orchestration prompt: `SC_PM_PROMPT.md`
