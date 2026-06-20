# Implementation Plan: Patient Role UI Restrictions

**Branch**: `016-patient-role-ui` | **Date**: 2026-05-26 | **Spec**: [spec.md](spec.md)  
**Input**: Feature specification from `specs/016-patient-role-ui/spec.md`

## Summary

Hide three staff-only action buttons from patient-role users on the patient profile page, and redirect patients to their own profile page immediately after login. This is a **pure frontend change** — the backend already enforces all action restrictions via policies. The redirect is implemented as a client-side `PatientRedirectGuard` component inside the dashboard layout, using the existing `useAuth()` hook and `/api/patients/me` proxy.

## Technical Context

**Language/Version**: TypeScript / JavaScript (React, Next.js 15)  
**Primary Dependencies**: Next.js 15 App Router, MUI v6, `useAuth()` context hook  
**Storage**: N/A — no new data, no schema changes  
**Testing**: `pnpm run lint` (ESLint); manual role-based verification  
**Target Platform**: Browser (Next.js SPA running at `frontend/`)  
**Project Type**: Web application — frontend only  
**Performance Goals**: Redirect completes within one network roundtrip to `/api/patients/me`  
**Constraints**: No backend changes; middleware.ts cannot perform role-based routing (Edge runtime limitation)  
**Scale/Scope**: Affects all patient-role user sessions; 3 component files modified, 1 new component

## Constitution Check

| Principle | Status | Notes |
|-----------|--------|-------|
| I. Dual-Track Architecture | ✅ Pass | FE-only; shared backend layer unmodified |
| II. Authorization at Every Layer | ✅ Pass | Backend policies already enforce all three restrictions (confirmed in clarification). Frontend hiding is a display supplement, not a security gate. |
| III. Test-First | ⚠️ Deferred | Principle III requires Pest tests (backend). This feature has no backend changes. Frontend test infrastructure not established in this project; manual verification checklist in `quickstart.md` covers acceptance scenarios. |
| IV. Code Quality Gates | ✅ Pass | `pnpm run lint` must pass on all modified files. No backend files changed, so Pint/Larastan do not apply. |
| V. Tasks Are Developer-Ready Specs | ✅ Pass | Both tasks below include goal, inputs, outputs, steps, rationale, and verification commands. |

**Complexity Tracking**: No violations requiring justification.

## Project Structure

### Documentation (this feature)

```text
specs/016-patient-role-ui/
├── plan.md              ← This file
├── research.md          ← Phase 0 output
├── data-model.md        ← Phase 1 output
├── quickstart.md        ← Phase 1 output
└── tasks.md             ← Phase 2 output (/speckit.tasks — not yet created)
```

### Source Code

```text
frontend/
├── src/
│   ├── app/
│   │   └── (dashboard)/
│   │       └── layout.tsx                              ← MODIFIED: add PatientRedirectGuard
│   ├── components/
│   │   └── PatientRedirectGuard.tsx                    ← NEW
│   └── views/
│       └── patients/
│           ├── patient-left/
│           │   └── PatientDetailsCard.tsx              ← MODIFIED: hide Suspend
│           ├── patient-right/
│           │   └── visits/
│           │       └── index.tsx                       ← MODIFIED: hide Add Visit (×2)
│           └── diet-plans/
│               └── DietPlanSection.tsx                 ← MODIFIED: hide Generate Diet Plan
```

**Structure Decision**: Option 2 (web application). No backend directories are touched. New component placed in `src/components/` following the existing project convention for shared layout-level components.

---

## Task Overview

| # | Task | Type | Files |
|---|------|------|-------|
| FE-1 | Patient redirect guard | FE | `PatientRedirectGuard.tsx` (new), `layout.tsx` |
| FE-2 | Hide staff-only buttons | FE | `PatientDetailsCard.tsx`, `visits/index.tsx`, `DietPlanSection.tsx` |

Both tasks are independently deployable. FE-2 can be done in any order relative to FE-1.

---

## FE-1: Patient Redirect Guard

**Goal**: Patients who log in (or navigate to any dashboard page) are automatically redirected to their own patient profile page at `/dashboard/patients/{id}`.

**Inputs**:
- `src/app/(dashboard)/layout.tsx` — dashboard layout wrapping `<AuthProvider>`
- `src/context/AuthContext.tsx` — `useAuth()` hook (`{ user, isLoading }`)
- `src/app/api/patients/me/route.ts` — existing proxy returning `{ patient: PatientResource | null }`

**Outputs**:
- `src/components/PatientRedirectGuard.tsx` — new client component
- `src/app/(dashboard)/layout.tsx` — updated to wrap `{children}` with the guard

**Steps**:

1. Create `src/components/PatientRedirectGuard.tsx`:

```tsx
'use client'

import { useEffect, useState } from 'react'
import { useRouter } from 'next/navigation'
import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import { useAuth } from '@/context/AuthContext'

export default function PatientRedirectGuard({ children }: { children: React.ReactNode }) {
  const { user, isLoading } = useAuth()
  const router = useRouter()
  const [patientError, setPatientError] = useState<string | null>(null)
  const [ready, setReady] = useState(false)

  useEffect(() => {
    if (isLoading) return
    if (user?.role !== 'pacijent') {
      setReady(true)
      return
    }

    fetch('/api/patients/me')
      .then(r => r.json())
      .then(data => {
        const id = data?.patient?.id
        if (id) {
          router.replace(`/dashboard/patients/${id}`)
        } else {
          setPatientError('Your account is not linked to a patient record. Please contact your administrator.')
          setReady(true)
        }
      })
      .catch(() => {
        setPatientError('Unable to load your patient record. Please try again.')
        setReady(true)
      })
  }, [isLoading, user])

  if (isLoading || (user?.role === 'pacijent' && !ready)) {
    return null
  }

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

2. Update `src/app/(dashboard)/layout.tsx` — wrap `{children}` with `<PatientRedirectGuard>`:

```tsx
// Add import at top:
import PatientRedirectGuard from '@/components/PatientRedirectGuard'

// Inside the JSX, replace {children} in both VerticalLayout and HorizontalLayout with:
<PatientRedirectGuard>{children}</PatientRedirectGuard>
```

**Decision rationale**: `middleware.ts` cannot perform role-based redirects because it runs on the Edge runtime and cannot call the auth/me endpoint. The guard lives inside `<AuthProvider>` so `useAuth()` is available. It renders `null` while loading to prevent a flash of dashboard content before the redirect fires. `router.replace()` is used instead of `router.push()` so patients cannot press Back to return to the dashboard.

**Verification**:
```bash
cd frontend && pnpm run lint
# Then manually:
# 1. Login as pacijent → lands on /dashboard/patients/{id}
# 2. While logged in as pacijent, navigate to /dashboard/home → redirected to /dashboard/patients/{id}
# 3. Login as doktor → lands on /dashboard/home (unchanged)
```

---

## FE-2: Hide Staff-Only Buttons

**Goal**: "Suspend", "Add Visit", and "Generate Diet Plan" buttons are not rendered when the viewer has role `pacijent`.

**Inputs**:
- `src/views/patients/patient-left/PatientDetailsCard.tsx` — contains Suspend button (lines 280–289)
- `src/views/patients/patient-right/visits/index.tsx` — contains Add Visit buttons (lines 117–125 and 132–135)
- `src/views/patients/diet-plans/DietPlanSection.tsx` — contains Generate Diet Plan button (line 139)

**Outputs**: Same three files, modified.

**Steps**:

1. **`PatientDetailsCard.tsx`** — add `useAuth` import and wrap the Suspend button:

```tsx
// Add import:
import { useAuth } from '@/context/AuthContext'

// Inside the component body (after existing hooks):
const { user } = useAuth()
const isPatient = user?.role === 'pacijent'

// Wrap the Suspend button (keep Edit button always visible):
{!isPatient && (
  <Button
    size='small'
    variant='tonal'
    color='error'
    startIcon={<IconUserCancel size={16} />}
    onClick={() => setConfirmOpen(true)}
    disabled={loading}
  >
    Suspend
  </Button>
)}
```

2. **`visits/index.tsx`** — add `useAuth` import and wrap both Add Visit buttons:

```tsx
// Add import:
import { useAuth } from '@/context/AuthContext'

// Inside VisitsTab component body:
const { user } = useAuth()
const isPatient = user?.role === 'pacijent'

// Empty-state Add Visit button — replace the Box containing the button with:
{!isPatient && (
  <Box sx={{ display: 'flex', justifyContent: 'center' }}>
    <Button variant='contained' onClick={() => setAddDialogOpen(true)}>
      Add Visit
    </Button>
  </Box>
)}

// Populated-state Add Visit button — wrap the top-right Box:
{!isPatient && (
  <Box sx={{ mb: 2, display: 'flex', justifyContent: 'flex-end' }}>
    <Button variant='contained' onClick={() => setAddDialogOpen(true)}>
      Add Visit
    </Button>
  </Box>
)}
// Also remove the Dialog for adding visits when isPatient (the dialog can remain but will never open)
```

3. **`DietPlanSection.tsx`** — add `useAuth` import and wrap the Generate Diet Plan button:

```tsx
// Add import:
import { useAuth } from '@/context/AuthContext'

// Inside DietPlanSection component body:
const { user } = useAuth()
const isPatient = user?.role === 'pacijent'

// Wrap only the Button in the header Box (keep Typography "Diet Plans" visible):
{!isPatient && (
  <Button variant='contained' onClick={handleGenerate} disabled={generating || isPending}>
    {generating ? 'Starting…' : isPending ? 'Generating…' : 'Generate Diet Plan'}
  </Button>
)}
```

**Decision rationale**: `useAuth()` is already the established project pattern for role-based conditional rendering (see `VerticalMenu.tsx`, `profile/index.tsx`). No new abstraction needed. Buttons are omitted from the render tree entirely (not disabled), satisfying FR-008. The `Edit` button on `PatientDetailsCard` is intentionally left unchanged — it is not in scope.

**Verification**:
```bash
cd frontend && pnpm run lint
# Then manually:
# 1. Login as pacijent → own patient page: no Suspend, no Add Visit, no Generate Diet Plan
# 2. All other patient content (health data, visit history, diet plans list) still visible
# 3. Login as doktor → patient page: Suspend visible, Add Visit visible, Generate Diet Plan visible
# 4. Login as admin  → same as doktor
```
