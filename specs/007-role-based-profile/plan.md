# Implementation Plan: Role-Based Profile Page

**Branch**: `007-role-based-profile` | **Date**: 2026-05-01 | **Spec**: [spec.md](./spec.md)

## Summary

Build a profile page at `/dashboard/profile` that renders role-specific content for admin, doctor, and patient users. The page uses the existing `useAuth()` hook for identity and role routing, with optional BFF endpoints (Tier 2) for real clinical and system data, and inline editing (Tier 3). Implementation follows the established page→view pattern and MUI component conventions.

## Technical Context

**Language/Version**: TypeScript · React 18 · Next.js 14 App Router  
**Primary Dependencies**: MUI v5 (`@mui/material`), `useAuth()` context, Next.js cookies API, Orval-generated API client types  
**Storage**: N/A (frontend only — reads from Laravel API via BFF)  
**Testing**: Manual role-switching verification per tier checkpoint  
**Target Platform**: Web browser (same as rest of frontend)  
**Project Type**: Web application — frontend feature  
**Performance Goals**: Profile page loads within 2 seconds (matches SC-002)  
**Constraints**: `auth_token` is `httpOnly` cookie — all Laravel API calls must go through Next.js BFF route handlers; cannot call Laravel from browser directly  
**Scale/Scope**: 3 role views · 10 new files · 1 modified file

## Constitution Check

| Principle | Status | Notes |
|-----------|--------|-------|
| I. Dual-Track Architecture | ✅ Pass | Frontend-only feature. No backend changes in Tier 1/2. Tier 3 uses existing PATCH endpoints. No Livewire code touched. |
| II. Authorization at Every Layer | ✅ Pass | All BFF endpoints check `auth_token` cookie and return 401 if missing. Laravel enforces policy on every proxied call. No direct browser-to-Laravel calls. |
| III. Test-First | ⚠️ N/A for FE | Constitution Principle III targets Pest (backend) tests. No backend code added until Tier 3. Tier 3 reuses existing `patientsUpdate` endpoint already covered by Pest tests. Manual UI verification via tier checkpoints in quickstart.md. |
| IV. Code Quality Gates | ✅ N/A | Pint / Larastan apply to backend only. Frontend uses TypeScript type checking (`tsc --noEmit`). |
| V. Tasks Are Developer-Ready Specs | ✅ Pass | quickstart.md contains numbered steps, full code snippets, file paths, and verification checkpoints per tier. |

## Project Structure

### Documentation (this feature)

```
specs/007-role-based-profile/
├── plan.md              ← this file
├── spec.md              ← feature specification
├── research.md          ← decisions and rationale
├── data-model.md        ← entities and state shapes
├── quickstart.md        ← developer tutorial (MAIN DELIVERABLE)
├── contracts/
│   └── bff-endpoints.md ← BFF and UI component contracts
└── checklists/
    └── requirements.md  ← spec quality checklist
```

### Source Code

```
frontend/src/
├── app/
│   ├── (dashboard)/dashboard/profile/
│   │   └── page.tsx                          ← Tier 1: thin page wrapper
│   └── api/
│       ├── patients/me/
│       │   └── route.ts                      ← Tier 2 GET, Tier 3 PATCH
│       └── dashboard/stats/
│           └── route.ts                      ← Tier 2: admin stats
└── views/profile/
    ├── index.tsx                             ← Tier 1: role router
    ├── AdminProfile.tsx                      ← Tier 1 → Tier 2
    ├── DoctorProfile.tsx                     ← Tier 1 → Tier 2
    ├── PatientProfile.tsx                    ← Tier 1 → Tier 2 → Tier 3
    └── shared/
        ├── ProfileHeader.tsx                 ← Tier 1
        ├── SectionCard.tsx                   ← Tier 1
        └── InfoRow.tsx                       ← Tier 1

components/layout/shared/
└── UserDropdown.tsx                          ← Modified: add Profile nav link
```

**Structure Decision**: Follows existing page→view separation (`dashboard/home/page.tsx` → `views/home/index.tsx`). Shared components extracted to `views/profile/shared/` to avoid duplication across the 3 role views.
