import {
  StorePatientRequestSocioeconomicMaritalStatus,
  StorePatientRequestSocioeconomicLivingArrangement,
  StorePatientRequestSocioeconomicEmploymentStatus,
  StorePatientRequestSocioeconomicIncomeLevel,
  StorePatientRequestSocioeconomicEducationLevel,
  StorePatientRequestSocioeconomicSmokingStatus,
  StorePatientRequestSocioeconomicAlcoholConsumption,
  StorePatientRequestSocioeconomicPhysicalActivityLevel,
  StorePatientRequestSocioeconomicTransportationAccess,
  StorePatientRequestSocioeconomicFoodSecurityStatus,
} from '@/api/generated/nutriBaseAPI.schemas'

const toLabel = (key: string): string =>
  key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())

const makeLabels = (enumObj: Record<string, string>, overrides: Record<string, string> = {}): Record<string, string> =>
  Object.fromEntries(Object.keys(enumObj).map(k => [k, overrides[k] ?? toLabel(k)]))

export const MARITAL_STATUS_LABELS = makeLabels(StorePatientRequestSocioeconomicMaritalStatus)

export const LIVING_ARRANGEMENT_LABELS = makeLabels(StorePatientRequestSocioeconomicLivingArrangement)

export const EMPLOYMENT_STATUS_LABELS = makeLabels(StorePatientRequestSocioeconomicEmploymentStatus, {
  unable_to_work: 'Unable to Work',
})

export const INCOME_LEVEL_LABELS = makeLabels(StorePatientRequestSocioeconomicIncomeLevel)

export const EDUCATION_LEVEL_LABELS = makeLabels(StorePatientRequestSocioeconomicEducationLevel, {
  no_formal: 'No Formal Education',
  bachelors: "Bachelor's",
  masters: "Master's",
})

export const SMOKING_STATUS_LABELS = makeLabels(StorePatientRequestSocioeconomicSmokingStatus, {
  current_light: 'Current (Light)',
  current_heavy: 'Current (Heavy)',
})

export const ALCOHOL_CONSUMPTION_LABELS = makeLabels(StorePatientRequestSocioeconomicAlcoholConsumption)

export const PHYSICAL_ACTIVITY_LABELS = makeLabels(StorePatientRequestSocioeconomicPhysicalActivityLevel)

export const TRANSPORTATION_ACCESS_LABELS = makeLabels(StorePatientRequestSocioeconomicTransportationAccess, {
  own_vehicle: 'Own Vehicle',
  public_transport: 'Public Transport',
})

export const FOOD_SECURITY_LABELS = makeLabels(StorePatientRequestSocioeconomicFoodSecurityStatus)
