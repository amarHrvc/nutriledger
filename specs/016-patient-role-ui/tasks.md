# Tasks: Patient Role UI Restrictions

**Input**: Design documents from `specs/016-patient-role-ui/`  
**Branch**: `016-patient-role-ui`  
**Stack**: Next.js 15 App Router · MUI v6 · TypeScript/JavaScript  
**Scope**: Frontend only — 1 new component, 4 modified files, no backend changes

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no blocking dependency)
- **[US1]** / **[US2]**: User story this task belongs to

---

## Architectural Notes (Read Before Implementing)

These explain *why* the implementation is the way it is. They are not optional.

### Why `PatientRedirectGuard` lives in the layout (not middleware)

`middleware.ts` runs on the Edge runtime. It only has access to cookies, not the user object. The role is resolved by `AuthContext` via a server-side fetch to `/api/auth/me` — a round-trip that cannot happen in Edge middleware. The layout is the earliest React boundary where `useAuth()` is available.

### Why the guard must check the current pathname — CRITICAL

`PatientRedirectGuard` lives in the `(dashboard)` layout. In Next.js App Router, **layouts persist across navigations within the same group** — they do NOT re-mount when the URL changes from `/dashboard/home` to `/dashboard/patients/123`. This has two implications:

1. The `useEffect` fires once when auth resolves. It will not re-fire just because the URL changed.
2. After calling `router.replace('/dashboard/patients/123')`, the guard's state is still `ready = false`. The guard still returns `null`. The patients page — now the active child — **never renders**. The patient sees a permanently blank page.

The fix is to check `usePathname()` in the effect. If the patient is already on their own page, skip the redirect and set `ready = true` so children render normally. If they're on any other dashboard page, redirect and hold `null` during the transition.

### Why `router.replace()` instead of `router.push()`

`replace` removes the previous entry from the history stack. A patient on `/dashboard/home` who gets redirected to `/dashboard/patients/123` should not be able to press Back and land back on the home page — that would just trigger the redirect again. `replace` makes the patient page the canonical history entry.

### Why buttons are removed from the DOM, not disabled

This is not just UX polish. A disabled button that's visible leaks information: it tells the patient the action EXISTS and they can't do it. Not rendering the button at all means the patient has no reason to wonder or attempt workarounds. The backend already enforces the restriction; the frontend is removing noise, not adding security.

### Why the `isLoading` spinner blocks ALL users, not just patients

While `isLoading: true`, the guard renders a spinner for everyone — doctors and admins included. This is a minor UX regression: currently, without the guard, the layout renders immediately and individual components handle their own loading states. With the guard, everyone waits one extra round-trip (~100–200ms, same-origin fetch to `/api/auth/me`).

The alternative — rendering children during `isLoading` — risks a patient briefly seeing the home dashboard before the redirect fires. Since we can't know the role until auth resolves, we can't safely render for patients and not for others. The spinner is the conservative choice. It is intentionally applied universally to avoid that flash.

If this becomes a perceived performance issue, the solution is to move auth resolution server-side (cookies → server component → pass user as prop) rather than fighting this guard.

### The `ready` flag does not reset on navigation

`ready` is `useState(false)` — it persists for the lifetime of the layout component (which persists across same-group navigations in App Router). If a patient somehow navigates to a non-patient page while `ready = true` (impossible from the normal UI, but possible via direct URL entry), the guard will briefly render the wrong page's children before the `useEffect` re-runs and triggers a new `router.replace`. This is an acceptable edge case: the backend already blocks unauthorized access, and patients cannot construct a URL to harmful data.

### Why `ConfirmDialog` and `Dialog` should also be conditionally rendered

The plan originally said "the dialog can remain — it will never open." True for now. But dead JSX that renders conditionally on state that can never change in that branch is a maintenance trap — the next developer will either be confused by it or worse, accidentally wire up a way to open it. Remove the unreachable markup explicitly.

---

## Phase 1: Setup

- [ ] T001 Verify `pnpm install` is up to date and `pnpm run dev` starts cleanly in `frontend/`

---

## Phase 2: User Story 1 — Patient Login Redirect (Priority: P1) 🎯 MVP

**Goal**: Authenticated patients land on `/dashboard/patients/{own-id}` after login and on any dashboard page navigation — not the generic home page. Non-patient roles are unaffected.

**Independent Test**: Log in as `pacijent` → URL is `/dashboard/patients/{id}`. Refresh that page → stays on it (no flicker, no blank). Navigate to `/dashboard/home` while patient-logged-in → redirected back. Log in as `doktor` → lands on `/dashboard/home` unchanged.

- [ ] T002 [US1] Create `PatientRedirectGuard` in `frontend/src/components/PatientRedirectGuard.tsx`

  **What this component does and why each decision was made:**

  ```tsx
  'use client'

  import { useEffect, useState } from 'react'
  import { usePathname, useRouter } from 'next/navigation'
  import Alert from '@mui/material/Alert'
  import Box from '@mui/material/Box'
  import CircularProgress from '@mui/material/CircularProgress'
  import { useAuth } from '@/context/AuthContext'

  export default function PatientRedirectGuard({ children }: { children: React.ReactNode }) {
    const { user, isLoading } = useAuth()
    const router = useRouter()
    const pathname = usePathname()   // needed to detect "already on own page"
    const [patientError, setPatientError] = useState<string | null>(null)
    const [ready, setReady] = useState(false)

    useEffect(() => {
      if (isLoading) return                           // auth not settled yet
      if (user?.role !== 'pacijent') {
        setReady(true)                                // doctor/admin: pass through immediately
        return
      }

      fetch('/api/patients/me')
        .then(r => r.json())
        .then(data => {
          const id = data?.patient?.id
          if (!id) {
            setPatientError(
              'Your account is not linked to a patient record. Please contact your administrator.'
            )
            setReady(true)
            return
          }

          const targetPath = `/dashboard/patients/${id}`

          if (pathname === targetPath) {
            // Already on the right page — CRITICAL: must set ready here.
            // Without this the guard stays null after a router.replace() that
            // landed on the same URL, because the layout does not re-mount.
            setReady(true)
          } else {
            router.replace(targetPath)
            // Don't set ready — hold null while the navigation settles.
            // The layout persists; useEffect will re-run once pathname changes
            // (pathname is in the dep array), at which point the branch above fires.
          }
        })
        .catch(() => {
          setPatientError('Unable to load your patient record. Please try again.')
          setReady(true)
        })
    }, [isLoading, user?.role, pathname])   // use user?.role (primitive) not user (object)

    // Hold render during auth load — applies to all roles but is brief (one network round-trip)
    if (isLoading) {
      return (
        <Box sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '100vh' }}>
          <CircularProgress />
        </Box>
      )
    }

    // Patient not yet ready (redirect in-flight or fetch pending)
    if (user?.role === 'pacijent' && !ready) return null

    if (patientError) {
      return (
        <Box sx={{ p: 6 }}>
          <Alert severity='error'>{patientError}</Alert>
        </Box>
      )
    }

    return <>{children}</>
  }
  ```

  **Key decisions explained in the code comments above. Do not remove those comments.**

  **`useEffect` dependency reasoning:**
  - `isLoading` — must re-run when auth resolves
  - `user?.role` — primitive string, stable reference, avoids spurious re-runs if `user` object identity changes
  - `pathname` — must re-run after `router.replace()` fires so the "already on own page" branch can set `ready = true`

- [ ] T003 [US1] Mount `PatientRedirectGuard` in `frontend/src/app/(dashboard)/layout.tsx`

  - Add import: `import PatientRedirectGuard from '@/components/PatientRedirectGuard'`
  - Wrap `{children}` inside **both** `VerticalLayout` and `HorizontalLayout` with `<PatientRedirectGuard>{children}</PatientRedirectGuard>`
  - The guard must be inside `<AuthProvider>` — it already is, as `AuthProvider` wraps the whole layout. Do not move the `AuthProvider` placement.

**Checkpoint — US1 done**: Patient refresh on own page: content renders (no blank). Patient on `/dashboard/home`: redirected. Doctor/admin: unaffected.

---

## Phase 3: User Story 2 — Hide Staff-Only Buttons (Priority: P1)

**Goal**: "Suspend", "Add Visit", and "Generate Diet Plan" are absent from the DOM when the viewer has role `pacijent`. All other page content visible.

**Independent Test**: Log in as `pacijent` → own patient page: no Suspend, no Add Visit (Visits tab), no Generate Diet Plan (Diet Plans tab). Visit list, diet plan history, all health data still visible. Log in as `doktor` → all three buttons present.

- [ ] T004 [P] [US2] Hide Suspend button and its ConfirmDialog in `frontend/src/views/patients/patient-left/PatientDetailsCard.tsx`

  - Add import: `import { useAuth } from '@/context/AuthContext'`
  - After existing hooks, add: `const { user } = useAuth()` and `const isPatient = user?.role === 'pacijent'`
  - Wrap the Suspend `<Button>` (lines 280–289) with `{!isPatient && ( ... )}`
  - **Also** wrap the `<ConfirmDialog ... />` at the bottom of the component's return with `{!isPatient && ( ... )}`  
    *Rationale: dead JSX that can never be opened is a maintenance trap. Remove it explicitly rather than leaving unreachable state wiring.*
  - The Edit button and `<Dialog open={editOpen}>` (the edit form dialog) remain always visible — do not touch them

- [ ] T005 [P] [US2] Hide both Add Visit buttons and their Dialog in `frontend/src/views/patients/patient-right/visits/index.tsx`

  - Add import: `import { useAuth } from '@/context/AuthContext'`
  - After existing hooks, add: `const { user } = useAuth()` and `const isPatient = user?.role === 'pacijent'`
  - **Empty-state location** (~line 117): wrap the `<Box>` containing the Add Visit button with `{!isPatient && ( ... )}`
  - **Populated-state location** (~line 132): wrap the `<Box sx={{ mb: 2, display: 'flex', justifyContent: 'flex-end' }}>` Add Visit button with `{!isPatient && ( ... )}`
  - **Also** wrap the entire "Add Visit Dialog" block (`<Dialog open={addDialogOpen}>...`) with `{!isPatient && ( ... )}`  
    *Same rationale as T004 — the dialog state `addDialogOpen` can never become `true` for a patient (no trigger), so don't render the Dialog at all.*
  - The Edit Visit dialog (`editDialogOpen`) and Edit buttons in the table remain — patients can see their visit history

- [ ] T006 [P] [US2] Hide Generate Diet Plan button in `frontend/src/views/patients/diet-plans/DietPlanSection.tsx`

  - Add import: `import { useAuth } from '@/context/AuthContext'`
  - After existing hooks, add: `const { user } = useAuth()` and `const isPatient = user?.role === 'pacijent'`
  - Wrap only the `<Button variant='contained' onClick={handleGenerate} ...>` (~line 139) with `{!isPatient && ( ... )}`
  - The `<Typography variant='h6'>Diet Plans</Typography>` heading stays
  - The `DietPlanCard`, `DietPlanHistory`, status alerts, and polling logic all remain — patients can view their diet plans
  - `handleGenerate` itself is defined but unreachable for patients — this is fine; it has no side-effects unless called

**Checkpoint — US2 done**: No unreachable dialogs or dead trigger state. All three buttons absent for patients. History and read-only content intact.

---

## Phase 4: Polish & Cross-Cutting Concerns

- [ ] T007 [P] Run `pnpm run lint` in `frontend/` — fix any errors introduced by T002–T006
- [ ] T008 Full manual verification — all scenarios in `quickstart.md` must pass, including the **refresh-on-own-page** case (patient refreshes `/dashboard/patients/{id}` → page loads normally, no blank, no loop)

---

## Dependencies & Execution Order

### Phase Dependencies

- **Phase 2 (US1)**: T002 → T003 (component before mount)
- **Phase 3 (US2)**: T004 ∥ T005 ∥ T006 (all different files)
- **US1 and US2 are independent** — different files throughout; can be worked in parallel
- **Phase 4**: requires Phase 2 + Phase 3 complete

### Parallel Example: US2

```
T004: PatientDetailsCard.tsx   → hide Suspend + ConfirmDialog
T005: visits/index.tsx         → hide Add Visit (×2) + Add Dialog
T006: DietPlanSection.tsx      → hide Generate Diet Plan button
```

---

## Implementation Strategy

### MVP First (US1 only — 2 tasks)

1. T002 — create guard with pathname-awareness
2. T003 — mount in layout
3. **Validate**: login as patient → redirect fires; refresh own page → renders normally (not blank)
4. Continue to US2

### Full Feature

1. US1 complete (T002, T003)
2. US2 in parallel (T004, T005, T006)
3. Polish (T007, T008)

---

## Notes

- No backend changes — backend policies already enforce all three action restrictions
- `useAuth()` is the established project pattern (see `VerticalMenu.tsx`)
- `usePathname()` is from `next/navigation`, same package as `useRouter()` — no new import needed
- The refresh-on-own-page test in T008 is the most important regression check — it catches the blank-page bug
- `router.replace()` keeps history clean so Back doesn't trigger another redirect loop
