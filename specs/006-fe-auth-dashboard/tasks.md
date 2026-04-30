# Tasks: Dashboard with Login & Logout (Sanctum Token Auth)

**Input**: Design documents from `/specs/006-fe-auth-dashboard/`  
**Branch**: `006-fe-auth-dashboard` | **Generated**: 2026-04-29  
**Prerequisites**: plan.md ✅ | spec.md ✅ | research.md ✅ | data-model.md ✅ | contracts/ ✅

**Tests**: No automated test tasks — this is a frontend-only feature verified by manual browser testing per quickstart.md.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies on each other)
- **[Story]**: Which user story this task belongs to (US1–US5)
- Exact file paths included in all descriptions

---

## Phase 1: Setup (Route Restructuring)

**Purpose**: Move existing pages to the correct `/dashboard/*` URL depth before any auth work begins. Next.js route groups `(name)` are invisible in URLs — pages must sit inside a `dashboard/` subdirectory to get `/dashboard/home`, `/dashboard/about`, etc.

**⚠️ CRITICAL**: All other tasks depend on this restructuring. The middleware, menu hrefs, and redirects all use `/dashboard/*` paths.

- [ ] T001 Create `frontend/src/app/(dashboard)/dashboard/` directory and move `frontend/src/app/(dashboard)/home/page.tsx` → `frontend/src/app/(dashboard)/dashboard/home/page.tsx`
- [ ] T002 Move `frontend/src/app/(dashboard)/about/page.tsx` → `frontend/src/app/(dashboard)/dashboard/about/page.tsx`
- [ ] T003 Delete `frontend/src/app/(dashboard)/page/page.tsx` (PoC API page — replaced by real auth flow)
- [ ] T004 Delete now-empty `frontend/src/app/(dashboard)/home/`, `frontend/src/app/(dashboard)/about/`, `frontend/src/app/(dashboard)/page/` directories

**Checkpoint**: Run `cd frontend && pnpm dev` → navigate to `http://localhost:3000/dashboard/home` and confirm "Home page!" renders; navigate to `http://localhost:3000/home` and confirm 404.

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Auth types, BFF Route Handlers, and AuthContext — all user stories depend on these. No story can be implemented until this phase is complete.

**⚠️ CRITICAL**: This phase MUST be complete before ANY user story work begins.

- [ ] T005 Create auth types and mapper function in `frontend/src/types/auth.ts` (AuthUser, AuthContextValue, toAuthUser)
- [ ] T006 [P] Ensure `frontend/.env.local` contains `NEXT_PUBLIC_API_URL` and `INTERNAL_API_URL` entries
- [ ] T007 [P] Create login Route Handler in `frontend/src/app/api/auth/login/route.ts` (proxies to Laravel, sets httpOnly cookie)
- [ ] T008 [P] Create logout Route Handler in `frontend/src/app/api/auth/logout/route.ts` (clears cookie, best-effort Laravel call)
- [ ] T009 [P] Create me Route Handler in `frontend/src/app/api/auth/me/route.ts` (reads cookie, returns AuthUser or 401)
- [ ] T010 Create AuthContext and AuthProvider client component in `frontend/src/context/AuthContext.tsx`
- [ ] T011 Modify `frontend/src/app/(dashboard)/layout.tsx` to wrap LayoutWrapper + ScrollToTop with AuthProvider

**Checkpoint**: `curl -X POST http://localhost:3000/api/auth/login -d '{"email":"admin@nutribase.com","password":"password"}' -H 'Content-Type: application/json' -v` → HTTP 200 + `Set-Cookie: auth_token=...` header present.

---

## Phase 3: User Story 1 — User Logs In and Reaches Dashboard (Priority: P1) 🎯 MVP

**Goal**: Replace the stub login form with a real auth flow — inline validation, fetch to `/api/auth/login`, server error display, and redirect to dashboard (or preserved `callbackUrl`).

**Independent Test**: Open `/login`, submit valid credentials for each role → verify redirect to `/dashboard/home` and no console errors. Submit wrong credentials → verify error Alert. Submit empty form → verify inline field errors with no network request fired.

**User Stories Covered**: US1 (FR-001, FR-002, FR-003, FR-010, FR-011, FR-012)

- [ ] T012 [US1] Modify `frontend/src/app/(blank-layout-pages)/login/page.tsx` to read and forward `callbackUrl` from `searchParams` to the Login component
- [ ] T013 [US1] Rewrite `frontend/src/views/Login.tsx` with: useState form state, inline validation (email format + non-empty password), fetch to `/api/auth/login`, server error Alert, callbackUrl-aware redirect, remove social buttons and "Create an account" link

**Checkpoint**: Full login flow works end-to-end. Error messages appear for bad input. Redirect lands on `/dashboard/home`. `callbackUrl=/dashboard/about` in query string → post-login lands on `/dashboard/about`. `callbackUrl=https://evil.com` → falls back to `/dashboard/home`.

---

## Phase 4: User Stories 2 & 5 — Route Protection + Auth Redirect (Priority: P2 / P3)

**Goal**: Protect all `/dashboard/*` routes at the Edge before React renders. Redirect authenticated users away from `/login`.

> Both user stories are implemented in the same file (`frontend/src/middleware.ts`).

**Independent Test (US2)**: Clear cookies → navigate to `http://localhost:3000/dashboard/home` → verify redirect to `/login?callbackUrl=/dashboard/home` with no dashboard content shown.

**Independent Test (US5)**: While logged in, navigate to `http://localhost:3000/login` → verify immediate redirect to `/dashboard/home` without the login form appearing.

**User Stories Covered**: US2 (FR-004, FR-013), US5 (FR-009)

- [ ] T014 [US2] [US5] Create `frontend/src/middleware.ts` with: `/dashboard/*` protection (redirect to `/login?callbackUrl=<path>` if no `auth_token` cookie), redirect authenticated users from `/login` to `/dashboard/home` (or valid `callbackUrl`), matcher that excludes `_next`, `api`, `images`, `assets`, `favicon.ico`

**Checkpoint**: 
1. No cookie → `/dashboard/home` redirects to `/login?callbackUrl=/dashboard/home` ✅  
2. Logged in → `/login` redirects to `/dashboard/home` ✅  
3. `/api/auth/me` is reachable (not intercepted by middleware) ✅  
4. Press back after logout → `/dashboard/home` redirects to `/login` ✅

---

## Phase 5: User Story 3 — Dashboard Home with Role-Aware Navigation (Priority: P2)

**Goal**: Replace the "Home page!" stub with a profile summary card (name, email, role chip). Wire role-conditional sidebar menu items.

**Independent Test**: Log in as `admin` → see 4 menu items (Dashboard, Patients, Visits, Users) and profile card with "Administrator" chip. Log in as `doktor` → 3 items (no Users). Log in as `pacijent` → 1 item (Dashboard only) and "Patient" chip.

**User Stories Covered**: US3 (FR-005, FR-006)

- [ ] T015 [P] [US3] Create `frontend/src/views/home/index.tsx` — DashboardHome client component with: CircularProgress loading state, Avatar with name initial, name/email/role Chip (color-coded per role), "Welcome back" heading; use `useAuth` hook for user data
- [ ] T016 [P] [US3] Modify `frontend/src/components/layout/vertical/VerticalMenu.tsx` — add `useAuth` import, read `user.role`, render role-conditional MenuItem groups: Dashboard (all), Patients + Visits (admin + doktor), Users (admin only)
- [ ] T017 [US3] Modify `frontend/src/app/(dashboard)/dashboard/home/page.tsx` to import and render DashboardHome from `@views/home` (replaces `<h1>Home page!</h1>` stub)

**Checkpoint**: All three roles render the correct profile card and menu items. Spinner appears during the initial `/api/auth/me` fetch. No browser console errors.

---

## Phase 6: User Story 4 — User Logs Out (Priority: P3)

**Goal**: Show the real authenticated user's name/email in the dropdown. Wire the logout button to `AuthContext.logout()` which calls `/api/auth/logout`, clears the cookie, and redirects to `/login`.

**Independent Test**: Log in, open user dropdown → real name + email visible. Click Logout → redirected to `/login`. Open DevTools → Application → Cookies → `auth_token` gone. Press browser back → `/dashboard/home` redirects to `/login`.

**User Stories Covered**: US4 (FR-007, FR-008)

- [ ] T018 [US4] Modify `frontend/src/components/layout/shared/UserDropdown.tsx` — add `useAuth` import, replace hardcoded "John Doe"/"admin@vuexy.com" with `user?.name`/`user?.email`, replace `handleUserLogout` stub with `await logout()` from context, remove dead placeholder MenuItems (My Profile, Settings, Pricing, FAQ)

**Checkpoint**: Real user name/email visible in dropdown. Logout clears cookie and lands on `/login`. Back navigation blocked by middleware.

---

## Phase 7: Polish & Cross-Cutting Concerns

**Purpose**: Final validation across all user stories.

- [ ] T019 Run `pnpm build` in `frontend/` — confirm zero TypeScript errors across all modified files
- [ ] T020 Run full quickstart.md verification flow: login → dashboard → role menu items → logout → back button blocked — confirm all 6 steps pass for all three roles (admin, doktor, pacijent)

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies — start immediately
- **Foundational (Phase 2)**: Depends on Phase 1 — BLOCKS all user stories
- **US1 (Phase 3)**: Depends on Phase 2 (needs Route Handlers + env) — can start after Phase 2
- **US2+US5 (Phase 4)**: Depends on Phase 1 (URL structure). No code imports from Phase 2 — middleware only reads the `auth_token` cookie name (a string constant). Can begin alongside Phase 2; complete Phase 2 first in practice so the cookie name is agreed upon before testing
- **US3 (Phase 5)**: Depends on Phase 2 (needs AuthContext/useAuth) — can start after Phase 2
- **US4 (Phase 6)**: Depends on Phase 2 (needs useAuth + logout) — can start after Phase 2
- **Polish (Phase 7)**: Depends on all phases complete

### User Story Dependencies

- **US1 (P1)**: Start after Phase 2 — independent
- **US2 (P2) + US5 (P3)**: Start after Phase 1 — independent of US1
- **US3 (P2)**: Start after Phase 2 — independent of US1 and US2
- **US4 (P3)**: Start after Phase 2 — independent of all others

### Within Each Phase

- T006 is independent of T005 — env file has no code dependency; can run at any point in Phase 2
- T007, T008, T009 are parallel after T005 — different Route Handler files, all import types from T005
- T010 depends on T005 — imports `AuthUser` and `AuthContextValue` from `src/types/auth.ts`
- T011 depends on T010 — wires `AuthProvider` (from T010) into `(dashboard)/layout.tsx`
- T015, T016 are parallel — different component files, both depend on T010 for `useAuth`
- T017 depends on T015 — imports `DashboardHome` component created in T015

### Parallel Opportunities

```bash
# Phase 2 — T006 alongside T005; T007/T008/T009 all together after T005:
Task: "T006 Configure frontend/.env.local"      # alongside T005 (no dependency)
Task: "T007 Create login Route Handler"         # parallel with T008, T009 (after T005)
Task: "T008 Create logout Route Handler"        # parallel with T007, T009 (after T005)
Task: "T009 Create me Route Handler"            # parallel with T007, T008 (after T005)

# Phase 5 — T015 and T016 together; T017 after T015:
Task: "T015 Create DashboardHome view"          # parallel with T016
Task: "T016 Add role-aware VerticalMenu items"  # parallel with T015
# T017 runs after T015 completes
```

---

## Implementation Strategy

### MVP First (US1 only)

1. Complete Phase 1: Route restructuring
2. Complete Phase 2: Types + BFF handlers + AuthContext
3. Complete Phase 3: Login form (US1)
4. **STOP and VALIDATE**: Login works end-to-end for all three roles
5. Demo: submit valid credentials → redirected to `/dashboard/home` (stub page; route protection added in Phase 4, profile card and role nav in Phase 5)

### Incremental Delivery

1. Phase 1 + 2 → Foundation ready
2. Phase 3 → Login works (US1) → **MVP demo**
3. Phase 4 → Route protection (US2, US5)
4. Phase 5 → Dashboard home + role nav (US3)
5. Phase 6 → Logout (US4)
6. Phase 7 → Final validation

### Solo Developer Order (Recommended)

`→` = must complete before next  |  `+` = independent, any order

```
T001 → T002 → T003 → T004     (Phase 1: Setup — all sequential)
T005 + T006                    (Phase 2: Types + ENV — independent of each other)
T007 + T008 + T009             (Phase 2: Route Handlers — all parallel, after T005)
T010 → T011                    (Phase 2: AuthContext → wire into layout)
T012 → T013                    (Phase 3: login page.tsx → Login.tsx)
T014                           (Phase 4: Middleware)
T015 + T016                    (Phase 5: DashboardHome + VerticalMenu — parallel)
T017                           (Phase 5: home/page.tsx — after T015)
T018                           (Phase 6: UserDropdown)
T019 → T020                    (Phase 7: build check → quickstart verification)
```

---

## Task Summary

| Phase | Tasks | Story | Priority |
|-------|-------|-------|----------|
| 1 — Setup | T001–T004 | — | Blocking |
| 2 — Foundational | T005–T011 | — | Blocking |
| 3 — Login flow | T012–T013 | US1 | P1 🎯 MVP |
| 4 — Route protection | T014 | US2, US5 | P2 / P3 |
| 5 — Dashboard + nav | T015–T017 | US3 | P2 |
| 6 — Logout | T018 | US4 | P3 |
| 7 — Polish | T019–T020 | — | Final |

**Total**: 20 tasks across 7 phases  
**Parallel opportunities**: T007/T008/T009 (Phase 2 — all three after T005), T015/T016 (Phase 5), T006 (alongside T005)  
**MVP scope**: Phases 1–3 (T001–T013) — delivers a working login → dashboard flow
