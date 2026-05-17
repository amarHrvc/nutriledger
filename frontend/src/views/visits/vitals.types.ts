export interface VitalSignFlag {
  field: string
  value: number | string
  threshold?: string
  category?: string
}

export interface VitalSignPreviousVisit {
  visitDate: string
  weight: string | null
  bmi: string | null
  systolicBp: number | null
  diastolicBp: number | null
  weightDelta: number | null
  bmiDelta: number | null
}

export interface VitalSignResourceAttributes {
  systolicBp: number | null
  diastolicBp: number | null
  heartRate: number | null
  temperature: string | null
  weight: string | null
  height: string | null
  bmi: string | null
  bmiCategory: 'normal' | 'underweight' | 'overweight' | 'obese' | null
  flags: VitalSignFlag[]
  visitId: string
  visitDate: string | null
  patientId: string | null
  patientName: string | null
  doctorName: string | null
  previousVisit: VitalSignPreviousVisit | null
  createdAt: string | null
  updatedAt: string | null
}

export interface VitalSignResource {
  id: string
  type: 'vital_sign'
  attributes: VitalSignResourceAttributes
}
