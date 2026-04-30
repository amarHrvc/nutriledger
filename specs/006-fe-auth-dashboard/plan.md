# Implementation Plan: Dashboard with Login & Logout (Sanctum Token Auth)

**Branch**: `006-fe-auth-dashboard` | **Date**: 2026-04-29 | **Spec**: [spec.md](spec.md)  
**Input**: Feature specification from `/specs/006-fe-auth-dashboard/spec.md`

> **Refined**: 2026-04-29 — fixed URL structure, AuthProvider placement, removed unavailable deps, added explicit code for all steps.

## Summary

Replace the login PoC stub with a real Sanctum token auth flow: httpOnly cookie storage, Next.js Middleware route protection, a React AuthContext, and role-aware dashboard UI — all built on top of the existing Vuexy layout components and orval-generated API types.

**No backend changes required.** Laravel's `/api/login`, `/api/logout`, and `/api/user` endpoints are already complete.

## Technical Context

**Language/Version**: TypeScript 5.9 (strict)  
**Primary Dependencies**: Next.js 16 (App Router), MUI v7, orval-generated API types, React built-ins for form state  
**Storage**: httpOnly cookie (`auth_token`) — no database, no Prisma changes  
**Testing**: Browser manual testing; no Pest tests (frontend feature)  
**Target Platform**: Browser via Next.js App Router  
**Project Type**: Web application — frontend layer only  
**Performance Goals**: Login → dashboard redirect < 500ms; page load < 2s  
**Constraints**: No new npm dependencies; reuse existing MUI + Vuexy components; use React `useState` for form state (react-hook-form is NOT in `package.json`)

## Constitution Check

*GATE: Must pass before implementation.*

| Principle | Applicability | Status |
|-----------|--------------|--------|
| I. Dual-Track Architecture | FE work only; no shared BE artifacts modified | ✅ Pass |
| II. Authorization at Every Layer | BE authorization unchanged. FE enforces via middleware + AuthContext; 401/403 handled gracefully | ✅ Pass |
| III. Test-First (Pest) | Pest tests apply to BE only. FE verified by manual browser testing | ✅ Pass (N/A for FE) |
| IV. Code Quality Gates (Pint/Larastan) | Apply to BE only. FE uses ESLint + TypeScript strict | ✅ Pass (N/A for FE) |
| V. Tasks Are Developer-Ready Specs | Each task is self-contained with inputs, outputs, steps, and verification | ✅ Pass |

**BE/FE separation**: This feature is 100% frontend. No task crosses both layers.

---

## Route Structure Decision (IMPORTANT)

Next.js route groups like `(dashboard)` are **invisible in URLs**. The current file `src/app/(dashboard)/home/page.tsx` maps to URL `/home`, not `/dashboard/home`.

To get clean `/dashboard/*` URLs (needed for middleware, menu hrefs, and future feature groups), pages must live one level deeper:

```
src/app/(dashboard)/dashboard/home/page.tsx   → URL: /dashboard/home  ✅
src/app/(dashboard)/home/page.tsx             → URL: /home            ❌ (current, wrong depth)
```

**FE-000 adds a restructuring step** that moves pages to the correct depth before any other task. All URL references in this plan use the `/dashboard/*` form.

---

## Project Structure

### Documentation (this feature)

```text
specs/006-fe-auth-dashboard/
├── plan.md              ← this file
├── research.md          ← Phase 0: tech decisions
├── data-model.md        ← Phase 1: types + state contracts
├── quickstart.md        ← Phase 1: how to run and verify
├── contracts/
│   └── nextjs-route-handlers.md  ← Phase 1: HTTP interface contracts
└── tasks.md             ← Phase 2 output (/speckit.tasks — NOT yet created)
```

### Source Code (affected paths)

```text
frontend/src/
├── middleware.ts                              # NEW
├── types/
│   └── auth.ts                               # NEW
├── context/
│   └── AuthContext.tsx                       # NEW
├── app/
│   ├── api/auth/
│   │   ├── login/route.ts                    # NEW
│   │   ├── logout/route.ts                   # NEW
│   │   └── me/route.ts                       # NEW
│   ├── (blank-layout-pages)/
│   │   └── login/page.tsx                    # MODIFIED — pass searchParams
│   └── (dashboard)/
│       ├── layout.tsx                        # MODIFIED — wrap with AuthProvider
│       ├── home/page.tsx                     # DELETED (moved in FE-000)
│       └── dashboard/
│           ├── home/page.tsx                 # NEW (moved from above)
│           ├── about/page.tsx                # NEW (moved from (dashboard)/about/)
│           └── page/page.tsx                 # DELETED (PoC page — remove)
├── views/
│   ├── home/
│   │   └── index.tsx                         # NEW — dashboard home view
│   └── Login.tsx                             # MODIFIED — real auth + error display
└── components/
    ├── layout/shared/
    │   └── UserDropdown.tsx                  # MODIFIED — real user + logout
    └── layout/vertical/
        └── VerticalMenu.tsx                  # MODIFIED — role-aware items
```

---

## Task Sequence

Tasks MUST be implemented in order. Each depends on the previous.

---

### FE-000 — Route Restructuring

**Goal**: Move existing pages to `/dashboard/*` URL depth so middleware, menu hrefs, and redirects are consistent.

**Inputs**: `src/app/(dashboard)/home/page.tsx`, `src/app/(dashboard)/about/page.tsx`, `src/app/(dashboard)/page/page.tsx`

**Outputs**:
- `src/app/(dashboard)/dashboard/home/page.tsx` (moved)
- `src/app/(dashboard)/dashboard/about/page.tsx` (moved)
- Old files at `(dashboard)/home/`, `(dashboard)/about/`, `(dashboard)/page/` deleted

**Ordered steps**:

1. Create `src/app/(dashboard)/dashboard/` directory.

2. Move `src/app/(dashboard)/home/page.tsx` → `src/app/(dashboard)/dashboard/home/page.tsx` (content unchanged for now).

3. Move `src/app/(dashboard)/about/page.tsx` → `src/app/(dashboard)/dashboard/about/page.tsx` (content unchanged).

4. Delete `src/app/(dashboard)/page/page.tsx` — the PoC API page is no longer needed; its functionality is replaced by the real API flow.

5. Delete the now-empty `src/app/(dashboard)/home/`, `src/app/(dashboard)/about/`, `src/app/(dashboard)/page/` directories.

6. Verify `src/app/(dashboard)/layout.tsx` is unchanged — it still covers all pages in the `(dashboard)` group regardless of depth.

**Decision rationale**: Route groups in Next.js App Router are organizational — they do not add URL path segments. Moving pages into a `dashboard/` subdirectory inside the group gives clean `/dashboard/*` URLs that match the sidebar menu hrefs and the middleware's protected prefix.

**Verification**: `pnpm dev` → navigate to `http://localhost:3000/dashboard/home` → see "Home page!" (existing stub). Navigate to `http://localhost:3000/home` → 404.

---

### FE-001 — Auth Types and ENV Setup

**Goal**: Define shared TypeScript types for auth and wire environment variables.

**Inputs**: `src/api/generated/nutriBaseAPI.schemas.ts` (existing orval types)

**Outputs**:
- `src/types/auth.ts` (new)
- `frontend/.env.local` (add `INTERNAL_API_URL` if not present)

**Ordered steps**:

1. Create `src/types/auth.ts`:

```ts
import type { UserResource } from '@/api/generated/nutriBaseAPI.schemas'

export interface AuthUser {
  id: number
  name: string
  email: string
  role: 'admin' | 'doktor' | 'pacijent'
}

export interface AuthContextValue {
  user: AuthUser | null
  isLoading: boolean
  logout: () => Promise<void>
}

export function toAuthUser(resource: UserResource): AuthUser {
  return {
    id: resource.id,
    name: resource.attributes.name,
    email: resource.attributes.email,
    role: resource.attributes.role as AuthUser['role'],
  }
}
```

2. Ensure `frontend/.env.local` contains:

```env
NEXT_PUBLIC_API_URL=http://localhost:8000
INTERNAL_API_URL=http://localhost:8000
```

`INTERNAL_API_URL` is server-only (not prefixed with `NEXT_PUBLIC_`). Route Handlers read it server-side; the browser never sees it.

**Decision rationale**: `AuthUser` is a flattened, stable frontend type decoupled from the orval `UserResource` JSON:API shape. `toAuthUser` is the single mapping point — if the API schema changes, only this function needs updating. The `import type` (not inline import) is required for TypeScript strict mode.

**Verification**: `pnpm build` — no TypeScript errors in `src/types/auth.ts`.

---

### FE-002 — Next.js Route Handlers (Auth BFF)

**Goal**: Three server-side Route Handlers that proxy auth to Laravel and manage the `auth_token` httpOnly cookie.

**Inputs**: `src/types/auth.ts` (FE-001), `INTERNAL_API_URL` env var

**Outputs**:
- `src/app/api/auth/login/route.ts`
- `src/app/api/auth/logout/route.ts`
- `src/app/api/auth/me/route.ts`

**Ordered steps**:

1. Create `src/app/api/auth/login/route.ts`:

```ts
import { cookies } from 'next/headers'
import { NextResponse } from 'next/server'
import { toAuthUser } from '@/types/auth'

const INTERNAL_API = process.env.INTERNAL_API_URL

export async function POST(request: Request) {
  const body = await request.json()

  let laravelRes: Response
  try {
    laravelRes = await fetch(`${INTERNAL_API}/api/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(body),
    })
  } catch {
    return NextResponse.json({ message: 'Service unavailable' }, { status: 503 })
  }

  const payload = await laravelRes.json()

  if (!laravelRes.ok) {
    return NextResponse.json(
      { message: payload.message ?? 'Authentication failed' },
      { status: laravelRes.status }
    )
  }

  const cookieStore = await cookies()
  cookieStore.set('auth_token', payload.data.token, {
    httpOnly: true,
    sameSite: 'lax',
    secure: process.env.NODE_ENV === 'production',
    path: '/',
    maxAge: 60 * 60 * 8,
  })

  const user = toAuthUser(payload.data.user)
  return NextResponse.json({ user }, { status: 200 })
}
```

2. Create `src/app/api/auth/logout/route.ts`:

```ts
import { cookies } from 'next/headers'
import { NextResponse } from 'next/server'

const INTERNAL_API = process.env.INTERNAL_API_URL

export async function POST() {
  const cookieStore = await cookies()
  const token = cookieStore.get('auth_token')?.value

  if (token) {
    try {
      await fetch(`${INTERNAL_API}/api/logout`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
      })
    } catch {
      // best-effort; always clear cookie regardless
    }
  }

  cookieStore.delete('auth_token')
  return new NextResponse(null, { status: 204 })
}
```

3. Create `src/app/api/auth/me/route.ts`:

```ts
import { cookies } from 'next/headers'
import { NextResponse } from 'next/server'
import { toAuthUser } from '@/types/auth'

const INTERNAL_API = process.env.INTERNAL_API_URL

export async function GET() {
  const cookieStore = await cookies()
  const token = cookieStore.get('auth_token')?.value

  if (!token) {
    return NextResponse.json({ message: 'Unauthenticated' }, { status: 401 })
  }

  let laravelRes: Response
  try {
    laravelRes = await fetch(`${INTERNAL_API}/api/user`, {
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
    })
  } catch {
    return NextResponse.json({ message: 'Service unavailable' }, { status: 503 })
  }

  if (!laravelRes.ok) {
    cookieStore.delete('auth_token')
    return NextResponse.json({ message: 'Unauthenticated' }, { status: 401 })
  }

  const payload = await laravelRes.json()
  return NextResponse.json({ user: toAuthUser(payload.data.user) }, { status: 200 })
}
```

**Decision rationale**: Route Handlers run on the server — the only layer that can set httpOnly cookies while also making authenticated fetch calls to Laravel. The browser never touches the raw Sanctum Bearer token. `cookies()` is async in Next.js 15+/16.

**Verification**:
```bash
# With Laravel running at :8000 and Next.js at :3000
curl -X POST http://localhost:3000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@nutribase.com","password":"password"}' -v
# Expect: HTTP 200 + Set-Cookie: auth_token=... in response headers

# Then:
curl http://localhost:3000/api/auth/me \
  --cookie "auth_token=<value_from_above>"
# Expect: HTTP 200 + { "user": { "name": "...", "role": "admin" } }
```

---

### FE-003 — Next.js Middleware (Route Protection)

**Goal**: Protect all `/dashboard/*` routes at the Edge; redirect authenticated users away from `/login`.

**Inputs**: Cookie name `auth_token` (FE-002), route structure from FE-000

**Outputs**: `src/middleware.ts` (new)

**Ordered steps**:

1. Create `src/middleware.ts`:

```ts
import { NextResponse } from 'next/server'
import type { NextRequest } from 'next/server'

export function middleware(request: NextRequest) {
  const { pathname } = request.nextUrl
  const token = request.cookies.get('auth_token')?.value

  const isProtected = pathname.startsWith('/dashboard')
  const isLoginPage = pathname === '/login'

  if (isProtected && !token) {
    const loginUrl = new URL('/login', request.url)
    loginUrl.searchParams.set('callbackUrl', pathname)
    return NextResponse.redirect(loginUrl)
  }

  if (isLoginPage && token) {
    const callbackUrl = request.nextUrl.searchParams.get('callbackUrl')
    const destination = callbackUrl?.startsWith('/dashboard') ? callbackUrl : '/dashboard/home'
    return NextResponse.redirect(new URL(destination, request.url))
  }

  return NextResponse.next()
}

export const config = {
  matcher: ['/((?!_next/static|_next/image|api|images|assets|favicon\\.ico).*)'],
}
```

**Decision rationale**: A single `pathname.startsWith('/dashboard')` check covers all current and future dashboard routes after FE-000. The `callbackUrl` guard (`startsWith('/dashboard')`) prevents open-redirect attacks — only same-origin dashboard paths are followed. `api/*` is excluded from the matcher so the auth Route Handlers themselves are never intercepted.

**Verification**:
1. Clear cookies → navigate to `http://localhost:3000/dashboard/home` → lands on `/login?callbackUrl=/dashboard/home`
2. Log in → redirected to `/dashboard/home`
3. While logged in → navigate to `http://localhost:3000/login` → redirected to `/dashboard/home`
4. Check that `http://localhost:3000/api/auth/me` is reachable (not intercepted by middleware)

---

### FE-004 — Auth Context

**Goal**: Provide authenticated user state to all dashboard components via React Context.

**Inputs**: `src/types/auth.ts` (FE-001), `/api/auth/me` Route Handler (FE-002)

**Outputs**:
- `src/context/AuthContext.tsx` (new)
- `src/app/(dashboard)/layout.tsx` (modified — wrap content with `AuthProvider`)

**Note on placement**: `Providers.tsx` is an `async` Server Component. `AuthProvider` must be a Client Component (uses `useState`, `useEffect`, `useRouter`). The correct placement is `(dashboard)/layout.tsx`, which already wraps `Providers` — we add `AuthProvider` inside it. This keeps auth context scoped to authenticated pages only.

**Ordered steps**:

1. Create `src/context/AuthContext.tsx`:

```tsx
'use client'

import { createContext, useContext, useEffect, useState, useCallback } from 'react'
import { useRouter } from 'next/navigation'
import type { AuthContextValue, AuthUser } from '@/types/auth'

const AuthContext = createContext<AuthContextValue>({
  user: null,
  isLoading: true,
  logout: async () => {},
})

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<AuthUser | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const router = useRouter()

  useEffect(() => {
    fetch('/api/auth/me')
      .then(res => (res.ok ? res.json() : null))
      .then(data => setUser(data?.user ?? null))
      .finally(() => setIsLoading(false))
  }, [])

  const logout = useCallback(async () => {
    await fetch('/api/auth/logout', { method: 'POST' })
    setUser(null)
    router.push('/login')
  }, [router])

  return (
    <AuthContext.Provider value={{ user, isLoading, logout }}>
      {children}
    </AuthContext.Provider>
  )
}

export const useAuth = () => useContext(AuthContext)
```

2. Modify `src/app/(dashboard)/layout.tsx` — add `AuthProvider` import and wrap `LayoutWrapper` + `ScrollToTop`:

```tsx
// Add import:
import { AuthProvider } from '@/context/AuthContext'

// Wrap inside Providers return:
return (
  <Providers direction={direction}>
    <AuthProvider>
      <LayoutWrapper
        systemMode={systemMode}
        verticalLayout={
          <VerticalLayout navigation={<Navigation mode={mode} />} navbar={<Navbar />} footer={<VerticalFooter />}>
            {children}
          </VerticalLayout>
        }
        horizontalLayout={
          <HorizontalLayout header={<Header />} footer={<HorizontalFooter />}>
            {children}
          </HorizontalLayout>
        }
      />
      <ScrollToTop className='mui-fixed'>
        <Button variant='contained' className='is-10 bs-10 rounded-full p-0 min-is-0 flex items-center justify-center'>
          <i className='tabler-arrow-up' />
        </Button>
      </ScrollToTop>
    </AuthProvider>
  </Providers>
)
```

**Decision rationale**: `AuthProvider` must be a Client Component (uses hooks). In Next.js App Router, Server Components can render Client Components as children — so wrapping inside the `async` `Providers` call works. But scoping it to `(dashboard)/layout.tsx` is cleaner: auth state is only hydrated for authenticated pages, not for the login page or other public routes.

**Verification**: On `/dashboard/home`, open DevTools Console → temporarily add `console.log(user)` in `DashboardHome` (FE-008) → after login, see user object logged.

---

### FE-005 — Login Form (Real Auth + Error Display)

**Goal**: Replace the stub login with a working form: inline validation, API call, server error display, callbackUrl redirect, remove social/register sections.

**Inputs**:
- `src/views/Login.tsx` (existing)
- `src/app/(blank-layout-pages)/login/page.tsx` (existing — needs searchParams threading)
- `/api/auth/login` Route Handler (FE-002)

**Outputs**:
- `src/views/Login.tsx` (modified)
- `src/app/(blank-layout-pages)/login/page.tsx` (modified)

**Note on validation**: `react-hook-form`, `@hookform/resolvers`, and `valibot` are **not in `package.json`**. Use React `useState` for form state and inline validation — no new packages needed for a two-field form.

**Ordered steps**:

1. Modify `src/app/(blank-layout-pages)/login/page.tsx` to read and forward `callbackUrl`:

```tsx
import type { Metadata } from 'next'
import Login from '@views/Login'
import { getServerMode } from '@core/utils/serverHelpers'

export const metadata: Metadata = {
  title: 'Login',
  description: 'Login to your account',
}

type Props = {
  searchParams: Promise<{ callbackUrl?: string }>
}

const LoginPage = async ({ searchParams }: Props) => {
  const { callbackUrl } = await searchParams
  const mode = await getServerMode()

  return <Login mode={mode} callbackUrl={callbackUrl} />
}

export default LoginPage
```

2. Rewrite `src/views/Login.tsx`. Key changes from the existing file:
   - Accept new `callbackUrl?: string` prop
   - Replace single `useState(isPasswordShown)` with form state
   - Replace `onSubmit` stub with real fetch call
   - Add error `Alert`
   - Remove social buttons section and "Create an account" link

```tsx
'use client'

import { useState } from 'react'
import { useRouter } from 'next/navigation'
import { styled, useTheme } from '@mui/material/styles'
import useMediaQuery from '@mui/material/useMediaQuery'
import Typography from '@mui/material/Typography'
import IconButton from '@mui/material/IconButton'
import InputAdornment from '@mui/material/InputAdornment'
import Button from '@mui/material/Button'
import Alert from '@mui/material/Alert'
import classnames from 'classnames'
import type { SystemMode } from '@core/types'
import Link from '@components/Link'
import Logo from '@components/layout/shared/Logo'
import CustomTextField from '@core/components/mui/TextField'
import themeConfig from '@configs/themeConfig'
import { useImageVariant } from '@core/hooks/useImageVariant'
import { useSettings } from '@core/hooks/useSettings'

// Keep existing styled components (LoginIllustration, MaskImg) unchanged.

type Props = {
  mode: SystemMode
  callbackUrl?: string
}

const LoginV2 = ({ mode, callbackUrl }: Props) => {
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [emailError, setEmailError] = useState('')
  const [passwordError, setPasswordError] = useState('')
  const [serverError, setServerError] = useState('')
  const [isPasswordShown, setIsPasswordShown] = useState(false)
  const [isSubmitting, setIsSubmitting] = useState(false)

  const router = useRouter()
  const { settings } = useSettings()
  const theme = useTheme()
  const hidden = useMediaQuery(theme.breakpoints.down('md'))
  const authBackground = useImageVariant(mode, lightImg, darkImg)
  const characterIllustration = useImageVariant(
    mode, lightIllustration, darkIllustration,
    borderedLightIllustration, borderedDarkIllustration
  )

  // Keep the same image vars (darkImg, lightImg, etc.) unchanged.

  const validate = (): boolean => {
    let valid = true
    setEmailError('')
    setPasswordError('')

    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim())) {
      setEmailError('Enter a valid email')
      valid = false
    }
    if (!password) {
      setPasswordError('Password is required')
      valid = false
    }

    return valid
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setServerError('')
    if (!validate()) return

    setIsSubmitting(true)
    try {
      const res = await fetch('/api/auth/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: email.trim(), password }),
      })
      const json = await res.json()

      if (!res.ok) {
        setServerError(json.message ?? 'Login failed. Please try again.')
        return
      }

      const destination = callbackUrl?.startsWith('/dashboard') ? callbackUrl : '/dashboard/home'
      router.push(destination)
    } catch {
      setServerError('Unable to reach the server. Please check your connection.')
    } finally {
      setIsSubmitting(false)
    }
  }

  return (
    <div className='flex bs-full justify-center'>
      {/* Left illustration panel — keep exactly as-is */}
      <div className={classnames('flex bs-full items-center justify-center flex-1 min-bs-[100dvh] relative p-6 max-md:hidden', { 'border-ie': settings.skin === 'bordered' })}>
        <LoginIllustration src={characterIllustration} alt='character-illustration' />
        {!hidden && <MaskImg alt='mask' src={authBackground} className={classnames({ 'scale-x-[-1]': theme.direction === 'rtl' })} />}
      </div>

      {/* Right form panel */}
      <div className='flex justify-center items-center bs-full bg-backgroundPaper !min-is-full p-6 md:!min-is-[unset] md:p-12 md:is-[480px]'>
        <Link className='absolute block-start-5 sm:block-start-[33px] inline-start-6 sm:inline-start-[38px]'>
          <Logo />
        </Link>
        <div className='flex flex-col gap-6 is-full sm:is-auto md:is-full sm:max-is-[400px] md:max-is-[unset] mbs-11 sm:mbs-14 md:mbs-0'>
          <div className='flex flex-col gap-1'>
            <Typography variant='h4'>{`Welcome to ${themeConfig.templateName}! 👋🏻`}</Typography>
            <Typography>Please sign-in to your account</Typography>
          </div>

          {serverError && (
            <Alert severity='error' onClose={() => setServerError('')}>
              {serverError}
            </Alert>
          )}

          <form noValidate autoComplete='off' onSubmit={handleSubmit} className='flex flex-col gap-5'>
            <CustomTextField
              autoFocus
              fullWidth
              label='Email'
              placeholder='Enter your email'
              type='email'
              value={email}
              onChange={e => setEmail(e.target.value)}
              error={!!emailError}
              helperText={emailError}
            />
            <CustomTextField
              fullWidth
              label='Password'
              placeholder='············'
              type={isPasswordShown ? 'text' : 'password'}
              value={password}
              onChange={e => setPassword(e.target.value)}
              error={!!passwordError}
              helperText={passwordError}
              slotProps={{
                input: {
                  endAdornment: (
                    <InputAdornment position='end'>
                      <IconButton edge='end' onClick={() => setIsPasswordShown(s => !s)} onMouseDown={e => e.preventDefault()}>
                        <i className={isPasswordShown ? 'tabler-eye-off' : 'tabler-eye'} />
                      </IconButton>
                    </InputAdornment>
                  ),
                },
              }}
            />
            <Button fullWidth variant='contained' type='submit' disabled={isSubmitting}>
              {isSubmitting ? 'Signing in…' : 'Login'}
            </Button>
          </form>
        </div>
      </div>
    </div>
  )
}

export default LoginV2
```

**Decision rationale**: `react-hook-form` is not in `package.json`. A two-field login form does not justify adding three packages (react-hook-form + @hookform/resolvers + valibot). React `useState` + inline validation is idiomatic and dependency-free. The `callbackUrl` guard (`startsWith('/dashboard')`) matches the same guard in middleware — prevents open-redirect attacks.

**Verification**:
- Submit with empty fields → inline error messages appear; no network request fired
- Submit with invalid email format → email error only
- Submit with wrong credentials → red Alert appears with "Invalid credentials"
- Submit valid credentials → redirected to `/dashboard/home`
- `/login?callbackUrl=/dashboard/about` → after login, lands on `/dashboard/about`
- `/login?callbackUrl=https://evil.com` → after login, falls back to `/dashboard/home` (guard applies)

---

### FE-006 — UserDropdown (Real User Data + Logout)

**Goal**: Show the authenticated user's name/email; wire logout button to the real API call.

**Inputs**: `src/components/layout/shared/UserDropdown.tsx` (existing), `useAuth` hook (FE-004)

**Outputs**: `src/components/layout/shared/UserDropdown.tsx` (modified)

**Ordered steps**:

1. Add import at the top of `UserDropdown.tsx`:

```tsx
import { useAuth } from '@/context/AuthContext'
```

2. In the component body, add after existing hooks:

```tsx
const { user, logout } = useAuth()
```

3. Replace the hardcoded `handleUserLogout` function:

```tsx
const handleUserLogout = async () => {
  await logout()
}
```

4. Replace hardcoded name/email in the dropdown header div:

```tsx
<div className='flex items-start flex-col'>
  <Typography className='font-medium' color='text.primary'>
    {user?.name ?? '…'}
  </Typography>
  <Typography variant='caption'>{user?.email ?? ''}</Typography>
</div>
```

5. Replace hardcoded `alt` on both `Avatar` components:

```tsx
<Avatar alt={user?.name ?? 'User'} src='/images/avatars/1.png' />
```

6. Remove the "My Profile", "Settings", "Pricing", "FAQ" `MenuItem` elements — these are Vuexy template stubs with no real routes yet. Keep only the Logout button. They can be restored when those feature routes are implemented.

**Decision rationale**: `useAuth` fetches user data on mount — no additional API call. Removing dead placeholder links prevents 404 navigation and keeps the dropdown clean. The `logout` function in `AuthContext` already handles the API call, cookie clear, and redirect.

**Verification**:
- Open user dropdown → real name and email from the logged-in account appear
- Click Logout → redirected to `/login`; open DevTools → Application → Cookies → `auth_token` is gone

---

### FE-007 — Role-Aware Vertical Menu

**Goal**: Show role-appropriate navigation items in the sidebar based on the authenticated user's role.

**Inputs**: `src/components/layout/vertical/VerticalMenu.tsx` (existing), `useAuth` (FE-004)

**Outputs**: `src/components/layout/vertical/VerticalMenu.tsx` (modified)

**Ordered steps**:

1. Add `useAuth` import at the top.

2. In the `VerticalMenu` component body, before the return, add:

```tsx
const { user } = useAuth()
const role = user?.role
```

3. Replace the existing `<Menu>` content with the role-conditional version:

```tsx
<Menu
  popoutMenuOffset={{ mainAxis: 23 }}
  menuItemStyles={menuItemStyles(verticalNavOptions, theme)}
  renderExpandIcon={({ open }) => <RenderExpandIcon open={open} transitionDuration={transitionDuration} />}
  renderExpandedMenuItemIcon={{ icon: <i className='tabler-circle text-xs' /> }}
  menuSectionStyles={menuSectionStyles(verticalNavOptions, theme)}
>
  {/* All roles */}
  <MenuItem href='/dashboard/home' icon={<i className='tabler-smart-home' />}>
    Dashboard
  </MenuItem>

  {/* Admin + Doctor */}
  {(role === 'admin' || role === 'doktor') && (
    <MenuItem href='/dashboard/patients' icon={<i className='tabler-users' />}>
      Patients
    </MenuItem>
  )}

  {/* Admin + Doctor */}
  {(role === 'admin' || role === 'doktor') && (
    <MenuItem href='/dashboard/visits' icon={<i className='tabler-stethoscope' />}>
      Visits
    </MenuItem>
  )}

  {/* Admin only */}
  {role === 'admin' && (
    <MenuItem href='/dashboard/users' icon={<i className='tabler-user-cog' />}>
      Users
    </MenuItem>
  )}
</Menu>
```

**Decision rationale**: Static role check is sufficient — no dynamic permissions API exists. The routes `/dashboard/patients`, `/dashboard/visits`, `/dashboard/users` are intentionally dead links at this stage. They will be created in Feature Groups 2 and 3. Clicking them navigates to a 404 until those features land.

**Verification**:
- Login as `admin` → all 4 items visible (Dashboard, Patients, Visits, Users)
- Login as `doktor` → 3 items visible (Dashboard, Patients, Visits); Users absent
- Login as `pacijent` → 1 item visible (Dashboard only)

---

### FE-008 — Dashboard Home View

**Goal**: Replace the "Home page!" stub with a proper welcome screen showing the user's profile summary.

**Inputs**: `src/app/(dashboard)/dashboard/home/page.tsx` (stub from FE-000), `useAuth` (FE-004)

**Outputs**:
- `src/views/home/index.tsx` (new)
- `src/app/(dashboard)/dashboard/home/page.tsx` (modified — import real view)

**Ordered steps**:

1. Update `src/app/(dashboard)/dashboard/home/page.tsx`:

```tsx
import DashboardHome from '@views/home'

export default function Page() {
  return <DashboardHome />
}
```

2. Create `src/views/home/index.tsx`:

```tsx
'use client'

import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Avatar from '@mui/material/Avatar'
import Typography from '@mui/material/Typography'
import Chip from '@mui/material/Chip'
import CircularProgress from '@mui/material/CircularProgress'
import { useAuth } from '@/context/AuthContext'

const ROLE_LABELS: Record<string, string> = {
  admin: 'Administrator',
  doktor: 'Doctor',
  pacijent: 'Patient',
}

const ROLE_COLORS: Record<string, 'primary' | 'secondary' | 'success'> = {
  admin: 'primary',
  doktor: 'secondary',
  pacijent: 'success',
}

export default function DashboardHome() {
  const { user, isLoading } = useAuth()

  if (isLoading) {
    return (
      <Box className='flex justify-center items-center' sx={{ minHeight: 200 }}>
        <CircularProgress />
      </Box>
    )
  }

  return (
    <Box className='flex flex-col gap-6'>
      <Typography variant='h4'>Welcome back, {user?.name ?? '…'}</Typography>

      <Card sx={{ maxWidth: 480 }}>
        <CardContent className='flex items-center gap-4'>
          <Avatar sx={{ width: 64, height: 64, fontSize: 28 }}>
            {user?.name?.charAt(0).toUpperCase() ?? '?'}
          </Avatar>
          <Box className='flex flex-col gap-1'>
            <Typography variant='h6'>{user?.name}</Typography>
            <Typography variant='body2' color='text.secondary'>{user?.email}</Typography>
            <Chip
              label={ROLE_LABELS[user?.role ?? ''] ?? user?.role}
              color={ROLE_COLORS[user?.role ?? ''] ?? 'default'}
              size='small'
              sx={{ width: 'fit-content', mt: 0.5 }}
            />
          </Box>
        </CardContent>
      </Card>

      <Typography variant='body2' color='text.secondary'>
        Use the sidebar to navigate to Patients, Visits, and other sections.
      </Typography>
    </Box>
  )
}
```

**Decision rationale**: `CircularProgress` (MUI) replaces "Loading…" text — consistent with Vuexy design language. `maxWidth: 480` on the card keeps it from stretching full-width on large screens. Avatar initial fallback `?` handles null user edge case. All components are from the existing MUI theme — no new imports.

**Verification**:
- After login, `http://localhost:3000/dashboard/home` renders: heading with real name, profile card with name/email/role chip
- Role chip is blue (admin), purple (doktor), green (pacijent)
- While loading (brief), a centered spinner is shown
- All three roles render without errors

---

## Complexity Tracking

No constitution violations. This feature is frontend-only with no shared artifact changes.

---

## Phase 2

Run `/speckit.tasks` to generate the full developer-ready `tasks.md` from this plan.
