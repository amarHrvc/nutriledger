# Contract: Documentation Endpoints

**Feature**: 004-scramble-api-docs
**Date**: 2026-04-07

---

## New Endpoints (added by Scramble)

These endpoints are only active in `local` and `testing` environments.

### GET /docs/api

**Purpose**: Interactive Swagger UI for browsing and manually testing all API endpoints.

**Response**: HTML page (Swagger UI)

**Auth required**: No

**Environment**: local, testing only

---

### GET /docs/api.json

**Purpose**: Live OpenAPI 3.1 spec in JSON format. Primary input for Orval code generation.

**Response**: `application/json` — OpenAPI 3.1 document

**Auth required**: No

**Environment**: local, testing only

**Orval usage**:
```
input.target: http://localhost:8000/docs/api.json
```

---

## Excluded from Spec

The following routes are excluded from the generated spec:

| Route | Reason |
|---|---|
| `GET /api/test/admin-only` | Test fixture, not real API surface |
| `GET /api/test/admin-doktor-only` | Test fixture, not real API surface |
| `GET /docs/api` | Scramble UI — not an API endpoint |
| `GET /docs/api.json` | Scramble spec — not an API endpoint |
