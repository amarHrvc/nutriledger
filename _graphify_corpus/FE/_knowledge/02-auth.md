# Auth System

## Overview

Uses **next-auth v4** with a server-side `AuthGuard` HOC. Auth is enforced at the layout level, not per-page.

## Flow

```
Request → [lang]/layout.tsx (TranslationWrapper)
         → (dashboard)/(private)/layout.tsx (Providers + AuthGuard)
              AuthGuard checks getServerSession()
              ↓ no session → <AuthRedirect> (redirects to /login)
              ↓ session ok → renders children with full layout
```

## Key Files

| File | Purpose |
|------|---------|
| `src/hocs/AuthGuard.tsx` | Server component, checks session, redirects if none |
| `src/components/AuthRedirect.tsx` | Redirect component (to login with return URL) |
| `src/libs/auth.ts` | NextAuth options (providers, adapter, callbacks) |
| `src/app/api/auth/[...nextauth]/route.ts` | NextAuth handler |
| `src/app/api/login/route.ts` | Custom login API (used by Credentials provider) |
| `src/app/api/login/users.ts` | Hardcoded demo users (replace with real DB lookup) |
| `src/prisma/schema.prisma` | Prisma schema (User, Session, Account tables) |

## Auth Providers

1. **CredentialsProvider** — posts to `/api/login`, expects `{ email, password }`
2. **GoogleProvider** — OAuth via `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` env vars

## Session

- Sessions persisted via **PrismaAdapter** (real DB)
- Session available server-side: `getServerSession()` from `next-auth`
- Session available client-side: `useSession()` from `next-auth/react`

## Guest-Only Routes

Routes under `(guest-only)/` redirect to dashboard if user is already authenticated:
- `/login`
- `/register`
- `/forgot-password`

## Making a Route Public (no auth)

Two options:
1. Put it under `(blank-layout-pages)/` but NOT under `(guest-only)/` — no auth check, no redirect
2. Put it under `app/front-pages/` — completely separate layout, no auth at all

## Making a Route Private

Simply place the page under `[lang]/(dashboard)/(private)/` — the `(private)` group layout applies `AuthGuard` automatically.

## Environment Variables (.env)

```env
NEXTAUTH_SECRET=your-secret
NEXTAUTH_URL=http://localhost:3000
API_URL=http://localhost:3000/api
DATABASE_URL=file:./dev.db          # SQLite default
GOOGLE_CLIENT_ID=...                # optional
GOOGLE_CLIENT_SECRET=...            # optional
```

## Replacing Demo Auth with Real Backend

Edit `src/app/api/login/route.ts` — replace the demo user lookup in `src/app/api/login/users.ts` with a real DB query. The Credentials provider in `src/libs/auth.ts` calls this endpoint.
