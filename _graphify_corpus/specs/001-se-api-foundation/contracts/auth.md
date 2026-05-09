# API Contract: Authentication

**Feature**: `001-se-api-foundation` | **Date**: 2026-03-15 | **Plan**: [../plan.md](../plan.md)

Base URL: `http://localhost:8000` (dev) — configured via `VITE_API_URL` on the FE side.

All endpoints return `Content-Type: application/json`.

---

## POST /api/login

Authenticates a user and issues a revocable Bearer token.

**Auth required**: No

### Request

```json
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "secret"
}
```

| Field | Type | Required | Rules |
|---|---|---|---|
| `email` | string | yes | valid email format |
| `password` | string | yes | non-empty |

### Responses

**200 OK — valid credentials**

```json
{
  "message": "Login successful.",
  "status": 200,
  "data": {
    "token": "1|abc123...",
    "user": {
      "id": 1,
      "name": "Ana Kovač",
      "email": "ana@example.com",
      "role": "doktor"
    }
  }
}
```

**422 Unprocessable Entity — missing/invalid fields**

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

**401 Unauthorized — wrong credentials**

```json
{
  "message": "Invalid credentials.",
  "status": 401,
  "data": null
}
```

---

## POST /api/logout

Revokes the caller's current access token immediately.

**Auth required**: Yes — `Authorization: Bearer {token}`

### Request

No body.

### Responses

**204 No Content — token revoked**

Empty body.

**401 Unauthorized — missing or invalid token**

```json
{
  "message": "Unauthenticated.",
  "status": 401,
  "data": null
}
```

---

## GET /api/user

Returns the identity of the authenticated user.

**Auth required**: Yes — `Authorization: Bearer {token}`

### Request

No body.

### Responses

**200 OK**

```json
{
  "message": "OK",
  "status": 200,
  "data": {
    "id": 1,
    "name": "Ana Kovač",
    "email": "ana@example.com",
    "role": "doktor"
  }
}
```

**401 Unauthorized — missing or invalid token**

```json
{
  "message": "Unauthenticated.",
  "status": 401,
  "data": null
}
```

---

## GET /api/ping

Public health-check. Used by the React SPA scaffold to verify CORS and connectivity.

**Auth required**: No

### Request

No body.

### Responses

**200 OK**

```json
{
  "message": "ok",
  "status": 200,
  "data": {
    "status": "ok"
  }
}
```

---

## Role Group Skeletons

These groups are defined in `routes/api.php` in this feature but contain no endpoints yet.
Populated by features 002-patients and 003-visits.

| Middleware | Roles | Populated by |
|---|---|---|
| `role:admin` | admin only | 002-patients (user management) |
| `role:admin,doktor` | admin + doktor | 002-patients, 003-visits |

**Forbidden response** (any role-protected endpoint, wrong role):

```json
{
  "message": "Forbidden.",
  "status": 403,
  "data": null
}
```

---

## CORS Headers

For requests from `http://localhost:5173` (React SPA dev server):

| Header | Value |
|---|---|
| `Access-Control-Allow-Origin` | `http://localhost:5173` |
| `Access-Control-Allow-Methods` | `GET, POST, PUT, PATCH, DELETE, OPTIONS` |
| `Access-Control-Allow-Headers` | `Content-Type, Authorization, X-Requested-With` |

Preflight `OPTIONS` requests receive `204 No Content` with the above headers.
