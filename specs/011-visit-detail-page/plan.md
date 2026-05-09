# Implementation Plan: Visit Detail Page

**Branch**: `011-visit-detail-page` | **Date**: 2026-05-09 | **Spec**: [spec.md](./spec.md)  
**Input**: Feature specification from `/specs/011-visit-detail-page/spec.md`

## Summary

Implement a dedicated visit detail page reachable from both the global visits list and the patient profile visits tab. The page displays all fields of a single visit (date, time, doctor, patient, notes) and exposes role-appropriate edit/delete actions. This is a **frontend-only feature** — the backend API endpoint (`GET /api/patients/{patient}/visits/{visit}`) and its generated client (`patientsVisitsShow`) already exist. Two new components are created, and two existing list views are updated to add a "View" link per row.

## Technical Context

**Language/Version**: TypeScript 5.x + React 19.2.3 (frontend); PHP 8.4 + Laravel 12 (backend — no changes)  
**Primary Dependencies**: Next.js 16.1.1 (App Router), Material-UI 7.3.6, orval-generated API client (`patientsVisitsShow`, `patientsVisitsDestroy`)  
**Storage**: N/A — read-only consumption of existing API; no schema changes  
**Testing**: Pest 4 (PHP backend — existing visit tests cover the API; no new backend tests needed for this FE feature)  
**Target Platform**: Web browser (Next.js App Router hybrid)  
**Project Type**: Web application — React SPA within Next.js App Router  
**Performance Goals**: Matches existing page-load performance of patient detail; single API call on mount  
**Constraints**: Must use `patientsVisitsShow` from generated client; no hand-rolled fetch for that endpoint; follow MUI patterns + Next.js App Router conventions already in codebase  
**Scale/Scope**: 2 new files, 2 modified files; no database migrations

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Status | Notes |
|---|---|---|
| **I. Dual-Track Architecture** | ✅ PASS | Frontend-only change; no Livewire code touched; shared artifacts (models, migrations, policies) unchanged |
| **II. Authorization at Every Layer** | ✅ PASS | Backend already enforces `auth:sanctum` middleware, `FormRequest::authorize()`, and `VisitPolicy::view()` on the `show` endpoint. Frontend respects `isEditable` flag and `user.role` for button visibility — server remains authoritative |
| **III. Test-First** | ✅ PASS | No new backend logic is introduced; existing Pest tests for `VisitController::show()` already cover happy path, 401, 403, 404. FE changes (new page + list links) are structural; no FE test framework exists in the project |
| **IV. Code Quality Gates** | ✅ PASS | No PHP files modified; Pint/Larastan gates not triggered. TypeScript/ESLint applies to FE files; follow existing code style |
| **V. Tasks Are Developer-Ready Specs** | ✅ PASS | Tasks will be defined in tasks.md with goal, inputs, outputs, steps, rationale, verification |

**No violations. No complexity tracking entry required.**

## Project Structure

### Documentation (this feature)

```text
specs/011-visit-detail-page/
├── plan.md              ← this file
├── research.md          ← Phase 0 output
├── data-model.md        ← Phase 1 output
├── quickstart.md        ← Phase 1 output
└── tasks.md             ← Phase 2 output (/speckit.tasks — NOT created by /speckit.plan)
```

### Source Code (repository root)

```text
frontend/
└── src/
    ├── app/(dashboard)/dashboard/visits/
    │   ├── page.tsx                          (existing — unchanged)
    │   └── [id]/
    │       └── page.tsx                      (NEW — route entry; reads useParams + useSearchParams)
    └── views/visits/
        ├── index.tsx                         (MODIFIED — add View link per row)
        ├── VisitDetail.tsx                   (NEW — main detail component)
        ├── VisitForm.tsx                     (unchanged)
        └── VisitEditForm.tsx                 (unchanged — reused in detail edit dialog)

frontend/src/views/patients/patient-right/visits/
    └── index.tsx                             (MODIFIED — add View link per row)
```

**Structure Decision**: Option 2 (Web application) — frontend-only changes in the `frontend/` subtree. The backend `backend/` is untouched. All new frontend files follow the established pattern: thin Next.js `page.tsx` route entry → client `View` component in `views/`.

## Design

### Component: `visits/[id]/page.tsx`

Thin Next.js route wrapper. Reads `id` from `useParams<{ id: string }>()` and `patientId` from `useSearchParams().get('patient')`. Delegates to `<VisitDetail visitId={id} patientId={patientId} />`. Shows `CircularProgress` while data loads, `Alert` on error — same pattern as `patients/[id]/page.tsx`.

### Component: `VisitDetail.tsx`

Client component (`'use client'`). Responsibilities:
1. On mount, call `patientsVisitsShow(Number(patientId), Number(visitId))` from `@/api/generated/visit/visit`.
2. Render a `Box` with:
   - Back link: `<Link href="/dashboard/visits">← Back to Visits</Link>` (Next.js `Link`)
   - Visit details `Card` showing: date (formatted), time, patient name (linked to `/dashboard/patients/[patientId]`), doctor name, notes (or empty-state text if null)
   - Action row (bottom of card): **Edit** button (visible when `isEditable === true`), **Delete** button (visible when `user.role === 'admin'`)
3. Edit action: set `editOpen = true`; render existing `VisitEditForm` inside `Dialog` (same as in list views).
4. Delete action: call `patientsVisitsDestroy(patientId, visitId)`, then `router.push('/dashboard/visits')`.

Auth context: `const { user } = useAuth()` to gate delete button visibility.

### List view modifications

Both `views/visits/index.tsx` and `views/patients/patient-right/visits/index.tsx`:
- Add a **View** `Button` (variant `'outlined'`, size `'small'`) to the actions column of each row.
- `href`: Next.js `Link` wrapping the button, pointing to `/dashboard/visits/${v.id}?patient=${v.attributes.patientId}`.
- The existing Edit button remains unchanged.

### URL contract

| Action | URL |
|---|---|
| Visits list | `/dashboard/visits` |
| Visit detail | `/dashboard/visits/[visitId]?patient=[patientId]` |
| Patient detail (linked from visit detail) | `/dashboard/patients/[patientId]` |
