// Socioeconomic types used by the frontend
// SocioeconomicData mirrors the GET response (camelCase)
// SocioeconomicFormPayload is the PATCH payload (snake_case) - all fields optional
// NOTE: Do NOT derive SocioeconomicFormPayload from generated UpdatePatientRequestSocioeconomic until Epic 8 is completed.

export interface SocioeconomicData {
  maritalStatus?: string | null
  numberOfDependents?: number | null
  livingArrangement?: string | null
  employmentStatus?: string | null
  occupation?: string | null
  incomeLevel?: string | null
  hasHealthInsurance?: boolean | null
  educationLevel?: string | null
  smokingStatus?: string | null
  alcoholConsumption?: string | null
  physicalActivityLevel?: string | null
  hasFamilySupport?: boolean | null
  hasCaregiver?: boolean | null
  transportationAccess?: string | null
  foodSecurityStatus?: string | null
  dietaryRestrictionsCultural?: string | null
  additionalNotes?: string | null
}

export interface SocioeconomicFormPayload {
  marital_status?: string | null
  number_of_dependents?: number | null
  living_arrangement?: string | null
  employment_status?: string | null
  occupation?: string | null
  income_level?: string | null
  has_health_insurance?: boolean | null
  education_level?: string | null
  smoking_status?: string | null
  alcohol_consumption?: string | null
  physical_activity_level?: string | null
  has_family_support?: boolean | null
  has_caregiver?: boolean | null
  transportation_access?: string | null
  food_security_status?: string | null
  dietary_restrictions_cultural?: string | null
  additional_notes?: string | null
}
