# 5. SYSTEM TESTING

## 5.1. Testing Approach

The NutriBase backend is tested exclusively with automated tests written using Pest 4 [5], a PHP testing framework built on top of PHPUnit 12. The test suite runs against an in-memory SQLite database using Laravel's `RefreshDatabase` trait, which rolls back all database state after each test. This ensures complete test isolation — no test depends on or is affected by the state left by another.

Tests are organised into two categories:

- **Unit tests** — exercise a single class or trait in isolation, with no database or HTTP involvement.
- **Feature tests** — make real HTTP requests against the full Laravel application stack, including routing, middleware, form request validation, controllers, service classes, and the database layer.

The `actingAs()` helper is used to authenticate requests as a specific user role without going through the login endpoint, enabling direct testing of authorization rules for each role combination. Laravel factories with named states (`->admin()`, `->doctor()`, `->patient()`, `->hasSocioeconomic()`) are used to create test data, keeping tests readable and aligned with the domain model.

Static analysis is performed separately by Larastan at level 5, which catches type mismatches and incorrect return types before the test suite runs.

## 5.2. Test Structure

The test suite is divided into 29 test classes covering five functional areas of the application. The table below summarises the test classes, their type, and their test count.

| Test Class | Type | Tests |
|---|---|---|
| `Tests\Unit\Traits\ApiResponsesTest` | Unit | 16 |
| `Tests\Feature\Api\ApiRoutesTest` | Feature | 6 |
| `Tests\Feature\Api\AuthTest` | Feature | 10 |
| `Tests\Feature\Api\RegisterRequestTest` | Feature | 14 |
| `Tests\Feature\Api\ResponseContractTest` | Feature | 5 |
| `Tests\Feature\Api\RoleAccessTest` | Feature | 6 |
| `Tests\Feature\Api\UpdateUserRequestTest` | Feature | 11 |
| `Tests\Feature\Api\UserManagementTest` | Feature | 52 |
| `Tests\Feature\Api\UserResourceTest` | Feature | 4 |
| `Tests\Feature\Auth\LoginRateLimiterTest` | Feature | 2 |
| `Tests\Feature\Auth\StoreUserRequestTest` | Feature | 16 |
| `Tests\Feature\Middleware\RoleMiddlewareTest` | Feature | 11 |
| `Tests\Feature\PatientModelTest` | Feature | 7 |
| `Tests\Feature\PatientPolicyTest` | Feature | 27 |
| `Tests\Feature\PatientResourceTest` | Feature | 13 |
| `Tests\Feature\PatientServiceTest` | Feature | 10 |
| `Tests\Feature\Patient\PatientApiTest` | Feature | 27 |
| `Tests\Feature\Patient\PatientAuthorizationTest` | Feature | 5 |
| `Tests\Feature\Patient\PatientSocioeconomicTest` | Feature | 11 |
| `Tests\Feature\Socioeconomic\PatientSocioeconomicFactoryTest` | Feature | 28 |
| `Tests\Feature\Socioeconomic\PatientSocioeconomicModelTest` | Feature | 14 |
| `Tests\Feature\Socioeconomic\PatientSocioeconomicPolicyTest` | Feature | 15 |
| `Tests\Feature\Socioeconomic\PatientSocioeconomicResourceTest` | Feature | 7 |
| `Tests\Feature\Socioeconomic\SocioeconomicMigrationTest` | Feature | 25 |
| `Tests\Feature\UserPolicyTest` | Feature | 24 |
| `Tests\Feature\visits\VisitPolicyTest` | Feature | 12 |
| `Tests\Feature\visits\VisitsFactoryTest` | Feature | 4 |
| `Tests\Feature\visits\VisitsMigrationTest` | Feature | 3 |
| `Tests\Feature\visits\VisitsModelTest` | Feature | 6 |
| **Total** | | **390** |

## 5.3. Unit Tests

### 5.3.1. ApiResponses Trait

The `ApiResponsesTest` class (16 tests) verifies the `ApiResponses` trait that all API controllers use to construct JSON responses. Each response helper method (`ok`, `created`, `success`, `error`, `noContent`, `paginated`) is tested individually to confirm that it returns the correct HTTP status code, includes the expected envelope keys (`message`, `status`, `data`), and behaves correctly both with and without an optional data payload. These tests have no database dependency and execute in under one second.

## 5.4. Feature Tests

### 5.4.1. Authentication and Authorisation

Authentication behaviour is covered by four test classes. `AuthTest` (10 tests) verifies the core login and logout flows: a valid login returns a Sanctum token with the correct expiry configured in `config/sanctum.php`; an invalid password returns HTTP 401; a missing required field returns HTTP 422 with field-level error details; and a deactivated (soft-deleted) account is rejected at login. It also verifies that the `GET /api/user` profile endpoint returns the full `UserResource` JSON:API shape and rejects unauthenticated requests with 401.

`LoginRateLimiterTest` (2 tests) confirms that the login route throttle is registered and enforces the five-requests-per-minute limit, returning HTTP 429 on subsequent attempts.

`RegisterRequestTest` (14 tests) and `StoreUserRequestTest` (16 tests) exercise the form request validation for user registration and admin-initiated user creation respectively. Every validation rule — required fields, string types, maximum lengths, valid email format, email uniqueness, password minimum length, password confirmation match, and valid role values — is verified with a dedicated test case. Both classes also test the authorization check, confirming that only administrators can use the user creation endpoint.

`RoleMiddlewareTest` (11 tests) and `RoleAccessTest` (6 tests) verify the custom `RoleMiddleware`, which guards routes by comparing the authenticated user's role string against the allowed roles declared in the route definition. All role combinations — admin-only, doctor-only, patient-only, admin-or-doctor — are tested for both allowed and denied roles, as well as for unauthenticated requests.

`ResponseContractTest` (5 tests) asserts the envelope shape of every HTTP error status code used in the API: 200 success, 422 validation error, 401 unauthenticated, 403 forbidden, and 404 not found. This protects the frontend integration contract against accidental changes to the error response structure.

### 5.4.2. User Management

`UserManagementTest` (52 tests) is the most comprehensive test class in the suite. It covers the complete lifecycle of the `User` resource through the `UserController` endpoints:

- **List** — admin sees all users including soft-deleted ones; doctors see only active users; patients receive a 403; unauthenticated requests receive a 401; paginated metadata and links are present.
- **Show** — admin can retrieve soft-deleted users; doctors receive 404 for soft-deleted users; patients can view their own profile but receive 403 for any other user.
- **Create** — admin can create a user with valid data; duplicate email returns 422; invalid role returns 422; created user's password is stored as a bcrypt hash.
- **Update** — admin can partially update any field; email uniqueness validation ignores the user's own current email; non-admins receive 403; non-existent users return 404.
- **Soft delete and restore** — admin can deactivate a user (soft delete); the deactivated user appears in the admin list with a non-null `deletedAt` attribute and cannot log in; admin can restore the user; admin cannot deactivate themselves.
- **Force delete** — admin can permanently remove a user; force-deleted email can be re-registered; non-admins receive 403.

`UserResourceTest` (4 tests) verifies that the `UserResource` output conforms to the JSON:API envelope with `type`, `id`, `attributes`, and `relationships` keys; that attribute keys use camelCase; and that the password field is never exposed in any response.

`UserPolicyTest` (24 tests) tests the `UserPolicy` class in isolation, verifying every policy method (`viewAny`, `view`, `create`, `update`, `delete`, `restore`, `forceDelete`) for all three roles and relevant edge cases such as self-deletion prevention.

### 5.4.3. Patient Management

Patient management is covered by seven test classes.

`PatientModelTest` (7 tests) tests the `Patient` Eloquent model directly: creation with required fields, the `belongsTo` relationship with `User`, the `fullName` computed accessor, soft delete and restore behaviour, and the `date_of_birth` date cast.

`PatientPolicyTest` (27 tests) exhaustively verifies `PatientPolicy` for all five policy methods across all three roles, including the rule that a patient may view and update their own record but not any other patient's record.

`PatientResourceTest` (13 tests) verifies the `PatientResource` JSON:API output: correct `type` and `id`; all expected camelCase attribute keys; correct `Y-m-d` formatting for `dateOfBirth`; ISO 8601 timestamps; presence of the `relationships` key; and conditional inclusion of `user` and `socioeconomic` relationship data based on whether those relations were eager-loaded.

`PatientServiceTest` (10 tests) tests the `PatientService` class in isolation. It confirms that `createPatient` persists the patient record, returns the patient with the user relation loaded, creates a linked socioeconomic record when that data is provided, and rolls back the database transaction when a required field is missing. For `updatePatient`, it verifies that changed fields are persisted, that a new socioeconomic record is created if none exists, that an existing socioeconomic record is updated in place, and that the socioeconomic table is not touched when no socioeconomic fields are included in the request.

`PatientApiTest` (27 tests) tests the patient API endpoints end-to-end. It covers the full CRUD surface, all five unauthenticated routes returning 401, validation errors on create (missing fields, invalid gender, invalid date format), patient-scoped list visibility (a patient only sees their own record in the collection), soft-delete cascade to the socioeconomic record, and the JSON:API envelope shape.

`PatientAuthorizationTest` (5 tests) focuses on the doctor role specifically, confirming that a doctor can perform all five CRUD operations on patient records.

`PatientSocioeconomicTest` (11 tests) tests the behaviour of the socioeconomic fields within the PATCH patient endpoint: creating a socioeconomic record when none exists, updating specific fields while leaving others unchanged, and returning 422 for each of the seven invalid enum field values (marital status, employment status, income level, smoking status, alcohol consumption, physical activity level, and food security status), verified using Pest datasets.

### 5.4.4. Socioeconomic Data

`PatientSocioeconomicModelTest` (14 tests) verifies the `PatientSocioeconomic` model: minimal and full creation, mass-assignable field list, boolean casts for `has_health_insurance`, `has_family_support`, and `has_caregiver`, integer cast for `number_of_dependents`, the `belongsTo` relationship with `Patient`, the one-to-one constraint, cascade deletion on patient force-delete, and nullable field acceptance.

`PatientSocioeconomicFactoryTest` (28 tests) verifies that the `PatientSocioeconomicFactory` produces valid values for every enum field, using Pest datasets to test each field in a single parameterised assertion block. It also confirms boolean output for the three boolean fields, correct auto-creation of a parent patient, and support for attribute overrides.

`PatientSocioeconomicPolicyTest` (15 tests) verifies `PatientSocioeconomicPolicy` for all role and method combinations.

`PatientSocioeconomicResourceTest` (7 tests) verifies the `PatientSocioeconomicResource` output shape, including camelCase key mapping, boolean field casting, null output for unset optional fields, ISO 8601 timestamps, and exclusion of the raw `patient_id` foreign key from the response.

`SocioeconomicMigrationTest` (25 tests) asserts the database schema directly: the `patient_socioeconomic` table exists; all expected columns are present; the three boolean columns default to `false`; the `patient_id` column is a foreign key referencing the `patients` table; and a unique constraint on `patient_id` enforces the one-to-one relationship at the database level.

### 5.4.5. Visits

`VisitsModelTest` (6 tests) verifies the `Visit` Eloquent model: creation with required fields, the `belongsTo` relationship with both `Patient` and `User` (doctor), the presence of optional `notes` and `date` fields, and the `hasMany` relationship from `Patient` back to `Visit`.

`VisitsMigrationTest` (3 tests) confirms that the `visits` table exists and contains all required and expected columns.

`VisitsFactoryTest` (4 tests) verifies that the `VisitFactory` produces a valid visit record linked to a doctor user and a patient, and that attribute overrides are respected.

`VisitPolicyTest` (12 tests) verifies `VisitPolicy` for all role and method combinations. Notable rules tested include: a doctor can view and update only their own visits, not visits created by another doctor; only an admin can delete visits; patients can view their own visits but cannot create, update, or delete them.

## 5.5. Test Results

The full test suite was executed on 2026-04-06 against the current codebase on branch `003-patient-management-api`. All tests passed with no failures or skipped tests.

```
Tests:    390 passed (994 assertions)
Duration: 12.69s
```

The 994 assertions across 390 test cases provide coverage of all implemented API endpoints, all role-based authorization rules, all validation constraints, all Eloquent model relationships, and the database schema for all three feature groups (Users, Patients, Visits).

## 5.6. Impact on Further Development

The automated test suite serves as a regression safety net for all subsequent development. Every new API endpoint added during Milestone 3 will be accompanied by a corresponding feature test class following the same conventions established here. The policy and form request test pattern can be directly replicated for the Visit API endpoints and any future resources.

The `SocioeconomicMigrationTest` pattern — asserting column existence and constraints directly — will be applied to any new migration to verify that schema changes deploy correctly without relying on manual inspection.

Frontend integration tests will be added at a later stage once the React SPA stabilises. The consistent JSON:API envelope contract verified in `ResponseContractTest` and the resource shape tests will serve as the basis for those end-to-end tests, ensuring that any breaking change to the API response shape is caught before it reaches the frontend.
