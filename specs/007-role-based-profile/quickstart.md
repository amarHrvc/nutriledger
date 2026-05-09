# Profile Page — Developer Implementation Guide

**Branch**: `007-role-based-profile` | **Date**: 2026-05-01  
**Stack**: Next.js 14 App Router · MUI v5 · TypeScript · React Context

This guide builds the profile page in three progressive tiers. Each tier is independently shippable.
Stop at any tier and you have a working, navigable feature.

---

## Prerequisites

- Branch checked out: `007-role-based-profile`
- Dev server running: `npm run dev` inside `frontend/`
- Logged in as all 3 roles at least once (to test each profile)
- Understand the existing pattern: `app/(dashboard)/dashboard/home/page.tsx` → `views/home/index.tsx`

---

## Tier 1 — Fast Path (~30 min)

**Goal**: All 3 profile variants visible, role-correct, and navigable. Uses only `useAuth()` — no new API calls.

### Step 1 — Create the page route

```
frontend/src/app/(dashboard)/dashboard/profile/page.tsx
```

```tsx
import ProfilePage from '@views/profile'

export default function Page() {
  return <ProfilePage />
}
```

That's it. The page inherits the dashboard layout and auth protection automatically.

---

### Step 2 — Create shared components

Create folder: `frontend/src/views/profile/shared/`

**`InfoRow.tsx`** — renders one label+value pair with a "Not provided" fallback:

```tsx
import Typography from '@mui/material/Typography'
import Box from '@mui/material/Box'

interface InfoRowProps {
  label: string
  value: string | null | undefined
}

export default function InfoRow({ label, value }: InfoRowProps) {
  return (
    <Box className='flex flex-col gap-0.5 py-1'>
      <Typography variant='caption' color='text.secondary'>
        {label}
      </Typography>
      <Typography variant='body2' color={value ? 'text.primary' : 'text.disabled'} fontStyle={value ? 'normal' : 'italic'}>
        {value ?? 'Not provided'}
      </Typography>
    </Box>
  )
}
```

**`SectionCard.tsx`** — titled card wrapper:

```tsx
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Typography from '@mui/material/Typography'
import Divider from '@mui/material/Divider'
import Box from '@mui/material/Box'

interface SectionCardProps {
  title: string
  children: React.ReactNode
}

export default function SectionCard({ title, children }: SectionCardProps) {
  return (
    <Card>
      <CardContent>
        <Typography variant='subtitle1' fontWeight={600} mb={1}>
          {title}
        </Typography>
        <Divider sx={{ mb: 2 }} />
        <Box className='flex flex-col gap-2'>
          {children}
        </Box>
      </CardContent>
    </Card>
  )
}
```

**`ProfileHeader.tsx`** — avatar + name + role badge (identical for all 3 roles):

```tsx
import Avatar from '@mui/material/Avatar'
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Chip from '@mui/material/Chip'
import Typography from '@mui/material/Typography'

const ROLE_LABELS: Record<string, string> = {
  admin: 'Administrator',
  doktor: 'Doctor',
  pacijent: 'Patient',
}

const ROLE_COLORS: Record<string, 'primary' | 'secondary' | 'success'> = {
  admin: 'primary',
  doktor: 'secondary',
  pacijent: 'success',
}

interface ProfileHeaderProps {
  name: string
  email: string
  role: string
}

export default function ProfileHeader({ name, email, role }: ProfileHeaderProps) {
  const initials = name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)

  return (
    <Card>
      <CardContent className='flex items-center gap-4'>
        <Avatar sx={{ width: 72, height: 72, fontSize: 28, bgcolor: 'primary.main' }}>
          {initials}
        </Avatar>
        <Box className='flex flex-col gap-1'>
          <Typography variant='h5'>{name}</Typography>
          <Typography variant='body2' color='text.secondary'>
            {email}
          </Typography>
          <Chip
            label={ROLE_LABELS[role] ?? role}
            color={ROLE_COLORS[role] ?? 'default'}
            size='small'
            sx={{ width: 'fit-content', mt: 0.5 }}
          />
        </Box>
      </CardContent>
    </Card>
  )
}
```

---

### Step 3 — Create AdminProfile (Tier 1 version)

`frontend/src/views/profile/AdminProfile.tsx`

```tsx
import Box from '@mui/material/Box'
import Typography from '@mui/material/Typography'

import ProfileHeader from './shared/ProfileHeader'
import SectionCard from './shared/SectionCard'
import type { AuthUser } from '@/types/auth'

interface AdminProfileProps {
  user: AuthUser
}

export default function AdminProfile({ user }: AdminProfileProps) {
  return (
    <Box className='flex flex-col gap-4'>
      <ProfileHeader name={user.name} email={user.email} role={user.role} />

      <SectionCard title='System Overview'>
        <Typography variant='body2' color='text.secondary'>
          System statistics will appear here in a future update.
        </Typography>
      </SectionCard>
    </Box>
  )
}
```

---

### Step 4 — Create DoctorProfile (Tier 1 version)

`frontend/src/views/profile/DoctorProfile.tsx`

```tsx
import Box from '@mui/material/Box'
import Typography from '@mui/material/Typography'

import ProfileHeader from './shared/ProfileHeader'
import SectionCard from './shared/SectionCard'
import type { AuthUser } from '@/types/auth'

interface DoctorProfileProps {
  user: AuthUser
}

export default function DoctorProfile({ user }: DoctorProfileProps) {
  return (
    <Box className='flex flex-col gap-4'>
      <ProfileHeader name={user.name} email={user.email} role={user.role} />

      <SectionCard title='Clinical Activity'>
        <Typography variant='body2' color='text.secondary'>
          Your consultation history will appear here.
        </Typography>
      </SectionCard>
    </Box>
  )
}
```

---

### Step 5 — Create PatientProfile (Tier 1 version)

`frontend/src/views/profile/PatientProfile.tsx`

```tsx
import Box from '@mui/material/Box'
import Typography from '@mui/material/Typography'

import ProfileHeader from './shared/ProfileHeader'
import SectionCard from './shared/SectionCard'
import type { AuthUser } from '@/types/auth'

interface PatientProfileProps {
  user: AuthUser
}

export default function PatientProfile({ user }: PatientProfileProps) {
  return (
    <Box className='flex flex-col gap-4'>
      <ProfileHeader name={user.name} email={user.email} role={user.role} />

      <SectionCard title='Medical Information'>
        <Typography variant='body2' color='text.secondary'>
          Your medical record will appear here.
        </Typography>
      </SectionCard>

      <SectionCard title='Emergency Contact'>
        <Typography variant='body2' color='text.secondary'>
          Emergency contact details will appear here.
        </Typography>
      </SectionCard>

      <SectionCard title='Recent Visits'>
        <Typography variant='body2' color='text.secondary'>
          Your visit history will appear here.
        </Typography>
      </SectionCard>
    </Box>
  )
}
```

---

### Step 6 — Create the role router

`frontend/src/views/profile/index.tsx`

```tsx
'use client'

import Box from '@mui/material/Box'
import CircularProgress from '@mui/material/CircularProgress'
import Typography from '@mui/material/Typography'

import { useAuth } from '@/context/AuthContext'
import AdminProfile from './AdminProfile'
import DoctorProfile from './DoctorProfile'
import PatientProfile from './PatientProfile'

export default function ProfilePage() {
  const { user, isLoading } = useAuth()

  if (isLoading) {
    return (
      <Box className='flex justify-center items-center' sx={{ minHeight: 300 }}>
        <CircularProgress />
      </Box>
    )
  }

  if (!user) {
    return <Typography color='error'>Unable to load profile.</Typography>
  }

  if (user.role === 'admin') return <AdminProfile user={user} />
  if (user.role === 'doktor') return <DoctorProfile user={user} />
  if (user.role === 'pacijent') return <PatientProfile user={user} />

  return <Typography color='error'>Unknown role: {user.role}</Typography>
}
```

---

### Step 7 — Wire navigation entry point

Add a "Profile" item to the user dropdown in `components/layout/shared/UserDropdown.tsx`.

Find the `<MenuList>` section and add a menu item before the Logout button:

```tsx
// Add this import at the top
import MenuItem from '@mui/material/MenuItem'

// Add inside MenuList, before the logout button div:
<MenuItem onClick={e => handleDropdownClose(e, '/dashboard/profile')}>
  <i className='tabler-user me-2' />
  Profile
</MenuItem>
```

---

### ✅ Tier 1 Checkpoint

Visit each of these while logged in as the corresponding role:

| URL | Role | Expected |
|-----|------|----------|
| `/dashboard/profile` | admin | Admin profile with "System Overview" placeholder |
| `/dashboard/profile` | doktor | Doctor profile with "Clinical Activity" placeholder |
| `/dashboard/profile` | pacijent | Patient profile with 3 placeholder sections |

All three should show the correct avatar initials, name, email, and role badge.

---

## Tier 2 — Medium Path (~2-3 hours)

**Goal**: Replace placeholder sections with real data from the backend.

### Step 1 — Create BFF endpoint for patient data

`frontend/src/app/api/patients/me/route.ts`

```ts
import { cookies } from 'next/headers'
import { NextResponse } from 'next/server'

const INTERNAL_API = process.env.INTERNAL_API_URL ?? 'http://localhost:8000'

export async function GET() {
  const cookieStore = await cookies()
  const token = cookieStore.get('auth_token')?.value

  if (!token) {
    return NextResponse.json({ message: 'Unauthenticated' }, { status: 401 })
  }

  let res: Response
  try {
    res = await fetch(INTERNAL_API + '/api/patients', {
      headers: { Authorization: 'Bearer ' + token, Accept: 'application/json' },
    })
  } catch {
    return NextResponse.json({ message: 'Service unavailable' }, { status: 503 })
  }

  if (!res.ok) {
    return NextResponse.json({ message: 'Failed to load patient data' }, { status: res.status })
  }

  const payload = await res.json()
  const patient = payload.data?.[0] ?? null

  return NextResponse.json({ patient })
}
```

---

### Step 2 — Create BFF endpoint for admin stats

`frontend/src/app/api/dashboard/stats/route.ts`

```ts
import { cookies } from 'next/headers'
import { NextResponse } from 'next/server'

const INTERNAL_API = process.env.INTERNAL_API_URL ?? 'http://localhost:8000'

export async function GET() {
  const cookieStore = await cookies()
  const token = cookieStore.get('auth_token')?.value

  if (!token) {
    return NextResponse.json({ message: 'Unauthenticated' }, { status: 401 })
  }

  const headers = { Authorization: 'Bearer ' + token, Accept: 'application/json' }

  try {
    const [usersRes, patientsRes] = await Promise.all([
      fetch(INTERNAL_API + '/api/users?per_page=1', { headers }),
      fetch(INTERNAL_API + '/api/patients?per_page=1', { headers }),
    ])

    if (!usersRes.ok || !patientsRes.ok) {
      return NextResponse.json({ message: 'Failed to load stats' }, { status: 502 })
    }

    const [usersPayload, patientsPayload] = await Promise.all([
      usersRes.json(),
      patientsRes.json(),
    ])

    return NextResponse.json({
      stats: {
        totalUsers: usersPayload.meta?.total ?? 0,
        totalPatients: patientsPayload.meta?.total ?? 0,
      },
    })
  } catch {
    return NextResponse.json({ message: 'Service unavailable' }, { status: 503 })
  }
}
```

---

### Step 3 — Upgrade PatientProfile with real medical data

Replace `frontend/src/views/profile/PatientProfile.tsx`:

```tsx
'use client'

import { useEffect, useState } from 'react'
import Box from '@mui/material/Box'
import CircularProgress from '@mui/material/CircularProgress'
import Typography from '@mui/material/Typography'

import type { PatientResource } from '@/api/generated/nutriBaseAPI.schemas'
import type { AuthUser } from '@/types/auth'
import ProfileHeader from './shared/ProfileHeader'
import SectionCard from './shared/SectionCard'
import InfoRow from './shared/InfoRow'

interface PatientProfileProps {
  user: AuthUser
}

export default function PatientProfile({ user }: PatientProfileProps) {
  const [patient, setPatient] = useState<PatientResource | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    fetch('/api/patients/me')
      .then(res => (res.ok ? res.json() : Promise.reject(res.status)))
      .then(data => setPatient(data.patient))
      .catch(() => setError('Could not load your medical record.'))
      .finally(() => setIsLoading(false))
  }, [])

  const attr = patient?.attributes

  return (
    <Box className='flex flex-col gap-4'>
      <ProfileHeader name={user.name} email={user.email} role={user.role} />

      {isLoading && (
        <Box className='flex justify-center py-8'>
          <CircularProgress size={24} />
        </Box>
      )}

      {error && (
        <Typography color='error' variant='body2'>
          {error}
        </Typography>
      )}

      {!isLoading && !error && (
        <>
          <SectionCard title='Personal Information'>
            <InfoRow label='Date of Birth' value={attr?.dateOfBirth} />
            <InfoRow label='Gender' value={attr?.gender} />
            <InfoRow label='Phone' value={attr?.phone} />
            <InfoRow label='Address' value={attr?.address} />
            <InfoRow label='City' value={attr?.city} />
            <InfoRow label='Postal Code' value={attr?.postalCode} />
          </SectionCard>

          <SectionCard title='Medical Information'>
            <InfoRow label='Blood Type' value={attr?.bloodType} />
            <InfoRow label='Allergies' value={attr?.allergies} />
            <InfoRow label='Medical Notes' value={attr?.medicalNotes} />
          </SectionCard>

          <SectionCard title='Emergency Contact'>
            <InfoRow label='Name' value={attr?.emergencyContactName} />
            <InfoRow label='Phone' value={attr?.emergencyContactPhone} />
          </SectionCard>
        </>
      )}
    </Box>
  )
}
```

---

### Step 4 — Upgrade AdminProfile with system stats

Replace `frontend/src/views/profile/AdminProfile.tsx`:

```tsx
'use client'

import { useEffect, useState } from 'react'
import Box from '@mui/material/Box'
import CircularProgress from '@mui/material/CircularProgress'
import Grid from '@mui/material/Grid2'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Typography from '@mui/material/Typography'

import type { AuthUser } from '@/types/auth'
import ProfileHeader from './shared/ProfileHeader'
import SectionCard from './shared/SectionCard'

interface AdminProfileProps {
  user: AuthUser
}

interface Stats {
  totalUsers: number
  totalPatients: number
}

function StatBlock({ label, value }: { label: string; value: number | null }) {
  return (
    <Card variant='outlined'>
      <CardContent className='text-center'>
        <Typography variant='h4' fontWeight={700}>
          {value ?? '—'}
        </Typography>
        <Typography variant='caption' color='text.secondary'>
          {label}
        </Typography>
      </CardContent>
    </Card>
  )
}

export default function AdminProfile({ user }: AdminProfileProps) {
  const [stats, setStats] = useState<Stats | null>(null)
  const [isLoading, setIsLoading] = useState(true)

  useEffect(() => {
    fetch('/api/dashboard/stats')
      .then(res => (res.ok ? res.json() : null))
      .then(data => setStats(data?.stats ?? null))
      .finally(() => setIsLoading(false))
  }, [])

  return (
    <Box className='flex flex-col gap-4'>
      <ProfileHeader name={user.name} email={user.email} role={user.role} />

      <SectionCard title='System Overview'>
        {isLoading ? (
          <Box className='flex justify-center py-4'>
            <CircularProgress size={24} />
          </Box>
        ) : (
          <Grid container spacing={2}>
            <Grid size={6}>
              <StatBlock label='Total Users' value={stats?.totalUsers ?? null} />
            </Grid>
            <Grid size={6}>
              <StatBlock label='Total Patients' value={stats?.totalPatients ?? null} />
            </Grid>
          </Grid>
        )}
      </SectionCard>
    </Box>
  )
}
```

---

### Step 5 — Upgrade DoctorProfile with patient count

Replace `frontend/src/views/profile/DoctorProfile.tsx`:

```tsx
'use client'

import { useEffect, useState } from 'react'
import Box from '@mui/material/Box'
import CircularProgress from '@mui/material/CircularProgress'
import Typography from '@mui/material/Typography'

import type { AuthUser } from '@/types/auth'
import ProfileHeader from './shared/ProfileHeader'
import SectionCard from './shared/SectionCard'

interface DoctorProfileProps {
  user: AuthUser
}

export default function DoctorProfile({ user }: DoctorProfileProps) {
  const [patientCount, setPatientCount] = useState<number | null>(null)
  const [isLoading, setIsLoading] = useState(true)

  useEffect(() => {
    // Uses the patient list endpoint — doctor sees all patients in the system.
    // A "my consultations" count would require a dedicated backend endpoint.
    fetch('/api/patients/me')
      .then(res => (res.ok ? res.json() : null))
      .then(data => {
        // For doctor, /api/patients/me returns the paginated list total via meta
        // Adjust if the BFF exposes meta differently
        setPatientCount(data?.meta?.total ?? null)
      })
      .finally(() => setIsLoading(false))
  }, [])

  return (
    <Box className='flex flex-col gap-4'>
      <ProfileHeader name={user.name} email={user.email} role={user.role} />

      <SectionCard title='Clinical Activity'>
        {isLoading ? (
          <Box className='flex justify-center py-4'>
            <CircularProgress size={24} />
          </Box>
        ) : (
          <>
            <Typography variant='body2'>
              <strong>{patientCount ?? '—'}</strong> patients registered in the system
            </Typography>
            <Typography variant='caption' color='text.secondary'>
              Individual consultation count requires a future backend endpoint.
            </Typography>
          </>
        )}
      </SectionCard>
    </Box>
  )
}
```

> **Note for doctor BFF**: The `/api/patients/me` BFF currently returns `data[0]` for patients. For doctors you need the full paginated meta. Either create a separate `/api/patients/summary` BFF endpoint that returns meta, or modify the existing one to return both.

---

### ✅ Tier 2 Checkpoint

| Role | Expected data shown |
|------|---------------------|
| `pacijent` | Full medical record with all fields, "Not provided" for nulls |
| `admin` | Stats cards: Total Users + Total Patients |
| `doktor` | Patient count from system |

---

## Tier 3 — Full Path (~4-6 hours additional)

**Goal**: Add inline edit for personal info (all roles) and medical fields (patient only).

### Overview

The edit pattern for this app:
1. Add a local `isEditing` boolean state to the profile component
2. Render form fields (MUI `TextField`) when `isEditing === true`, `InfoRow` when false
3. On save, call the appropriate PATCH endpoint via BFF → Laravel
4. On success, update local state and exit edit mode

---

### Step 1 — Add BFF PATCH endpoint for patient update

`frontend/src/app/api/patients/me/route.ts` — add a `PATCH` handler:

```ts
export async function PATCH(request: Request) {
  const cookieStore = await cookies()
  const token = cookieStore.get('auth_token')?.value

  if (!token) {
    return NextResponse.json({ message: 'Unauthenticated' }, { status: 401 })
  }

  // Get patient id from the GET /api/patients first
  const patientsRes = await fetch(INTERNAL_API + '/api/patients', {
    headers: { Authorization: 'Bearer ' + token, Accept: 'application/json' },
  })
  const patientsPayload = await patientsRes.json()
  const patientId = patientsPayload.data?.[0]?.id

  if (!patientId) {
    return NextResponse.json({ message: 'Patient record not found' }, { status: 404 })
  }

  const body = await request.json()

  const updateRes = await fetch(INTERNAL_API + `/api/patients/${patientId}`, {
    method: 'PATCH',
    headers: {
      Authorization: 'Bearer ' + token,
      Accept: 'application/json',
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(body),
  })

  const result = await updateRes.json()

  return NextResponse.json(result, { status: updateRes.status })
}
```

---

### Step 2 — Add edit toggle to PatientProfile

Add these imports and state to `PatientProfile.tsx`:

```tsx
import { useState } from 'react'
import TextField from '@mui/material/TextField'
import Button from '@mui/material/Button'
import Stack from '@mui/material/Stack'
```

Add state:
```tsx
const [isEditing, setIsEditing] = useState(false)
const [editValues, setEditValues] = useState({
  phone: attr?.phone ?? '',
  address: attr?.address ?? '',
  city: attr?.city ?? '',
  postalCode: attr?.postalCode ?? '',
  emergencyContactName: attr?.emergencyContactName ?? '',
  emergencyContactPhone: attr?.emergencyContactPhone ?? '',
  bloodType: attr?.bloodType ?? '',
  allergies: attr?.allergies ?? '',
})
const [isSaving, setIsSaving] = useState(false)
```

Add save handler:
```tsx
const handleSave = async () => {
  setIsSaving(true)
  try {
    const res = await fetch('/api/patients/me', {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(editValues),
    })
    if (res.ok) {
      const data = await res.json()
      setPatient(data.data)
      setIsEditing(false)
    }
  } finally {
    setIsSaving(false)
  }
}
```

Replace each editable `InfoRow` with a conditional:
```tsx
{isEditing ? (
  <TextField
    label='Phone'
    value={editValues.phone}
    onChange={e => setEditValues(prev => ({ ...prev, phone: e.target.value }))}
    size='small'
    fullWidth
  />
) : (
  <InfoRow label='Phone' value={attr?.phone} />
)}
```

Add edit/save/cancel buttons at the top of each section:
```tsx
<Stack direction='row' spacing={1} justifyContent='flex-end' mb={1}>
  {isEditing ? (
    <>
      <Button size='small' onClick={() => setIsEditing(false)}>Cancel</Button>
      <Button size='small' variant='contained' onClick={handleSave} disabled={isSaving}>
        {isSaving ? 'Saving...' : 'Save'}
      </Button>
    </>
  ) : (
    <Button size='small' onClick={() => setIsEditing(true)}>Edit</Button>
  )}
</Stack>
```

---

### ✅ Tier 3 Checkpoint

| Action | Expected |
|--------|----------|
| Patient clicks Edit | Fields become TextFields |
| Patient edits phone, saves | New phone persists after reload |
| Patient cancels edit | Original values restored |
| Patient submits invalid data | Validation errors from Laravel shown per-field |
| Doctor/Admin visit profile | No Edit button visible (Tier 3 edit not wired for them) |

---

## File Summary

```
frontend/src/
├── app/
│   ├── (dashboard)/dashboard/profile/
│   │   └── page.tsx                          ← new (Tier 1)
│   └── api/
│       ├── patients/me/route.ts              ← new (Tier 2 GET, Tier 3 PATCH)
│       └── dashboard/stats/route.ts          ← new (Tier 2)
└── views/profile/
    ├── index.tsx                             ← new (Tier 1)
    ├── AdminProfile.tsx                      ← new (Tier 1 → upgraded Tier 2)
    ├── DoctorProfile.tsx                     ← new (Tier 1 → upgraded Tier 2)
    ├── PatientProfile.tsx                    ← new (Tier 1 → upgraded Tier 2 → Tier 3)
    └── shared/
        ├── ProfileHeader.tsx                 ← new (Tier 1)
        ├── SectionCard.tsx                   ← new (Tier 1)
        └── InfoRow.tsx                       ← new (Tier 1)
```

**Total new files**: 10  
**Modified files**: 1 (`UserDropdown.tsx` — navigation entry point)
