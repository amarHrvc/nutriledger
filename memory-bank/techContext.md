# Tech Context

**Last Updated:** 2025-12-24

## Technologies
- Backend: PHP 8.4 with Laravel 12 (Fortify-style auth), Livewire 3, Volt, and Flux UI components.
- Frontend: Blade + Livewire with Tailwind CSS v4 for styling.
- Tooling: Laravel Boost, Laravel Pint, IDE helper, Larastan, Laravel Debugbar, and Pest 4 for automated tests.

## Development Setup
- Standard Laravel 12 project structure with configuration in `bootstrap/app.php` and Livewire/Volt components under `app/` and `resources/views`.
- Local environment configured via `.env`, with npm tooling for Tailwind and asset compilation.
- Development roadmap and high-level requirements captured in `resources/_tasks/dev_tasks.md` and `resources/_tasks/auth.md`.

## Constraints and Dependencies
- "Learning mode" is active: AI should provide specifications and a few example tests, not full classes or complete test suites; the developer implements the code using TDD.
- New features must use Laravel policies, Livewire components, and Pest feature tests consistently for authorization and behavior.
- Patient Management Phase 1 should be completed before starting the Socioeconomic extension defined in `_docs/_Feature/2_PATIENT_MANAGEMENT_FEATURE_TASKS.md`.
