# TASK004 - Patient Navigation Integration

**Status:** Pending  
**Added:** 2025-12-24  
**Updated:** 2025-12-24

## Original Request
Implement **Task 11: Navigation Integration** from `_docs/_Feature/2_PATIENT_MANAGEMENT_FEATURE_TASKS.md`, integrating patient management screens into the app’s navigation (e.g., sidebar, menus, dashboard links).

## Thought Process
Once the core patient components (list, create, view, edit, delete) are in place, the UI must expose them through consistent navigation patterns used elsewhere (e.g., admin/user management).
This task is about wiring routes into layouts, ensuring role-based visibility, and making patient features discoverable.

## Implementation Plan
- Review existing navigation layout(s) and patterns (e.g., admin user management links).
- Add patient-related links (list, create) for authorized roles (admin/doktor) only.
- Ensure navigation correctly highlights active sections and matches UX goals in Product Context.
- Add minimal smoke tests (or browser tests later) to confirm navigation works and respects authorization.
- Update `_docs/_CURRENT_STATE.md` and Memory Bank once navigation is integrated.

## Progress Tracking

**Overall Status:** Not Started - 0%

### Subtasks
| ID | Description | Status | Updated | Notes |
|----|-------------|--------|---------|-------|
| 4.1 | Audit existing layouts and navigation structure | Not Started | 2025-12-24 | Identify where patient links should live |
| 4.2 | Add patient navigation links for admin/doktor roles | Not Started | 2025-12-24 | Hide links from `pacijent` users |
| 4.3 | Add basic tests (or later browser tests) for navigation behavior | Not Started | 2025-12-24 | Ensure routes reachable only when authorized |
| 4.4 | Sync docs and Memory Bank to reflect integrated navigation | Not Started | 2025-12-24 | Update `_docs/_CURRENT_STATE.md` summary |

## 📝 Expanded Task Specification

### 🎯 Goal
Integrate patient management screens into the app’s navigation, ensuring discoverability and role-based access for all patient features.

### 📚 Key Concepts
- Navigation layouts (sidebar, dashboard, menus)
- Role-based visibility (admin/doktor only)
- Route integration
- UX consistency
- TDD with Pest (smoke/browser tests)

### 📝 TDD Approach
#### Step 1: Write Tests First (RED)
**File:** `tests/Feature/PatientNavigationTest.php`
**Command to create:**
```bash
php artisan make:test PatientNavigationTest
```
**Example Tests:**
```php
test('admin sees patient navigation links', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']))
        ->get('/dashboard')
        ->assertSee('Patients')
        ->assertSee('Add Patient');
});
// TODO: Test doktor sees links
// TODO: Test pacijent does not see links
// TODO: Test navigation highlights active section
// TODO: Test links route to correct screens
```
**Run tests (should FAIL):**
```bash
php artisan test --filter=PatientNavigation
```

#### Step 2: Implement Navigation (GREEN)
- Audit: resources/views/layouts/* for navigation structure
- Add: patient links for admin/doktor in sidebar/menu/dashboard
- Hide: links for pacijent users
- Ensure: active highlighting and UX consistency

#### Step 3: Run and Refactor (REFACTOR)
- Run: php artisan test --filter=PatientNavigation
- Manual: Login as each role, verify navigation and access

### 🧠 Why This Way?
- Role-based navigation improves security and UX
- Consistent patterns make features discoverable
- Tests prevent regressions in navigation

### ✅ Verification
```bash
# Run tests
php artisan test --filter=PatientNavigation
# Manual test
# 1. Login as admin/doktor/pacijent
# 2. Check sidebar/menu/dashboard for patient links
# 3. Confirm links route to correct screens
# 4. Confirm links hidden for unauthorized roles
```

## Progress Log
### 2025-12-24
- Created TASK004 to track implementation of Patient Management Phase 1 Task 11 (navigation integration for patient features).
- Clarified dependency on completion of core patient components and existing layout patterns.
