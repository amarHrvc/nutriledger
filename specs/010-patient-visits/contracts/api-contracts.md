# API Contracts: Patient Visits Feature (010)

All endpoints use `Authorization: Bearer <token>` (Sanctum). All responses follow the project envelope: `{ message, status, data }`.

---

## 1. GET /api/visits  *(new)*

**Purpose**: Global visits list — scoped by role.

**Auth**: admin or doktor role required (`auth:sanctum` + implicit policy scope in controller).

**Query params**: none for MVP (pagination handled server-side, page 1 default).

**Response 200**:
```json
{
  "message": "Visits retrieved successfully.",
  "status": 200,
  "data": [ VisitResource, ... ],
  "meta": { "current_page": 1, "last_page": 3, "per_page": 15, "total": 42 },
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." }
}
```

**Scoping**:
- Doctor: `WHERE doctor_id = auth()->id()`
- Admin: no filter (all visits)

**Ordering**: upcoming visits first (`date >= today` DESC by date+time), then past visits (`date < today` DESC by date+time).

**Response 401**: unauthenticated.  
**Response 403**: patient role.

---

## 2. GET /api/patients/{patient}/visits  *(existing — no contract change)*

Returns paginated visits for one patient. Auth: admin/doctor (any patient), patient (own only). No changes to this contract.

---

## 3. POST /api/patients/{patient}/visits  *(existing — extended)*

**Purpose**: Create a new visit for a patient.

**Auth**: admin or doktor (`auth:sanctum` + `role:admin,doktor` middleware).

**Request body**:
```json
{
  "date": "2026-06-10",
  "time": "14:30",
  "notes": "Optional free-text notes.",
  "doctor_id": 3
}
```

| Field | Required | Rules |
|---|---|---|
| `date` | yes | valid date, future or today |
| `time` | yes | valid time (HH:mm) |
| `notes` | no | string, max 10000 chars |
| `doctor_id` | no (admin only) | integer, exists in users with role=doktor |

**doctor_id behaviour**:
- Doctor submitting: `doctor_id` ignored even if provided. Visit created with `doctor_id = auth()->id()`.
- Admin submitting: `doctor_id` required if provided; if omitted, defaults to `auth()->id()`.

**Response 201**:
```json
{
  "message": "Visit created successfully.",
  "status": 201,
  "data": { "visit": VisitResource }
}
```

**Response 422**: validation errors.  
**Response 403**: patient role or doctor trying to create for another doctor.

---

## 4. PATCH /api/patients/{patient}/visits/{visit}  *(existing — extended)*

**Purpose**: Edit an existing visit.

**Auth**: admin (any visit within window) or owning doctor (own visits within window).

**Business rule**: Visit must satisfy `date >= today - 1 calendar day`. If locked → 403.

**Request body** (all fields optional):
```json
{
  "date": "2026-06-10",
  "time": "15:00",
  "notes": "Updated notes."
}
```

**Response 200**:
```json
{
  "message": "Visit updated successfully.",
  "status": 200,
  "data": { "visit": VisitResource }
}
```

**Response 403**: locked (older than 1 day) or wrong doctor.  
**Response 404**: visit not found or doesn't belong to patient.

---

## 5. BFF Routes (Next.js)

### GET /api/visits  *(new)*
Proxies `GET /api/visits` on the Laravel backend using `customFetchMutator` (Bearer token pattern).

### GET /api/patients/[id]/visits  *(fix existing)*
Replace raw Cookie forwarding with `customFetchMutator`. Returns paginated visits for one patient.

### POST /api/patients/[id]/visits  *(new)*
Proxies `POST /api/patients/{id}/visits`. Reads JSON body from request, forwards with Bearer token.

### PATCH /api/patients/[id]/visits/[visitId]  *(new)*
Proxies `PATCH /api/patients/{id}/visits/{visitId}`. Reads JSON body, forwards with Bearer token.
