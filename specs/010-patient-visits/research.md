# Research: Patient Visits Feature (010)

## Decision 1 — Date/Time Input Component

**Decision**: Use `<TextField type='datetime-local'>` (native HTML5 input wrapped in MUI TextField) — no new package.

**Rationale**: `@mui/x-date-pickers` is not installed. The project already uses `<TextField type='date' InputLabelProps={{ shrink: true }}>` for Date of Birth in `PatientForm.tsx`. Extending this to `type='datetime-local'` follows the exact same pattern, adds no dependency, and works in all modern browsers. The submitted value (`2026-05-15T14:30`) is split client-side into `date=2026-05-15` and `time=14:30` before POST.

**Alternatives considered**:
- `@mui/x-date-pickers` + `dayjs` — correct for a full calendar UI, but adds two new packages; no approval sought; overkill for this feature.
- Two separate `<TextField type='date'>` + `<TextField type='time'>` — works but degrades UX vs a single combined input.

---

## Decision 2 — Visit Data Model: `date` + `time` vs `scheduled_at`

**Decision**: Keep existing `date` (DATE) column, add new `time` (TIME, nullable for migration safety) column to `visits`.

**Rationale**: Renaming `date` → `scheduled_at` and changing type to DATETIME would require touching all existing Pest tests, `VisitResource`, both FormRequests, `VisitController`, and the existing migration. The two-column approach is a non-breaking additive change. The `VisitResource` concatenates them for consumers if needed, and the frontend splits a `datetime-local` value on submit.

**Alternatives considered**:
- `scheduled_at` DATETIME single column — cleaner long-term but breaks all existing visit tests and code. Deferred to a future refactor.

---

## Decision 3 — Global Visits Listing Endpoint

**Decision**: Add `GET /api/visits` route calling new `VisitController::globalIndex()`. Doctor → own visits only (`where('doctor_id', auth()->id())`). Admin → all visits.

**Rationale**: The Visits page must show a cross-patient list scoped by role. The existing per-patient `GET /api/patients/{patient}/visits` is the wrong shape — it requires knowing patient IDs upfront. A global endpoint with role-scoped filtering is the clean REST solution.

**Alternatives considered**:
- Frontend fetches per-patient then merges — rejected: doctor doesn't have a list of their patient IDs without another round-trip.
- Reuse `index()` with a flag — rejected: changes an existing contract that has tests.

---

## Decision 4 — 1-Day Edit Lock Enforcement Layer

**Decision**: Enforce in `VisitPolicy::update()` by checking `$visit->date->toDateString() >= now()->subDay()->toDateString()`.

**Rationale**: Policy is the authoritative layer for all authorization rules per Principle II of the constitution. A policy check is tested explicitly, blocks API-level bypass, and is the correct place for "can this user perform this action on this resource" logic. The calendar-day calculation (`>= yesterday's date`) matches the spec: visits from yesterday are still editable; the day before is locked.

**Alternatives considered**:
- Frontend-only lock — rejected: insecure, bypassable with a direct API call.
- `UpdateVisitRequest::authorize()` — possible, but policy is the right layer; `authorize()` already delegates to the policy.
- Rolling 24-hour window — rejected per spec: calendar day boundary is specified.

---

## Decision 5 — Admin Doctor Assignment on Create

**Decision**: Add optional `doctor_id` to `StoreVisitRequest` (validates it references a user with `role=doktor`). In `VisitController::store()`: if admin and `doctor_id` provided → use it; otherwise → `auth()->id()`.

**Rationale**: Minimal delta on the existing `store()` path. Doctors always create visits for themselves (immutable). Admin gets the extra field without a separate endpoint.

**Alternatives considered**:
- Separate `POST /api/admin/visits` endpoint — over-engineering; the only difference is `doctor_id` selectability.

---

## Decision 6 — `isEditable` Flag in VisitResource

**Decision**: Compute `isEditable: bool` in `VisitResource` and include it in `attributes`.

**Rationale**: The frontend edit button visibility is driven by `attributes.isEditable`. This keeps the business rule (calendar day check) in one place on the backend and prevents the frontend from duplicating it. If the rule changes, only the resource changes.

---

## Decision 7 — BFF Route Architecture for Visits

**Decision**: Two BFF routes:
- `GET /api/visits/route.ts` — proxies `GET /api/visits` (new global endpoint)
- `frontend/src/app/api/patients/[id]/visits/route.ts` — rewrite: GET proxies patient visits, POST proxies create visit

Both use the `customFetchMutator` pattern (Bearer token from `auth_token` cookie) consistent with all other BFF routes.

**Rationale**: The existing `visits/route.ts` uses raw `fetch` with Cookie forwarding — this is the bug from `nutri-ledger-5l2`. The visits feature build is the right time to fix this and implement the full BFF correctly.

---

## Existing Assets (no changes needed)

- `VisitPolicy` — already has correct `create`, `view`, `viewAny`, `delete` logic; only `update` needs the 1-day check added.
- `VisitController` — `show`, `update`, `destroy` methods are complete; `index` is complete for per-patient use; only `store` and `globalIndex` need changes.
- `routes/api.php` — existing visit routes are correct; only `GET /api/visits` needs adding.
- `VerticalMenu.tsx` — Visits nav item already exists for admin+doctor roles ✓.
- `visits/page.tsx` — Next.js page shell already exists ✓.
- `PatientRightTabs` — Visits tab already wired to `VisitsTab` component ✓.
