# Nutri-Ledger — Current Development State

> **Last Updated:** 2026-05-09
> **Active Branch:** `011-visit-detail-page`
> **develop HEAD:** `9530517 feat: visits detail page (011-visit-detail-page)`

---

## Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.3 — pure JSON REST API (no Livewire) |
| Auth | Laravel Sanctum (token-based) |
| Frontend | Next.js 15 (App Router, Turbopack) + Material UI (MUI) |
| API Client | Orval — generates typed fetch client from OpenAPI spec |
| Tests | Pest 4 (backend), SQLite in-memory |
| Static analysis | Larastan level 5 |

Commands: run from `backend/` for PHP, from `frontend/` for JS.

---

## Branch History — What Landed on develop

All squash merges completed 2026-05-09 on `develop-test`, then merged to `develop`:

| Commit | Feature |
|---|---|
| `b438cc5` | `004` — API docs & Orval code generation (Scramble) |
| `1e695a6` | `005` — Visit management backend (partial squash) |
| `a6a4ddf` | `005` — Visits encounters delta (postman, docs, backend) |
| `7909686` | `006` — Frontend auth & dashboard shell (Next.js scaffold) |
| `821b2c1` | `009` — Patient management frontend (patients-view) |
| `bc83fca` | `010` — Patient visits UI + visit API improvements |
| `9530517` | `011` — Visit detail page |

Earlier squashes (before `b438cc5`): `001` bootstrap, `002` user mgmt API, `003` patient mgmt API, `feature/*` socioeconomic + auth extensions.

---

## Features Implemented

### Backend (`backend/`)
- **Auth** — Sanctum login/logout/register (`AuthController`)
- **Users** — CRUD, soft delete, restore, force delete (`UserController`, `UserPolicy`)
- **Patients** — CRUD, soft delete, socioeconomic data (`PatientController`, `PatientService`, `PatientPolicy`)
- **Visits** — Store, index (global + per-patient), show, update, destroy (`VisitController`, `VisitPolicy`)
  - Role rules: doctor creates/edits own visits; admin full access; patient views own only
  - Time field, 1-day edit lock, eager-loaded doctor relation

### Frontend (`frontend/`)
- **Auth** — Login, logout, Sanctum cookie flow, `AuthContext` with role
- **Dashboard** — Vertical + horizontal layout, role-conditional sidebar nav
- **Users** — List, detail, create, edit, soft delete, restore, force delete
- **Patients** — List, detail (tabs: overview, visits, socioeconomic), create, edit
- **Visits** — Global visits view (upcoming/past split), per-patient visits tab, create, edit
- **Visit detail** — `/dashboard/visits/[id]` — view, edit (if editable), delete (admin only), back nav

### BFF API Routes (`frontend/src/app/api/`)
All use Orval-generated functions — no raw `customFetchMutator` in route handlers.

---

## Roles

| Action | admin | doktor | pacijent |
|---|---|---|---|
| Manage users | ✅ | ❌ | ❌ |
| View all patients | ✅ | ✅ | ❌ |
| Create/edit visits | ✅ | ✅ own | ❌ |
| View own visits | ✅ | ✅ | ✅ own |
| Delete visits | ✅ | ❌ | ❌ |

---

## Active Branch — 011-visit-detail-page

Changes on this branch (not yet squash-merged — already on develop via squash):
- `GET` + `DELETE` handlers added to `/api/patients/[id]/visits/[visitId]/route.ts`
- `VisitDetail.tsx` component — fields display, edit modal, admin delete
- `/dashboard/visits/[id]/page.tsx` — Next.js route page
- View buttons added to both visits list views (global + patient tab)

---

## Stale Remote Branches (safe to delete)

These are superseded — their content is fully inside the develop squash commits:

- `origin/006-fe-auth-dashboard`
- `origin/feat/009-patient-management/bff-routes`
- `origin/feat/009-patient-management/patient-list`
- `origin/feat/009-patient-management/patients-view`
- `origin/feat/patient-009-patient-list`
- `origin/feat/patient-009-visits`

Note: `007-role-based-profile` and `008-admin-user-management` never had dedicated branches — implemented directly within the `009` branch.

---

## Key Files

| File | Purpose |
|---|---|
| `backend/app/Http/Controllers/Api/VisitController.php` | Visit CRUD |
| `backend/app/Policies/VisitPolicy.php` | Visit authorization |
| `frontend/src/views/visits/index.tsx` | Global visits view |
| `frontend/src/views/visits/VisitDetail.tsx` | Visit detail component |
| `frontend/src/views/patients/patient-right/visits/index.tsx` | Per-patient visits tab |
| `frontend/src/api/generated/visit/visit.ts` | Orval-generated visit API client |
| `frontend/src/context/AuthContext.tsx` | Auth + role context |

---

*Update this file at the end of each session.*
