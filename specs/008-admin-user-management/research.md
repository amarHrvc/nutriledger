# Research: Admin User Management

**Branch**: `008-admin-user-management` | **Date**: 2026-05-01

## Decision 1: BFF vs Direct Client API Calls

**Decision**: BFF (Next.js route handlers) proxy all requests to Laravel. The client only calls `/api/users/*` (same origin).

**Rationale**: The `auth_token` Sanctum cookie is `httpOnly` — unreadable from JavaScript. The BFF reads it server-side, extracts the bearer token (`split('|')[1]`), and injects it as `Authorization: Bearer <token>`. This is the established pattern for every other API call in the project (profile, patients, dashboard stats).

**Alternatives considered**: Direct client-side fetch to `localhost:8000` — rejected because the token is inaccessible from the browser and CORS would need to be opened.

---

## Decision 2: Orval-Generated Clients in BFF Handlers

**Decision**: BFF route handlers call Orval-generated typed functions (`usersIndex`, `usersShow`, `usersStore`, `usersUpdate`, `usersDestroy`, `usersRestore`, `usersForceDelete`) from `src/api/generated/user/user.ts`. Auth headers are injected via the `options` argument.

**Rationale**: Consistent with the profile/patients/dashboard-stats BFF routes already in the codebase. Provides type-safety from the OpenAPI spec and a single source of truth for endpoint URLs.

**Note on `UsersIndex200` schema**: The generated `UsersIndex200.data` and `.meta` fields are typed as `string` (OpenAPI limitation) but are objects/arrays at runtime. Cast with `as any` to access pagination properties — same pattern as `PatientsIndex200`.

**Alternatives considered**: Raw `fetch` calls inside BFF handlers — rejected to maintain Orval as the single source of endpoint URL truth.

---

## Decision 3: View State Machine (single page, no router navigation)

**Decision**: `views/users/index.tsx` owns a `view` state (`'list' | 'detail' | 'create' | 'edit'`) and conditionally renders `UserList`, `UserDetail`, or `UserForm`. Navigation between views is local state — no URL routing for detail/form panels.

**Rationale**: The project currently has no client-side router (Next.js App Router is file-based, but no `Link` navigation between user records). The profile page uses the same single-component, state-driven approach. Adding URL-based routing (`/dashboard/users/[id]`) would require creating dynamic route files and is out of scope for MVP.

**Alternatives considered**: Dynamic route at `/dashboard/users/[id]/page.tsx` — possible in the future but adds complexity and requires URL synchronisation. Deferred to a later iteration.

---

## Decision 4: Client-Side Search

**Decision**: The search input filters the array of users already fetched for the current page — no server-side search endpoint.

**Rationale**: The spec assumption explicitly states this is sufficient for MVP. The user list for a clinical nutrition platform is small (tens to hundreds of users). Filtering on the client avoids an extra BFF call on every keystroke.

**Alternatives considered**: Server-side search via a `?search=` query param forwarded to Laravel — Laravel's `where('name', 'like', ...)` would support it, but the backend endpoint doesn't expose a search param today and adding one is out of scope.

---

## Decision 5: Confirmation Pattern (MUI Dialog)

**Decision**: A shared `ConfirmDialog` component wrapping MUI `Dialog` handles all destructive actions (deactivate, restore, force-delete). It receives `open`, `title`, `message`, `onConfirm`, `onCancel` props.

**Rationale**: Reusable across three action types. MUI Dialog is already a dependency. Matches the design language of the rest of the application.

**Alternatives considered**: Inline `window.confirm()` — rejected: non-dismissible, breaks design consistency, not testable.

---

## Decision 6: Pagination Forwarding

**Decision**: The BFF `GET /api/users` route forwards the `?page` query param from the frontend request to the backend by calling `fetch(getUsersIndexUrl() + '?page=' + page, { headers })` — using Orval's URL helper but calling fetch directly to append the param.

**Rationale**: `usersIndex(options?)` doesn't accept a `page` argument (Orval generates a flat `RequestInit` options parameter, not query params). Using the Orval URL helper preserves the single source of truth for the base URL while still supporting pagination.

**Alternatives considered**: Ignoring pagination (load all users) — rejected because backend paginates by default (15/page) and large lists would be silently truncated.

---

## Resolved Clarifications

All spec items were concrete — no NEEDS CLARIFICATION markers existed. No additional clarifications required.
