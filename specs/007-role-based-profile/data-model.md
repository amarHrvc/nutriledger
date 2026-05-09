# Data Model: Role-Based Profile Page

**Branch**: `007-role-based-profile` | **Date**: 2026-05-01

All types below are derived from the existing Orval-generated schemas in
`frontend/src/api/generated/nutriBaseAPI.schemas.ts`. No new backend models are introduced.

---

## Entities Used

### AuthUser *(from `src/types/auth.ts`)*

Already in context via `useAuth()`. Available to all profile views with no additional fetch.

```ts
interface AuthUser {
  id: number
  name: string
  email: string
  role: 'admin' | 'doktor' | 'pacijent'
}
```

### PatientResourceAttributes *(from generated schemas)*

Used by the Patient profile view (Tier 2+). Fetched via BFF `/api/patients/me`.

```ts
type PatientResourceAttributes = {
  firstName: string
  lastName: string
  fullName: string
  dateOfBirth: string
  gender: string
  phone: string | null
  address: string | null
  city: string | null
  postalCode: string | null
  emergencyContactName: string
  emergencyContactPhone: string
  bloodType: string | null
  allergies: string | null
  medicalNotes: string | null        // read-only for patient (doctor-authored)
  createdAt: string
  updatedAt: string
}
```

**Editable by patient** (Tier 3): `phone`, `address`, `city`, `postalCode`, `emergencyContactName`, `emergencyContactPhone`, `bloodType`, `allergies`
**Read-only for patient**: `firstName`, `lastName`, `dateOfBirth`, `gender`, `medicalNotes`, `createdAt`

### VisitResourceAttributes *(from generated schemas)*

Used by the Doctor profile view summary (Tier 2+).

```ts
type VisitResourceAttributes = {
  date: string
  notes: string | null
  doctorName?: string
  createdAt: string
  updatedAt: string
}
```

### SystemStats *(computed, not stored)*

Used by Admin profile (Tier 2+). Derived from `GET /api/users` meta.

```ts
interface SystemStats {
  totalUsers: number    // from usersIndex meta.total
  totalPatients: number // from patientsIndex meta.total (admin call)
  totalDoctors: number  // derived: totalUsers - totalPatients - totalAdmins (approximation)
                        // OR separate filtered calls — see contracts/bff-endpoints.md
}
```

---

## State Shape Per Profile View

### PatientProfile local state (Tier 2)

```ts
{
  patient: PatientResource | null
  isLoading: boolean
  error: string | null
}
```

### DoctorProfile local state (Tier 2)

```ts
{
  patientCount: number | null
  recentVisitDates: string[]         // last 3 visit dates across all patients
  isLoading: boolean
}
```

> **Limitation**: No "my consultations" endpoint exists. Patient count is a system-level count (all patients), not filtered by doctor. See research.md Decision 6.

### AdminProfile local state (Tier 2)

```ts
{
  stats: { totalUsers: number; totalPatients: number } | null
  isLoading: boolean
}
```

---

## Validation Rules (Tier 3 — Edit)

| Field | Rule |
|-------|------|
| `phone` | Optional, string, max 20 chars |
| `address` | Optional, string, max 255 chars |
| `city` | Optional, string, max 100 chars |
| `postalCode` | Optional, string, max 20 chars |
| `emergencyContactName` | Required if emergency contact is being set |
| `emergencyContactPhone` | Required if emergency contact is being set |
| `bloodType` | Optional, one of: A+, A-, B+, B-, AB+, AB-, O+, O- |
| `allergies` | Optional, free text, max 1000 chars |

Source: `UpdatePatientRequest` in generated schemas — use the existing `patientsUpdate()` API function.
