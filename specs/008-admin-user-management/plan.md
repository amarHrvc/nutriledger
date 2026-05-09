# Implementation Plan: Admin User Management

**Branch**: `008-admin-user-management` | **Date**: 2026-05-01 | **Spec**: [spec.md](spec.md)  
**Input**: Feature specification from `/specs/008-admin-user-management/spec.md`

## Summary

Build an admin-only Users section in the Next.js dashboard that exposes all seven user operations (list, view, create, edit, soft-delete, restore, force-delete) from the existing Laravel REST API. The implementation follows the established BFF pattern: Next.js route handlers proxy requests to the Laravel backend using Orval-generated typed fetch clients, reading the `auth_token` httpOnly cookie server-side. The view layer is built with MUI components following the same structure as the profile page (page.tsx → views/index.tsx → role-variant component).

## Technical Context

**Language/Version**: TypeScript 5 / Next.js 14 (App Router) + PHP 8.3 / Laravel 12  
**Primary Dependencies**: MUI v5, Orval v8.7.0 (generated clients in `src/api/generated/user/user.ts`), Next.js App Router route handlers  
**Storage**: N/A (frontend only; backend persists to SQLite/MySQL via Laravel)  
**Testing**: Manual browser testing (FE) + Pest tests already exist on BE  
**Target Platform**: Browser (client components) + Node.js edge (BFF route handlers)  
**Project Type**: Web application — dashboard SPA slice  
**Performance Goals**: Standard interactive app — search and list render within 200ms of data arrival  
**Constraints**: Admin-only access; `auth_token` cookie must never be forwarded to the client; Orval clients used for all backend calls  
**Scale/Scope**: ~7 BFF routes, ~7 view components, 1 new dashboard page

## Constitution Check

| Principle | Status | Notes |
|---|---|---|
| I. Dual-Track Architecture | ✅ PASS | FE-only change; no backend modifications. Shares existing `UserResource` contracts. |
| II. Authorization at Every Layer | ✅ PASS | BFF routes validate `auth_token` cookie → 401 if absent. Laravel enforces role via `UserPolicy`. No client-side auth bypass. |
| III. Test-First | ✅ PASS (FE exception) | Backend Pest tests already cover all 7 operations. FE BFF routes are thin proxies; manual browser verification is sufficient for this track. |
| IV. Code Quality Gates | ✅ PASS | TypeScript strict types used throughout; no `any` except where Orval schema types are `string` at runtime (documented). |
| V. Tasks Are Developer-Ready | ✅ PASS | Each task in quickstart.md has goal, inputs, outputs, steps, and verification. |

**Complexity Tracking**: No violations.

## Project Structure

### Documentation (this feature)

```text
specs/008-admin-user-management/
├── plan.md              ← this file
├── research.md          ← Phase 0 decisions
├── data-model.md        ← entity shape + state transitions
├── quickstart.md        ← developer tutorial (main deliverable)
├── contracts/
│   └── bff-endpoints.md ← BFF route contracts
└── checklists/
    └── requirements.md  ← spec quality checklist
```

### Source Code

```text
frontend/src/
├── app/
│   ├── (dashboard)/dashboard/users/
│   │   └── page.tsx                      ← NEW: server component entry point
│   └── api/users/
│       ├── route.ts                       ← NEW: BFF GET list / POST create
│       └── [id]/
│           ├── route.ts                   ← NEW: BFF GET show / PATCH update / DELETE soft-delete
│           ├── restore/
│           │   └── route.ts              ← NEW: BFF POST restore
│           └── force/
│               └── route.ts             ← NEW: BFF DELETE force-delete
└── views/users/
    ├── index.tsx                          ← NEW: client orchestrator (view state machine)
    ├── UserList.tsx                       ← NEW: searchable paginated table
    ├── UserDetail.tsx                     ← NEW: detail view + action buttons
    ├── UserForm.tsx                       ← NEW: create / edit form
    └── shared/
        ├── UserStatusChip.tsx             ← NEW: Active / Deactivated chip
        └── ConfirmDialog.tsx              ← NEW: reusable confirmation modal
```

**Structure Decision**: Mirrors the profile page pattern exactly — thin `page.tsx` server component delegates to a `views/users/index.tsx` client component that owns all state. BFF routes live under `app/api/users/` parallel to existing `app/api/profile/` and `app/api/patients/` routes.
