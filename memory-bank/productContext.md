# Product Context

**Last Updated:** 2025-12-24

## Purpose
Nutri Ledger centralizes patient nutrition-related data (visits, labs, vitals, medications, recommendations, and reminders) in a simple, clinic-friendly web application.

## Target Users
- Clinic admins who manage users, roles, and overall configuration.
- Doctors and nutritionists who create visits, record clinical data, and manage recommendations.
- Support staff who help onboard patients and track reminders and follow-ups.

## Problems This Solves
- Scattered patient data across spreadsheets, paper notes, and multiple tools.
- Lack of structured tracking for visits, labs, vitals, medications, and follow-up recommendations.
- Difficulty getting a quick overview of a patient’s status or the clinic’s workload (upcoming reminders, active medications, recent labs).

## User Experience Goals
- Keep everyday flows (user management, patient creation, viewing/editing profiles) simple enough for non-technical clinic staff.
- Prefer feature-complete vertical slices (DB → model/factory → policy → routes → Livewire → view → tests) that behave consistently across the app.
- Present patient and user information clearly, separating admin tooling from day-to-day clinical views.
