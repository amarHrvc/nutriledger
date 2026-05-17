# Research: Patient Socioeconomic Profile (013)

**Date**: 2026-05-17  
**Branch**: `013-socioeconomic-profile`

---

## Decision 1: How does the FE read socioeconomic data?

**Question**: The spec states "no backend changes needed", but `PatientResource` only returns a JSON:API-style relationship reference (`{ type: "patient_socioeconomic", id: "..." }`) — the 17 socioeconomic attributes are NOT in the API response for `GET /api/patients/{id}`.

**Finding**: Confirmed by reading `PatientResource.php` (`toArray`): the `relationships.socioeconomic.data` block contains only `type` and `id`. No `included` array is emitted. There is no `GET /api/patients/{patient}/socioeconomic` endpoint in `routes/api.php`. The generated OpenAPI client (`nutriBaseAPI.schemas.ts`) reflects this — no `PatientSocioeconomicAttributes` type exists in the response schema.

**Decision**: Embed the full socioeconomic attributes inline in the `PatientResource` response. Add an `socioeconomicData` key to `attributes` (or as a top-level key alongside `attributes` and `relationships`) that includes the complete `PatientSocioeconomicResource` attributes when the relationship is loaded. This is a **minimal, backward-compatible backend change** (additive only — existing consumers are unaffected). A new endpoint is not needed.

**Rationale**: Embedding avoids a second HTTP request on every patient detail page load. It is consistent with how `PatientDetailsCard` already loads the patient once and renders all tabs. The change is additive; existing consumers that don't read `socioeconomicData` are unaffected.

**Alternatives considered**:
- `GET /api/patients/{id}/socioeconomic` new endpoint — rejected: adds a second request on page load, more backend surface than needed.
- Read socioeconomic from `relationships.socioeconomic.data.id` and then call a second endpoint — rejected: no such endpoint exists and adds round-trip latency.
- Frontend reads the relationship `id` and does `GET /api/patient-socioeconomic/{id}` — rejected: no such route; would require more backend work.

---

## Decision 2: How is the Socioeconomic form integrated into PatientForm / PatientEditForm?

**Question**: Should the socioeconomic section in create/edit patient forms be a shared sub-component (reducing duplication) or duplicated inline fields?

**Finding**: `PatientForm.tsx` and `PatientEditForm.tsx` already share the same field list pattern with individual `useState` hooks per field. Introducing a shared `SocioeconomicFields` sub-component (accepting state + setters as props) would reduce the 17-field duplication while keeping each parent form's submit logic self-contained.

**Decision**: Extract a `SocioeconomicFields` sub-component that accepts `value: SocioeconomicFormData` and `onChange: (data: SocioeconomicFormData) => void`. Both `PatientForm` and `PatientEditForm` use it inside a MUI `Accordion` collapsed by default. `SocioeconomicForm` (the standalone dialog form for the tab's Edit button) also uses `SocioeconomicFields` internally.

**Rationale**: One definition of all 17 controls, three callers. Any future field addition or enum change only needs updating in one place.

**Alternatives considered**:
- Duplicate the 17 fields in each form — rejected: maintenance burden, error-prone.
- A wizard/stepper pattern for patient creation — rejected: over-engineering for optional fields.

---

## Decision 3: Enum label display strategy

**Question**: Where should the mapping from snake_case enum values (e.g., `employed_full_time`) to human-readable labels (e.g., `Employed Full Time`) live?

**Decision**: A single `SOCIOECONOMIC_LABELS` constant file (`src/views/patients/socioeconomic/labels.ts`) exports one record per enum field. Both the display tab and the form `Select` components import from this file.

**Rationale**: Single source of truth for labels. The file is co-located with the feature components. `null` / missing values are handled at the display layer by the tab (renders "—"), not by the labels map.

---

## Decision 4: Boolean field rendering in the display tab

**Question**: Booleans (`has_health_insurance`, `has_family_support`, `has_caregiver`) — MUI `Chip`, plain text, or icon?

**Decision**: MUI `Chip` with `label="Yes"` / `label="No"` and `color="success"` / `color="default"`. This is consistent with the boolean rendering pattern used in the existing Vitals flags display.

---

## Decision 5: Constitution compliance for the BE change

**Question**: The spec said "no backend changes", but we need one. Does this violate the constitution?

**Decision**: No violation. The constitution's Principle II (Authorization) and Principle III (Test-First) apply. The BE change is an additive serialization addition (no new route, no new model, no new policy). Per Principle III, a Pest test must assert the new `socioeconomicData` key appears in the `GET /api/patients/{id}` response. Per Principle IV, Pint + Larastan must pass. The CLAUDE.md explicitly states `PatientResource` is in `backend/app/Http/Resources/Api/` — updating it is within scope.

---

## Summary of Research Findings

| Area | Finding | Impact on Plan |
|---|---|---|
| API response | `PatientResource` does not expose socioeconomic attributes | Requires BE task: embed `socioeconomicData` in `PatientResource` |
| Existing FE patterns | `useState` per field; MUI Select/TextField/Chip patterns | `SocioeconomicFields` follows same conventions |
| Enum values | Fully defined in `nutriBaseAPI.schemas.ts` as TypeScript const objects | Import from generated schema for form values; labels in separate map |
| Authorization | FE role check via `useAuth()` already used in `VisitDetail`; backend enforced by policy | `SocioeconomicTab` conditionally shows Edit button based on `user.role` |
| No new routes | PATCH `/api/patients/{id}` with `{ socioeconomic: {...} }` already works | No new API endpoints needed |
