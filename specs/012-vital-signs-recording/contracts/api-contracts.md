# API Contracts: Vital Signs Recording & History (012)

All endpoints require `Authorization: Bearer <token>` (Sanctum).
All responses follow the project envelope: `{ message, status, data }`.
Route scoping: `{visit}` must belong to `{patient}` — returns 404 if not.

---

## 1. Record Vital Signs

**`POST /api/patients/{patient}/visits/{visit}/vitals`**

**Authorization**: `doktor` (own visits only) | `admin` (any visit)

**Request body** (all fields optional; at least one must be non-null):
```json
{
  "systolic_bp": 125,
  "diastolic_bp": 82,
  "heart_rate": 72,
  "temperature": 36.8,
  "weight": 74.5,
  "height": 175.0
}
```

**Validation rules**:
| Field | Rule |
|---|---|
| `systolic_bp` | nullable, integer, 0–350 |
| `diastolic_bp` | nullable, integer, 0–250 |
| `heart_rate` | nullable, integer, 0–300 |
| `temperature` | nullable, numeric, 30–45 |
| `weight` | nullable, numeric, 0–700 |
| `height` | nullable, numeric, 0–300 |
| (cross-field) | at least one field must be non-null |

**Success — 201 Created**:
```json
{
  "message": "Vital signs recorded.",
  "status": 201,
  "data": {
    "vitalSign": { "id": "42", "type": "vital_sign", "attributes": { ... } }
  }
}
```

**Error responses**:
| Status | Condition |
|---|---|
| 401 | Unauthenticated |
| 403 | Patient role, or doctor accessing another doctor's visit |
| 404 | Patient or visit not found, or visit not belonging to patient |
| 409 | Vitals already exist for this visit |
| 422 | Validation failure (all fields empty, or field out of range) |

---

## 2. Get Vitals for a Visit

**`GET /api/patients/{patient}/visits/{visit}/vitals`**

**Authorization**: `admin` | `doktor` | `pacijent` (own visits only)

**No request body.**

**Success — 200 OK**:
```json
{
  "message": "Vital signs retrieved.",
  "status": 200,
  "data": {
    "vitalSign": {
      "id": "42",
      "type": "vital_sign",
      "attributes": {
        "systolicBp": 125,
        "diastolicBp": 82,
        "heartRate": 72,
        "temperature": "36.8",
        "weight": "74.50",
        "height": "175.00",
        "bmi": "24.33",
        "bmiCategory": "normal",
        "flags": [],
        "visitId": "17",
        "visitDate": "2026-05-10",
        "patientId": "3",
        "patientName": "Marko Petrović",
        "doctorName": "Dr. Amina Hadžić",
        "previousVisit": {
          "visitDate": "2026-04-01",
          "weight": "76.80",
          "bmi": "25.12",
          "systolicBp": 138,
          "diastolicBp": 88,
          "weightDelta": -2.30,
          "bmiDelta": -0.79
        },
        "createdAt": "2026-05-10T09:14:00+00:00",
        "updatedAt": "2026-05-10T09:14:00+00:00"
      }
    }
  }
}
```

**`previousVisit` is null** when no earlier visit with vitals exists for this patient.

**Error responses**:
| Status | Condition |
|---|---|
| 401 | Unauthenticated |
| 403 | Patient accessing another patient's vitals |
| 404 | Patient/visit not found, visit not belonging to patient, or no vitals recorded |

---

## 3. Update Vitals for a Visit

**`PATCH /api/patients/{patient}/visits/{visit}/vitals`**

**Authorization**: `doktor` (own visits only) | `admin` (any visit)

**Request body** — all fields optional; only provided fields are updated:
```json
{
  "weight": 73.2,
  "height": 175.0
}
```

BMI is recomputed from the merged (existing + updated) weight and height values.

**Success — 200 OK**: Same shape as GET response. `bmi` reflects recomputed value.

**Error responses**:
| Status | Condition |
|---|---|
| 401 | Unauthenticated |
| 403 | Patient role, or doctor accessing another doctor's visit |
| 404 | Patient/visit not found, visit not belonging to patient, or no vitals to update |
| 422 | Field out of range |

---

## 4. Delete Vitals for a Visit

**`DELETE /api/patients/{patient}/visits/{visit}/vitals`**

**Authorization**: `admin` only

**No request body.**

**Success — 204 No Content**: Empty body.

**Error responses**:
| Status | Condition |
|---|---|
| 401 | Unauthenticated |
| 403 | Doctor or patient role |
| 404 | Patient/visit not found, visit not belonging to patient, or no vitals to delete |

---

## 5. Patient Vitals History

**`GET /api/patients/{patient}/vitals`**

**Authorization**: `admin` | `doktor` | `pacijent` (own history only)

**Query parameters** (optional):
| Parameter | Format | Description |
|---|---|---|
| `from` | `YYYY-MM-DD` | Filter: visit date on or after this date |
| `to` | `YYYY-MM-DD` | Filter: visit date on or before this date |
| `page` | integer | Pagination page (default: 1) |

**Success — 200 OK** (paginated):
```json
{
  "message": "Vitals history retrieved.",
  "status": 200,
  "data": [
    {
      "id": "42",
      "type": "vital_sign",
      "attributes": {
        "systolicBp": 125,
        "diastolicBp": 82,
        "heartRate": 72,
        "temperature": "36.8",
        "weight": "74.50",
        "height": "175.00",
        "bmi": "24.33",
        "bmiCategory": "normal",
        "flags": [],
        "visitId": "17",
        "visitDate": "2026-05-10",
        "patientId": "3",
        "patientName": "Marko Petrović",
        "doctorName": "Dr. Amina Hadžić",
        "previousVisit": null,
        "createdAt": "2026-05-10T09:14:00+00:00",
        "updatedAt": "2026-05-10T09:14:00+00:00"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 2,
    "per_page": 15,
    "to": 15,
    "total": 27
  },
  "links": {
    "first": "https://api.example.com/api/patients/3/vitals?page=1",
    "last":  "https://api.example.com/api/patients/3/vitals?page=2",
    "prev":  null,
    "next":  "https://api.example.com/api/patients/3/vitals?page=2"
  }
}
```

**Note**: `previousVisit` is null in the history list — use adjacent rows for delta computation.

**Error responses**:
| Status | Condition |
|---|---|
| 401 | Unauthenticated |
| 403 | Patient accessing another patient's history |
| 404 | Patient not found |

---

## BFF Routes (Next.js)

| BFF Path | Method | Proxies to |
|---|---|---|
| `/api/patients/[id]/visits/[visitId]/vitals` | GET | `GET /api/patients/{patient}/visits/{visit}/vitals` |
| `/api/patients/[id]/visits/[visitId]/vitals` | POST | `POST /api/patients/{patient}/visits/{visit}/vitals` |
| `/api/patients/[id]/visits/[visitId]/vitals` | PATCH | `PATCH /api/patients/{patient}/visits/{visit}/vitals` |
| `/api/patients/[id]/visits/[visitId]/vitals` | DELETE | `DELETE /api/patients/{patient}/visits/{visit}/vitals` |
| `/api/patients/[id]/vitals` | GET | `GET /api/patients/{patient}/vitals` |

All BFF handlers use Orval-generated functions (`patientsVisitsVitalsShow`, `patientsVisitsVitalsStore`, `patientsVisitsVitalsUpdate`, `patientsVisitsVitalsDestroy`, `patientsVitals`). Must be regenerated after backend changes: `pnpm run api:generate`.
