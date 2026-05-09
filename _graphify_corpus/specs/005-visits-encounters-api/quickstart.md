# Quickstart: Visits & Encounters REST API

**Branch**: `005-visits-encounters-api` | **Date**: 2026-04-10

## Prerequisites

- Branch `005-visits-encounters-api` is checked out (already is)
- Domain layer is present: `Visit` model, `VisitPolicy`, `VisitFactory`, migration all exist
- Run migrations if not already run: `php artisan migrate`
- Tests pass before starting: `php artisan test tests/Feature/visits/`

## Task Order

Work through tasks in this order. Each task is independently verifiable.

```
T01 → T02 → T03 → T04 → T05 → T06 → T07 → T08
```

| Task | File(s) | What |
|---|---|---|
| T01 | `app/Models/Visit.php` | Fix `date` cast: datetime → date |
| T02 | `app/Policies/VisitPolicy.php` | Fix `viewAny` (add patient) + fix `create` (doctor only) |
| T03 | `app/Http/Requests/StoreVisitRequest.php` | Fix rules: date constraint, notes max, remove doctor_id |
| T04 | `app/Http/Requests/Api/UpdateVisitRequest.php` | Create new request class |
| T05 | `app/Http/Resources/Api/VisitResource.php` | Create resource class |
| T06 | `app/Http/Controllers/Api/VisitController.php` | Create controller (5 methods) |
| T07 | `routes/api.php` | Register nested apiResource route |
| T08 | `tests/Feature/Visit/Visit*Test.php` | Write 44+ HTTP tests |

## Verification After Each Task

**T01**: `php artisan test tests/Feature/visits/VisitsMigrationTest.php tests/Feature/visits/VisitsModelTest.php`

**T02**: `php artisan test tests/Feature/visits/VisitPolicyTest.php`
- Note: existing tests assert admin can create — these need updating to assert admin cannot create

**T03–T04**: `php artisan test --filter=Visit` (domain tests must still pass)

**T05–T07**: Manual curl / Postman or run full test suite

**T08**: `php artisan test tests/Feature/Visit/` (all 44+ tests pass)

**Full suite (final gate)**:
```bash
vendor/bin/pint --dirty
composer run analyse
php artisan test
```

## Key Commands

```bash
# Create the new controller
php artisan make:controller Api/VisitController --no-interaction

# Create UpdateVisitRequest
php artisan make:request Api/UpdateVisitRequest --no-interaction

# Create VisitResource
php artisan make:resource Api/VisitResource --no-interaction

# Run only visit tests
php artisan test tests/Feature/visits/ tests/Feature/Visit/

# Check static types
composer run analyse

# Fix style
vendor/bin/pint --dirty
```

## Common Pitfalls

- **`doctor_id` must NEVER come from the request body** — always `$request->user()->id`. The StoreVisitRequest must not have `doctor_id` in rules.
- **`->scoped(['visit' => 'patient'])`** — without this, a doctor can access `/api/patients/5/visits/99` where visit 99 belongs to patient 8.
- **Policy `viewAny` must return true for patients** — the route scoping (404 for wrong patient) handles cross-patient blocking, not the policy.
- **`create` policy must return `isDoctor()` only** — admins can do everything else but not create visits.
- **Existing policy tests assert admin CAN create** — those tests must be updated alongside the policy fix.
- **Eager load `doctor`** — without `->with('doctor')`, the index returns N+1 queries (one per visit to fetch the doctor).
- **Order by date DESC** — always include `orderBy('date', 'desc')` in the index query.
