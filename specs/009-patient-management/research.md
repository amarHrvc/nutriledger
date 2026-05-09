# Research: Patient Management

**Branch**: `009-patient-management` | **Date**: 2026-05-04

---

## Decision 1: Patient Creation — BFF Two-Call Strategy (Q1→A1)

**Decision**: BFF orchestrates two sequential calls — `POST /api/users` (role=pacijent) → extract `user_id` → `POST /api/patients`.

**Rationale**: No backend change required. `usersStore` and `patientsStore` Orval clients are sufficient. If the patient create call fails after the user is created, the orphaned user can be cleaned up manually. Acceptable for clinical admin usage volume.

**Alternatives considered**: A2 (new atomic backend endpoint) — cleaner but requires new FormRequest + controller + route. Out of scope.

---

## Decision 2: Active Patients Only in List (revised)

**Decision**: Patient list and detail pages show **active patients only**. No `withTrashed()` changes to `PatientController`.

**Rationale**: Simpler — no backend changes needed. Deactivated patients are managed via user management (restoring the linked user account). This keeps patient management purely a FE feature.

**Consequence**: `PatientStatusChip` not needed. `deletedAt`/`isDeleted` not needed on `PatientResource`. The "Suspend" button in the detail card always shows (the detail page is only reachable for active patients).

---

## Decision 3: Deactivate-Only Lifecycle (Q2→B)

**Decision**: Patient detail has a single "Suspend" action (soft delete via `DELETE /api/patients/{id}`). After deactivation the user is navigated back to the patients list. Restore is handled via user management.

**Rationale**: No new backend routes needed. Restore parity is preserved — admin restores the user account and the patient record simultaneously via user management. Keeps this feature purely FE.

---

## API Shape Reference

**PatientResource attributes** (existing, no changes):

```ts
{
  firstName, lastName, fullName,
  dateOfBirth, gender,
  phone, address, city, postalCode,
  emergencyContactName, emergencyContactPhone,
  bloodType, allergies, medicalNotes,
  createdAt, updatedAt
}
```

**Required fields for StorePatientRequest**:
- `user_id` (int, existing user with role pacijent — provided by BFF after creating user)
- `first_name`, `last_name` (string, max 50)
- `date_of_birth` (date, before today)
- `gender` (M | F)
- `phone` (string, max 33)
- `emergency_contact_name`, `emergency_contact_phone` (string)

**Optional**: `address`, `city`, `postal_code`, `blood_type`, `allergies`, `medical_notes`, `socioeconomic.*`
