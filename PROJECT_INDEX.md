# Project Index: nutri-ledger

Generated: 2026-02-26 | Branch: feature/patient_management

## Project Overview

Patient ledger MVP for clinics. Laravel 12 + Livewire 3 full-page components + Flux UI free + Tailwind v4 + Pest 4. Fortify handles all auth including 2FA. Three roles: `admin`, `doktor`, `pacijent`.

---

## Stack

| Package | Version |
|---|---|
| PHP | ^8.2 |
| laravel/framework | ^12.0 |
| laravel/fortify | ^1.30 |
| livewire/flux | ^2.1.1 |
| livewire/volt | ^1.7.0 |
| pestphp/pest | ^4.1 |
| laravel/pint | ^1.18 |
| tailwindcss | v4 |

Dev: `laravel/boost`, `larastan/larastan`, `barryvdh/laravel-debugbar`, `barryvdh/laravel-ide-helper`

---

## Directory Structure

```
app/
  Http/
    Controllers/Auth/   VerifyEmailController
    Middleware/         RoleMiddleware
    Requests/           StorePatientRequest, UpdatePatientRequest
  Livewire/
    Actions/            Logout
    Admin/              CreateUser, DeleteUser, EditUser, UserManagement
    Auth/               ForgotPassword, Login, Register, ResetPassword, VerifyEmail
    Patient/            CreatePatient, EditPatient, PatientList, ViewPatient
    Settings/           Appearance, DeleteUserForm, Password, Profile, TwoFactor
  Models/               Patient, User
  Policies/             PatientPolicy, UserPolicy
  Providers/            AppServiceProvider, FortifyServiceProvider

database/
  factories/            PatientFactory, UserFactory
  migrations/           users, cache, jobs, 2fa_columns, role_on_users, soft_deletes, patients
  seeders/              DatabaseSeeder, PatientSeeder

resources/views/
  livewire/admin/       create-user, delete-user, edit-user, user-management
  livewire/auth/        confirm-password, forgot-password, login, register,
                        reset-password, two-factor-challenge, verify-email
  livewire/patient/     create-patient, edit-patient, patient-list, view-patient
  livewire/settings/    appearance, delete-user-form, password, profile, two-factor
  components/layouts/   app.blade.php, auth.blade.php, app/header, app/sidebar
  flux/                 (published Flux component overrides)

routes/
  web.php               main routes
  auth.php              Fortify auth routes
  console.php

tests/
  Feature/
    Admin/              UserManagementTest
    Auth/               Authentication, EmailVerification, PasswordConfirmation,
                        PasswordReset, Registration, TwoFactorChallenge
    Livewire/           DeleteUserTest
    Middleware/         RoleMiddlewareTest
    Settings/           PasswordUpdateTest, ProfileUpdateTest, TwoFactorAuthenticationTest
    CreatePatientComponentTest, PatientListComponent, PatientModelTest,
    PatientPolicyTest, PatientRouteTest, DashboardTest
  Unit/                 ExampleTest
```

---

## Models

### User (`app/Models/User.php`)
- Traits: `HasFactory`, `Notifiable`, `TwoFactorAuthenticatable`, `SoftDeletes`
- Fields: `name`, `email`, `password`, `role`
- Roles: `admin`, `doktor`, `pacijent` (string enum, no Enum class)
- Methods: `isAdmin()`, `isDoctor()`, `isPatient()`, `initials()`
- Relations: `hasOne(Patient::class)`

### Patient (`app/Models/Patient.php`)
- Traits: `HasFactory`, `SoftDeletes`
- Fields: `user_id`, `first_name`, `last_name`, `date_of_birth`, `gender (M/F)`, `phone`, `address`, `city`, `postal_code`, `emergency_contact_name`, `emergency_contact_phone`, `blood_type`, `allergies`, `medical_notes`
- Cast: `date_of_birth` → `date`
- Accessor: `fullName` (computed `first_name last_name`)
- Relations: `belongsTo(User::class)`
- Constraint: `user_id` is unique (one patient per user)

---

## Routes

| Method | URI | Name | Middleware | Component |
|---|---|---|---|---|
| GET | `/` | `home` | — | welcome view |
| GET | `/dashboard` | `dashboard` | auth, verified | dashboard view |
| GET | `/settings/profile` | `settings.profile` | auth | Settings\Profile |
| GET | `/settings/password` | `settings.password` | auth | Settings\Password |
| GET | `/settings/appearance` | `settings.appearance` | auth | Settings\Appearance |
| GET | `/settings/two-factor` | `two-factor.show` | auth | Settings\TwoFactor |
| GET | `/admin/users` | `admin.users.index` | auth, role:admin | view |
| GET | `/admin/users/create` | `admin.users.create` | auth, role:admin | view |
| GET | `/admin/users/{user}/edit` | `admin.users.edit` | auth, role:admin | view |
| GET | `/patients` | `patients.index` | auth, can:viewAny | Patient\PatientList |
| GET | `/patients/create` | `patients.create` | auth, can:create | Patient\CreatePatient |
| GET | `/patients/{patient}` | `patients.show` | auth | Patient\ViewPatient |
| POST | `/patients/{patient}/edit` | `patients.edit` | auth | Patient\EditPatient |

Auth routes in `routes/auth.php` (Fortify-handled).

---

## Authorization

- `RoleMiddleware` — registered as `role:admin` in `bootstrap/app.php`; checks `users.role` string against passed roles, aborts 403 on mismatch
- `PatientPolicy` gates:
  - `viewAny`: admin, doktor
  - `create`: admin, doktor
  - `view`: admin, doktor, own patient (`user_id === patient.user_id`)
  - `update`: admin, doktor, own patient
  - `delete`/`restore`: admin, doktor
  - `forceDelete`: admin only
- `UserPolicy` — gates for user management (see `app/Policies/UserPolicy.php`)

---

## Key Patterns

- Livewire full-page components (`extends Component`) — not Volt functional API — used for all pages
- Authorization in `render()` via `$this->authorize(...)`, plus `can:` route middleware
- `PatientList` uses `WithPagination`, search via `where('first_name', 'like', ...)` with `orWhereHas('user', ...)`
- Form Requests: `StorePatientRequest`, `UpdatePatientRequest` (not inline validation)
- Flux UI free components for all UI elements
- SoftDeletes on both `User` and `Patient`
- Admin routes use blade views (not full-page Livewire) with embedded Livewire components (e.g., `CreateUser`, `DeleteUser`)

---

## Scripts

```bash
composer run dev        # serve + queue + vite concurrently
composer run test       # php artisan test
composer run analyse    # PHPStan via larastan
vendor/bin/pint --dirty # format changed files
```

---

## Tests

- 22 test files, all Pest
- Feature tests: Admin, Auth, Middleware, Patient CRUD, Settings
- No Browser tests yet

---

## Active Development

- Branch: `feature/patient_management`
- Patient CRUD fully scaffolded (Model, Policy, Form Requests, Livewire components, routes, tests)
- Admin user management complete
