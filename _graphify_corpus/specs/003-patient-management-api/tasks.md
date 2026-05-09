# Tasks: Patient Management API

**Input**: Design documents from `/specs/003-patient-management-api/`
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/

**Tests**: Required (SC-004 mandates 35+ Pest tests)

**Organization**: Tasks grouped by user story for independent implementation and testing.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (US1, US2, US3)
- All paths relative to `backend/`

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Verify existing infrastructure and add missing foundational pieces

- [ ] T001 Verify Patient and PatientSocioeconomic models have required fields per data-model.md in app/Models/
- [ ] T002 [P] Create PatientService in app/Services/PatientService.php for transactional CRUD operations
- [ ] T003 [P] Create PatientResource in app/Http/Resources/Api/PatientResource.php (JSON:API compliant)
- [ ] T004 [P] Create PatientSocioeconomicResource in app/Http/Resources/Api/PatientSocioeconomicResource.php

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**CRITICAL**: No user story work can begin until this phase is complete

- [ ] T005 Update StorePatientRequest validation rules in app/Http/Requests/StorePatientRequest.php (all patient + socioeconomic fields, enum validation)
- [ ] T006 [P] Update UpdatePatientRequest validation rules in app/Http/Requests/UpdatePatientRequest.php (sometimes rules, user_id immutable)
- [ ] T007 Create PatientController in app/Http/Controllers/Api/PatientController.php with index, store, show, update, destroy methods
- [ ] T008 Register patient API routes in routes/api.php (apiResource under auth:sanctum + role middleware)
- [ ] T009 Verify PatientPolicy covers viewAny, view, create, update, delete (patient self-access rules) in app/Policies/PatientPolicy.php

**Checkpoint**: Foundation ready - user story implementation can now begin

---

## Phase 3: User Story 1 - Manage patients (Priority: P1) MVP

**Goal**: Admin/Doctor can create, list, view, update, and soft-delete patient records with socioeconomic data

**Independent Test**: Using authenticated Admin/Doctor credentials, perform full CRUD via API and validate responses and persisted data

### Tests for User Story 1

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [ ] T010 [P] [US1] Test admin can create patient with socioeconomic data (201 response) in tests/Feature/Patient/PatientApiTest.php
- [ ] T011 [P] [US1] Test doctor can create patient with socioeconomic data (201 response) in tests/Feature/Patient/PatientApiTest.php
- [ ] T012 [P] [US1] Test admin can list all patients with pagination in tests/Feature/Patient/PatientApiTest.php
- [ ] T013 [P] [US1] Test doctor can list all patients with relationships loaded in tests/Feature/Patient/PatientApiTest.php
- [ ] T014 [P] [US1] Test admin can view single patient with socioeconomic relationship in tests/Feature/Patient/PatientApiTest.php
- [ ] T015 [P] [US1] Test admin can update patient fields in tests/Feature/Patient/PatientApiTest.php
- [ ] T016 [P] [US1] Test admin can soft-delete patient in tests/Feature/Patient/PatientApiTest.php
- [ ] T017 [P] [US1] Test soft-deleted patient excluded from list queries in tests/Feature/Patient/PatientApiTest.php
- [ ] T018 [P] [US1] Test unauthenticated request returns 401 in tests/Feature/Patient/PatientApiTest.php
- [ ] T019 [P] [US1] Test validation errors return 422 with field-specific messages in tests/Feature/Patient/PatientApiTest.php
- [ ] T020 [P] [US1] Test response conforms to JSON:API envelope with camelCase keys in tests/Feature/Patient/PatientApiTest.php

### Implementation for User Story 1

- [ ] T021 [US1] Implement PatientService::createPatient() with transactional patient + socioeconomic creation in app/Services/PatientService.php
- [ ] T022 [US1] Implement PatientService::updatePatient() with partial socioeconomic update/create in app/Services/PatientService.php
- [ ] T023 [US1] Implement PatientController::index() with pagination and relationship loading in app/Http/Controllers/Api/PatientController.php
- [ ] T024 [US1] Implement PatientController::store() using PatientService in app/Http/Controllers/Api/PatientController.php
- [ ] T025 [US1] Implement PatientController::show() with socioeconomic include in app/Http/Controllers/Api/PatientController.php
- [ ] T026 [US1] Implement PatientController::update() using PatientService in app/Http/Controllers/Api/PatientController.php
- [ ] T027 [US1] Implement PatientController::destroy() for soft-delete in app/Http/Controllers/Api/PatientController.php
- [ ] T028 [US1] Run US1 tests and verify all pass

**Checkpoint**: User Story 1 complete - Admin/Doctor CRUD fully functional

---

## Phase 4: User Story 2 - Patient self-view and update (Priority: P2)

**Goal**: Patient can view and update their own profile and socioeconomic details; cannot access other patients

**Independent Test**: Authenticate as patient and GET/PATCH own resource; verify 403 for other patient IDs

### Tests for User Story 2

- [ ] T029 [P] [US2] Test patient can view own patient record in tests/Feature/Patient/PatientApiTest.php
- [ ] T030 [P] [US2] Test patient cannot view other patient records (403) in tests/Feature/Patient/PatientApiTest.php
- [ ] T031 [P] [US2] Test patient can update own profile fields in tests/Feature/Patient/PatientApiTest.php
- [ ] T032 [P] [US2] Test patient cannot update other patient records (403) in tests/Feature/Patient/PatientApiTest.php
- [ ] T033 [P] [US2] Test patient list returns only own record in tests/Feature/Patient/PatientApiTest.php
- [ ] T034 [P] [US2] Test patient cannot delete any patient (403) in tests/Feature/Patient/PatientApiTest.php

### Implementation for User Story 2

- [ ] T035 [US2] Update PatientController::index() to filter by user when requester is patient in app/Http/Controllers/Api/PatientController.php
- [ ] T036 [US2] Verify PatientPolicy::view() correctly restricts patient to own record in app/Policies/PatientPolicy.php
- [ ] T037 [US2] Verify PatientPolicy::update() correctly restricts patient to own record in app/Policies/PatientPolicy.php
- [ ] T038 [US2] Run US2 tests and verify all pass

**Checkpoint**: User Story 2 complete - Patient self-service working

---

## Phase 5: User Story 3 - Incremental socioeconomic data capture (Priority: P3)

**Goal**: Clinician or patient can add/update socioeconomic fields independently; creates record if absent

**Independent Test**: Update only socioeconomic subset and verify other patient fields unchanged; socioeconomic record created if absent

### Tests for User Story 3

- [ ] T039 [P] [US3] Test PATCH with only socioeconomic fields creates record if absent in tests/Feature/Patient/PatientApiTest.php
- [ ] T040 [P] [US3] Test PATCH with socioeconomic fields updates existing record in tests/Feature/Patient/PatientApiTest.php
- [ ] T041 [P] [US3] Test partial socioeconomic payload only updates provided fields in tests/Feature/Patient/PatientApiTest.php
- [ ] T042 [P] [US3] Test patient core fields unchanged when only socioeconomic updated in tests/Feature/Patient/PatientApiTest.php
- [ ] T043 [P] [US3] Test socioeconomic enum validation (invalid values return 422) in tests/Feature/Patient/PatientApiTest.php

### Implementation for User Story 3

- [ ] T044 [US3] Enhance PatientService::updatePatient() to create socioeconomic if absent on partial update in app/Services/PatientService.php
- [ ] T045 [US3] Add socioeconomic enum validation rules to UpdatePatientRequest in app/Http/Requests/UpdatePatientRequest.php
- [ ] T046 [US3] Run US3 tests and verify all pass

**Checkpoint**: User Story 3 complete - Incremental socioeconomic capture working

---

## Phase 6: Edge Cases & Authorization Matrix

**Purpose**: Cover edge cases and complete authorization test coverage

### Tests for Edge Cases

- [ ] T047 [P] Test user_id change attempt returns 422 in tests/Feature/Patient/PatientApiTest.php
- [ ] T048 [P] Test soft-deleted socioeconomic restored with patient (via User restore) in tests/Feature/Patient/PatientApiTest.php
- [ ] T049 [P] Test concurrent update preserves atomicity (last-write-wins) in tests/Feature/Patient/PatientApiTest.php
- [ ] T050 [P] Test invalid enum values return 422 with specific field errors in tests/Feature/Patient/PatientApiTest.php

### Authorization Matrix Tests

- [ ] T051 [P] Test admin has full CRUD access in tests/Feature/Patient/PatientAuthorizationTest.php
- [ ] T052 [P] Test doctor has full CRUD access in tests/Feature/Patient/PatientAuthorizationTest.php
- [ ] T053 [P] Test patient limited to own record (view/update only) in tests/Feature/Patient/PatientAuthorizationTest.php
- [ ] T054 [P] Test patient cannot create new patients (403) in tests/Feature/Patient/PatientAuthorizationTest.php
- [ ] T055 [P] Test patient cannot delete patients (403) in tests/Feature/Patient/PatientAuthorizationTest.php

**Checkpoint**: Edge cases and authorization matrix covered

---

## Phase 7: Polish & Cross-Cutting Concerns

**Purpose**: Final quality checks and documentation

- [ ] T056 Run vendor/bin/pint --dirty for code formatting
- [ ] T057 Run composer run analyse for Larastan static analysis
- [ ] T058 Run full test suite (php artisan test) and verify 35+ tests pass
- [ ] T059 [P] Update PatientFactory to produce realistic socioeconomic data in database/factories/PatientFactory.php
- [ ] T060 Verify JSON:API response envelope consistency across all endpoints
- [ ] T061 Run quickstart.md validation steps

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories
- **User Stories (Phase 3-5)**: All depend on Foundational phase completion
  - User stories can proceed in parallel (if staffed)
  - Or sequentially in priority order (P1 -> P2 -> P3)
- **Edge Cases (Phase 6)**: Depends on all user stories being complete
- **Polish (Phase 7)**: Depends on all phases being complete

### User Story Dependencies

- **User Story 1 (P1)**: Can start after Foundational (Phase 2) - No dependencies on other stories
- **User Story 2 (P2)**: Can start after Foundational (Phase 2) - Uses same controller as US1 but independently testable
- **User Story 3 (P3)**: Can start after Foundational (Phase 2) - Enhances PatientService from US1

### Within Each User Story

- Tests MUST be written and FAIL before implementation
- Service methods before controller methods
- Core implementation before integration
- Story complete before moving to next priority

### Parallel Opportunities

- T002, T003, T004 can run in parallel (different files)
- T005, T006 can run in parallel (different request files)
- All US1 tests (T010-T020) can run in parallel
- All US2 tests (T029-T034) can run in parallel
- All US3 tests (T039-T043) can run in parallel
- All Edge Case tests (T047-T055) can run in parallel

---

## Parallel Example: User Story 1 Tests

```bash
# Launch all US1 tests in parallel:
Task: "Test admin can create patient with socioeconomic data"
Task: "Test doctor can create patient with socioeconomic data"
Task: "Test admin can list all patients with pagination"
Task: "Test admin can view single patient"
# ... etc
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational (CRITICAL - blocks all stories)
3. Complete Phase 3: User Story 1
4. **STOP and VALIDATE**: Test User Story 1 independently
5. Deploy/demo if ready

### Incremental Delivery

1. Complete Setup + Foundational -> Foundation ready
2. Add User Story 1 -> Test independently -> MVP!
3. Add User Story 2 -> Test independently -> Patient self-service
4. Add User Story 3 -> Test independently -> Incremental data capture
5. Add Edge Cases -> Full coverage
6. Polish -> Production ready

---

## Existing Infrastructure Summary

**Already implemented** (verify only):
- `app/Models/Patient.php` - Model with relationships
- `app/Models/PatientSocioeconomic.php` - Model exists
- `app/Policies/PatientPolicy.php` - Policy with role checks
- `app/Http/Requests/StorePatientRequest.php` - Stub (needs rules)
- `app/Http/Requests/UpdatePatientRequest.php` - Exists (needs rules)
- Database migrations for patients and patient_socioeconomic

**To be created**:
- `app/Services/PatientService.php` - Transaction service
- `app/Http/Controllers/Api/PatientController.php` - API controller
- `app/Http/Resources/Api/PatientResource.php` - JSON:API resource
- `app/Http/Resources/Api/PatientSocioeconomicResource.php` - JSON:API resource
- `tests/Feature/Patient/PatientApiTest.php` - Main test file
- `tests/Feature/Patient/PatientAuthorizationTest.php` - Auth matrix tests
- API routes in `routes/api.php`

---

## Notes

- [P] tasks = different files, no dependencies
- [Story] label maps task to specific user story for traceability
- Each user story should be independently completable and testable
- Verify tests fail before implementing
- Commit after each task or logical group
- Stop at any checkpoint to validate story independently
- Patient restore handled via User restore endpoint (Admin-only, User Management API) - no restore endpoint needed in this API
