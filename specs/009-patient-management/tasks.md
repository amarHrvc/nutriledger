# Tasks: Patient Management

**Input**: Design documents from `/specs/009-patient-management/`  
**Prerequisites**: plan.md ✅, spec.md ✅, research.md ✅, data-model.md ✅, contracts/ ✅

**Tests**: No FE unit tests required per plan.md specification.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (US1–US5)

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Verify the existing project foundation supports the patient feature before writing code.

- [ ] T001 Verify Orval-generated patient clients exist in `frontend/src/api/generated/patient/patient.ts` (patientsIndex, patientsStore, patientsShow, patientsUpdate, patientsDestroy) and user clients exist in `frontend/src/api/generated/user/user.ts` (usersStore) — confirm `usersStore` response shape contains `data.user.id`

---

## Phase 2: Foundational (BFF Routes — Blocking Prerequisites)

**Purpose**: BFF API routes that ALL view components depend on. Must be complete before any view can be implemented or tested.

**⚠️ CRITICAL**: No user story work can begin until both BFF routes are ready.

- [ ] T002 [P] Create `frontend/src/app/api/patients/route.ts` — GET handler calls patientsIndex() and forwards res.data with original status; POST handler reads body, calls usersStore({ name, email, password, password_confirmation, role: 'pacijent' }), returns early on non-201 with original status+body, extracts userId from userRes.data.data.user.id, calls patientsStore({ user_id: userId, ...patientFields }), returns patientsStore response (reference: frontend/src/app/api/users/route.ts)
- [ ] T003 [P] Create `frontend/src/app/api/patients/[id]/route.ts` — GET handler calls patientsShow(id); PATCH handler reads body and calls patientsUpdate(id, body); DELETE handler calls patientsDestroy(id) and returns Response(null, { status: res.status }); all await params via `const { id } = await params` (reference: frontend/src/app/api/users/[id]/route.ts)

**Checkpoint**: Both BFF routes live and responding — view implementation can now begin.

---

## Phase 3: User Story 1 — Browse and Search Patients (Priority: P1) 🎯 MVP

**Goal**: Authenticated admin/doctor navigates to `/dashboard/patients`, sees a searchable paginated list of active patients, and clicks a row to open the detail page.

**Independent Test**: Navigate to `/dashboard/patients`, verify the list renders patient rows, type in the search field and verify results filter by name, click a row and verify navigation to `/dashboard/patients/{id}`.

### Implementation

- [ ] T004 [US1] Create `frontend/src/views/patients/PatientList.tsx` — model after `frontend/src/views/users/UserList.tsx`; state: patients, search, pageIndex, rowsPerPage, loading, formOpen; useEffect: GET /api/patients on mount + addEventListener('patients:changed', reload) + cleanup; TanStack Table columns: attributes.fullName, attributes.dateOfBirth, attributes.gender, attributes.phone (no status column — list only shows active patients); client-side globalFilter on search; TablePagination slices table.getRowModel().rows; row click → router.push('/dashboard/patients/${row.original.id}'); "Add New Patient" button → setFormOpen(true); dialog renders placeholder for PatientForm (to be wired in US3); no console.log in committed code
- [ ] T005 [P] [US1] Create `frontend/src/views/patients/index.tsx` — thin PatientsView wrapper: `'use client'; import PatientList from './PatientList'; export default function PatientsView() { return <PatientList /> }`
- [ ] T006 [P] [US1] Create `frontend/src/app/(dashboard)/dashboard/patients/page.tsx` — Next.js page: `import PatientsView from '@views/patients/index'; export default function Page() { return <PatientsView /> }`
- [ ] T007 [US1] Add Patients nav entry to `frontend/src/components/layout/vertical/VerticalMenu.tsx` — follow existing nav item pattern, link to `/dashboard/patients`, use appropriate icon (e.g. people/user-group)

**Checkpoint**: Sidebar shows Patients link. `/dashboard/patients` renders list with search and pagination. Row click navigates to detail URL.

---

## Phase 4: User Story 2 — View Patient Detail (Priority: P2)

**Goal**: Clicking a patient row shows a two-column detail page: left identity card with patient info, right tabs (Overview, Medical, Visits).

**Independent Test**: Navigate to `/dashboard/patients/{validId}`, verify left card shows initials avatar + full name + gender + phone + member-since; click Medical tab and verify blood type/allergies/notes display; click Visits tab and verify visit list or "No visits recorded."; navigate to `/dashboard/patients/99999` and verify error Alert renders instead of spinner.

### Implementation

- [ ] T008 [US2] Create `frontend/src/app/(dashboard)/dashboard/patients/[id]/page.tsx` — `'use client'`; loadPatient: fetch GET /api/patients/${id}, check res.ok; on error: setError(json?.message ?? 'Failed to load patient.'); useEffect: loadPatient() + addEventListener('patients:changed', loadPatient) + cleanup; render: error → `<Alert severity="error">`, loading → `<CircularProgress>`, data → `<PatientDetail patient={patient} />` (reference: `frontend/src/app/(dashboard)/dashboard/users/[id]/page.tsx`)
- [ ] T009 [US2] Create `frontend/src/views/patients/PatientDetail.tsx` — MUI Grid container; left cell `size={{ xs: 12, md: 5, lg: 4 }}` renders `<PatientLeftOverview patient={patient} />`; right cell `size={{ xs: 12, md: 7, lg: 8 }}` renders `<PatientRightTabs patient={patient} />`
- [ ] T010 [US2] Create `frontend/src/views/patients/patient-left/PatientDetailsCard.tsx` — avatar with initials derived from attributes.fullName; full name heading; gender chip; details section: email (from linked user), attributes.dateOfBirth, attributes.phone, attributes.createdAt (member since); stats row: visits count placeholder "—"; Suspend action button (implemented in US5 — render button disabled or as placeholder here); import ConfirmDialog from `@/views/users/shared/ConfirmDialog` for future use (reference: `frontend/src/views/users/user-left/UserDetailsCard.tsx`)
- [ ] T011 [US2] Create `frontend/src/views/patients/patient-left/index.tsx` — Grid container wrapper that renders `<PatientDetailsCard patient={patient} />`
- [ ] T012 [US2] Create `frontend/src/views/patients/patient-right/index.tsx` — MUI TabContext with three tabs: Overview | Medical | Visits; manages active tab state; renders corresponding TabPanel for each tab; passes patient prop into each panel
- [ ] T013 [P] [US2] Create `frontend/src/views/patients/patient-right/overview/index.tsx` — InfoRow grid (follow user-right/overview pattern); rows: Full Name, Date of Birth, Gender, Phone; Address / City / Postal Code (omit row if all null); Emergency Contact Name + Phone; Member Since (attributes.createdAt)
- [ ] T014 [P] [US2] Create `frontend/src/views/patients/patient-right/medical/index.tsx` — InfoRow display; Blood Type, Allergies, Medical Notes; show "—" for null fields
- [ ] T015 [US2] Create `frontend/src/app/api/patients/[id]/visits/route.ts` if not already present — GET handler: extracts id from params, calls Orval visitsIndex(id) (or equivalent), forwards res.data with original status
- [ ] T016 [US2] Create `frontend/src/views/patients/patient-right/visits/index.tsx` — on tab mount: fetch GET /api/patients/${patient.id}/visits; state: visits, loading, error; render: CircularProgress while loading, `<Alert severity="error">` on error, "No visits recorded." when data is empty array, otherwise list of rows showing date + doctor name + notes snippet

**Checkpoint**: `/dashboard/patients/{id}` renders full detail with all three tabs functional. Non-existent id shows error alert.

---

## Phase 5: User Story 3 — Create Patient (Priority: P3)

**Goal**: "Add New Patient" opens a dialog with a combined form that creates the linked user account and patient record in one submit via BFF two-call orchestration.

**Independent Test**: Open create dialog, submit empty form — required field errors appear inline. Fill all required fields and submit — dialog closes, success toast shown, list refreshes with new patient visible.

### Implementation

- [ ] T017 [US3] Create `frontend/src/views/patients/PatientForm.tsx` — two visual sections: **Account** (name, email, password, password_confirmation — all required) and **Patient Info** (firstName, lastName required max 50; dateOfBirth type=date required must be past; gender Select M/F required; phone required; emergencyContactName + emergencyContactPhone required; address, city, postalCode optional; bloodType optional Select A+/A-/B+/B-/AB+/AB-/O+/O-; allergies and medicalNotes optional multiline TextField); on submit: POST /api/patients with combined body; 422 → bind json.errors to individual fields (errors['name']?.[0], errors['first_name']?.[0], etc.); non-ok → top-level `<Alert severity="error">` banner; 201 → dispatchEvent(new CustomEvent('patients:changed')) + call onSuccess?.(); props: onSuccess?: () => void, onCancel?: () => void (reference: `frontend/src/views/users/UserForm.tsx`)
- [ ] T018 [US3] Update `frontend/src/views/patients/PatientList.tsx` — replace create dialog placeholder with `<Dialog open={formOpen} onClose={() => setFormOpen(false)}><PatientForm onSuccess={() => setFormOpen(false)} onCancel={() => setFormOpen(false)} /></Dialog>`

**Checkpoint**: Create dialog submits, new patient appears in list automatically, 422 validation errors surface inline.

---

## Phase 6: User Story 4 — Edit Patient (Priority: P4)

**Goal**: An Edit button on the patient detail page opens a pre-filled form. Saving updates the record via PATCH and refreshes the detail view automatically.

**Independent Test**: Open patient detail, click Edit — form opens with all current values pre-filled. Change a field, save — form closes, detail page shows updated value without manual refresh. Submit with invalid data — inline errors appear.

### Implementation

- [ ] T019 [US4] Create `frontend/src/views/patients/PatientEditForm.tsx` — same Patient Info field set as PatientForm (omit Account section — no name/email/password fields); all fields pre-populated from patient.attributes prop values; on submit: PATCH /api/patients/${patient.id} with body; 422 → bind json.errors to fields; non-ok → `<Alert severity="error">` banner; 200 → dispatchEvent(new CustomEvent('patients:changed')) + call onSuccess?.(); props: patient: PatientResource, onSuccess?: () => void, onCancel?: () => void
- [ ] T020 [US4] Update `frontend/src/views/patients/patient-left/PatientDetailsCard.tsx` — add editOpen state; add Edit button (secondary variant) in the actions area; render `<Dialog open={editOpen} onClose={() => setEditOpen(false)}><PatientEditForm patient={patient} onSuccess={() => setEditOpen(false)} onCancel={() => setEditOpen(false)} /></Dialog>`

**Checkpoint**: Edit dialog pre-fills correctly, PATCH updates are reflected on detail page via patients:changed event.

---

## Phase 7: User Story 5 — Deactivate Patient (Priority: P5)

**Goal**: A Suspend button soft-deletes the patient via DELETE and navigates back to the patient list.

**Independent Test**: Click Suspend on patient detail left card, verify ConfirmDialog appears. Click Cancel — patient unchanged. Click Suspend again, confirm — success toast shown, browser navigates to `/dashboard/patients`, patient no longer appears in the list.

**Scope note** (per research.md Decision 3): Restore and force-delete are out of scope — restore is handled via User Management by restoring the linked user account.

### Implementation

- [ ] T021 [US5] Update `frontend/src/views/patients/patient-left/PatientDetailsCard.tsx` — add confirmAction state; wire Suspend button to openConfirm('deactivate'); on ConfirmDialog confirm: DELETE /api/patients/${patient.id}; on !res.ok: toast.error(json?.message ?? 'Failed to suspend patient.'); on success: toast.success('Patient suspended.') then router.push('/dashboard/patients'); import useRouter from next/navigation; ConfirmDialog already imported in T010

**Checkpoint**: Suspend + confirm soft-deletes patient, shows success toast, redirects to list. Cancel leaves patient unchanged and no navigation occurs.

---

## Phase 8: Polish & Cross-Cutting Concerns

**Purpose**: Final verification across all user stories before feature is considered complete.

- [ ] T022 [P] Audit all new patient view files for stray `console.log` statements — remove any found in `frontend/src/views/patients/` and `frontend/src/app/(dashboard)/dashboard/patients/`
- [ ] T023 Smoke test all five user stories end-to-end: sidebar nav → list → search → row click → detail tabs (Overview, Medical, Visits) → Edit → Suspend with cancel → create new patient via dialog → verify list refresh

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies — start immediately
- **Foundational (Phase 2)**: Depends on Phase 1 — BLOCKS all user stories
- **US1 (Phase 3)**: Depends on Foundational
- **US2 (Phase 4)**: Depends on Foundational; independent of US1 (different files)
- **US3 (Phase 5)**: Depends on US1 (PatientList must exist to wire the dialog, T018)
- **US4 (Phase 6)**: Depends on US2 (PatientDetailsCard must exist to add Edit button, T020)
- **US5 (Phase 7)**: Depends on US2 (PatientDetailsCard must exist to wire Suspend, T021)
- **Polish (Phase 8)**: Depends on all user stories

### User Story Dependencies

| Story | Depends On | Notes |
|---|---|---|
| US1 (P1) | Foundational | Independent of other stories |
| US2 (P2) | Foundational | Independent of US1 (different files) |
| US3 (P3) | US1 | Wires dialog into PatientList |
| US4 (P4) | US2 | Extends PatientDetailsCard |
| US5 (P5) | US2 | Extends PatientDetailsCard |

### Parallel Opportunities

- **T002 + T003**: Both BFF route files — no shared state, run simultaneously
- **T005 + T006**: views/patients/index.tsx and patients/page.tsx — parallel after T004
- **T013 + T014**: overview/index.tsx and medical/index.tsx — parallel after T012
- **T022**: Audit task — parallel with any read-only review

---

## Parallel Example: Phase 2 (BFF Routes)

```
T002 → frontend/src/app/api/patients/route.ts (GET list + POST create)
T003 → frontend/src/app/api/patients/[id]/route.ts (GET + PATCH + DELETE)
# Both can run simultaneously — different files, no shared state
```

## Parallel Example: Phase 4 US2 (Detail Tabs)

```
T013 → patient-right/overview/index.tsx
T014 → patient-right/medical/index.tsx
# Both can run simultaneously after T012 (tab orchestrator) is complete
```

---

## Implementation Strategy

### MVP First (US1 Only)

1. Complete Phase 1: Verify Orval clients
2. Complete Phase 2: BFF routes (CRITICAL — blocks everything)
3. Complete Phase 3: Patient list + navigation (US1)
4. **STOP and VALIDATE**: `/dashboard/patients` loads list, search filters, row click navigates
5. Demo if ready

### Incremental Delivery

1. Phase 1+2 → Foundation ready
2. Phase 3 (US1) → Patient list browseable → **Demo**
3. Phase 4 (US2) → Patient detail with tabs → **Demo**
4. Phase 5 (US3) → Patient creation via dialog → **Demo**
5. Phase 6 (US4) → Patient editing → **Demo**
6. Phase 7 (US5) → Patient suspension → **Demo**
7. Phase 8 → Polish → **Ship**

---

## Notes

- [P] tasks = different files, no dependencies between them — safe to run in parallel
- [Story] label maps each task to a specific user story for traceability
- No FE unit tests — explicitly not required per plan.md
- All mutations dispatch `patients:changed` CustomEvent — PatientList (T004) and detail page (T008) both listen and reload
- Auth cookies stay server-side — Orval clients are called from BFF route handlers only, never from the browser
- `ConfirmDialog` reused from `@/views/users/shared/ConfirmDialog` — no new shared primitive needed
- After deactivation the patient detail 404s — always navigate away with `router.push('/dashboard/patients')`, never dispatch+refresh
- Restore and force-delete are intentionally out of scope (research.md Decision 3) — restore is via User Management
