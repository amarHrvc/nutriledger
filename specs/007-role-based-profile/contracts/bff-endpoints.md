# BFF Endpoint Contracts — Profile Page (Tier 2)

These are the Next.js route handler endpoints to create in `frontend/src/app/api/`.
Each reads the `auth_token` httpOnly cookie and proxies to the Laravel API.
All follow the same pattern as `app/api/auth/me/route.ts`.

---

## GET /api/patients/me

**File**: `frontend/src/app/api/patients/me/route.ts`
**Used by**: PatientProfile view
**Auth**: Requires valid `auth_token` cookie. Returns 401 if missing.

**Laravel target**: `GET /api/patients` (patient's own policy returns only their record)

**Response** (200):
```json
{
  "patient": {
    "id": "1",
    "type": "patient",
    "attributes": {
      "firstName": "Ana",
      "lastName": "Kovač",
      "fullName": "Ana Kovač",
      "dateOfBirth": "1990-05-15",
      "gender": "female",
      "phone": "+387 61 123 456",
      "address": "Ulica 123",
      "city": "Sarajevo",
      "postalCode": "71000",
      "emergencyContactName": "Marko Kovač",
      "emergencyContactPhone": "+387 61 999 000",
      "bloodType": "A+",
      "allergies": "Penicillin",
      "medicalNotes": null,
      "createdAt": "2026-01-10T12:00:00Z",
      "updatedAt": "2026-04-30T09:00:00Z"
    }
  }
}
```

**Error responses**: 401 (no cookie), 404 (patient record not found), 503 (Laravel unreachable)

---

## GET /api/dashboard/stats

**File**: `frontend/src/app/api/dashboard/stats/route.ts`
**Used by**: AdminProfile view
**Auth**: Requires valid `auth_token` cookie. Returns 401 if missing.

**Laravel targets**:
- `GET /api/users?per_page=1` — extracts `meta.total` for totalUsers
- `GET /api/patients?per_page=1` — extracts `meta.total` for totalPatients

**Response** (200):
```json
{
  "stats": {
    "totalUsers": 42,
    "totalPatients": 35
  }
}
```

**Implementation note**: Make both Laravel calls in parallel (`Promise.all`). If either fails, return 503 with a partial error — do not return stale counts.

**Error responses**: 401 (no cookie), 403 (not admin), 503 (Laravel unreachable)

---

## UI Component Contracts

### ProfileHeader

```ts
interface ProfileHeaderProps {
  name: string
  email: string
  role: 'admin' | 'doktor' | 'pacijent'
}
```

Renders: Avatar (initials), name, email, role Chip. Identical for all 3 roles.

### SectionCard

```ts
interface SectionCardProps {
  title: string
  children: React.ReactNode
}
```

Renders: MUI Card with a section title and children content.

### InfoRow

```ts
interface InfoRowProps {
  label: string
  value: string | null | undefined
}
```

Renders: label in muted text, value in normal text. If `value` is null/undefined/empty → renders "Not provided" in muted italic.
