# BFF Endpoint Contracts: Admin User Management

**Branch**: `008-admin-user-management` | **Date**: 2026-05-01

All routes live under `frontend/src/app/api/users/`. All routes require the `auth_token` httpOnly cookie. Token missing → 401. All routes call Orval-generated functions from `src/api/generated/user/user.ts` with injected auth headers.

---

## GET /api/users

**File**: `app/api/users/route.ts`  
**Orval function**: `usersIndex(options)`  
**Query params**: `?page=N` (forwarded to backend, default 1)

**Request**: No body  
**Response 200**:
```json
{
  "users": [ UserResource, ... ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 15,
    "total": 42
  }
}
```
**Response 401**: `{ "message": "Unauthenticated" }`  
**Response 502**: `{ "message": "Failed to load users" }`

---

## POST /api/users

**File**: `app/api/users/route.ts`  
**Orval function**: `usersStore(body, options)`

**Request body**:
```json
{
  "name": "Jane Doe",
  "email": "jane@clinic.com",
  "password": "secret123",
  "password_confirmation": "secret123",
  "role": "doktor"
}
```
**Response 201**:
```json
{ "user": UserResource }
```
**Response 401**: `{ "message": "Unauthenticated" }`  
**Response 422**: `{ "message": "...", "errors": { "email": ["..."] } }` — forwarded as-is from Laravel

---

## GET /api/users/[id]

**File**: `app/api/users/[id]/route.ts`  
**Orval function**: `usersShow(id, options)`

**Response 200**:
```json
{ "user": UserResource }
```
**Response 401/403/404**: forwarded from Laravel

---

## PATCH /api/users/[id]

**File**: `app/api/users/[id]/route.ts`  
**Orval function**: `usersUpdate(id, body, options)`

**Request body** (all fields optional):
```json
{ "name": "New Name", "role": "admin" }
```
**Response 200**:
```json
{ "user": UserResource }
```
**Response 422**: forwarded from Laravel

---

## DELETE /api/users/[id]

**File**: `app/api/users/[id]/route.ts`  
**Orval function**: `usersDestroy(id, options)`  
**Action**: Soft-delete (deactivate)

**Response 204**: No content  
**Response 401/403/404**: forwarded from Laravel

---

## POST /api/users/[id]/restore

**File**: `app/api/users/[id]/restore/route.ts`  
**Orval function**: `usersRestore(id, options)`

**Response 200**:
```json
{ "user": UserResource }
```

---

## DELETE /api/users/[id]/force

**File**: `app/api/users/[id]/force/route.ts`  
**Orval function**: `usersForceDelete(id, options)`  
**Action**: Permanent delete (irreversible)

**Response 204**: No content
