# NutriBase — SD Project

Clinical nutrition management platform. Laravel 12 REST API + React SPA.

## Repository Structure

```
├── backend/    — Laravel 12 REST API (Sanctum auth, role-based access, Pest tests)
└── frontend/   — React SPA
```

## Backend Stack

- Laravel 12, PHP 8.4
- Laravel Sanctum — token-based authentication
- Three roles: `admin`, `doktor`, `pacijent`
- Pest 4 — feature and unit tests
- Larastan level 5, Laravel Pint

## Implemented Features

### 001 — API Foundation
Laravel REST API scaffold. Sanctum installed, role middleware registered, `ApiController`
base class with `ApiResponses` trait (JSON envelope), global exception handling, CORS config,
Larastan and Pint configured. Full test suite passing.

### 002 — User Management API
Full CRUD for user administration. `UserController` (index, show, store, destroy, restore,
forceDelete), `UserResource` with JSON:API envelope, `UserPolicy` with 7 authorization rules,
`StoreUserRequest` / `UpdateUserRequest` validation, security event logging on sensitive
operations, rate limiting on auth endpoints. TDD cycle with Pest feature and unit tests.

## Running Locally

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan test
```
