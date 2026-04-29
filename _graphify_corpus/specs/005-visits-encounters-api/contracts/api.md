# API Contract: Visit Endpoints

**Branch**: `005-visits-encounters-api` | **Date**: 2026-04-10  
**Base URL**: `/api/patients/{patient}/visits`  
**Authentication**: Bearer token (Sanctum) required on all endpoints

---

## Shared Response Envelope

All responses follow the project's `ApiResponses` trait convention.

**Success (single resource)**:
```json
{
  "message": "...",
  "status": 200,
  "data": {
    "visit": { <VisitResource> }
  }
}
```

**Success (collection)**:
```json
{
  "message": "Visits retrieved successfully.",
  "status": 200,
  "data": [ <VisitResource>, ... ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 3,
    "per_page": 15,
    "to": 15,
    "total": 37
  },
  "links": {
    "first": "http://host/api/patients/5/visits?page=1",
    "last": "http://host/api/patients/5/visits?page=3",
    "prev": null,
    "next": "http://host/api/patients/5/visits?page=2"
  }
}
```

**Error (validation)**:
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "date": ["The date field is required."],
    "notes": ["The notes field must not be greater than 10000 characters."]
  }
}
```

---

## VisitResource Shape

```json
{
  "type": "visit",
  "id": "42",
  "attributes": {
    "date": "2026-03-15",
    "notes": "Patient presents with mild hypertension...",
    "doctorName": "Dr. Sarah Johnson",
    "createdAt": "2026-03-15T14:30:00+00:00",
    "updatedAt": "2026-03-15T16:45:00+00:00"
  },
  "relationships": {
    "patient": {
      "data": { "type": "patient", "id": "5" }
    },
    "doctor": {
      "data": { "type": "user", "id": "12" }
    }
  }
}
```

Notes:
- `id` is always a string (matches PatientResource)
- `date` is `Y-m-d` format (from model `date` cast)
- `doctorName` is a denormalized attribute — present when doctor relationship is loaded (all endpoints load it)
- `createdAt` / `updatedAt` are ISO-8601
- No `includes` section — not present in PatientResource or UserResource; denormalized `doctorName` provides the display name the frontend needs

---

## Endpoints

### GET /api/patients/{patient}/visits

List visit history for a patient, ordered by date descending. Paginated (15 per page default).

**Authorization**:
- Admin: all patients
- Doctor: all patients
- Patient: own patient only (other patient returns 404 via scoped route binding)
- Unauthenticated: 401

**Response**: 200 OK — collection envelope

**Error cases**:
- 401 — no token
- 404 — patient not found

---

### POST /api/patients/{patient}/visits

Create a new visit record for a patient.

**Authorization**:
- Doctor: allowed (doctor_id auto-assigned from auth user)
- Admin: 403 Forbidden
- Patient: 403 Forbidden

**Request Body**:
```json
{
  "date": "2026-04-10",
  "notes": "Optional clinical notes..."
}
```

| Field | Type | Required | Validation |
|---|---|---|---|
| `date` | string (Y-m-d) | yes | Valid date, not in future |
| `notes` | string | no | Max 10,000 characters |

**Do NOT send**: `doctor_id`, `patient_id` — both are auto-assigned from auth and URL.

**Response**: 201 Created — single resource envelope with key `"visit"`

**Error cases**:
- 401 — no token
- 403 — admin or patient attempting to create
- 404 — patient not found
- 422 — validation failure

---

### GET /api/patients/{patient}/visits/{visit}

Retrieve a single visit record.

**Authorization**:
- Admin: any visit
- Doctor: any visit
- Patient: own visits only (other patient's visit returns 404)

**Response**: 200 OK — single resource envelope with key `"visit"` (doctor + patient loaded)

**Error cases**:
- 401 — no token
- 403 — patient accessing other patient's visit (actually 404 via scoped binding)
- 404 — visit not found or belongs to different patient

---

### PUT/PATCH /api/patients/{patient}/visits/{visit}

Update date or notes on a visit.

**Authorization**:
- Admin: any visit
- Doctor: only visits they conducted (other doctor's visit → 403)
- Patient: 403

**Request Body** (all fields optional on PATCH):
```json
{
  "date": "2026-04-09",
  "notes": "Updated clinical notes..."
}
```

| Field | Type | Required | Validation |
|---|---|---|---|
| `date` | string (Y-m-d) | no | Valid date, not in future |
| `notes` | string\|null | no | Max 10,000 characters |

**Response**: 200 OK — single resource envelope with key `"visit"`

**Error cases**:
- 401 — no token
- 403 — patient attempting update, or doctor updating another doctor's visit
- 404 — visit not found or belongs to different patient
- 422 — validation failure (e.g., future date)

---

### DELETE /api/patients/{patient}/visits/{visit}

Permanently delete a visit record. No recovery.

**Authorization**:
- Admin: allowed
- Doctor: 403
- Patient: 403

**Response**: 204 No Content

**Error cases**:
- 401 — no token
- 403 — doctor or patient attempting delete
- 404 — visit not found or belongs to different patient

---

## HTTP Status Code Summary

| Status | When |
|---|---|
| 200 OK | Successful read or update |
| 201 Created | Successful visit creation |
| 204 No Content | Successful deletion |
| 401 Unauthorized | No valid authentication token |
| 403 Forbidden | Authenticated but insufficient role/ownership |
| 404 Not Found | Resource doesn't exist or visit doesn't belong to patient |
| 422 Unprocessable Entity | Validation failure |
