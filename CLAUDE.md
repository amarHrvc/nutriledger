# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Development (runs PHP server + queue + Vite concurrently)
composer run dev

# Run all tests
composer run test
php artisan test

# Run a single test file
php artisan test tests/Feature/PatientListTest.php

# Run a single test by name
php artisan test --filter="test name here"

# Static analysis
composer run analyse

# Auto-format (changed files only)
vendor/bin/pint --dirty

# Frontend
npm run dev
npm run build
```

## Architecture

### Stack
- Laravel 12 + Livewire 3 (full-page components, not Volt functional API) + Flux UI free v2.1.1
- Tailwind CSS v4 (use `@import` syntax, no deprecated utilities)
- Pest 4 (Feature + Unit tests, SQLite in-memory for tests)
- Larastan level 5, Laravel Pint for code style

### Livewire-First Pattern
All interactive pages are Livewire full-page components — no traditional controllers for UI. Components live in `app/Livewire/` (class) and `resources/views/livewire/` (view). Routes point directly to Livewire component classes.

### Authorization Model
- Three roles: `admin`, `doktor`, `pacijent` (stored as string on `users.role`, no Enum class)
- `RoleMiddleware` registered as `role:admin` in `bootstrap/app.php`
- Gates/policies (`PatientPolicy`, `UserPolicy`) enforce fine-grained access — always use `can:` middleware or `$this->authorize()` in Livewire
- Soft deletes on `users` and `patients` — use `forceDelete()` only for admin

### Form Validation
Use dedicated Form Request classes (`StorePatientRequest`, `UpdatePatientRequest`) in `app/Http/Requests/` — do not inline validation in Livewire components.

### Auth
Laravel Fortify manages auth (login, register, password reset, email verification, 2FA). Auth routes are in `routes/auth.php`. Do not rewrite auth logic — extend Fortify actions if needed.

### Key Models
- `User`: `isAdmin()`, `isDoctor()`, `isPatient()`, `initials()` helpers; includes `TwoFactorAuthenticatable`, `SoftDeletes`
- `Patient`: one-to-one with `User`, computed `fullName` accessor, medical metadata fields (blood type, allergies, emergency contact, medical notes)

### UI Components
Use Flux UI free components (`<flux:button>`, `<flux:input>`, `<flux:modal>`, `<flux:badge>`, etc.) for all UI elements. Layouts: `layouts.app` (authenticated) and `layouts.auth` (guest).

### Testing Conventions
- Feature tests use `RefreshDatabase` and Pest's `actingAs()` helper
- Test files mirror the feature: `tests/Feature/Patient/`, `tests/Feature/Admin/`, etc.
- Policies, routes, Livewire component rendering, and middleware all have separate test coverage
- Write tests before or alongside implementation (TDD encouraged)
