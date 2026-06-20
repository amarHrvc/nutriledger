# Data Model: Patient Socioeconomic Profile (013)

**Branch**: `013-socioeconomic-profile`  
**Date**: 2026-05-17

---

## Backend: PatientSocioeconomic Model

Already exists at `backend/app/Models/PatientSocioeconomic.php`. No changes to the model.

### Database columns (`patient_socioeconomic` table)

| Column | Type | Nullable | Notes |
|---|---|---|---|
| `id` | bigint PK | no | |
| `patient_id` | FK → patients | no | unique (1:1) |
| `marital_status` | enum | yes | single, married, divorced, widowed, separated, other |
| `number_of_dependents` | integer | yes | 0 is a valid value |
| `living_arrangement` | enum | yes | alone, with_family, with_partner, shared_housing, care_facility, other |
| `employment_status` | enum | yes | employed_full_time, employed_part_time, self_employed, unemployed, retired, student, unable_to_work, other |
| `occupation` | string | yes | free text |
| `income_level` | enum | yes | low, lower_middle, middle, upper_middle, high |
| `has_health_insurance` | boolean | no | default false |
| `education_level` | enum | yes | no_formal, primary, secondary, vocational, bachelors, masters, doctorate, other |
| `smoking_status` | enum | yes | never, former, current_light, current_heavy |
| `alcohol_consumption` | enum | yes | none, occasional, moderate, heavy |
| `physical_activity_level` | enum | yes | sedentary, lightly_active, moderately_active, very_active |
| `has_family_support` | boolean | no | default false |
| `has_caregiver` | boolean | no | default false |
| `transportation_access` | enum | yes | own_vehicle, public_transport, rideshare, walking, limited, none |
| `food_security_status` | enum | yes | food_secure, marginally_secure, food_insecure, severely_insecure |
| `dietary_restrictions_cultural` | text | yes | free text |
| `additional_notes` | text | yes | free text |

---

## Backend: PatientResource change (the one BE task)

**File**: `backend/app/Http/Resources/Api/PatientResource.php`

Add a `socioeconomicData` key to the `attributes` block using `whenLoaded`:

```php
'socioeconomicData' => $this->whenLoaded('socioeconomic',
    fn() => $this->socioeconomic
        ? (new PatientSocioeconomicResource($this->socioeconomic))->toArray($request)
        : null
),
```

This embeds the full `PatientSocioeconomicResource` attributes inline when the `socioeconomic` relation is loaded (which `PatientController::show()` already does via `$patient->load(['socioeconomic', 'user'])`).

The `PatientController::index()` also loads socioeconomic when `format !== 'summary'`. The embedded data will appear there too — this is fine and additive.

---

## Frontend: TypeScript types

### `SocioeconomicData` (inferred from API response, to be defined in `socioeconomic/types.ts`)

```typescript
// src/views/patients/socioeconomic/types.ts

export interface SocioeconomicData {
  maritalStatus: string | null
  numberOfDependents: number | null
  livingArrangement: string | null
  employmentStatus: string | null
  occupation: string | null
  incomeLevel: string | null
  hasHealthInsurance: boolean
  educationLevel: string | null
  smokingStatus: string | null
  alcoholConsumption: string | null
  physicalActivityLevel: string | null
  hasFamilySupport: boolean
  hasCaregiver: boolean
  transportationAccess: string | null
  foodSecurityStatus: string | null
  dietaryRestrictionsCultural: string | null
  additionalNotes: string | null
}

// Used by form components (snake_case to match API payload)
export interface SocioeconomicFormPayload {
  marital_status?: string | null
  number_of_dependents?: number | null
  living_arrangement?: string | null
  employment_status?: string | null
  occupation?: string | null
  income_level?: string | null
  has_health_insurance?: boolean
  education_level?: string | null
  smoking_status?: string | null
  alcohol_consumption?: string | null
  physical_activity_level?: string | null
  has_family_support?: boolean
  has_caregiver?: boolean
  transportation_access?: string | null
  food_security_status?: string | null
  dietary_restrictions_cultural?: string | null
  additional_notes?: string | null
}
```

### Enum label maps (`socioeconomic/labels.ts`)

```typescript
export const MARITAL_STATUS_LABELS: Record<string, string> = {
  single: 'Single', married: 'Married', divorced: 'Divorced',
  widowed: 'Widowed', separated: 'Separated', other: 'Other',
}

export const LIVING_ARRANGEMENT_LABELS: Record<string, string> = {
  alone: 'Alone', with_family: 'With Family', with_partner: 'With Partner',
  shared_housing: 'Shared Housing', care_facility: 'Care Facility', other: 'Other',
}

export const EMPLOYMENT_STATUS_LABELS: Record<string, string> = {
  employed_full_time: 'Employed Full Time', employed_part_time: 'Employed Part Time',
  self_employed: 'Self Employed', unemployed: 'Unemployed', retired: 'Retired',
  student: 'Student', unable_to_work: 'Unable to Work', other: 'Other',
}

export const INCOME_LEVEL_LABELS: Record<string, string> = {
  low: 'Low', lower_middle: 'Lower Middle', middle: 'Middle',
  upper_middle: 'Upper Middle', high: 'High',
}

export const EDUCATION_LEVEL_LABELS: Record<string, string> = {
  no_formal: 'No Formal Education', primary: 'Primary', secondary: 'Secondary',
  vocational: 'Vocational', bachelors: 'Bachelor\'s', masters: 'Master\'s',
  doctorate: 'Doctorate', other: 'Other',
}

export const SMOKING_STATUS_LABELS: Record<string, string> = {
  never: 'Never', former: 'Former Smoker',
  current_light: 'Current (Light)', current_heavy: 'Current (Heavy)',
}

export const ALCOHOL_CONSUMPTION_LABELS: Record<string, string> = {
  none: 'None', occasional: 'Occasional', moderate: 'Moderate', heavy: 'Heavy',
}

export const PHYSICAL_ACTIVITY_LABELS: Record<string, string> = {
  sedentary: 'Sedentary', lightly_active: 'Lightly Active',
  moderately_active: 'Moderately Active', very_active: 'Very Active',
}

export const TRANSPORTATION_ACCESS_LABELS: Record<string, string> = {
  own_vehicle: 'Own Vehicle', public_transport: 'Public Transport',
  rideshare: 'Rideshare', walking: 'Walking', limited: 'Limited', none: 'None',
}

export const FOOD_SECURITY_LABELS: Record<string, string> = {
  food_secure: 'Food Secure', marginally_secure: 'Marginally Secure',
  food_insecure: 'Food Insecure', severely_insecure: 'Severely Insecure',
}
```

---

## Frontend: Component tree

```
views/patients/socioeconomic/
├── types.ts                     # SocioeconomicData, SocioeconomicFormPayload
├── labels.ts                    # All enum → label maps
├── SocioeconomicTab.tsx         # Patient detail tab — reads data, shows Edit button
├── SocioeconomicSection.tsx     # A single display section (title + grid of InfoRows)
└── SocioeconomicFields.tsx      # Shared 17-field form controls (used by form + edit dialog)

views/patients/
├── PatientForm.tsx              # MODIFIED — adds Accordion with <SocioeconomicFields>
├── PatientEditForm.tsx          # MODIFIED — adds Accordion with <SocioeconomicFields>
└── patient-right/
    └── index.tsx                # MODIFIED — adds Socioeconomic tab
```

---

## API payload: PATCH /api/patients/{id}

Existing endpoint, existing behaviour. The `socioeconomic` key is added as a nested object:

```json
{
  "socioeconomic": {
    "marital_status": "married",
    "employment_status": "employed_full_time",
    "has_health_insurance": true,
    "number_of_dependents": 2
  }
}
```

All keys are optional. Keys not present are left unchanged by the backend service.

## Validation rules (backend, already implemented)

All socioeconomic fields are optional (`nullable`). The `number_of_dependents` field accepts integers ≥ 0. Enum fields must match their allowed values or be null.
