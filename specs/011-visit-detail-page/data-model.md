# Data Model: Visit Detail Page

**Phase**: 1 — Design  
**Branch**: `011-visit-detail-page`  
**Date**: 2026-05-09

## Overview

No new entities or schema changes are required. The feature consumes the existing `VisitResource` type from the generated API client. This document describes the data shape as consumed by the frontend detail view.

---

## Entity: VisitResource (consumed, not modified)

**Source**: `frontend/src/api/generated/nutriBaseAPI.schemas.ts`  
**API endpoint**: `GET /api/patients/{patient}/visits/{visit}` → `patientsVisitsShow(patient: number, visit: number)`

```typescript
interface VisitResource {
  type: 'visit';
  id: string;
  attributes: {
    date: string;             // ISO date string "YYYY-MM-DD"
    time: string | null;      // "HH:MM" or null
    notes: string | null;     // Clinical notes or null
    doctorName: string | null;
    patientName: string | null;
    patientId: string | null; // Used to build patient detail link + API calls
    isEditable: boolean;      // Server-computed; true if within 1-day edit window AND
                              // user is the recording doctor or admin
    createdAt: string | null;
    updatedAt: string | null;
  };
  relationships: {
    patient: { data?: { type: 'patient'; id: string } };
    doctor:  { data?: { type: 'user';    id: string } };
  };
}
```

### Validation rules (enforced by existing backend)

| Field | Rule |
|---|---|
| `date` | Required, must be on or before today |
| `time` | Optional, format `HH:MM` |
| `notes` | Optional, max 10,000 characters |
| `isEditable` | Read-only; computed server-side |

### State transitions

```
visit.isEditable = true   →  Edit button rendered (for authorized roles)
visit.isEditable = false  →  Edit button hidden / disabled
user.role = 'admin'       →  Delete button rendered
user.role != 'admin'      →  Delete button hidden
```

---

## Data flow on the detail page

```
URL: /dashboard/visits/[id]?patient=[patientId]
         │                         │
         └── useParams().id        └── useSearchParams().get('patient')
                   │                              │
                   └──────────────────────────────┘
                                  │
                         patientsVisitsShow(patientId, visitId)
                                  │
                         VisitResource → VisitDetail component
```

### Error states handled

| Status | Display |
|---|---|
| 401 | Redirect to login (handled by auth mutator) |
| 403 | `Alert severity="error"` — access denied message |
| 404 | `Alert severity="error"` — visit not found message |
| Network error | `Alert severity="error"` — generic error message |
| Loading | `CircularProgress` centered |

---

## No new entities or migrations needed

All data is read from the existing visits table via the existing API. No new tables, columns, relationships, or factories are required for this feature.
