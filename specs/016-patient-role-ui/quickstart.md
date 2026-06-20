# Quickstart: Patient Role UI Restrictions (016)

## Prerequisites

- `frontend/` dependencies installed (`pnpm install`)
- Backend running (or a working `.env.local` pointing to the API)
- A test patient user account (role: `pacijent`) with a linked patient record

## What This Feature Changes

Two things, both frontend-only:

1. **Patient redirect** — patients land on their own profile page after login instead of dashboard
2. **Hidden controls** — "Suspend", "Add Visit", and "Generate Diet Plan" buttons are not rendered for patient-role sessions

## Files to Create

```
frontend/src/components/PatientRedirectGuard.tsx   ← NEW
```

## Files to Modify

```
frontend/src/app/(dashboard)/layout.tsx                              ← wrap children with PatientRedirectGuard
frontend/src/views/patients/patient-left/PatientDetailsCard.tsx      ← hide Suspend button
frontend/src/views/patients/patient-right/visits/index.tsx           ← hide Add Visit button (2 locations)
frontend/src/views/patients/diet-plans/DietPlanSection.tsx           ← hide Generate Diet Plan button
```

## PatientRedirectGuard Sketch

```tsx
'use client'

import { useEffect, useState } from 'react'
import { useRouter } from 'next/navigation'
import { useAuth } from '@/context/AuthContext'

export default function PatientRedirectGuard({ children }: { children: React.ReactNode }) {
  const { user, isLoading } = useAuth()
  const router = useRouter()
  const [error, setError] = useState<string | null>(null)
  const [ready, setReady] = useState(false)

  useEffect(() => {
    if (isLoading) return
    if (user?.role !== 'pacijent') { setReady(true); return }

    fetch('/api/patients/me')
      .then(r => r.json())
      .then(data => {
        const id = data?.patient?.id
        if (id) {
          router.replace(`/dashboard/patients/${id}`)
        } else {
          setError('Your account is not linked to a patient record. Please contact your administrator.')
          setReady(true)
        }
      })
      .catch(() => {
        setError('Unable to load your patient record. Please try again.')
        setReady(true)
      })
  }, [isLoading, user])

  if (isLoading || (!ready && user?.role === 'pacijent')) return null
  if (error) return <div style={{ padding: 32, color: 'red' }}>{error}</div>
  return <>{children}</>
}
```

## Button Hiding Pattern

```tsx
// In any component that already imports useAuth (or add the import):
const { user } = useAuth()
const isPatient = user?.role === 'pacijent'

// Wrap the button:
{!isPatient && (
  <Button ...>Suspend</Button>
)}
```

Apply the same `{!isPatient && ...}` guard to:
- Suspend button in `PatientDetailsCard.tsx`
- Both Add Visit buttons in `visits/index.tsx` (empty state + populated state)
- Generate Diet Plan button in `DietPlanSection.tsx`

## Verification

```bash
# Lint
cd frontend && pnpm run lint

# Manual test checklist:
# 1. Log in as pacijent → should land on /dashboard/patients/{own-id}
# 2. Navigate to /dashboard/home while logged in as pacijent → redirect to own patient page
# 3. On patient page as pacijent: Suspend button absent, Add Visit absent, Generate Diet Plan absent
# 4. Log in as doktor → lands on /dashboard/home, all three buttons visible on patient pages
# 5. Log in as admin  → same as doktor
```
