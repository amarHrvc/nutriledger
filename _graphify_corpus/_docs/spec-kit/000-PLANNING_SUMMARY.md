# Spec-Kit Planning Summary: Groups 1-3 (SE MVP Core)

**Created:** 2026-03-19  
**Status:** All prompts complete, ready for spec-kit workflow  
**Total Documentation:** 72.7KB across 3 comprehensive prompts

---

## Overview

This document summarizes the spec-kit planning work for NutriLedger's core API features (SE MVP Groups 1-3). All three prompts are **complete and ready** for the spec-kit workflow: `specify → plan → tasks → implement`.

---

## Completed Prompts

### ✅ Group 1: Auth & User Management
**File:** `002-group1-auth-user-management-prompt.md` (15.6KB)  
**BD Issue:** nutri-ledger-est (P1)  
**Complexity:** ⭐⭐⭐

**What It Covers:**
- UserController CRUD (index, show, store, update, destroy, restore, forceDelete)
- UserResource (JSON:API transformation)
- StoreUserRequest / UpdateUserRequest (form validation)
- API routes for user management
- Role-based authorization (Admin full access, Doctor/Patient limited)
- Soft deletes with restore capability
- 20+ comprehensive tests

**Domain Layer Status:**
- ✅ User model (complete with helpers: isAdmin(), isDoctor(), isPatient(), initials())
- ✅ UserPolicy (comprehensive authorization matrix)
- ✅ AuthController (login, register, logout working)
- ✅ RoleMiddleware (JSON-aware, no redirects)
- ❌ UserController, UserResource, form requests (need to build)

**Key Patterns:**
- Standard CRUD with soft deletes
- Role-based access control (3 roles: admin, doktor, pacijent)
- Self-profile access for patients/doctors
- Admin-only force delete

**Unique Aspects:**
- Role strings stored as Bosnian: "doktor", "pacijent" (not English)
- `initials()` helper returns "FN LN" format
- Two-factor authentication fields present (Fortify)
- Email verification supported

---

### ✅ Group 2: Patient Management
**File:** `003-group2-patient-management-prompt.md` (30.9KB)  
**BD Issue:** nutri-ledger-fg2 (P1)  
**Complexity:** ⭐⭐⭐⭐⭐ (Most complex)

**What It Covers:**
- **Dual-entity creation:** Patient + PatientSocioeconomic atomically
- PatientController CRUD with service layer
- PatientService (handles DB transactions, splits data)
- PatientResource (nested socioeconomic includes)
- Form validation for 50+ fields across both tables
- Relationship diagram: User (1:1) Patient (1:1) Socioeconomic
- 35+ comprehensive tests

**Domain Layer Status:**
- ✅ Patient model (14 fields, fullName accessor, relationships)
- ✅ PatientSocioeconomic model (20+ fields, social determinants of health)
- ✅ PatientPolicy (comprehensive authorization matrix)
- ✅ Factories (both models)
- ✅ Form request stubs (exist but no validation rules)
- ❌ PatientController, PatientService, PatientResource (need to build)

**Database Structure:**
- **patients table:** 14 fields (user_id FK, personal info, emergency contact, medical metadata)
- **patient_socioeconomic table:** 20+ fields (marital status, employment, income, smoking, alcohol, food security, etc.)
- **Relationship:** 1:1:1 (User ← Patient ← Socioeconomic)

**Key Patterns:**
- **Service layer required** for dual-entity creation
- **Transaction management** for atomic operations
- **Data splitting** (patient fields vs socio fields)
- **Flexible updates** (can update patient only, socio only, or both)
- **Eager loading** to avoid N+1 queries

**Authorization Matrix:**

| Role | List | View | Create | Update | Delete | Restore | Force Delete |
|------|------|------|--------|--------|--------|---------|--------------|
| Admin | All | All | Yes | All | All | Yes | Yes |
| Doctor | All | All | Yes | All | All | No | No |
| Patient | None | Own | No | Own | No | No | No |

**Unique Aspects:**
- **Dual-entity complexity:** Most complex CRUD in the system
- **Service layer introduction:** First feature requiring service pattern
- **Social determinants:** Comprehensive socioeconomic data (20+ fields)
- **Medical metadata:** Blood type, allergies, emergency contact
- **Tech debt:** Allergies as text (should be separate table, deferred)
- **Computed accessor:** `fullName` is not a DB field

---

### ✅ Group 3: Visits & Encounters
**File:** `004-group3-visits-encounters-prompt.md` (26.2KB)  
**BD Issue:** nutri-ledger-9d3 (P1)  
**Complexity:** ⭐⭐⭐⭐

**What It Covers:**
- **Nested RESTful routes:** `/api/patients/{patient}/visits`
- VisitController (nested resource controller)
- VisitResource (JSON:API transformation)
- StoreVisitRequest / UpdateVisitRequest
- **Doctor ownership pattern** (only edit own visits)
- **Hard deletes only** (no soft deletes)
- Chronological ordering (newest first)
- 30+ comprehensive tests

**Domain Layer Status (⚠️ On feature/3_visits branch):**
- ✅ Visit model (relationships, date casting)
- ✅ Visit migration (table exists)
- ✅ Visit factory (functional)
- ✅ VisitPolicy (comprehensive authorization)
- ✅ Patient `visits()` relationship
- ⚠️ **MUST MERGE feature/3_visits** before API work
- ❌ VisitController, VisitResource, form requests (need to build)

**Database Structure:**
- **visits table:** 5 fields (patient_id FK, doctor_id FK, date, notes)
- **Relationships:** Patient (1:many) Visit (many:1) User/Doctor
- **No soft deletes:** Hard delete only for MVP simplicity

**Key Patterns:**
- **Nested routes:** Visits don't exist without patient context
- **Scoped binding:** Laravel ensures visit belongs to patient
- **Auto-assign doctor:** `doctor_id` always from auth user (never trusted from request)
- **Ownership validation:** Only doctor who created can edit
- **URL validation:** Always check `visit->patient_id === patient->id`
- **Chronological order:** Always ordered by date DESC

**Authorization Matrix:**

| Role | List | Create | View | Edit Own | Edit Other's | Delete |
|------|------|--------|------|----------|--------------|--------|
| Admin | ✅ All | ❌ No | ✅ Any | ✅ Yes | ✅ Yes | ✅ Yes |
| Doctor | ✅ All | ✅ Yes | ✅ Any | ✅ Yes | ❌ No | ❌ No |
| Patient | ✅ Own | ❌ No | ✅ Own | ❌ No | ❌ No | ❌ No |

**Unique Aspects:**
- **Admin paradox:** Can delete but NOT create (only doctors conduct visits)
- **Doctor ownership:** Can only edit visits THEY created
- **Branch merge required:** Domain layer exists but unmerged
- **Nested routes:** First feature using nested RESTful structure
- **No service layer:** Simpler than Patient despite nested structure
- **Hard delete only:** No soft deletes (intentional for MVP)
- **Patient read-only:** Zero exceptions — completely read-only access

---

## Feature Comparison Matrix

| Aspect | User Management | Patient Management | Visit Management |
|--------|-----------------|-------------------|------------------|
| **Complexity** | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ |
| **Entities** | 1 (User) | 2 (Patient + Socioeconomic) | 1 (Visit) |
| **Fields** | ~8 | ~50+ | ~5 |
| **Route Type** | Flat (`/api/users`) | Flat (`/api/patients`) | **Nested** (`/api/patients/{patient}/visits`) |
| **Service Layer** | Optional | **Required** | Not needed |
| **Soft Deletes** | Yes | Yes | **No (hard only)** |
| **Ownership Rules** | Standard RBAC | Patient-owned record | **Doctor-owned record** |
| **Relationship Loading** | Simple | Nested (user, socioeconomic) | Nested (patient, doctor) |
| **Tests Required** | 20+ | 35+ | 30+ |
| **Prerequisites** | Auth only | User complete | **User + Patient + Branch Merge** |
| **Transaction Mgmt** | Not needed | **Required (dual-entity)** | Not needed |
| **Auto-Assignment** | None | None | **doctor_id from auth** |

---

## Key Technical Patterns

### 1. **Dual-Entity Creation (Patient Only)**
- Patient + PatientSocioeconomic must be created atomically
- Service layer handles DB transactions
- Data splitting (patient fields vs socio fields)
- Flexible updates (can update either or both)

### 2. **Nested RESTful Routes (Visits Only)**
- Routes: `/api/patients/{patient}/visits`
- Scoped binding ensures visit belongs to patient
- Context-aware: visits don't exist without patient
- URL validation: always verify `visit->patient_id === patient->id`

### 3. **Doctor Ownership (Visits Only)**
- `doctor_id` auto-assigned from authenticated user
- Doctors can only edit visits THEY created
- Never trust `doctor_id` from request body
- Policy enforces ownership checks

### 4. **Authorization Patterns (All)**
- RoleMiddleware for broad checks (`role:admin`)
- Policies for resource-specific rules
- Controller uses `$this->authorize()`
- Admin full access, Doctor manage, Patient read-only

### 5. **JSON:API Structure (All)**
```json
{
  "data": {
    "type": "resource-type",
    "id": 123,
    "attributes": { "camelCase": "keys" },
    "relationships": { "related": { "data": {...} } },
    "includes": { "related": { "type": "...", "attributes": {...} } },
    "links": { "self": "url" }
  }
}
```

### 6. **Testing Strategy (All)**
- CRUD operations (all endpoints)
- Authorization (all role combinations)
- Validation (422 errors for all rules)
- Response structure (JSON:API compliance)
- Soft deletes (where applicable)
- Relationship loading (N+1 prevention)

---

## Prerequisites & Dependencies

### Must Be Complete First
- ✅ Group 1 (Auth & User Management) — Need authenticated users
- ✅ Group 2 (Patient Management) — Visits belong to patients
- ⚠️ **feature/3_visits branch** — MUST MERGE before Visit API work

### Already Complete (Domain Layer)
**Users:**
- ✅ User model, migration, factory, policy
- ✅ AuthController (login/register/logout working)
- ✅ RoleMiddleware (JSON-aware)

**Patients:**
- ✅ Patient model, migration, factory, policy
- ✅ PatientSocioeconomic model, migration, factory
- ✅ Form request stubs (no validation rules yet)

**Visits (on feature/3_visits):**
- ✅ Visit model, migration, factory, policy
- ✅ Patient `visits()` relationship
- ⚠️ Unmerged branch

### Blocks (This Work Blocks)
- nutri-ledger-jb2 (M3 — Patterns, Tests & Deployment) — Needs API tests
- Phase 6 FE — Frontend needs API endpoints
- Phase 7 Polish — Quality gate verification
- Future Groups 4-11 — Vitals, Labs, Meds nest under visits

---

## Implementation Priorities

### Recommended Order

**Option A: Sequential (Increasing Complexity)**
1. **User Management** (simplest, builds foundation)
   - No service layer
   - Standard CRUD + soft deletes
   - Foundation for other features
2. **Patient Management** (most complex)
   - Introduces service layer pattern
   - Dual-entity creation
   - Most needed by frontend
3. **Visits Management** (medium complexity)
   - Nested routes pattern
   - Doctor ownership
   - Requires User + Patient complete

**Option B: Frontend-Driven**
1. **Patient Management** (FE needs it first)
2. **User Management** (profile management)
3. **Visits Management** (after merge)

**Option C: Merge First, Then API**
1. **Merge feature/3_visits** (resolve prerequisite)
2. **User Management** (simplest starting point)
3. **Patient Management** (complex but critical)
4. **Visits Management** (domain layer ready)

---

## Testing Requirements Summary

**SE M3 Milestone:** Minimum 5 HTTP tests  
**Recommended:** 20+ per feature for comprehensive coverage

**Total Test Count by Feature:**
- User Management: 20+ tests
- Patient Management: 35+ tests
- Visit Management: 30+ tests
- **Grand Total:** 85+ tests minimum

**Test Organization:**
```
tests/Feature/Api/
├── User/
│   ├── UserListTest.php
│   ├── UserCreateTest.php
│   ├── UserViewTest.php
│   ├── UserUpdateTest.php
│   ├── UserDeleteTest.php
│   └── UserRestoreTest.php
├── Patient/
│   ├── PatientListTest.php
│   ├── PatientCreateTest.php
│   ├── PatientViewTest.php
│   ├── PatientUpdateTest.php
│   ├── PatientDeleteTest.php
│   └── PatientRestoreTest.php
└── Visit/
    ├── VisitListTest.php
    ├── VisitCreateTest.php
    ├── VisitViewTest.php
    ├── VisitUpdateTest.php
    └── VisitDeleteTest.php
```

---

## API Standards Reference

**From LARAVEL_API_RULES.md:**

### Response Format
- All controllers use `ApiResponses` trait
- Methods: `ok()`, `created()`, `noContent()`, `error()`
- Status codes: 200 (OK), 201 (Created), 204 (No Content), 401 (Unauthorized), 403 (Forbidden), 404 (Not Found), 422 (Validation), 500 (Server Error)

### JSON:API Structure
- Resources return `{data: {type, id, attributes, relationships, links}}`
- CamelCase keys in JSON (not snake_case from DB)
- Nouns not verbs in URLs
- Plural resource names
- Lowercase URLs

### Versioning
- Level 0 (unversioned) — routes in api.php
- No `/v1/` prefix yet (deferred for MVP)

### Routing
- Use `Route::apiResource()` not `Route::resource()`
- No create/edit form routes (those are for traditional web apps)
- Scoped binding for nested routes

---

## Out of Scope (Future Enhancements)

**Common across all features:**
- ❌ Search/filtering (deferred to later phase)
- ❌ Bulk operations (bulk update, bulk delete)
- ❌ Export functionality (CSV, PDF)
- ❌ Advanced sorting (multi-column)
- ❌ Field-level permissions (all-or-nothing for MVP)
- ❌ Audit logging (who changed what when)
- ❌ Caching layer (optimize later)
- ❌ Rate limiting per user (global only)

**User-specific:**
- ❌ User avatar uploads
- ❌ Password strength requirements
- ❌ Session management UI
- ❌ Activity logs

**Patient-specific:**
- ❌ Patient search (by name, birth date, etc.)
- ❌ Allergies as separate table (tech debt)
- ❌ Medical history timeline
- ❌ Patient attachments (scans, reports)

**Visit-specific:**
- ❌ Visit templates
- ❌ Visit attachments
- ❌ Visit time tracking
- ❌ Visit billing/coding
- ❌ Visit signatures
- ❌ Visit approval workflow
- ❌ Telemedicine flag

---

## Next Steps: Spec-Kit Workflow

### 1. Invoke spec-kit.specify (Create Specifications)
```bash
# For each prompt, run:
speckit.specify --input 002-group1-auth-user-management-prompt.md
speckit.specify --input 003-group2-patient-management-prompt.md
speckit.specify --input 004-group3-visits-encounters-prompt.md
```
**Output:** `spec.md` for each feature (structured specification)

### 2. Invoke spec-kit.plan (Generate Plans)
```bash
# For each spec, run:
speckit.plan --spec [generated-spec.md]
```
**Output:** `plan.md` for each feature (design artifacts, technical approach)

### 3. Invoke spec-kit.tasks (Generate Task Lists)
```bash
# For each plan, run:
speckit.tasks --plan [generated-plan.md]
```
**Output:** `tasks.md` for each feature (actionable checklist with dependencies)

### 4. Convert to BD Issues
- Expand bd epics with concrete subtasks from spec-kit tasks
- Set proper dependencies (Group 1 → Group 2 → Group 3)
- Set priorities (all P1 for SE MVP)
- Link to spec/plan/task docs

### 5. Begin Implementation
- Start with User API (simplest)
- Then Patient API (with service layer)
- Then Visit API (after merge)
- Follow TDD: write tests first, then implement

---

## Important Notes

### Branch Management
- ⚠️ **feature/3_visits MUST BE MERGED** before Visit API work
- Verify all Livewire-era tests pass after merge
- Run migrations on development database

### Quality Gates
- Laravel Pint (code style)
- Larastan level 5 (static analysis)
- All tests must pass
- No N+1 queries (use query debugger)

### Documentation Requirements
- Postman collection for all endpoints
- API documentation (inline or separate)
- Update README with API usage examples

### Git Strategy
- Work on `develop` branch (not `master`)
- Feature branches for each group (optional)
- Commit after each major milestone
- Keep commits atomic and descriptive

---

## Questions to Resolve (Before Implementation)

1. **Visit Branch:** Merge feature/3_visits now or wait until Visit API work starts?
2. **Restore Endpoint:** POST vs PATCH for `/api/users/{id}/restore`?
3. **Pagination:** Default to 15 per page or different number?
4. **Search:** Implement in initial API or defer to later phase?
5. **Service Layer:** Should User/Visit also use service layer for consistency?
6. **Validation Messages:** Custom messages for all rules or Laravel defaults?

---

## Reference Documents

**Must Read:**
1. `LARAVEL_API_RULES.md` — Core design patterns (38.9KB)
2. `API_PREREQUISITES_AND_APPROACH.md` — Current state, approach (13.8KB)
3. `DB_SCHEMA_FINAL.md` — Database structure
4. `SE_MVP_PLAN.md` — User stories, milestones
5. `IMPLEMENTATION_STATUS.md` — Current progress

**Feature-Specific:**
- `1_AUTH_USER_MANAGEMENT_FEATURE_TASKS.md` — Livewire implementation
- `2_PATIENT_MANAGEMENT_FEATURE_TASKS.md` — Livewire implementation
- `3_VISITS_ENCOUNTERS_FEATURE_TASKS.md` — Livewire implementation

---

## Summary Stats

**Documentation Size:**
- User Management prompt: 15.6KB
- Patient Management prompt: 30.9KB
- Visit Management prompt: 26.2KB
- **Total:** 72.7KB

**Entities:**
- User (1 table)
- Patient (2 tables: patient + patient_socioeconomic)
- Visit (1 table)

**Endpoints:**
- User: 7 endpoints (index, show, store, update, destroy, restore, forceDelete)
- Patient: 7 endpoints (same as User)
- Visit: 5 endpoints (index, show, store, update, destroy)
- **Total:** 19 endpoints

**Tests:**
- User: 20+ tests
- Patient: 35+ tests
- Visit: 30+ tests
- **Total:** 85+ tests

**Lines of Code (Estimated):**
- Controllers: ~1,000 lines
- Resources: ~400 lines
- Form Requests: ~300 lines
- Tests: ~3,500 lines
- **Total:** ~5,200 lines

---

**Status:** All prompts complete ✅  
**Ready for:** spec-kit.specify workflow  
**Last Updated:** 2026-03-19
