# UI Contracts: Next.js Route Handlers (Auth BFF)

These are the internal HTTP contracts between the browser and the Next.js app server.
The Next.js Route Handlers proxy to Laravel and manage the `auth_token` httpOnly cookie.

---

## POST /api/auth/login

**Purpose**: Authenticate user; set auth cookie; return user profile.

**Request**:
```
POST /api/auth/login
Content-Type: application/json

{ "email": "user@example.com", "password": "secret" }
```

**Responses**:

| Status | Body | Cookie action |
|--------|------|---------------|
| 200 | `{ "user": AuthUser }` | Sets `auth_token` (httpOnly, 8h) |
| 401 | `{ "message": "Invalid credentials" }` | None |
| 401 | `{ "message": "Account is deactivated" }` | None |
| 422 | `{ "message": "...", "errors": { field: string[] } }` | None |
| 503 | `{ "message": "Service unavailable" }` | None |

---

## POST /api/auth/logout

**Purpose**: Terminate session; clear auth cookie.

**Request**:
```
POST /api/auth/logout
(no body)
```

**Responses**:

| Status | Body | Cookie action |
|--------|------|---------------|
| 204 | (empty) | Clears `auth_token` |

Always 204 — even if Laravel logout call fails, cookie is cleared.

---

## GET /api/auth/me

**Purpose**: Return authenticated user profile using the stored cookie.

**Request**:
```
GET /api/auth/me
(no body; reads auth_token cookie internally)
```

**Responses**:

| Status | Body |
|--------|------|
| 200 | `{ "user": AuthUser }` |
| 401 | `{ "message": "Unauthenticated" }` |

Called by `AuthContext` on initial dashboard mount.

---

## Middleware Route Protection Contract

```
Incoming request URL              Cookie present?  Action
─────────────────────────────     ───────────────  ──────────────────────────────────────
/dashboard/* or /home or /about   No               Redirect → /login?callbackUrl=<url>
/login or /                       Yes              Redirect → /dashboard/home
/login or /                       No               Allow (render login page)
/api/*                            Any              Skip (no middleware on API routes)
/_next/*                          Any              Skip
/images/*, /assets/*              Any              Skip
```

**callbackUrl preservation**: On redirect to `/login`, the originally requested path is attached as `?callbackUrl=/dashboard/patients`. After successful login, the login form reads this param and redirects there instead of `/dashboard/home`.

---

## AuthUser shape (TypeScript)

Returned by `/api/auth/login` and `/api/auth/me`:

```ts
interface AuthUser {
  id: number
  name: string           // display name
  email: string
  role: 'admin' | 'doktor' | 'pacijent'
}
```
