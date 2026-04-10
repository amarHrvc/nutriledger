# data-model.md

## Entities

### Patient
Fields:
- id: bigint
- user_id: bigint (unique, FK users.id)
- first_name: string (required)
- last_name: string (required)
- date_of_birth: date (required)
- gender: enum ['M','F'] (required)
- phone: string (required)
- address: string (nullable)
- city: string (nullable)
- postal_code: string (nullable)
- emergency_contact_name: string (required)
- emergency_contact_phone: string (required)
- blood_type: enum ['A+','A-','B+','B-','AB+','AB-','O+','O-'] (nullable)
- allergies: text (nullable)
- medical_notes: text (nullable)
- deleted_at: timestamp (soft deletes)
- created_at: timestamp
- updated_at: timestamp

Indexes:
- unique index on user_id
- idx_patient_name on (last_name, first_name)
- idx_patient_deleted_at on (deleted_at)

Relationships:
- belongsTo User
- hasOne PatientSocioeconomic
- hasMany Visits (out of scope)

### PatientSocioeconomic
Fields:
- id: bigint
- patient_id: bigint (unique, FK patients.id)
- marital_status: enum [single, married, divorced, widowed, separated, other]
- number_of_dependents: int (nullable)
- living_arrangement: enum (nullable)
- employment_status: enum [employed_full_time, employed_part_time, self_employed, unemployed, retired, student, unable_to_work, other]
- occupation: string (nullable)
- income_level: enum [low, lower_middle, middle, upper_middle, high]
- has_health_insurance: boolean (default false)
- education_level: enum (nullable)
- smoking_status: enum [never, former, current_light, current_heavy]
- alcohol_consumption: enum [none, occasional, moderate, heavy]
- physical_activity_level: enum [sedentary, lightly_active, moderately_active, very_active]
- has_family_support: boolean (default false)
- has_caregiver: boolean (default false)
- transportation_access: enum (nullable)
- food_security_status: enum [food_secure, food_insecure, unsure]
- dietary_restrictions_cultural: text (nullable)
- additional_notes: text (nullable)
- created_at: timestamp
- updated_at: timestamp
- deleted_at: timestamp (soft delete)

Relationships:
- belongsTo Patient

