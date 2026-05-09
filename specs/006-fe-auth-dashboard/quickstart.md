# Quickstart: 006-fe-auth-dashboard

## Prerequisites

- Laravel backend running at `http://localhost:8000` with Sanctum configured
- Next.js dev server running: `cd frontend && pnpm dev`
- `.env.local` in `frontend/`:

```env
NEXT_PUBLIC_API_URL=http://localhost:8000
INTERNAL_API_URL=http://localhost:8000   # used by Route Handlers (server-side)
```

## Running the feature

```bash
cd frontend
pnpm dev
```

Open `http://localhost:3000` → redirects to `/login` (unauthenticated).

**Test credentials** (from Laravel seeder):
- Admin: `admin@nutribase.com` / password
- Doctor: `doctor@nutribase.com` / password
- Patient: `patient@nutribase.com` / password

## Flow to verify manually

1. Navigate to `http://localhost:3000` → lands on `/login`
2. Submit valid credentials → redirected to `/dashboard/home`
3. Verify user name + role badge visible in header and/or sidebar
4. Navigate to `/login` while logged in → redirected back to `/dashboard/home`
5. Click logout (top-right user dropdown) → redirected to `/login`
6. Press browser back → `/dashboard/home` redirects to `/login` again

## Key files modified / created

| File | What changed |
|------|-------------|
| `src/middleware.ts` | **NEW** — route protection at edge |
| `src/app/api/auth/login/route.ts` | **NEW** — login Route Handler |
| `src/app/api/auth/logout/route.ts` | **NEW** — logout Route Handler |
| `src/app/api/auth/me/route.ts` | **NEW** — me Route Handler |
| `src/context/AuthContext.tsx` | **NEW** — auth state context |
| `src/types/auth.ts` | **NEW** — AuthUser type |
| `src/components/Providers.tsx` | **MODIFIED** — wrap with AuthProvider |
| `src/views/Login.tsx` | **MODIFIED** — real auth, form validation, error display |
| `src/components/layout/shared/UserDropdown.tsx` | **MODIFIED** — real user data + logout API call |
| `src/components/layout/vertical/VerticalMenu.tsx` | **MODIFIED** — role-aware menu items |
| `src/views/home/index.tsx` | **NEW** — dashboard home view (profile summary) |
| `src/app/(dashboard)/home/page.tsx` | **MODIFIED** — import real home view |

## Architecture diagram

```
Browser
  │
  ├─ [GET /dashboard/home] ──► Next.js Middleware ──► no cookie? → redirect /login
  │                                                   cookie ok? → render page
  │
  ├─ [POST /api/auth/login] ──► Route Handler ──► Laravel POST /api/login
  │                              ↓ sets auth_token cookie (httpOnly)
  │                              ↓ returns { user }
  │
  ├─ [GET /api/auth/me] ──► Route Handler (reads cookie) ──► Laravel GET /api/user
  │                          ↓ returns { user } or 401
  │
  └─ [POST /api/auth/logout] ──► Route Handler ──► Laravel POST /api/logout (best-effort)
                                  ↓ clears auth_token cookie
                                  ↓ returns 204
```

## Environment variable note

`NEXT_PUBLIC_API_URL` is browser-visible. `INTERNAL_API_URL` is server-only (Route Handlers).
If Laravel and Next.js run on the same machine, both point to `http://localhost:8000`.
In production/Docker, `INTERNAL_API_URL` may differ (e.g., `http://backend:8000`).
