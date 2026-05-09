# Quickstart: SE API Foundation

**Feature**: `001-se-api-foundation` | **Date**: 2026-03-15

Gets a developer running with the full BE+FE stack for this feature end-to-end.

---

## Prerequisites

- PHP 8.4, Composer
- Node.js 20+, npm
- MySQL running (or use SQLite for local dev — see `.env.example`)
- Existing `.env` with `DB_*` values set

---

## BE Setup

### 1. Install Sanctum

```bash
php artisan install:api
```

This generates `config/sanctum.php` and creates the `personal_access_tokens` migration.

### 2. Run migrations

```bash
php artisan migrate
```

### 3. Configure CORS

In `config/cors.php`, add `http://localhost:5173` to `allowed_origins`:

```php
'allowed_origins' => ['http://localhost:5173'],
```

### 4. Start the Laravel server

```bash
php artisan serve
# Listening on http://localhost:8000
```

### 5. Verify the ping endpoint

```bash
curl http://localhost:8000/api/ping
# Expected: {"message":"ok","status":200,"data":{"status":"ok"}}
```

### 6. Verify login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
# Expected: {"message":"Login successful.","status":200,"data":{"token":"...","user":{...}}}
```

*(Requires a seeded user — run `php artisan db:seed` if needed)*

---

## FE Setup

### 1. Create the scaffold

```bash
npm create vite@latest frontend -- --template react
cd frontend
npm install
npm install axios
```

### 2. Configure environment

```bash
cp .env.example .env
# .env already contains: VITE_API_URL=http://localhost:8000
```

### 3. Start the dev server

```bash
npm run dev
# Listening on http://localhost:5173
```

### 4. Verify CORS in browser

Open `http://localhost:5173` in a browser. The smoke-test component in `App.jsx` calls
`GET /api/ping` on mount. Check the browser console — you should see:

```
{ message: "ok", status: 200, data: { status: "ok" } }
```

No CORS errors in the console = CORS is configured correctly.

---

## Running Tests

```bash
# All tests
php artisan test

# Auth feature tests only
php artisan test tests/Feature/Api/AuthTest.php

# With coverage (optional)
php artisan test --coverage
```

---

## Quality Gates

Run these before marking any task done:

```bash
# 1. Code style
vendor/bin/pint --dirty

# 2. Static analysis
composer run analyse

# 3. Affected tests
php artisan test tests/Feature/Api/AuthTest.php
```

All three must pass with no errors.
