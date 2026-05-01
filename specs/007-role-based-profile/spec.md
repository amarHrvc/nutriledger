# Feature Specification: Role-Based Profile Page

**Feature Branch**: `007-role-based-profile`  
**Created**: 2026-05-01  
**Status**: Draft  
**Input**: User description: "I want to build a profile page for the administrator, doctor, or patient. It will show different contexts for each of the users. The main goal or main result of this plan should be a tutorial-like MD file I can give to a developer directly to implement."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Patient Views Own Profile (Priority: P1)

A patient logs in and navigates to their profile page. They see a complete picture of their health record on file: personal details, medical information (blood type, allergies, medical notes), emergency contact, and a summary of their most recent visits.

**Why this priority**: Patients are the most numerous users and their profile contains the most clinically sensitive and unique data. This view is the primary value of the feature.

**Independent Test**: Log in as a patient user and navigate to the profile page. The page must show patient-specific sections and no administrative or clinical-staff content.

**Acceptance Scenarios**:

1. **Given** a logged-in patient, **When** they visit their profile page, **Then** they see: full name, date of birth, gender, phone, address, blood type, allergies, medical notes, emergency contact name and phone, and a list of their most recent visits ordered newest-first.
2. **Given** a logged-in patient with an unset medical field (e.g., blood type), **When** they view their profile, **Then** the field shows "Not provided" rather than being blank.
3. **Given** a logged-in patient with no recorded visits, **When** they view their profile, **Then** the visits section shows a meaningful empty state message, not an error or blank space.

---

### User Story 2 - Doctor Views Own Profile (Priority: P2)

A doctor logs in and views their profile. They see their professional identity and a summary of their clinical activity: total number of consultations conducted and their most recent visit dates.

**Why this priority**: Doctors need a professional context view — not a patient medical record — but still need role-meaningful activity data. Simpler than patient profile but still distinct.

**Independent Test**: Log in as a doctor user and navigate to the profile page. Doctor-specific activity sections must appear; patient medical fields (blood type, allergies, emergency contact) must not appear.

**Acceptance Scenarios**:

1. **Given** a logged-in doctor, **When** they visit their profile page, **Then** they see: full name, email, role label ("Doctor"), total consultations conducted, and their most recent visit dates.
2. **Given** a logged-in doctor who has conducted no visits, **When** they view their profile, **Then** the activity section shows "No consultations recorded yet" rather than zeroes or blank.
3. **Given** a logged-in doctor, **When** they view their profile, **Then** they do NOT see any patient medical fields.

---

### User Story 3 - Administrator Views Own Profile (Priority: P3)

An administrator logs in and views their profile. They see their account information alongside a high-level system summary: total registered users, total patients, and total doctors in the platform.

**Why this priority**: Admin profile is operationally lower priority since admins are few, but still needs a meaningful role-aware view distinct from doctor and patient.

**Independent Test**: Log in as an admin user and navigate to the profile page. Admin-specific system stats must appear; clinical data must not appear.

**Acceptance Scenarios**:

1. **Given** a logged-in admin, **When** they visit their profile page, **Then** they see: full name, email, role label ("Administrator"), and system stats: total users, total patients, total doctors.
2. **Given** a logged-in admin, **When** the system has no patients or doctors, **Then** stats show "0" rather than an error or blank.
3. **Given** a logged-in admin, **When** they view their profile, **Then** they do NOT see patient medical fields or doctor consultation records.

---

### User Story 4 - User Edits Own Personal Information (Priority: P2)

Any authenticated user (admin, doctor, or patient) can update their own personal information directly from their profile page: name, phone number, and address. Changes are persisted immediately.

**Why this priority**: Profile editing is expected on any profile page and prevents users from needing a separate settings screen for basic personal info. Applies equally to all roles.

**Independent Test**: Log in as any role, navigate to profile, edit a personal field (e.g., phone number), save, and reload — the updated value must persist.

**Acceptance Scenarios**:

1. **Given** any authenticated user, **When** they update their phone number and save, **Then** the new phone number is shown immediately and persists after page reload.
2. **Given** any authenticated user, **When** they submit an invalid value (e.g., letters in a phone field), **Then** a clear validation message is shown and no change is saved.
3. **Given** any authenticated user, **When** they cancel an edit without saving, **Then** the original values are restored.

---

### Edge Cases

- A user attempting to navigate to another user's profile URL must receive an access-denied response and be redirected — never shown another user's data.
- If profile data fails to load (network error), the page shows an error state with a retry option — not a blank or partially rendered layout.
- A patient with all medical fields null renders gracefully with "Not provided" on every field — no layout breaks.
- An admin viewing the profile while the system has zero records in any category displays "0" counts, not loading states or errors.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST display a profile page accessible to all authenticated users regardless of role.
- **FR-002**: System MUST render role-specific content — the page sections and data shown adapt based on the logged-in user's role (admin, doctor, patient).
- **FR-003**: Patient profiles MUST display the following sections: Personal Information (name, date of birth, gender, phone, address), Medical Information (blood type, allergies, medical notes), Emergency Contact (name, phone), Recent Visits (last 5 visits, newest first).
- **FR-004**: Doctor profiles MUST display the following sections: Personal Information (name, email), Clinical Activity (total consultations, last 3 visit dates).
- **FR-005**: Administrator profiles MUST display the following sections: Personal Information (name, email), System Statistics (total users, total patients, total doctors).
- **FR-006**: System MUST display null or empty fields with a "Not provided" placeholder — never a blank space.
- **FR-007**: System MUST show a meaningful empty state message when a data section has no records (e.g., no visits).
- **FR-008**: System MUST prevent any user from viewing another user's profile — access is always scoped to the authenticated user's own profile.
- **FR-009**: All roles MUST be able to edit their own personal information (name, phone, address) directly from the profile page.
- **FR-010**: System MUST validate edited fields before saving and display clear field-level error messages on invalid input.
- **FR-011**: Patient profiles MUST allow patients to edit their own medical fields. [NEEDS CLARIFICATION: Should patients be able to self-edit medical fields (blood type, allergies, medical notes), or are those fields doctor-managed only and read-only for the patient?]
- **FR-012**: System MUST make the profile page reachable from the authenticated navigation (e.g., avatar dropdown or sidebar).

### Key Entities

- **User**: The authenticated account holder with a role (admin, doctor, patient), name, and email. The profile page is always scoped to the currently logged-in user.
- **Patient**: A user with the patient role. Carries medical metadata: blood type, allergies, medical notes, emergency contact details, date of birth, gender, and address.
- **Visit**: A clinical encounter linked to a patient and a doctor. Surfaced on patient profiles (visit history) and doctor profiles (activity summary).
- **System Statistics**: Aggregated counts (total users, total patients, total doctors) shown on the admin profile. Computed on demand — not a stored entity.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Each role sees only the sections and data relevant to their role — zero cross-role data leakage, verifiable by logging in with each role and confirming absent sections.
- **SC-002**: The profile page fully loads within 2 seconds for all roles under normal conditions.
- **SC-003**: All null or empty fields display a placeholder label — no blank fields visible in any role view, verifiable with an account that has no optional data set.
- **SC-004**: Any attempt to access another user's profile is denied 100% of the time regardless of how the URL is constructed.
- **SC-005**: A developer given the implementation guide produced from this spec can build the feature end-to-end without requiring a clarification meeting — measured by a successful first-attempt implementation.

## Assumptions

- **Self-profile only**: Each user sees only their own profile. Admins access other users through the existing user management area, not this profile page.
- **Recent visits count**: Patient profile shows the last 5 visits, newest first. Doctor activity shows total count and last 3 visit dates.
- **Password change is out of scope**: Changing passwords is a separate security concern handled elsewhere in the application.
- **No new backend endpoints required for read-only data**: All data shown on the profile page is available through existing API endpoints.
- **Profile navigation entry point**: The profile page is accessible via the avatar/user dropdown in the top navigation bar.
- **Edit scope**: All roles can edit name, phone, and address. Whether patients can edit medical fields (FR-011) requires clarification before implementation begins.
