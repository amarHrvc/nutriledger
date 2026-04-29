# MVP DEVELOPMENT ROADMAP  
Tech Stack: Laravel 12 + Livewire  
Format: Feature groups > tasks (Title + Description)

---

## Feature Group 1 — System Bootstrap

### Setup Laravel Project with Livewire & Breeze  
Install Laravel 12, Breeze scaffolding with Livewire stack. Configure local .env, npm/Yarn, and prepare Tailwind if needed.

### Setup Global Layouts & Navigation Structure  
Build base Blade layout (Livewire-compatible). Include topnav/sidebar, responsive structure, and clean user UI for authenticated areas.

### Role-Based Access Control (Admin, Doctor, User)  
Implement role-based access through middleware or permission packages like spatie/laravel-permission. Restrict views/actions based on user role.

---

## Feature Group 2 — User & Patient Registration

### Admin: Create & Manage Users (Doctors & Patients)  
Build an Admin dashboard to create, edit, deactivate users with roles. Basic CRUD for user management.

### Patient Registration (Admin/Doctor-Created)  
Form to onboard a new patient linked to a user. Store bio-data: name, gender, DOB, contact. Set up 1-1 relationship with user.

### View & Edit Patient Profile  
Build patient profile page with editable fields and persistence. Optional: tab sections (e.g. Bio, History, Appointments).

### Record Socio-Economic Data  
Create form and model attachment for marital status, occupation, lifestyle, and support. Linked to patient profile.

---

## Feature Group 3 — Visits & Encounters

### Visit List (Per Patient)  
Show visit list under patient profile, sorted by date. Include visit ID, doctor, and short note preview.

### Add New Visit  
Form to create a visit. Default doctor to current session user. Add visit date, notes field, and associate with patient.

### View Visit Detail Page  
Full visit detail with nested views for Labs, Vitals, Medications, and Recommendations. Use Livewire tabs or collapsibles.

---

## Feature Group 4 — Vital Signs

### Record Vital Signs During Visit  
Vitals form on visit detail. Inputs for BP, pulse, temp, weight, etc. Persist under visit_id.

### View Vitals History (Per Visit)  
Show table or timeline graph under visit. Optionally auto-calculate BMI and flag abnormal values visually.

---

## Feature Group 5 — Medications

### List Patient Medications  
Table of medications grouped by status (active, discontinued). Includes medication name, dose, frequency.

### Add Medication  
Add new medication to patient. Form with duration, frequency, dosage, and expiration (valid_to).

---

## Feature Group 6 — Lab Results

### Log Lab Result  
Input form for lab: parameter, value, unit, category. Associate to patient and optionally a visit. Store created and reviewed timestamps.

### Show Lab Results Chronologically  
List lab entries by created_at date. Provide filters by date, type, or parameter.

### Attach Labs to Visit Review  
Display visit-linked labs in detail view. Supports follow-up workflows.

---

## Feature Group 7 — Recommendations

### Add Recommendations (Visit-Based)  
Form to attach recommendations to a visit: diet plan, supplementation, lifestyle suggestions. Set status and validity date.

### View & Track Current Recommendations  
List patient recommendations, filtered by active/expired. Includes overview of recommendation categories.

### Support Recommendation Expiry  
Implement logic to automatically mark expired recommendations based on date_valid_to.

---

## Feature Group 8 — Reminders

### Add Patient Reminder  
Form to schedule reminders for labs, check-ups, or follow-ups. Linked to patient and optionally a visit.

### View Upcoming Reminders  
Patient UI and/or dashboard showing future reminders sorted by upcoming date.

---

## Feature Group 9 — Dashboards & Summaries

### Clinic Dashboard  
Dashboard page showing metrics such as total patients, upcoming reminders, active meds, and recent labs.

### Patient Mini Overview Widget  
Embedded card or view showing recent vitals, medications, allergies, age, gender.

---

## Feature Group 10 — Testing & Infrastructure

### Backend Validation & Model Rules  
Apply rules via Livewire or Laravel FormRequest classes to validate form input: required fields, data types, value ranges.

### Create Test Data: Factories & Seeders  
Build and run factories for core entities: users, patients, visits, labs, meds. Populate development DB for testing.

### QA & Smoke Test Key Flows  
Test core sequences manually or via feature tests:
- Patient creation
- Adding a visit
- Logging data (labs, vitals)
- Storing medications and recommendations
- Verifying reminders

---

## Feature Group 11 — MVP Finalization

### Finalize Route & Role Protection  
Verify all routes/pages are correctly protected by roles and permissions. Block unauthorized access where necessary.

### Final Schema & Index Review  
Confirm appropriate indexes exist on key lookup fields (e.g., patient_id, visit_id). Verify foreign key integrity and cascading rules.

### Prepare for MVP Staging  
Prepare environment configs, deployment scripts, and staging database for final deployment/demonstration.

---