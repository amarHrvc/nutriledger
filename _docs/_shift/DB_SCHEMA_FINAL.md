# DB Schema — Final (Actual Implementation)

Source of truth: `database/migrations/`. This document reflects the actual schema, not the original `database/mvp_schema` planning file.

---

## SE Scope Tables (Groups 1-3)

### `users`
| Column | Type | Notes |
|---|---|---|
| id | bigint unsigned | PK, auto-increment |
| name | varchar(255) | tech debt: should be first_name + last_name — deferred |
| email | varchar(255) | unique |
| email_verified_at | timestamp | nullable |
| password | varchar(255) | |
| role | enum | `admin`, `doktor`, `pacijent` — default `pacijent` |
| two_factor_secret | text | nullable — Fortify 2FA |
| two_factor_recovery_codes | text | nullable |
| two_factor_confirmed_at | timestamp | nullable |
| remember_token | varchar(100) | nullable |
| deleted_at | timestamp | nullable — soft deletes |
| created_at / updated_at | timestamp | |

**Notes:**
- Role values are Bosnian (`doktor`, `pacijent`) — documented in API, kept as-is for SE timeline
- `RoleMiddleware` guards all role-protected routes and is already JSON-aware

---

### `patients`
| Column | Type | Notes |
|---|---|---|
| id | bigint unsigned | PK |
| user_id | bigint unsigned | FK → users.id, unique, cascadeOnDelete |
| first_name | varchar(255) | |
| last_name | varchar(255) | |
| date_of_birth | date | |
| gender | enum | `M`, `F` |
| phone | varchar(255) | |
| address | varchar(255) | nullable |
| city | varchar(255) | nullable |
| postal_code | varchar(255) | nullable |
| emergency_contact_name | varchar(255) | |
| emergency_contact_phone | varchar(255) | |
| blood_type | enum | `A+`, `A-`, `B+`, `B-`, `AB+`, `AB-`, `O+`, `O-` — nullable |
| allergies | text | nullable — tech debt: should be separate table, deferred |
| medical_notes | text | nullable |
| deleted_at | timestamp | nullable — soft deletes |
| created_at / updated_at | timestamp | |

---

### `patient_socioeconomic`
| Column | Type | Notes |
|---|---|---|
| id | bigint unsigned | PK |
| patient_id | bigint unsigned | FK → patients.id, unique, cascadeOnDelete |
| marital_status | enum | `single`, `married`, `divorced`, `widowed`, `separated`, `other` — nullable |
| number_of_dependents | integer | nullable |
| living_arrangement | enum | `alone`, `with_family`, `with_partner`, `shared_housing`, `care_facility`, `other` — nullable |
| employment_status | enum | `employed_full_time`, `employed_part_time`, `self_employed`, `unemployed`, `retired`, `student`, `unable_to_work`, `other` — nullable |
| occupation | varchar(255) | nullable |
| income_level | enum | `low`, `lower_middle`, `middle`, `upper_middle`, `high` — nullable |
| has_health_insurance | boolean | default false |
| education_level | enum | `no_formal`, `primary`, `secondary`, `vocational`, `bachelors`, `masters`, `doctorate`, `other` — nullable |
| smoking_status | enum | `never`, `former`, `current_light`, `current_heavy` — nullable |
| alcohol_consumption | enum | `none`, `occasional`, `moderate`, `heavy` — nullable |
| physical_activity_level | enum | `sedentary`, `lightly_active`, `moderately_active`, `very_active` — nullable |
| has_family_support | boolean | default false |
| has_caregiver | boolean | default false |
| transportation_access | enum | `own_vehicle`, `public_transport`, `rideshare`, `walking`, `limited`, `none` — nullable |
| food_security_status | enum | `food_secure`, `marginally_secure`, `food_insecure`, `severely_insecure` — nullable |
| dietary_restrictions_cultural | text | nullable |
| additional_notes | text | nullable |
| created_at / updated_at | timestamp | |

---

### `visits`
| Column | Type | Notes |
|---|---|---|
| id | bigint unsigned | PK |
| patient_id | bigint unsigned | FK → patients.id, cascadeOnDelete |
| doctor_id | bigint unsigned | FK → users.id, cascadeOnDelete |
| date | date | |
| notes | text | nullable |
| created_at / updated_at | timestamp | |

**SE scope note:** Visit detail = date + doctor + notes only. No soft deletes on visits (hard delete acceptable for MVP).

---

## Infrastructure Tables (Framework-managed)

- `password_reset_tokens` — Fortify password reset
- `sessions` — session store
- `cache` — Laravel cache
- `jobs` / `job_batches` / `failed_jobs` — queue system
- `personal_access_tokens` — added by Sanctum install

---

## Post-SE Tables (Groups 4-11, not implemented)

| Table | Group | Purpose |
|---|---|---|
| `vital_signs` | 4 | BP, heart rate, temperature, weight, height per visit |
| `medications` | 5 | Prescribed medications per visit |
| `labs` | 5 | Lab results per visit |
| `recommendations` | 5 | Doctor recommendations per visit |
| `reminders` | 6 | Follow-up reminders per patient |
| `body_measurements` | 7 | Detailed body composition data |
| `food_preferences` | 8 | Dietary preferences per patient |
| `physical_activity` | 9 | Activity tracking per patient |

---

## Tech Debt Flags

| Item | Status | Risk |
|---|---|---|
| `users.name` should be `first_name` + `last_name` | Deferred — migration risk during SE timeline | Medium — requires data migration + factory/seeder updates |
| `patients.allergies` text → separate `allergies` table | Deferred post-SE | Low — isolated change |
| Role enum values (`doktor`, `pacijent`) in Bosnian | Document in API, keep as-is | None — API consumers just need to know accepted values |

---

## Relationships Summary

```
User (1) ──────────── (1) Patient
Patient (1) ──────── (1) PatientSocioeconomic
Patient (1) ──────── (many) Visit
User/Doctor (1) ──── (many) Visit  [via doctor_id]
```

---

## ER Diagram Coverage

The Class Diagram delivered in M1 covers these 4 entities with all FK relationships. This file is the textual specification. Both together satisfy SE documentation requirements.
