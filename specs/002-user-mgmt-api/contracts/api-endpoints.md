# API Contract: Auth & User Management

**Branch**: `002-user-mgmt-api` | **Date**: 2026-03-19
**Base URL**: `/api`
**Auth**: Bearer token (`Authorization: Bearer {token}`) for protected endpoints

---

## Auth Endpoints

### POST /api/register
**Access**: Public (no token required)
**Rate limit**: None
**Purpose**: Register a new user account and receive an access token.

**Request body**:
```json
{
  "name": "Ana Kovač",
  "email": "ana@example.com",
  "password": "secret123",
  "password_confirmation": "secret123",
  "role": "pacijent"
}
```
- `role` is optional; defaults to `pacijent`

**Success 201**:
```json
{
  "message": "Registered successfully",
  "status": 201,
  "data": {
    "token": "1|plaintext...",
    "user": {
      "type": "user",
      "id": 1,
      "attributes": { "name": "...", "email": "...", "role": "pacijent", "createdAt": "..." }
    }
  }
}
```

**Failure 422** — validation errors:
```json
{ "message": "...", "status": 422, "errors": { "email": ["The email has already been taken."] } }
```

---

### POST /api/login
**Access**: Public
**Rate limit**: 5 requests/min per IP (`throttle:login`)
**Purpose**: Authenticate and receive an access token.

**Request body**:
```json
{ "email": "ana@example.com", "password": "secret123" }
```

**Success 200**:
```json
{
  "message": "Authenticated",
  "status": 200,
  "data": {
    "token": "1|plaintext...",
    "user": {
      "type": "user",
      "id": 1,
      "attributes": { "name": "...", "email": "...", "role": "pacijent", "deletedAt": null, "createdAt": "...", "updatedAt": "..." },
      "links": { "self": "http://localhost/api/users/1" }
    }
  }
}
```

**Failure 401** — wrong credentials or deactivated account:
```json
{ "message": "Invalid credentials", "status": 401 }
```

**Failure 422** — missing fields:
```json
{ "message": "...", "status": 422, "errors": { "email": ["The email field is required."] } }
```

**Failure 429** — rate limit exceeded:
```json
{ "message": "Too many login attempts.", "status": 429 }
```

---

### POST /api/logout
**Access**: `auth:sanctum`
**Purpose**: Revoke the current access token.

**Request**: No body. Token in `Authorization` header.

**Success 204**: No content.

**Failure 401**: Token missing or invalid.

---

### GET /api/user
**Access**: `auth:sanctum`
**Purpose**: Retrieve the authenticated user's own profile.

**Success 200**:
```json
{
  "message": "OK",
  "status": 200,
  "data": {
    "type": "user",
    "id": 1,
    "attributes": { "name": "...", "email": "...", "role": "...", "deletedAt": null, "createdAt": "...", "updatedAt": "..." },
    "relationships": { "patient": { "data": null } },
    "links": { "self": "http://localhost/api/users/1" }
  }
}
```

**Note**: `relationships.patient.data` is `null` when the user has no linked patient record (e.g., role is `admin` or `doktor`). The `links.self` always points to the canonical `/api/users/{id}` URL regardless of which endpoint returned this resource.

**Failure 401**: Token missing or invalid.

---

## User Management Endpoints (Admin Only)

All endpoints below require:
- `Authorization: Bearer {token}` — authenticated user
- User must have role `admin` — otherwise 403

---

### GET /api/users
**Middleware**: `auth:sanctum`, `role:admin`
**Purpose**: List all users (active + deactivated), paginated.

**Query params** (optional): `page=1`

**Success 200**:
```json
{
  "message": "OK",
  "status": 200,
  "data": {
    "data": [
      {
        "type": "user", "id": 1,
        "attributes": { "name": "...", "email": "...", "role": "admin", "deletedAt": null, "createdAt": "...", "updatedAt": "..." },
        "links": { "self": "http://localhost/api/users/1" }
      }
    ],
    "meta": { "current_page": 1, "last_page": 1, "per_page": 15, "total": 1 },
    "links": { "first": "...", "last": "...", "prev": null, "next": null }
  }
}
```

**Note**: The outer `data` key comes from the `ApiResponses` envelope. The inner `data` array, `meta`, and `links` are produced by Laravel's paginated `ResourceCollection` (`UserResource::collection($paginator)`). This double-`data` nesting is the expected shape when wrapping a paginated collection in the standard envelope.


**Failure 401/403**: Unauthenticated or non-admin.

---

### POST /api/users
**Middleware**: `auth:sanctum`, `role:admin`
**Purpose**: Create a new user account.

**Request body**:
```json
{ "name": "Marko Marić", "email": "marko@example.com", "password": "secret123", "role": "doktor" }
```

**Success 201**:
```json
{
  "message": "User created successfully",
  "status": 201,
  "data": { "type": "user", "id": 5, "attributes": { ... }, "links": { "self": "..." } }
}
```

**Failure 422**: Validation errors.
**Failure 401/403**: Unauthenticated or non-admin.

---

### GET /api/users/{id}
**Middleware**: `auth:sanctum`, `role:admin`
**Purpose**: View a specific user's profile.

**Success 200**:
```json
{
  "message": "OK",
  "status": 200,
  "data": { "type": "user", "id": 5, "attributes": { ... }, "relationships": { "patient": { ... } }, "links": { ... } }
}
```

**Failure 404**: User not found (or permanently deleted).
**Failure 401/403**: Unauthenticated or non-admin.

---

### PUT/PATCH /api/users/{id}
**Middleware**: `auth:sanctum`, `role:admin`
**Purpose**: Update one or more fields on a user account. All fields optional (partial update).

**Request body** (any subset):
```json
{ "name": "Updated Name", "email": "new@example.com", "password": "newpass123", "role": "pacijent" }
```

**Success 200**:
```json
{
  "message": "User updated successfully",
  "status": 200,
  "data": { "type": "user", "id": 5, "attributes": { ... }, "links": { ... } }
}
```

**Failure 422**: Validation errors (e.g., duplicate email).
**Failure 404**: User not found.
**Failure 401/403**: Unauthenticated or non-admin.

---

### DELETE /api/users/{id}
**Middleware**: `auth:sanctum`, `role:admin`
**Purpose**: Soft-delete (deactivate) a user account. Data is preserved and restorable.

**Constraint**: Admin cannot deactivate themselves → 403.

**Success 204**: No content.

**Failure 401**: Unauthenticated.
**Failure 403**: Attempting to deactivate own account.
**Failure 404**: User not found.

---

### POST /api/users/{id}/restore
**Middleware**: `auth:sanctum`, `role:admin`
**Purpose**: Restore a previously deactivated (soft-deleted) user account.

**Note**: Resolved via `User::withTrashed()->findOrFail($id)` — finds both active and soft-deleted users. Calling restore on an already-active user is a no-op (no error, returns the user unchanged). Returns 404 only for permanently deleted or non-existent IDs.

**Success 200**:
```json
{
  "message": "User restored successfully",
  "status": 200,
  "data": { "type": "user", "id": 5, "attributes": { "deletedAt": null, ... }, "links": { ... } }
}
```

**Failure 404**: User not found (permanently deleted or non-existent ID).
**Failure 401/403**: Unauthenticated or non-admin.

---

### DELETE /api/users/{id}/force
**Middleware**: `auth:sanctum`, `role:admin`
**Purpose**: Permanently and irreversibly delete a user account. Releases their email for re-registration.

**Note**: Resolved via `User::withTrashed()->findOrFail($id)` — permanently deletes regardless of whether the user is currently active or deactivated. Returns 404 only for non-existent IDs.

**Success 204**: No content.

**Failure 404**: User not found.
**Failure 401/403**: Unauthenticated or non-admin.

---

## Response Envelope Reference

| Scenario | Structure |
|----------|-----------|
| Success with data | `{ message, status, data: {...} }` |
| Success no content | HTTP 204, empty body |
| Validation failure | `{ message, status: 422, errors: { field: [...] } }` |
| Auth failure | `{ message, status: 401 }` |
| Forbidden | `{ message, status: 403 }` |
| Not found | `{ message, status: 404 }` |
| Rate limited | `{ message, status: 429 }` |
