# Research: Dashboard with Login & Logout (006-fe-auth-dashboard)

## 1. Token Storage Strategy

**Decision**: httpOnly cookie (not localStorage)

**Rationale**:
- httpOnly cookies are invisible to JavaScript → immune to XSS attacks
- Cookies are sent automatically on every request, including SSR requests
- Next.js Middleware can read cookies server-side to protect routes before any React renders
- localStorage is JS-accessible → any injected script can steal the token

**Current PoC gap**: `client.ts` reads `localStorage.getItem('token')`. This works but is insecure. The plan migrates to cookies while keeping `client.ts` for unauthenticated calls.

**Alternatives considered**:
- `sessionStorage` — same XSS exposure as localStorage, dies on tab close
- `memory` (JS variable) — safe from XSS but lost on reload, incompatible with SSR

**Implementation note**: Two cookies are set on login:
- `auth_token` (httpOnly: true) — for middleware route protection; JS cannot read
- Client-side auth state comes from calling `/api/auth/me` server Route Handler, not from the cookie directly

---

## 2. Route Protection Strategy

**Decision**: Next.js Middleware (`src/middleware.ts`)

**Rationale**:
- Middleware runs at the Edge before any React component renders — zero flash of unauthorized content
- Can read httpOnly cookies (server-side access)
- Single location for all redirect logic (protected → login, login → dashboard if authed)
- Preserves `callbackUrl` query param so user lands on the page they requested after login

**Alternatives considered**:
- Client-side `AuthGuard` HOC — risks flash of protected content; React renders before redirect fires
- Server Component guard in layout — works but has to be in every layout separately; middleware is DRY

**Middleware matcher config** (applies only to relevant routes — skips `/_next`, `/api`, `/images`, etc.):
```ts
export const config = {
  matcher: ['/((?!_next|api|images|favicon.ico|assets).*)']
}
```

Protected path prefix: `/(dashboard)/` → redirect to `/login?callbackUrl=<url>` if no `auth_token` cookie.
Login path: `/login` → redirect to `/dashboard/home` (or `callbackUrl`) if `auth_token` cookie present.

---

## 3. Auth State on the Client

**Decision**: React Context (`AuthContext`) populated via `GET /api/auth/me`

**Rationale**:
- Dashboard layout calls `/api/auth/me` on mount (Server Component can do this at render time)
- Context makes `user` (name, email, role) available to all dashboard components without prop drilling
- `UserDropdown` reads from context → no hardcoded "John Doe"
- `VerticalMenu` reads `user.role` from context → renders role-appropriate menu items

**Alternatives considered**:
- Redux slice for auth — overkill; auth state is simple and stable within a session
- Zustand — not in the project's dep list; avoid adding dependencies
- `useSession()` from next-auth — next-auth is not installed; PoC is plain fetch

---

## 4. Next.js BFF Route Handlers for Auth

**Decision**: Three Route Handlers proxy auth operations to Laravel

**Rationale**:
- Route Handlers run on the server → can read/write httpOnly cookies
- The client never touches the Sanctum Bearer token directly (it lives only in the httpOnly cookie server-side)
- Consistent error handling and response normalization in one place

**Handlers**:

| Route | Method | Action |
|-------|--------|--------|
| `/api/auth/login` | POST | Calls `POST /api/login` on Laravel; on 200 sets `auth_token` cookie; returns `{ user }` |
| `/api/auth/logout` | POST | Calls `POST /api/logout` on Laravel (best-effort); always clears `auth_token` cookie |
| `/api/auth/me` | GET | Reads `auth_token` cookie; calls `GET /api/user` on Laravel with Bearer header; returns `{ user }` or 401 |

**Cookie settings** (`auth_token`):
- `httpOnly: true`
- `sameSite: 'lax'`
- `secure: true` (production only; `process.env.NODE_ENV === 'production'`)
- `path: '/'`
- `maxAge: 60 * 60 * 8` (8 hours default; can be extended for "remember me" later)

---

## 5. Existing Components to Reuse (from graph + knowledge scan)

**From `_knowledge/08-components.md` and actual source**:

| Component | Current state | Change needed |
|-----------|--------------|---------------|
| `UserDropdown.tsx` | Hardcoded "John Doe", logout just navigates to `/login` | Wire to AuthContext for real user; call `/api/auth/logout` |
| `NavbarContent.tsx` | Renders `UserDropdown` + `ModeDropdown` + `NavToggle` | No change needed |
| `VerticalMenu.tsx` | Hardcoded Home/About links | Add role-conditional menu items |
| `Login.tsx` | Submits with `router.push('/')`, no real auth | Call `/api/auth/login`; handle errors; remove social buttons |
| `(dashboard)/layout.tsx` | `Providers + LayoutWrapper`, no AuthGuard | Middleware handles protection; layout only wraps with `AuthProvider` |
| `Providers.tsx` | Redux + MUI theme + Settings | Wrap with `AuthProvider` |

**Existing layout system** (from `_knowledge/04-layout-system.md`):
- `LayoutWrapper` + `VerticalLayout` + `HorizontalLayout` — already in dashboard layout, no changes needed
- `BlankLayout` — already used for login page via `(blank-layout-pages)/layout.tsx`, no changes needed

**Existing menu system** (from `_knowledge/05-navigation.md`):
- `@menu/vertical-menu` — `Menu` + `MenuItem` — already used in `VerticalMenu.tsx`
- Icon format: `tabler-{name}` (Iconify Tabler set)
- No i18n needed (app stripped `[lang]` routing)

---

## 6. Dashboard Home Page Content

**Decision**: Profile summary card + role badge + placeholder navigation cards

**Rationale**:
- FR-005 requires user name and role visible on dashboard
- Spec explicitly says dashboard is a "navigation hub and profile summary" — no live clinical data
- Reuse MUI `Card`, `Avatar`, `Typography`, `Chip` components (already in theme)
- Match Vuexy visual language (same component library used everywhere)

**Layout**: Vertical layout (sidebar), default mode from `themeConfig`. Use existing `VerticalLayout` + `VerticalMenu` already in `(dashboard)/layout.tsx`.

---

## 7. Role-Aware Navigation

**Decision**: Conditionally render `MenuItem` groups based on `user.role` from `AuthContext`

**Roles and menu access** (from domain model + constitution):

| Role | Navigation sections |
|------|-------------------|
| `admin` | Dashboard home, Patients, Users, Visits |
| `doktor` | Dashboard home, Patients, Visits |
| `pacijent` | Dashboard home (own profile/visits only — future feature groups) |

**Implementation**: No dynamic data fetch — simple `if/switch` on `user.role` in `VerticalMenu.tsx`.

---

## 8. Login Form Validation

**Decision**: React Hook Form + Valibot (already in project from `_knowledge/08-components.md`)

**Rationale**: Already installed and used in `src/views/forms/form-validation/`. No new dependency needed.

**Validation rules**:
- `email`: required, valid email format (Valibot `email()`)
- `password`: required, minLength 1 (server validates strength)

**Error display**: MUI `TextField` `error` + `helperText` props (already used in `CustomTextField`).

---

## 9. Laravel Backend — No Changes Required

**Decision**: Backend API is already complete for this feature

**Evidence** (from orval-generated types):
- `POST /api/login` → `{ data: { token, user } }` — fully typed in `auth.ts`
- `POST /api/logout` → 204 — fully typed
- `GET /api/user` → `{ data: { user } }` — fully typed
- All response shapes typed in `nutriBaseAPI.schemas.ts`

**CORS**: Laravel must allow `http://localhost:3000` as origin with `credentials: true` (Sanctum is already configured for this in the existing setup per constitution).
