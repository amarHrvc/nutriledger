# Research: Role-Based Profile Page

**Branch**: `007-role-based-profile` | **Date**: 2026-05-01

---

## Decision 1: FR-011 — Patient Medical Field Editability

**Decision**: Patients can self-edit contact fields (phone, address, city, postal code, emergency contact name/phone) and health-summary fields they own (blood type, allergies). `medical_notes` is doctor-written during visits — read-only for patients on this page.

**Rationale**: Patients know their own allergies and blood type; these do not require clinical authority. Medical notes, however, are authored by doctors during visits and carry clinical meaning — patient self-editing would compromise data integrity. This aligns with FR-009 (personal info editable) while scoping clinical authority to doctors.

**Alternatives considered**:
- All fields read-only for patients → unhelpful for patients managing their own health information
- All fields editable by patients → clinical notes could be corrupted

---

## Decision 2: Client-Side Data Fetching Strategy

**Decision**: Use the Next.js BFF pattern for all profile data fetching. Client components call `/api/*` Next.js route handlers; those handlers read the `httpOnly` `auth_token` cookie and forward requests to Laravel. This mirrors the existing `/api/auth/me` endpoint.

**Rationale**: The `auth_token` cookie is `httpOnly` — browser JS cannot read it. The generated Orval clients call Laravel directly, which means they cannot include the bearer token from client components. Creating thin BFF route handlers is the consistent, secure pattern already established in this codebase.

**Alternatives considered**:
- Store a non-httpOnly token copy in localStorage: security regression, exposes token to XSS
- Use React Server Components: valid, but adds complexity for components that also need client interactivity (edit forms). BFF is simpler for this mixed case.
- Use generated Orval clients directly via a client-side token: not possible since the token is httpOnly

---

## Decision 3: Profile Route Location

**Decision**: `/dashboard/profile` — inside `(dashboard)` group, under `dashboard/` URL segment.

**Rationale**: Consistent with existing `/dashboard/home` and `/dashboard/about`. The `(dashboard)` group provides the sidebar layout and the middleware already protects all `/dashboard/*` paths. No new middleware rules needed.

**Alternatives considered**:
- `/profile` at top level of `(dashboard)`: would be a cleaner URL, but inconsistent with current URL convention where all app pages use `/dashboard/` prefix

---

## Decision 4: Implementation Tier Strategy

**Decision**: Three progressive tiers in the tutorial:

| Tier | Name | Time | Data Source | Output |
|------|------|------|-------------|--------|
| 1 | Fast Path | ~30 min | `useAuth()` only | All 3 profiles render with name/email/role, role-specific placeholder sections |
| 2 | Medium Path | ~2-3 hours | BFF endpoints per role | Real patient medical data, doctor activity, admin user counts |
| 3 | Full Path | ~4-6 hours | Same BFFs + PATCH calls | Inline editing for personal info; patient medical field editing |

**Rationale**: Tier 1 delivers immediately navigable, role-correct profiles with zero new API endpoints — useful as a visible milestone. Tier 2 adds the clinical value. Tier 3 adds editability. Each tier is independently shippable.

---

## Decision 5: Shared Component Architecture

**Decision**: Three shared sub-components extracted from the profile views:
- `ProfileHeader` — avatar initials, name, role badge (identical for all 3 roles)
- `SectionCard` — MUI Card wrapper with a titled section (used for every data group)
- `InfoRow` — label + value row with "Not provided" fallback (used for every data field)

**Rationale**: All 3 profiles share the same header. Data rows and card sections repeat across all views. Extracting them avoids copy-paste drift and keeps each role view focused on data, not layout.

**Alternatives considered**:
- One monolithic ProfilePage component with conditionals: harder to read and maintain
- Completely separate standalone pages per role with no shared code: maximum duplication

---

## Decision 6: BFF Endpoints to Create (Tier 2)

| Endpoint | Method | Laravel target | Used by |
|----------|--------|----------------|---------|
| `/api/patients/me` | GET | `GET /api/patients` (first result) | Patient profile |
| `/api/visits/my-summary` | GET | `GET /api/patients/{id}/visits?per_page=3` | Doctor profile (approximation) |
| `/api/users/counts` | GET | `GET /api/users` (meta.total) | Admin profile |

> **Note on doctor activity**: There is no "visits where I am the doctor" backend endpoint in the current API. For Tier 2, the doctor profile shows total patients in the system (from `/api/patients` meta.total) as a proxy. A proper "my consultations" count requires a new backend endpoint — documented as a future task.

---

## Existing Codebase Patterns (confirmed)

| Pattern | Source |
|---------|--------|
| Page → View separation | `app/(dashboard)/dashboard/home/page.tsx` → `views/home/index.tsx` |
| Auth data from context | `useAuth()` in `views/home/index.tsx`, `components/layout/shared/UserDropdown.tsx` |
| Role-conditional rendering | `VerticalMenu.tsx` — `role === 'admin'`, `role === 'doktor'` checks |
| MUI components in use | `Avatar`, `Box`, `Card`, `CardContent`, `Chip`, `Typography`, `CircularProgress` |
| BFF route handler | `app/api/auth/me/route.ts` — reads cookie, calls Laravel, returns JSON |
| Role label mapping | `ROLE_LABELS` in `views/home/index.tsx` |
