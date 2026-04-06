# Developer Quickstart: Auth & User Management API

**Branch**: `002-user-mgmt-api` | **Date**: 2026-03-19

---

## Prerequisites

- PHP 8.4, Composer, SQLite (for tests)
- Laravel 12 dev server running: `composer run dev` (from `backend/`)
- Sanctum installed and configured (already done)

---

## Branch Setup

```bash
git checkout 002-user-mgmt-api
cd backend
composer install
cp .env.example .env         # if not already done
php artisan key:generate
php artisan migrate
```

---

## Environment Variables

Add to `.env`:

```dotenv
SANCTUM_EXPIRATION=1440   # Token lifetime in minutes (24 hours)
```

---

## Running Tests

```bash
# All tests for this feature
php artisan test tests/Feature/Api/

# Specific groups
php artisan test --filter=UserManagement
php artisan test --filter=AuthTest

# Full suite
php artisan test
```

---

## Key Files Modified / Created

| File | Status | Purpose |
|------|--------|---------|
| `app/Traits/ApiResponses.php` | Modify | Add `$data` param to `ok()`, `created()` |
| `app/Policies/UserPolicy.php` | Refactor | Admin-only; uncomment all methods |
| `app/Http/Controllers/Api/AuthController.php` | Modify | Add `register()`, `me()` |
| `app/Http/Controllers/Api/UserController.php` | Rebuild | Full CRUD + `restore()` + `forceDelete()` |
| `app/Http/Requests/Api/RegisterRequest.php` | Create | Registration validation |
| `app/Http/Requests/Api/StoreUserRequest.php` | Create | User creation validation |
| `app/Http/Requests/Api/UpdateUserRequest.php` | Create | User update validation |
| `app/Http/Resources/Api/UserResource.php` | Create | JSON:API resource shape |
| `config/sanctum.php` | Modify | Set `expiration` from env |
| `routes/api.php` | Modify | Replace manual route; add apiResource + custom routes |
| `app/Providers/AppServiceProvider.php` | Modify | Register `login` rate limiter |
| `tests/Feature/Api/AuthTest.php` | Update | Fix envelope structure; add new scenarios |
| `tests/Feature/Api/UserManagementTest.php` | Create | 20+ CRUD + auth + policy tests |
| `tests/Feature/Api/UserPolicyTest.php` | Create | Policy unit tests |

---

## Quick Verification

After implementation, run:

```bash
# Gate 1: Code style
vendor/bin/pint --dirty

# Gate 2: Static analysis
composer run analyse

# Gate 3: Tests
php artisan test tests/Feature/Api/
```

All three gates must be green before any task is considered complete.

---

## Postman / Manual Testing

Import `contracts/api-endpoints.md` as reference.

```bash
# Register
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@example.com","password":"password","password_confirmation":"password"}'

# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'

# List users (with admin token)
curl http://localhost:8000/api/users \
  -H "Authorization: Bearer {token}"
```
