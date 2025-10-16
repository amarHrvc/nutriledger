# Review Scope

## Target

`feature/socioeconomic-data` branch — all code added/modified relative to `feature/patient_management` base.
This covers the full socioeconomic patient data feature (Tasks 12–20).

## Stack
- PHP 8.4 / Laravel 12 / Livewire 3 (full-page components, not Volt)
- Flux UI free v2.1.1 / Tailwind v4
- Pest v4 / SQLite in-memory for tests
- Larastan level 5

## Files in Scope (29 files)

### Application Code
- `app/Http/Requests/StoreSocioeconomicRequest.php` — form request, validation rules for all enum + scalar fields
- `app/Livewire/Patient/Socioeconomic/ViewSocioeconomic.php` — read-only display component
- `app/Livewire/Patient/Socioeconomic/ManageSocioeconomic.php` — unified create/edit component (17 public properties)
- `app/Livewire/Patient/Socioeconomic/DeleteSocioeconomic.php` — delete with confirmation
- `app/Livewire/Patient/ViewPatient.php` — modified: added socioeconomic eager load
- `app/Models/PatientSocioeconomic.php` — model with fillable, casts, belongsTo
- `app/Models/Patient.php` — modified: added `socioeconomic()` HasOne relationship
- `app/Policies/PatientSocioeconomicPolicy.php` — RBAC: admin/doktor can CRUD, pacijent can view own
- `app/Providers/AppServiceProvider.php` — modified: manual Gate::policy registration
- `app/helpers.php` — global `format_enum_label()` helper

### Database
- `database/migrations/2026_02_26_233216_create_patient_socioeconomic_table.php` — creates table with enum columns + unique patient_id FK
- `database/migrations/2026_02_27_085609_update_patient_socioeconomic_enums_to_strings.php` — SQLite workaround: recreates table as VARCHAR columns (no ALTER COLUMN in SQLite)
- `database/factories/PatientSocioeconomicFactory.php` — factory with full enum coverage

### Routes
- `routes/web.php` — 4 new routes: show, create (with `->can()`), edit, delete under `/patients/{patient}/socioeconomic`

### Views
- `resources/views/livewire/patient/socioeconomic/view-socioeconomic.blade.php`
- `resources/views/livewire/patient/socioeconomic/manage-socioeconomic.blade.php`
- `resources/views/livewire/patient/socioeconomic/delete-socioeconomic.blade.php`
- `resources/views/livewire/patient/view-patient.blade.php` — modified: socioeconomic summary section

### Tests (9 test files)
- `tests/Feature/Socioeconomic/SocioeconomicMigrationTest.php`
- `tests/Feature/Socioeconomic/PatientSocioeconomicModelTest.php`
- `tests/Feature/Socioeconomic/PatientSocioeconomicFactoryTest.php`
- `tests/Feature/Socioeconomic/PatientSocioeconomicPolicyTest.php`
- `tests/Feature/Socioeconomic/SocioeconomicRoutesTest.php`
- `tests/Feature/Socioeconomic/ViewSocioeconomicComponentTest.php`
- `tests/Feature/Socioeconomic/ManageSocioeconomicComponentTest.php`
- `tests/Feature/Socioeconomic/DeleteSocioeconomicComponentTest.php`
- `tests/Feature/Socioeconomic/PatientProfileIntegrationTest.php`

## Key Design Decisions
- Single `ManageSocioeconomic` component handles both create and edit (toggled by `$isEditing`)
- Enum values stored as VARCHAR in DB (SQLite-compatible), validated at app layer via FormRequest + inline `rules()` in component
- Validation rules duplicated: `StoreSocioeconomicRequest` and `ManageSocioeconomic::rules()` have identical rule sets
- `format_enum_label()` global helper for display formatting
- Policy manually registered via `Gate::policy()` in AppServiceProvider (not using Laravel's auto-discovery)
- Authorization checked in both `mount()` and action methods (double-check pattern)
- `socioeconomic/edit` route has no `->can()` middleware (authorization only in component `mount()`)

## Flags
- Security Focus: no
- Performance Critical: no
- Strict Mode: no
- Framework: Laravel 12 + Livewire 3 + Flux UI free

## Review Phases
1. Code Quality & Architecture
2. Security & Performance
3. Testing & Documentation
4. Best Practices & Standards
5. Consolidated Report
