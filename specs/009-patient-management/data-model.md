# Data Model: Patient Management

**Branch**: `009-patient-management` | **Date**: 2026-05-03

---

## Entities

### Patient

Primary clinical record. One-to-one with `User`.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | int | — | PK |
| `user_id` | int (FK) | yes | Links to `users.id`, role must be `pacijent` |
| `first_name` | string (50) | yes | |
| `last_name` | string (50) | yes | |
| `date_of_birth` | date | yes | Must be before today |
| `gender` | enum: M, F | yes | |
| `phone` | string (33) | yes | |
| `address` | string (100) | no | |
| `city` | string (33) | no | |
| `postal_code` | string (20) | no | |
| `emergency_contact_name` | string (100) | yes | |
| `emergency_contact_phone` | string (50) | yes | |
| `blood_type` | enum | no | A+, A-, B+, B-, AB+, AB-, O+, O- |
| `allergies` | text | no | |
| `medical_notes` | text | no | |
| `deleted_at` | timestamp | — | Soft delete; null = active |

**Computed**: `full_name` = `first_name + ' ' + last_name`

**Relationships**:
- `belongsTo User` (1:1)
- `hasMany Visit`
- `hasOne PatientSocioeconomic`

**State transitions**:
```
Active ──[DELETE /api/patients/{id}]──→ Deactivated (deleted_at set)
```
Restore and force delete are out of scope for this feature.

---

### PatientSocioeconomic

Optional supplementary record. Read-only in this feature (created on patient create if provided, not editable via this feature's UI).

| Field | Type | Notes |
|---|---|---|
| `marital_status` | enum | single, married, divorced, widowed, separated, other |
| `employment_status` | enum | employed_full_time, employed_part_time, self_employed, unemployed, retired, student, unable_to_work, other |
| `income_level` | enum | low, lower_middle, middle, upper_middle, high |
| `has_health_insurance` | bool | |
| `smoking_status` | enum | never, former, current_light, current_heavy |
| `alcohol_consumption` | enum | none, occasional, moderate, heavy |
| `physical_activity_level` | enum | sedentary, lightly_active, moderately_active, very_active |
| `food_security_status` | enum | food_secure, food_insecure, unsure |
| `number_of_dependents` | int | |
| `additional_notes` | text | |

---

### User (referenced, not owned by this feature)

Created as part of patient creation (role=pacijent). Fields used by create form:

| Field | Type | Required |
|---|---|---|
| `name` | string | yes |
| `email` | string | yes |
| `password` | string | yes (create only) |
| `password_confirmation` | string | yes (create only) |
| `role` | fixed: `pacijent` | — |

---

### Visit (read-only in this feature)

Displayed in the Visits tab of patient detail. Not created or edited here.

| Field | Type | Notes |
|---|---|---|
| `date` | date | Visit date |
| `notes` | text | Clinical notes |
| `doctor` | User relationship | Attending doctor |

---

## API Resource Shape (FE-facing, after BE-2)

```ts
interface PatientResource {
  type: 'patient'
  id: string
  attributes: {
    firstName: string
    lastName: string
    fullName: string
    dateOfBirth: string       // ISO date
    gender: 'M' | 'F'
    phone: string | null
    address: string | null
    city: string | null
    postalCode: string | null
    emergencyContactName: string
    emergencyContactPhone: string
    bloodType: string | null
    allergies: string | null
    medicalNotes: string | null
    deletedAt: string | null  // ISO datetime, added by BE-2
    isDeleted: boolean        // added by BE-2
    createdAt: string
    updatedAt: string
  }
  relationships: {
    user: { data?: { type: 'user'; id: string } }
    socioeconomic: { data?: { type: 'patient_socioeconomic'; id: string } | null }
  }
}
```
