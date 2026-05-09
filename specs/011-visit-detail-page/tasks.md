# Tasks: Visit Detail Page

**Input**: Design documents from `/specs/011-visit-detail-page/`  
**Branch**: `011-visit-detail-page`  
**Prerequisites**: plan.md ✅ | spec.md ✅ | research.md ✅ | data-model.md ✅ | quickstart.md ✅

**Scope**: Frontend-only. Backend API (`GET /api/patients/{patient}/visits/{visit}`) is already implemented. No PHP files are created or modified.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no shared state)
- **[Story]**: Which user story this task belongs to (US1 / US2 / US3)

---

## Phase 1: Setup

No new packages or configuration needed. The feature reuses existing MUI, Next.js App Router, and the generated API client already installed in the project. **Skip directly to Phase 2.**

---

## Phase 2: Foundational (Blocking Prerequisite)

**Purpose**: Create the Next.js App Router route entry point that all user stories share. Nothing can be tested until this route exists.

**⚠️ CRITICAL**: Complete T001 before starting any Phase 3+ task.

---

- [ ] T001 Create route file `frontend/src/app/(dashboard)/dashboard/visits/[id]/page.tsx`

  **Goal**: Provide the URL entry point `/dashboard/visits/[id]?patient=[patientId]` for the visit detail page.

  **Inputs**:
  - Pattern: `frontend/src/app/(dashboard)/dashboard/visits/page.tsx` (thin server wrapper)
  - Pattern: `frontend/src/app/(dashboard)/dashboard/patients/[id]/page.tsx` (dynamic route with client state)

  **Outputs**: `frontend/src/app/(dashboard)/dashboard/visits/[id]/page.tsx` (new file)

  **Steps**:
  1. Create directory `frontend/src/app/(dashboard)/dashboard/visits/[id]/`.
  2. Create `page.tsx` as a client component — it reads `id` from `useParams` and `patientId` from `useSearchParams`, then renders `<VisitDetail>` (which will be created in T002):

     ```tsx
     'use client'

     import { useParams, useSearchParams } from 'next/navigation'
     import Box from '@mui/material/Box'
     import VisitDetail from '@views/visits/VisitDetail'

     export default function Page() {
       const { id } = useParams<{ id: string }>()
       const searchParams = useSearchParams()
       const patientId = searchParams.get('patient') ?? ''

       return (
         <Box sx={{ p: 3 }}>
           <VisitDetail visitId={id} patientId={patientId} />
         </Box>
       )
     }
     ```

  3. `VisitDetail` does not exist yet — TypeScript will error until T002 is complete. That is expected.

  **Decision rationale**: Keeping the route page as a thin wrapper (IDs only, no fetching) matches the pattern in `visits/page.tsx` and keeps all visit-domain logic inside `views/visits/`. The `patientId` is passed as a URL search param because the show API requires both IDs, and the patientId is always available in `VisitResource.attributes.patientId` when linking from any list view (see research.md Decision 1).

  **Verification**: After T002 is complete — `npm run dev`, navigate to `/dashboard/visits/1?patient=1`, confirm page renders without a 404 route error.

---

**Checkpoint**: Route is registered. Phase 3 can begin.

---

## Phase 3: User Story 1 — View Full Visit Details (Priority: P1) 🎯 MVP

**Goal**: Doctor or admin clicks a visit in the global list → sees the full detail page with all visit fields, back navigation, and appropriate empty/error states.

**Independent Test**: Log in as a doctor. Go to `/dashboard/visits`. Click "View" on any row. Confirm the detail page shows date, time, doctor name, patient name, and notes (or "No notes recorded" if absent). Confirm "← Back to Visits" link works. Confirm a 404 URL shows an error message. No edit/delete buttons appear yet (those are US3).

---

- [ ] T002 [US1] Create `frontend/src/views/visits/VisitDetail.tsx`

  **Goal**: Render a full visit detail view: fetch the visit using `patientsVisitsShow`, display all fields in a Card, show a back link, handle loading/error/empty-notes states.

  **Inputs**:
  - `frontend/src/api/generated/visit/visit.ts` — `patientsVisitsShow(patient: number, visit: number)`
  - `frontend/src/api/generated/nutriBaseAPI.schemas.ts` — `VisitResource`, `PatientsVisitsShow200`
  - Pattern: `frontend/src/views/patients/patient-right/visits/index.tsx` — Card + CardContent + Table layout
  - Pattern: `frontend/src/views/visits/index.tsx` — error/loading states using `Alert`, `CircularProgress`
  - `frontend/src/context/AuthContext.tsx` — `useAuth()` (needed in T005 for role checks; import now)

  **Outputs**: `frontend/src/views/visits/VisitDetail.tsx` (new file)

  **Steps**:
  1. Check `nutriBaseAPI.schemas.ts` to confirm the shape of `PatientsVisitsShow200`. It wraps `VisitResource` in a `data` key: `{ message: string, status: number, data: VisitResource }`. The generated client places the parsed JSON in `res.data`, so the visit is at `res.data.data`.

  2. Create `VisitDetail.tsx`:

     ```tsx
     'use client'

     import { useCallback, useEffect, useState } from 'react'
     import Link from 'next/link'
     import Alert from '@mui/material/Alert'
     import Box from '@mui/material/Box'
     import Card from '@mui/material/Card'
     import CardContent from '@mui/material/CardContent'
     import CircularProgress from '@mui/material/CircularProgress'
     import Divider from '@mui/material/Divider'
     import Stack from '@mui/material/Stack'
     import Typography from '@mui/material/Typography'

     import type { VisitResource } from '@/api/generated/nutriBaseAPI.schemas'
     import { patientsVisitsShow } from '@/api/generated/visit/visit'

     interface Props {
       visitId: string
       patientId: string
     }

     export default function VisitDetail({ visitId, patientId }: Props) {
       const [visit, setVisit] = useState<VisitResource | null>(null)
       const [loading, setLoading] = useState(true)
       const [error, setError] = useState<string | null>(null)

       const loadVisit = useCallback(async () => {
         try {
           setLoading(true)
           setError(null)
           const res = await patientsVisitsShow(Number(patientId), Number(visitId))
           if (res.status === 200) {
             setVisit((res.data as { data: VisitResource }).data)
           } else if (res.status === 403) {
             setError('You do not have permission to view this visit.')
           } else if (res.status === 404) {
             setError('Visit not found.')
           } else {
             setError('Failed to load visit.')
           }
         } catch {
           setError('Failed to load visit.')
         } finally {
           setLoading(false)
         }
       }, [patientId, visitId])

       useEffect(() => {
         loadVisit()
       }, [loadVisit])

       if (loading) {
         return (
           <Box sx={{ display: 'flex', justifyContent: 'center', py: 8 }}>
             <CircularProgress />
           </Box>
         )
       }

       if (error) {
         return (
           <Box>
             <Box sx={{ mb: 2 }}>
               <Link href="/dashboard/visits" style={{ textDecoration: 'none', color: 'inherit' }}>
                 ← Back to Visits
               </Link>
             </Box>
             <Alert severity="error">{error}</Alert>
           </Box>
         )
       }

       if (!visit) return null

       const { date, time, doctorName, patientName, patientId: pid, notes } = visit.attributes

       return (
         <Box>
           {/* Back navigation */}
           <Box sx={{ mb: 3 }}>
             <Link href="/dashboard/visits" style={{ textDecoration: 'none', color: 'inherit' }}>
               ← Back to Visits
             </Link>
           </Box>

           <Card>
             <CardContent>
               <Typography variant="h6" sx={{ mb: 2 }}>
                 Visit Details
               </Typography>
               <Divider sx={{ mb: 3 }} />

               <Stack spacing={2}>
                 <Box sx={{ display: 'flex', gap: 2 }}>
                   <Typography variant="body2" color="text.secondary" sx={{ minWidth: 100 }}>
                     Date
                   </Typography>
                   <Typography>
                     {new Date(date + 'T00:00:00').toLocaleDateString()}
                   </Typography>
                 </Box>

                 <Box sx={{ display: 'flex', gap: 2 }}>
                   <Typography variant="body2" color="text.secondary" sx={{ minWidth: 100 }}>
                     Time
                   </Typography>
                   <Typography>{time || '—'}</Typography>
                 </Box>

                 <Box sx={{ display: 'flex', gap: 2 }}>
                   <Typography variant="body2" color="text.secondary" sx={{ minWidth: 100 }}>
                     Patient
                   </Typography>
                   <Link
                     href={`/dashboard/patients/${pid}`}
                     style={{ textDecoration: 'none', color: 'inherit' }}
                   >
                     <Typography sx={{ '&:hover': { textDecoration: 'underline' } }}>
                       {patientName || '—'}
                     </Typography>
                   </Link>
                 </Box>

                 <Box sx={{ display: 'flex', gap: 2 }}>
                   <Typography variant="body2" color="text.secondary" sx={{ minWidth: 100 }}>
                     Doctor
                   </Typography>
                   <Typography>{doctorName || '—'}</Typography>
                 </Box>

                 <Divider />

                 <Box>
                   <Typography variant="body2" color="text.secondary" sx={{ mb: 1 }}>
                     Notes
                   </Typography>
                   {notes ? (
                     <Typography sx={{ whiteSpace: 'pre-wrap', wordBreak: 'break-word' }}>
                       {notes}
                     </Typography>
                   ) : (
                     <Typography color="text.disabled" fontStyle="italic">
                       No notes recorded.
                     </Typography>
                   )}
                 </Box>
               </Stack>

               {/* Action row — placeholder for T005 (edit/delete) */}
             </CardContent>
           </Card>
         </Box>
       )
     }
     ```

  3. Confirm TypeScript compiles without errors (`npm run build` or the dev server TypeScript check).

  **Decision rationale**: Fetching inside the component (not the route page) matches the pattern in `VisitsView`. The `patientsVisitsShow` generated client is used as instructed; no raw `fetch` is written. The back link is a static `href` (not `router.back()`) to work for direct/bookmarked URLs (research.md Decision 4). Notes use `whiteSpace: pre-wrap` + `wordBreak: break-word` to handle long content without layout overflow.

  **Verification**:
  ```
  npm run dev
  # Log in as doctor → navigate to /dashboard/visits/[existingId]?patient=[patientId]
  # Confirm: all fields render, "← Back to Visits" works, notes show "No notes recorded" when null
  # Navigate to /dashboard/visits/99999?patient=1 → confirm "Visit not found." error
  ```

---

- [ ] T003 [P] [US1] Add "View" button to global visits list in `frontend/src/views/visits/index.tsx`

  **Goal**: Each row in the global visits list gains a "View" button that navigates to the visit detail page.

  **Inputs**:
  - `frontend/src/views/visits/index.tsx` — existing `VisitsTable` inner component, Action column
  - `VisitResource.attributes.patientId` — needed for the detail URL
  - T001 complete (route exists)

  **Outputs**: `frontend/src/views/visits/index.tsx` (modified)

  **Steps**:
  1. Add `Link` import from `'next/link'` at the top of the file.
  2. In the `VisitsTable` inner component, add a "View" button **before** the existing "Edit" button in the Action `TableCell`:

     ```tsx
     import Link from 'next/link'

     // Inside VisitsTable, in the TableRow for each visit:
     <TableCell align='center'>
       <Box sx={{ display: 'flex', gap: 1, justifyContent: 'center' }}>
         <Button
           size='small'
           variant='outlined'
           component={Link}
           href={`/dashboard/visits/${v.id}?patient=${v.attributes.patientId}`}
         >
           View
         </Button>
         <Button
           size='small'
           variant='outlined'
           onClick={() => handleEditVisit(v)}
           disabled={!v.attributes.isEditable}
         >
           Edit
         </Button>
       </Box>
     </TableCell>
     ```

  3. Wrap the two buttons in a `Box` with `display: 'flex', gap: 1` to keep them side by side. Import `Box` if not already imported (it is — already in the existing imports).

  **Decision rationale**: `component={Link}` passes Next.js client-side navigation to the MUI Button, avoiding a full page reload. Placing "View" before "Edit" matches natural read-before-edit priority.

  **Verification**:
  ```
  npm run dev
  # Go to /dashboard/visits
  # Confirm "View" button appears on each row
  # Click "View" → confirm navigation to /dashboard/visits/[id]?patient=[patientId]
  # Confirm the existing "Edit" button still works
  ```

---

**Checkpoint**: User Story 1 is complete. Doctor/admin can navigate to a visit detail page from the global visits list and see all fields.

---

## Phase 4: User Story 2 — Patient Views Own Visit Detail (Priority: P2)

**Goal**: Patient clicks a visit in their profile's Visits tab → navigates to the same detail page. Cross-patient URL access is denied with an appropriate error message (backend enforces 403; frontend displays it).

**Independent Test**: Log in as a patient. Go to `/dashboard/patients/[ownPatientId]`. Open the Visits tab. Click "View" on a visit row. Confirm the detail page renders with no edit/delete controls. Then manually navigate to `/dashboard/visits/[otherPatientVisitId]?patient=[otherPatientId]` → confirm "You do not have permission to view this visit." is shown.

---

- [ ] T004 [P] [US2] Add "View" button to patient profile visits tab in `frontend/src/views/patients/patient-right/visits/index.tsx`

  **Goal**: Each row in the patient profile Visits tab gains a "View" button for navigation to the detail page.

  **Inputs**:
  - `frontend/src/views/patients/patient-right/visits/index.tsx` — existing `TableRow` with Action column, `patient.id` available as prop
  - T001 complete (route exists)

  **Outputs**: `frontend/src/views/patients/patient-right/visits/index.tsx` (modified)

  **Steps**:
  1. Add `Link` import from `'next/link'` at the top.
  2. In the `TableBody` map, add a "View" button **before** the existing "Edit" button:

     ```tsx
     import Link from 'next/link'

     // Inside the TableBody visits.map():
     <TableCell align='right'>
       <Box sx={{ display: 'flex', gap: 1, justifyContent: 'flex-end' }}>
         <Button
           size='small'
           variant='outlined'
           component={Link}
           href={`/dashboard/visits/${visit.id}?patient=${patient.id}`}
         >
           View
         </Button>
         <Button
           size='small'
           variant='outlined'
           onClick={() => handleEditClick(visit)}
           disabled={!visit.attributes.isEditable}
         >
           Edit
         </Button>
       </Box>
     </TableCell>
     ```

  3. Note: `patient.id` from the component prop is the correct patientId — do not use `visit.attributes.patientId` here (both are equivalent but `patient.id` is already available and avoids a potential null check).
  4. Import `Box` — it is already imported in this file.

  **Decision rationale**: Same approach as T003. Using `patient.id` (from component prop) rather than `visit.attributes.patientId` avoids a null coercion and is semantically clearer — we already know the patient from the tab context.

  **Verification**:
  ```
  npm run dev
  # Log in as a patient
  # Go to /dashboard/patients/[ownId] → Visits tab
  # Confirm "View" button appears on each visit row
  # Click "View" → confirm navigation to /dashboard/visits/[id]?patient=[patientId]
  # Confirm detail page shows visit data with no edit/delete buttons (US3 not yet implemented)
  # As a second patient, manually navigate to /dashboard/visits/[firstPatientVisitId]?patient=[firstPatientId]
  # Confirm "You do not have permission to view this visit." error message
  ```

---

**Checkpoint**: User Stories 1 and 2 are complete. All user roles can reach the visit detail page through the appropriate navigation path. Cross-patient access is denied.

---

## Phase 5: User Story 3 — Actions from Visit Detail Page (Priority: P3)

**Goal**: Authorized users can edit or delete a visit directly from the detail page without returning to the list.

**Independent Test**: Log in as a doctor. Navigate to an editable visit's detail page. Confirm "Edit" button is visible. Click it — confirm the edit dialog opens with pre-filled data. Save changes — confirm the detail page refreshes with updated data. Log in as admin. Navigate to any visit detail. Confirm both "Edit" and "Delete" buttons appear. Click "Delete" — confirm the visit is removed and the page redirects to `/dashboard/visits`.

---

- [ ] T005 [US3] Add edit and delete actions to `frontend/src/views/visits/VisitDetail.tsx`

  **Goal**: Conditionally render "Edit" (for `isEditable` visits) and "Delete" (for admin users) buttons on the detail page, wiring them to the existing `VisitEditForm` dialog and `patientsVisitsDestroy` respectively.

  **Inputs**:
  - `frontend/src/views/visits/VisitDetail.tsx` — created in T002
  - `frontend/src/views/visits/VisitEditForm.tsx` — existing form, already used in list views; accepts `visit`, `patientId`, `onSuccess`, `onCancel`
  - `frontend/src/api/generated/visit/visit.ts` — `patientsVisitsDestroy(patient: number, visit: number)`
  - `frontend/src/context/AuthContext.tsx` — `useAuth()` returns `{ user }` where `user.role` is `'admin' | 'doktor' | 'pacijent'`
  - Pattern: `frontend/src/views/visits/index.tsx` — Dialog + DialogTitle + DialogContent edit pattern

  **Outputs**: `frontend/src/views/visits/VisitDetail.tsx` (modified)

  **Steps**:
  1. Add new imports at the top:

     ```tsx
     import { useRouter } from 'next/navigation'
     import Button from '@mui/material/Button'
     import Dialog from '@mui/material/Dialog'
     import DialogContent from '@mui/material/DialogContent'
     import DialogTitle from '@mui/material/DialogTitle'

     import { patientsVisitsDestroy } from '@/api/generated/visit/visit'
     import { useAuth } from '@/context/AuthContext'
     import VisitEditForm from './VisitEditForm'
     ```

  2. Inside the component, add router and auth:

     ```tsx
     const router = useRouter()
     const { user } = useAuth()
     ```

  3. Add `editOpen` state alongside existing state:

     ```tsx
     const [editOpen, setEditOpen] = useState(false)
     ```

  4. Add the delete handler after `loadVisit`:

     ```tsx
     const handleDelete = async () => {
       try {
         await patientsVisitsDestroy(Number(patientId), Number(visitId))
         router.push('/dashboard/visits')
       } catch {
         setError('Failed to delete visit.')
       }
     }
     ```

  5. Replace the `{/* Action row — placeholder for T005 (edit/delete) */}` comment with:

     ```tsx
     {(visit.attributes.isEditable || user?.role === 'admin') && (
       <Box sx={{ mt: 3, display: 'flex', gap: 2 }}>
         {visit.attributes.isEditable && (
           <Button variant="outlined" onClick={() => setEditOpen(true)}>
             Edit
           </Button>
         )}
         {user?.role === 'admin' && (
           <Button variant="outlined" color="error" onClick={handleDelete}>
             Delete
           </Button>
         )}
       </Box>
     )}
     ```

  6. Add the edit Dialog after the closing `</Card>` tag:

     ```tsx
     <Dialog open={editOpen} onClose={() => setEditOpen(false)} maxWidth="sm" fullWidth>
       <DialogTitle>Edit Visit</DialogTitle>
       <DialogContent sx={{ pt: 2 }}>
         <VisitEditForm
           visit={visit}
           patientId={String(visit.attributes.patientId ?? patientId)}
           onSuccess={() => {
             setEditOpen(false)
             loadVisit()
           }}
           onCancel={() => setEditOpen(false)}
         />
       </DialogContent>
     </Dialog>
     ```

  7. Confirm TypeScript compiles — `visit` used in the Dialog must be in scope (it is, after the early-return null checks).

  **Decision rationale**: The `isEditable` flag is computed server-side (accounts for both the 1-day window and whether the user is the recording doctor or admin) — the FE trusts this flag rather than re-implementing the rule. Delete is gated client-side by `role === 'admin'` for UX only; the server enforces `VisitPolicy::delete()` regardless. After a successful edit, `loadVisit()` re-fetches fresh data rather than patching local state, ensuring FR-010 (real-time data). Using `router.push` after delete matches the behavior described in the spec Assumptions.

  **Verification**:
  ```
  npm run dev
  # As doctor on an editable visit detail: confirm "Edit" button appears, dialog opens, save updates the page
  # As doctor on a non-editable visit: confirm "Edit" button is absent
  # As admin on any visit: confirm both "Edit" (if editable) and "Delete" appear
  # As admin, click Delete: confirm redirect to /dashboard/visits and visit no longer in the list
  # As patient: confirm no Edit or Delete buttons appear
  ```

---

**Checkpoint**: All three user stories are complete and independently testable.

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Verify edge cases identified in the spec, confirm UI consistency, and validate the complete feature against spec acceptance scenarios.

---

- [ ] T006 [P] Verify edge cases and final UI review in `frontend/src/views/visits/VisitDetail.tsx`

  **Goal**: Confirm all spec edge cases are handled and the UI is consistent with existing pages.

  **Steps**:
  1. **Long notes**: Create or use a visit with very long notes (1000+ characters). Navigate to its detail page. Confirm text wraps within the Card without horizontal scroll or overflow (the `whiteSpace: pre-wrap` + `wordBreak: break-word` styles from T002 should handle this).
  2. **Direct URL without referrer**: Open a new browser tab and navigate directly to `/dashboard/visits/[id]?patient=[patientId]`. Confirm "← Back to Visits" link renders correctly and works.
  3. **Missing patientId in URL**: Navigate to `/dashboard/visits/[id]` without the `?patient=` param. The `patientId` will be `''` → `patientsVisitsShow(NaN, NaN)` → API returns 404. Confirm the "Visit not found." error message is shown (not a JS error or blank page).
  4. **Non-numeric IDs**: Navigate to `/dashboard/visits/abc?patient=xyz`. Same as above — confirm graceful error message.
  5. **Loading state**: Throttle the network in browser DevTools (Slow 3G). Navigate to a visit detail page. Confirm the `CircularProgress` spinner appears before the data loads.
  6. Review the final rendered page against the checklist in `specs/011-visit-detail-page/checklists/requirements.md` — confirm all FR-001 through FR-010 are visually satisfied.

  **Verification**: Manual QA across all edge cases above. All pass with no console errors and no broken layout.

---

- [ ] T007 [P] Update spec checklist `specs/011-visit-detail-page/checklists/requirements.md`

  **Goal**: Mark checklist items as verified after implementation is complete.

  **Steps**:
  1. Re-read `specs/011-visit-detail-page/checklists/requirements.md`.
  2. Confirm each checklist item passes based on the implemented feature.
  3. Add a "Verified" note with the date at the bottom of the Notes section.

  **Verification**: Checklist file updated; all items marked complete.

---

## Dependencies & Execution Order

### Phase Dependencies

```
Phase 2 (T001)
    └── Phase 3 (T002, T003) ← MVP complete after this phase
           └── Phase 4 (T004) ← can start in parallel with Phase 3
                  └── Phase 5 (T005)
                         └── Phase 6 (T006, T007)
```

### User Story Dependencies

- **US1 (P1)**: T001 → T002 → T003. No dependency on US2 or US3.
- **US2 (P2)**: T001 → T004. Independent of US1 at the implementation level; both modify different files. Can start after T001.
- **US3 (P3)**: T002 must be complete (modifies VisitDetail.tsx). T003 and T004 are independent.

### Within Each Phase

- T003 and T004 modify different files → **can run in parallel** once T002 is complete.
- T006 and T007 modify different files → **can run in parallel** once T005 is complete.
- T005 depends on T002 (modifies the same VisitDetail.tsx).

---

## Parallel Examples

### User Story 1 + 2 list-link tasks (after T002 is complete)

```
Task T003: Add View button to frontend/src/views/visits/index.tsx
Task T004: Add View button to frontend/src/views/patients/patient-right/visits/index.tsx
```

Both touch separate files — assign to different developers or run as parallel agent tasks.

### Polish tasks (after T005 is complete)

```
Task T006: Edge case QA review
Task T007: Update spec checklist
```

---

## Implementation Strategy

### MVP (User Story 1 only — 3 tasks)

1. **T001** — Create route entry point
2. **T002** — Create VisitDetail component
3. **T003** — Add View link to global visits list

**Result**: Doctor/admin can navigate from the global visits list to a fully-rendered visit detail page. All spec success criteria SC-001 through SC-005 are verifiable.

### Incremental Delivery

1. Complete T001 + T002 + T003 → **Demo MVP to stakeholders**
2. Add T004 → Patient flow enabled
3. Add T005 → Edit/Delete actions on detail page
4. Add T006 + T007 → Polish & verification complete

---

## Notes

- `[P]` tasks operate on different files and can be worked in parallel.
- `[Story]` labels enable traceability back to spec.md user stories.
- No PHP / backend files are created or modified in any task.
- The generated API client (`patientsVisitsShow`, `patientsVisitsDestroy`) must be used as-is — do not write custom fetch calls for these endpoints.
- Role strings in the frontend are `'admin'`, `'doktor'`, `'pacijent'` — match the backend `users.role` column values exactly.
- After each task, run `npm run build` or check the dev server TypeScript output to catch type errors early.
