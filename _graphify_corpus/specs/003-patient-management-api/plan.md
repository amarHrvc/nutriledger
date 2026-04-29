# Implementation Plan: Patient Management API

**Branch**: `003-patient-management-api` | **Date**: 2026-03-31 | **Spec**: specs/003-patient-management-api/spec.md

## Summary

Build a REST API for Patient Management in NutriLedger. Admins and Doctors can create, list, view, update, and soft-delete patient records (personal, medical, socioeconomic). Role-based authorization enforced via Sanctum + PatientPolicy. JSON:API-compliant responses with camelCase keys. Dual-entity creation (Patient + PatientSocioeconomic) is transactional. Patient restore is handled via User restore (Admin-only, User Management API) — no restore endpoint in this API.

## Technical Context

**Language/Version**: PHP 8.4, Laravel 12
**Primary Dependencies**: Laravel Sanctum, Pest for tests, Larastan, Laravel Pint
**Storage**: MySQL / MariaDB (project DB), Eloquent ORM
**Testing**: Pest (HTTP feature tests)
**Target Platform**: Linux/PHP-FPM (CI runners and production), local dev (native)
**Project Type**: Web service (REST API)
**Performance Goals**: 95% of standard CRUD responses < 1s in test environment
**Constraints**: Follow project conventions (JSON:API envelope, camelCase response keys), transactions for multi-entity operations
**Scale/Scope**: Typical clinic deployment; scope limited to Patient & PatientSocioeconomic models

## Constitution Check

- Project constitution file: `.specify/memory/constitution.md`
- Gate: Service layer required for multi-entity creation and transactions. The project constitution encourages small, testable services — PASS.
- Gate: Use of JSON:API envelope — already present in repo — PASS.

## Project Structure

### Documentation (this feature)

```text
specs/003-patient-management-api/
├── plan.md              # This file
├── research.md          # Phase 0 output
├── data-model.md        # Phase 1 output
├── quickstart.md        # Phase 1 output
├── contracts/           # Phase 1 output
└── tasks.md             # Phase 2 output (speckit.tasks)
```

### Source Code (repository root)

```text
app/
├── Http/
│   └── Controllers/Api/PatientController.php
├── Services/PatientService.php
├── Http/Requests/StorePatientRequest.php
├── Http/Requests/UpdatePatientRequest.php
├── Policies/PatientPolicy.php
├── Models/Patient.php
├── Models/PatientSocioeconomic.php
├── Http/Resources/Api/PatientResource.php
└── Http/Resources/Api/PatientSocioeconomicResource.php

routes/api.php             # add apiResource('patients', ...)

tests/Feature/Patient/
├── PatientApiTest.php          # main feature tests
└── PatientAuthorizationTest.php # authorization matrix
```

**Structure Decision**: Use existing Laravel app/ directory layout. All new files follow this layout.

## Phase 0: Research (resolve clarifications)

Create research.md summarizing decisions and rationale for previously noted clarifications and unknowns.

### Research tasks
- Research 1: JSON:API v1 compliance implications for Laravel Resources (how to structure includes and relationships).
- Research 2: Enum value mapping and validation rules to use (confirm lists sourced from spec prompt).
- Research 3: Soft-delete lifecycle for related models in Laravel (best practice for cascade soft-deletes and restoration semantics).
- Research 4: Test patterns — Pest HTTP tests for authorization matrix and JSON:API validation.

## Phase 1: Design & Contracts

### Data model outputs (data-model.md)
- Entities: Patient, PatientSocioeconomic, User (reference)
- Fields: complete lists from spec (include enum lists)
- Relationships: Patient belongsTo User; Patient hasOne PatientSocioeconomic
- Indexes: user_id unique, idx_patient_name, idx_patient_deleted_at
- Migration notes: ensure foreign key constraints and soft-deletes

### API contracts (/contracts)
- POST /api/patients (request schema, response schema JSON:API)
- GET /api/patients (query params: page, per_page)
- GET /api/patients/{id}
- PATCH /api/patients/{id}
- DELETE /api/patients/{id}

> **Note**: No restore endpoint. Patient restore is handled via `POST /api/users/{id}/restore` (Admin-only, User Management API). Restoring the linked User automatically restores the Patient and PatientSocioeconomic records.

### Quickstart
- Steps to run tests and create sample data using factories

## Phase 2: Tasks (hand off to speckit.tasks)

- Break design into implementation tasks (controllers, services, resources, requests, tests, docs)
