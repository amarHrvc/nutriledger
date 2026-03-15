# User Stories — nutri-ledger

**Project:** Clinic Nutrition Management platform for tracking patient records, medical histories, and dietary consultations across a multi-role clinical workflow (admin,
doctor, patient).

**Scope:** Feature Groups 1 (Auth/Users), 2 (Patients), 3 (Visits)

**Total:** 27 functional + 3 non-functional = 30 stories

---

## Functional User Stories

### Authentication & User Management (Group 1)

1. As an **admin**, I can log in with my email and password so that I can access the management dashboard.
2. As a **doctor**, I can log in to the system so that I can manage my assigned patients and visits.
3. As a **patient**, I can log in to the system so that I can view my own medical records.
4. As an **admin**, I can create new user accounts and assign them a role (admin, doctor, patient) so that staff and patients can access the system.
5. As an **admin**, I can view a list of all registered users so that I can manage system access.
6. As an **admin**, I can edit a user's profile information so that records stay up to date.
7. As an **admin**, I can deactivate (soft-delete) a user account so that access is revoked without losing historical data.
8. As an **admin**, I can restore a soft-deleted user account so that access can be reinstated when needed.
9. As an **admin**, I can permanently delete a user account so that their data is fully removed from the system.
10. As any **authenticated user**, I can update my own profile information so that my account details are current.

### Patient Management (Group 2)

11. As an **admin or doctor**, I can register a new patient with their personal details (name, date of birth, gender, contact info) so that they become part of the system.
12. As an **admin or doctor**, I can enter a patient's medical metadata (blood type, allergies, medical notes) during registration so that clinically relevant information is captured upfront.
13. As an **admin or doctor**, I can view the full list of all active patients so that I can quickly find and access any patient record.
14. As an **admin or doctor**, I can view the full profile of a specific patient, including their personal, medical, and socioeconomic data.
15. As a **patient**, I can view my own profile and medical information so that I am informed about my records.
16. As an **admin or doctor**, I can update a patient's personal and contact information so that records remain accurate.
17. As an **admin or doctor**, I can update a patient's medical metadata (blood type, allergies, notes) so that the clinical record reflects the current state.
18. As an **admin or doctor**, I can view and update a patient's socioeconomic profile (employment, income, lifestyle, food security) so that the care plan accounts for social determinants.
19. As an **admin**, I can soft-delete a patient record so that it is removed from the active list without losing data.
20. As an **admin**, I can restore a soft-deleted patient record so that the patient can be reactivated.
21. As an **admin or doctor**, I can search or filter patients by name so that I can quickly locate specific records in a large list.

### Visits & Encounters (Group 3)

22. As a **doctor**, I can create a new visit record for a patient, recording the date and clinical notes, so that each encounter is documented.
23. As an **admin or doctor**, I can view the full visit history for a specific patient so that I have a chronological overview of their encounters.
24. As a **doctor**, I can view the details of a specific visit (date, attending doctor, notes) so that I can review what was recorded.
25. As a **doctor**, I can edit the notes of a visit I conducted so that I can correct or supplement the documentation.
26. As an **admin**, I can delete a visit record so that erroneous entries can be removed.
27. As a **patient**, I can view my own visit history (read-only) so that I can see when and why I attended the clinic.

---

## Non-Functional User Stories

28. As a **system user**, I expect all API responses for standard CRUD operations to return within 500ms so that the application feels responsive.
29. As a **system administrator**, I require that all API endpoints enforce authentication — unauthenticated requests must receive a 401 response — so that patient data is protected.
30. As a **patient or doctor**, I can use the application comfortably on a mobile device (minimum 375px viewport) so that the system is accessible on the go.
