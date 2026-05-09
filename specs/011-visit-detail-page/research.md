# Research: Visit Detail Page

**Phase**: 0 — Outline & Research  
**Branch**: `011-visit-detail-page`  
**Date**: 2026-05-09

## Decisions

---

### Decision 1: URL structure for the visit detail page

**Decision**: `/dashboard/visits/[id]?patient=[patientId]`

**Rationale**: The visits list page lives at `/dashboard/visits` (flat route, consistent with `/dashboard/patients` and `/dashboard/users`). The detail page naturally lives at `/dashboard/visits/[id]`. The backend `show` endpoint requires both a `patientId` and a `visitId`; the `patientId` is always available in `VisitResource.attributes.patientId` when building the link from any list view, so it can be passed as a URL search parameter (`?patient=X`). The Next.js page reads `id` from `useParams` and `patientId` from `useSearchParams`, then calls `patientsVisitsShow(patientId, visitId)` from the generated client.

**Alternatives considered**:
- `/dashboard/patients/[patientId]/visits/[visitId]` — more RESTful but inconsistent with the flat entity structure used for patients and users; adds a nested route where the pattern doesn't exist yet.
- `/dashboard/visits/[visitId]/[patientId]` — double segment route; less idiomatic for Next.js App Router; unclear which is primary key.

---

### Decision 2: Page layout

**Decision**: Single-column layout with a top-level `VisitDetail` component containing a `VisitDetailCard` card (full width) for visit metadata and a separate notes section below it.

**Rationale**: A visit is a flat scalar record (date, time, doctor, patient, notes). The patient detail page uses a 2-column grid (left sidebar + right tabs) because a patient has multiple data domains (medical history, visits, overview). A visit has one domain; a 2-column split would leave the right column nearly empty. A single-column card layout matches the information density of the data. Mirrors the MUI `Card` + `CardContent` pattern already used in the patient profile visits tab.

**Alternatives considered**:
- 2-column grid (left card + right notes): overkill for the data; left column would be sparse.
- Full-page form layout: wrong mental model — this is a read view with conditional actions, not a form.

---

### Decision 3: Edit and delete actions on the detail page

**Decision**: Edit opens the existing `VisitEditForm` in a `Dialog` (same as the list views). Delete calls `patientsVisitsDestroy` then navigates to `/dashboard/visits`. Visibility is role-gated by `useAuth()` + `visit.attributes.isEditable`.

**Rationale**: This directly reuses existing `VisitEditForm` without any modifications. The `isEditable` flag is already computed server-side and returned in `VisitResource.attributes` — the FE trusts this flag rather than reimplementing the "1-day edit window" rule. Delete is admin-only per `VisitPolicy`; the FE hides the button for non-admin roles but the server enforces the authorization regardless.

**Alternatives considered**:
- Inline edit (expand a form in-page): inconsistent with how edit works everywhere else in the codebase (dialogs).
- Separate edit route: unnecessary complexity; edit is already handled by `VisitEditForm`.

---

### Decision 4: Back navigation

**Decision**: Static "Back to Visits" link using Next.js `<Link href="/dashboard/visits">` always rendered at the top of the detail page.

**Rationale**: Users may arrive at the detail page via direct URL (bookmarked, shared link) or via browser reload — `router.back()` would fail in these cases. A static link to the visits list is always reliable. The patient name on the detail card will also link to `/dashboard/patients/[patientId]`, giving users a second navigation path if they arrived from the patient profile tab.

**Alternatives considered**:
- `router.back()`: unreliable when there is no history (direct navigation, bookmarks).
- Breadcrumb component: adds complexity; the existing pages don't use breadcrumbs.

---

### Decision 5: No backend changes required

**Decision**: This feature is frontend-only. The existing `GET /api/patients/{patient}/visits/{visit}` endpoint and `patientsVisitsShow` generated client function are sufficient.

**Rationale**: The backend already implements the `show` action in `VisitController`, enforces `VisitPolicy::view()`, and returns a `VisitResource` with all required attributes (`date`, `time`, `doctorName`, `patientName`, `patientId`, `notes`, `isEditable`). No new backend tasks are needed.

**Alternatives considered**:
- Adding a global `/api/visits/{id}` endpoint to avoid requiring `patientId` in the URL: unnecessary work; the nested endpoint already exists and the patientId is always available from the list response.

---

## Summary of Resolved Unknowns

| Unknown | Resolution |
|---|---|
| How to pass patientId to detail page | URL search param `?patient=[id]` |
| Which layout pattern to follow | Single-column card (not 2-column grid) |
| How to implement edit on detail page | Reuse existing `VisitEditForm` dialog |
| How to implement delete | `patientsVisitsDestroy` → navigate to `/dashboard/visits` |
| Backend work required | None — all API endpoints already exist |
