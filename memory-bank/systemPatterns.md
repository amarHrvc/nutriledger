# System Patterns

**Last Updated:** 2025-12-24

## Architecture Overview
Nutri Ledger is a Laravel 12 application using Livewire (and Volt) for interactive screens and Tailwind-based layouts.
The domain is organized around patients, their visits/encounters, and related clinical data (vitals, labs, medications, recommendations, reminders).

## Key Design Patterns
- Role-based access control (admin, doctor, patient) guarding routes, dashboards, and sensitive actions.
- Patient-centric workflows: most data (visits, labs, medications, reminders) is accessed through the patient profile.
- Visit-centric subviews: each visit detail view acts as a hub for vitals, labs, medications, and recommendations captured during that encounter.
- MVP work is broken into feature groups (bootstrap, registration, visits, vitals, labs, medications, recommendations, reminders, dashboards, testing/infrastructure, finalization) as described in `resources/_tasks/dev_tasks.md`.

## Component Relationships
- User accounts have a `role` attribute (`admin`, `doktor`, `pacijent`) and patient users have a 1–1 `patient` relationship.
- Patients belong to users and (in later phases) will have a 1–1 socioeconomic profile plus related clinical resources (visits, vitals, labs, medications, recommendations, reminders).
- Policy classes (for example, `UserPolicy`, `PatientPolicy`) gate access to Livewire components and their routes.
- Feature groups are implemented as vertical slices: migrations → models/factories → policies → routes → Livewire components → Blade views → Pest tests.
