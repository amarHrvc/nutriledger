# Data Model: Patient Role UI Restrictions (016)

## Summary

This feature introduces **no new entities and no schema changes**. It is a pure frontend display change. All entities below are pre-existing and referenced for implementation clarity only.

---

## Existing Entities (referenced, not changed)

### AuthUser (`src/types/auth.ts`)

| Field | Type | Notes |
|-------|------|-------|
| `id` | `number` | User record ID |
| `name` | `string` | Display name |
| `email` | `string` | Login email |
| `role` | `'admin' \| 'doktor' \| 'pacijent'` | Governs which UI controls are rendered |

**Role used in this feature**: `role === 'pacijent'` — the only role that triggers redirect and button hiding.

---

### PatientResource (`src/api/generated/nutriBaseAPI.schemas.ts`)

| Field | Type | Notes |
|-------|------|-------|
| `id` | `string` | Patient record ID — used as redirect target (`/dashboard/patients/{id}`) |
| `attributes.fullName` | `string` | — |
| *(other attributes)* | — | Not relevant to this feature |

**Source for redirect ID**: `/api/patients/me` proxy → `data[0].id`

---

## No Backend Changes

- No new migrations
- No new models
- No changes to `UserResource` or any API response shape
- `/api/patients/me` proxy already exists and is used by `PatientProfile.tsx`
