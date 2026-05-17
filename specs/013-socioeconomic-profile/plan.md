# Implementation Plan: Patient Socioeconomic Profile

**Branch**: `013-socioeconomic-profile` | **Date**: 2026-05-17 | **Spec**: [spec.md](./spec.md)  
**Input**: Feature specification from `/specs/013-socioeconomic-profile/spec.md`

## Summary

Add a Socioeconomic tab to the patient detail page displaying 17 clinical/lifestyle fields across 5 sections, with an Edit dialog for admin/doctor roles and an optional collapsed accordion in the patient create/edit forms. Requires one additive backend change: embed `socioeconomicData` attributes in `PatientResource` (the attributes are currently stored and writable but not returned in the API response).

## Technical Context

**Language/Version**: PHP 8.3 (backend) · TypeScript / Next.js 15 (frontend)  
**Primary Dependencies**: Laravel 12 + Sanctum (BE) · MUI v5, React 18 (FE)  
**Storage**: MySQL via Eloquent — `patient_socioeconomic` table (already migrated)  
**Testing**: Pest 4 + SQLite in-memory (BE) · manual browser verification (FE)  
**Target Platform**: Web — Chrome/modern browsers  
**Project Type**: Web application (decoupled BE + FE monorepo)  
**Performance Goals**: Patient detail page loads in <2s; socioeconomic tab switches instantly (data already in patient response)  
**Constraints**: No new API routes. No new DB migrations. No new Laravel policies (existing `PatientSocioeconomicPolicy` covers authorization). Backward-compatible BE change only.  
**Scale/Scope**: ~5 patients in demo seed; clinical app, not high-throughput

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-checked post-design.*

| Principle | Requirement | Status |
|---|---|---|
| I. Dual-Track Architecture | FE and BE tasks are separate. No Livewire changes. | ✅ PASS |
| II. Authorization at Every Layer | BE: `PatientSocioeconomicPolicy` already enforces read/write. FE: Edit button conditionally rendered based on `user.role`. | ✅ PASS |
| III. Test-First | New Pest test (`PatientSocioeconomicTest`) asserts `socioeconomicData` in `GET /api/patients/{id}` response before BE task is marked done. | ✅ PASS |
| IV. Code Quality Gates | Pint + Larastan must be green after BE-1. FE has no type-check gate defined but uses TypeScript strict conventions. | ✅ PASS |
| V. Tasks Are Developer-Ready | Each task below has: Goal, Inputs, Outputs, Steps, Rationale, Verification. | ✅ PASS |

**Post-design re-check**: The `socioeconomicData` addition to `PatientResource` is additive (no existing key removed or renamed). All existing Pest tests that assert the patient response shape will still pass. Confirmed.

## Project Structure

### Documentation (this feature)

```text
specs/013-socioeconomic-profile/
├── plan.md              ← This file
├── research.md          ← Phase 0 output
├── data-model.md        ← Phase 1 output
├── quickstart.md        ← Phase 1 output
├── contracts/
│   └── patient-resource.md   ← Updated response shape
└── tasks.md             ← Phase 2 output (created by /speckit.tasks)
```

### Source Code (affected files)

```text
backend/
├── app/Http/Resources/Api/
│   └── PatientResource.php          ← MODIFY: embed socioeconomicData
└── tests/Feature/Api/
    └── PatientSocioeconomicTest.php ← CREATE: assert new response key

frontend/src/views/patients/
├── socioeconomic/                   ← CREATE directory
│   ├── types.ts                     ← SocioeconomicData, SocioeconomicFormPayload
│   ├── labels.ts                    ← enum → human-readable label maps
│   ├── SocioeconomicTab.tsx         ← Patient detail tab (read + edit trigger)
│   ├── SocioeconomicSection.tsx     ← Single display section card
│   └── SocioeconomicFields.tsx      ← Shared 17-field form controls
├── PatientForm.tsx                  ← MODIFY: add Accordion with SocioeconomicFields
├── PatientEditForm.tsx              ← MODIFY: add Accordion with SocioeconomicFields
└── patient-right/
    └── index.tsx                    ← MODIFY: add Socioeconomic tab
```

## Tasks (reference — detail in tasks.md)

Tasks are ordered by dependency. BE-1 must complete before FE tasks that read `socioeconomicData`.

| ID | Layer | Title | Depends On |
|---|---|---|---|
| BE-1 | Backend | Embed socioeconomicData in PatientResource | — |
| FE-1 | Frontend | Create types.ts and labels.ts | — |
| FE-2 | Frontend | Create SocioeconomicSection display component | FE-1 |
| FE-3 | Frontend | Create SocioeconomicFields form sub-component | FE-1 |
| FE-4 | Frontend | Create SocioeconomicTab (view + edit dialog) | FE-2, FE-3, BE-1 |
| FE-5 | Frontend | Add Socioeconomic tab to PatientRightTabs | FE-4 |
| FE-6 | Frontend | Add socioeconomic accordion to PatientForm (create) | FE-3 |
| FE-7 | Frontend | Add socioeconomic accordion to PatientEditForm (edit) | FE-3 |

## Complexity Tracking

No constitution violations. No complexity justification required.
