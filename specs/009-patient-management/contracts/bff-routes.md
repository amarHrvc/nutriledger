# BFF Route Contracts: Patient Management

**Branch**: `009-patient-management` | **Date**: 2026-05-03

All routes live under `frontend/src/app/api/patients/`.
All routes call Orval-generated clients. Auth cookies stay server-side.

---

## GET /api/patients

List all patients (admin/doctor) or own record only (patient role).

**BFF file**: `app/api/patients/route.ts`  
**Orval call**: `patientsIndex()`  
**Response**: forwards `res.data` with original status code

---

## POST /api/patients

Create a new user (role=pacijent) then create a patient linked to that user.

**BFF file**: `app/api/patients/route.ts`  
**Orval calls**: `usersStore(userPayload)` → `patientsStore({ user_id, ...patientPayload })`

**Request body**:
```json
{
  "name": "string",
  "email": "string",
  "password": "string",
  "password_confirmation": "string",
  "first_name": "string",
  "last_name": "string",
  "date_of_birth": "YYYY-MM-DD",
  "gender": "M | F",
  "phone": "string",
  "emergency_contact_name": "string",
  "emergency_contact_phone": "string",
  "address": "string | null",
  "city": "string | null",
  "postal_code": "string | null",
  "blood_type": "string | null",
  "allergies": "string | null",
  "medical_notes": "string | null"
}
```

**Error handling**:
- `usersStore` 422 → return 422 with `json.errors` (user field validation)
- `usersStore` non-ok → return that status with `json.message`
- `patientsStore` 422 → return 422 with `json.errors` (patient field validation)
- `patientsStore` non-ok → return that status with `json.message`

---

## GET /api/patients/[id]

Show single patient with full detail.

**BFF file**: `app/api/patients/[id]/route.ts`  
**Orval call**: `patientsShow(id)`  
**Response**: `{ data: { patient: PatientResource } }` or error

---

## PATCH /api/patients/[id]

Update patient personal and medical fields.

**BFF file**: `app/api/patients/[id]/route.ts`  
**Orval call**: `patientsUpdate(id, body)`  
**Response**: `{ data: { patient: PatientResource } }` or error

---

## DELETE /api/patients/[id]

Soft delete (deactivate) a patient.

**BFF file**: `app/api/patients/[id]/route.ts`  
**Orval call**: `patientsDestroy(id)`  
**Response**: 204 No Content

---

## Not in scope

- `POST /api/patients/[id]/restore` — restore is out of scope (Q2 → B)
- `DELETE /api/patients/[id]/force` — force delete is out of scope (Q2 → B)
