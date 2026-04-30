# Data Model: Dashboard with Login & Logout (006-fe-auth-dashboard)

> Frontend-only feature. No database migrations. All types sourced from orval-generated `nutriBaseAPI.schemas.ts`.

## Existing Types (orval-generated — do not modify)

### `LoginRequest`
```ts
// src/api/generated/nutriBaseAPI.schemas.ts
interface LoginRequest {
  email: string    // required, valid email
  password: string // required
}
```

### `UserResource`
```ts
interface UserResource {
  type: 'users'
  id: number
  attributes: {
    name: string
    email: string
    role: string        // 'admin' | 'doktor' | 'pacijent'
    createdAt: string
    updatedAt: string
    deletedAt: string | null
  }
  relationships: {
    patient?: Patient
  }
  links: { self: string }
}
```

### `Login200`
```ts
// What Laravel returns on successful login
type Login200 = {
  message: 'Authenticated'
  status: 200
  data: {
    token: string        // Sanctum Bearer token
    user: UserResource
  }
}
```

### `UserMe200`
```ts
type UserMe200 = {
  message: 'Profile retrieved'
  status: 200
  data: { user: UserResource }
}
```

---

## New Frontend Types (to create in `src/types/auth.ts`)

### `AuthUser`

Flattened view of the authenticated user for client-side consumption. Derived from `UserResource.attributes`.

```ts
interface AuthUser {
  id: number
  name: string
  email: string
  role: 'admin' | 'doktor' | 'pacijent'
}
```

**Derivation**: Map from `UserResource` after successful login/me response.
```ts
function toAuthUser(resource: UserResource): AuthUser {
  return {
    id: resource.id,
    name: resource.attributes.name,
    email: resource.attributes.email,
    role: resource.attributes.role as AuthUser['role'],
  }
}
```

### `AuthContextValue`

Shape of the React context consumed by all dashboard components.

```ts
interface AuthContextValue {
  user: AuthUser | null      // null = not yet loaded
  isLoading: boolean         // true during initial /api/auth/me fetch
  logout: () => Promise<void>
}
```

---

## Cookie Schema

Stored server-side (httpOnly); not a JS type — documented for clarity.

| Cookie | httpOnly | Value | Lifetime |
|--------|----------|-------|----------|
| `auth_token` | `true` | Raw Sanctum Bearer token string | 8 hours (`maxAge: 28800`) |

---

## Route Handler Response Contracts

### `POST /api/auth/login`

**Request body**: `{ email: string, password: string }`

**Success 200**:
```json
{ "user": { "id": 1, "name": "Dr. Smith", "email": "smith@clinic.com", "role": "doktor" } }
```
Side effect: sets `auth_token` httpOnly cookie.

**Error 401**:
```json
{ "message": "Invalid credentials" }
```

**Error 422**:
```json
{ "message": "Validation failed", "errors": { "email": ["The email field is required."] } }
```

**Error 500**:
```json
{ "message": "Service unavailable" }
```

---

### `POST /api/auth/logout`

**Request**: no body

**Success 204**: no body  
Side effect: clears `auth_token` cookie.

Always returns 204 (even if Laravel logout fails — local session is always cleared).

---

### `GET /api/auth/me`

**Request**: no body (reads `auth_token` cookie internally)

**Success 200**:
```json
{ "user": { "id": 1, "name": "Dr. Smith", "email": "smith@clinic.com", "role": "doktor" } }
```

**Error 401** (no cookie or invalid token):
```json
{ "message": "Unauthenticated" }
```

---

## State Transitions

```
[No session]
    │ POST /api/auth/login (valid credentials)
    ▼
[Authenticated]  ←─── GET /api/auth/me (on layout mount, validates session is live)
    │ POST /api/auth/logout  OR  auth_token expires
    ▼
[No session]
```

**Session expiry handling**: If `/api/auth/me` returns 401 (token expired server-side), middleware will catch the missing/invalid cookie on next navigation and redirect to `/login`. The auth context also sets `user: null` which triggers a client-side redirect via the `useAuth` hook guard pattern in the dashboard layout.
