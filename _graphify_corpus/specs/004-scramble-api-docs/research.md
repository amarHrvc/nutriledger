# Research: Setup Scramble API Documentation

**Feature**: 004-scramble-api-docs
**Date**: 2026-04-07

---

## Decision 1: Scramble vs Scribe

**Decision**: Scramble (`dedoc/scramble`)

**Rationale**: Zero-annotation inference from Form Requests, Eloquent Resources, and route definitions. The project already follows exactly the structure Scramble infers from — typed Form Requests with `rules()`, Resources with `toArray()`, and named `apiResource` routes. Scramble serves a live spec at runtime, which feeds directly into Orval without a build step. Scribe requires a static generation command each time the API changes and would duplicate effort already covered by Pint/Larastan gates.

**Alternatives considered**: Scribe (`knuckleswtf/scribe`) — generates Postman collections and HTML docs but requires re-running manually after every change. Not needed here because our only consumer is Orval and the interactive UI.

---

## Decision 2: Docs Endpoint Access Control (dev-only vs always-on)

**Decision**: Restrict Scramble routes to non-production environments via `AppServiceProvider::boot()`.

**Rationale**: The spec and interactive UI expose the full API surface including request/response shapes for every endpoint. This is a development and integration tool, not a public-facing resource. Restricting to `App::environment(['local', 'testing'])` prevents the spec from being served in production without adding extra middleware or `.env` flags.

**Implementation**: Wrap `Scramble::routes()` registration in an environment check. Laravel's `App::isProduction()` or `App::environment()` is the idiomatic approach.

**Alternatives considered**: Auth-guarding the docs routes — adds friction during development; overkill for a docs endpoint that doesn't expose actual data.

---

## Decision 3: Excluding Test Routes from the Spec

**Decision**: Use Scramble's route exclusion via a closure on `Scramble::routes()` or the `exclude` option in `scramble.php` config filtering by route name prefix.

**Rationale**: Routes under `api/test/*` (e.g., `test/admin-only`, `test/admin-doktor-only`) are test fixtures, not real API surface. Including them would pollute the spec with noise. Scramble provides a `routes` callback that receives each route and returns a bool — return false for routes whose name starts with `test`.

**Alternatives considered**: Manually annotating test routes with `@ignoreRoute` — requires touching every test route file; annotation approach contradicts FR-010 (zero annotations).

---

## Decision 4: Orval Client Mode for Next.js 16

**Decision**: Orval with `fetch` client (not `react-query` or `axios`).

**Rationale**: The frontend is Next.js 16 (App Router) with an existing `src/api/client.ts` using native `fetch`. Orval's `fetch` client mode generates typed wrapper functions and TypeScript interfaces without introducing Axios or TanStack Query as dependencies. This matches the existing pattern: the generated functions follow the same fetch-based contract as the hand-written `client.ts`. TanStack Query would require `'use client'` boundaries on every page that consumes data — unsuitable for App Router server components.

**Alternatives considered**:
- `react-query` client: Works in Next.js but requires wrapping all consuming components in `'use client'` and adding a QueryClientProvider. Over-engineered for current scope.
- `axios` client: Adds a dependency not currently in the project. Fetch is already available natively.

---

## Decision 5: Package Manager for Frontend

**Decision**: `pnpm` (already in use — `pnpm-lock.yaml` present).

**Rationale**: Project uses pnpm. All Orval install and generate commands use `pnpm` equivalents.

---

## Decision 6: Orval Config File Location

**Decision**: `frontend/orval.config.ts` at the frontend root.

**Rationale**: Collocated with `package.json` so `pnpm run api:generate` resolves relative paths correctly. Orval reads `orval.config.ts` from the directory where it is invoked.

---

## Resolved Clarifications

All NEEDS CLARIFICATION items from spec resolved:

- **Docs endpoint in production**: Restricted to `local` and `testing` environments only (Decision 2).
- **Routes without Form Requests**: Scramble emits an empty/minimal request body schema. Test routes excluded entirely (Decision 3). `ping` endpoint is unauthenticated and will appear correctly with no body schema.
- **Frontend code gen tool offline**: Orval will exit with a network error if the backend is unreachable. Expected behavior — documented in quickstart.md.
