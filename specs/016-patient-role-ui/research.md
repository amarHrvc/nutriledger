# Research: Patient Role UI Restrictions (016)

## Decision 1: Patient Redirect Strategy

**Decision**: Client-side `PatientRedirectGuard` component mounted inside the dashboard layout, using `useAuth()` to detect role and `/api/patients/me` to resolve the patient's record ID.

**Rationale**: `middleware.ts` only has access to the `auth_token` cookie — it cannot read the user's role without an async call, which is not supported in Next.js Edge middleware. The role is only available after `AuthContext` resolves. A client component mounted inside `<AuthProvider>` (already in `layout.tsx`) is the correct place for role-based routing logic.

**Alternatives considered**:
- Middleware-based redirect: Rejected — Edge runtime cannot fetch the auth/me endpoint to read role.
- Redirect in the login page `onSuccess` handler: Rejected — does not cover the direct-URL-navigation case (FR-002).
- Add `patient_id` to the auth/me response and use it directly: Considered, but `/api/patients/me` already exists and returns the full `PatientResource` including `id`. No backend change needed.

---

## Decision 2: Patient ID Resolution at Redirect Time

**Decision**: Call the existing `/api/patients/me` frontend proxy (`GET /api/patients/me`) to get the patient's own record ID.

**Rationale**: `PatientProfile.tsx` already uses this endpoint successfully. It calls the backend `patientsIndex` with `paginate: false` and returns `data[0]` — the patient's own record (backend policy ensures patients only see their own record). The `PatientResource.id` from this response is used as the redirect target.

**No-linked-record case**: If `/api/patients/me` returns `{ patient: null }`, the guard renders an inline error: "Your account is not linked to a patient record. Please contact your administrator." No redirect or crash.

**Alternatives considered**:
- Include `patient_id` in the `UserResource` (backend change): Rejected — unnecessary; the existing `/api/patients/me` proxy achieves the same without backend work.
- Redirect to `/dashboard/profile` as fallback: Rejected — spec requires an inline error (FR-009), not a silent redirect.

---

## Decision 3: Button Hiding Pattern

**Decision**: Conditional rendering via `useAuth()` hook with `user?.role === 'pacijent'` guard. Buttons are simply not rendered (not disabled) when viewer is a patient.

**Rationale**: This is the established project pattern (see `VerticalMenu.tsx`, `profile/index.tsx`). `useAuth()` is already imported in most view components or can be added with one line. No new abstraction is needed.

**Exact locations**:
| Button | File | Lines |
|--------|------|-------|
| Suspend | `src/views/patients/patient-left/PatientDetailsCard.tsx` | 280–289 |
| Add Visit (empty state) | `src/views/patients/patient-right/visits/index.tsx` | 117–125 |
| Add Visit (populated state) | `src/views/patients/patient-right/visits/index.tsx` | 132–135 |
| Generate Diet Plan | `src/views/patients/diet-plans/DietPlanSection.tsx` | 139 |

**Alternatives considered**:
- Disable buttons instead of hiding: Rejected — spec FR-008 explicitly requires "not rendered, not merely disabled".
- Role-based prop drilling from PatientDetail down: Rejected — `useAuth()` hook is simpler and avoids prop threading through `PatientDetail → PatientDetailsCard` and `PatientDetail → PatientRightTabs → VisitsTab / DietPlanSection`.

---

## Decision 4: Guard Component Placement

**Decision**: New `PatientRedirectGuard` client component wraps `{children}` inside `app/(dashboard)/layout.tsx`.

**Rationale**: The dashboard layout already wraps `AuthProvider`. The guard needs `useAuth()` to be available, so it must be a client component inside that provider. Placing it at layout level ensures every dashboard page is guarded, covering both post-login and direct URL navigation (FR-001 and FR-002).

**Guard behavior**:
1. While `isLoading` — render nothing (prevent flash of dashboard content)
2. If `user.role === 'pacijent'` — fetch `/api/patients/me`:
   - On success with patient: `router.replace('/dashboard/patients/{id}')`
   - On success with null patient: render inline error message
3. Otherwise — render `{children}` normally
