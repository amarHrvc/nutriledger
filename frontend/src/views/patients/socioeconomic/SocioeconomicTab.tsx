'use client'

import React from 'react'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Grid from '@mui/material/Grid'
import Typography from '@mui/material/Typography'
import Dialog from '@mui/material/Dialog'
import DialogTitle from '@mui/material/DialogTitle'
import DialogContent from '@mui/material/DialogContent'
import DialogActions from '@mui/material/DialogActions'
import CircularProgress from '@mui/material/CircularProgress'
import Alert from '@mui/material/Alert'

import { useAuth } from '@/context/AuthContext'
import type { PatientResource, PatientResourceAttributes } from '@/api/generated/nutriBaseAPI.schemas'
import type { SocioeconomicData } from './types'
import SocioeconomicSection from './SocioeconomicSection'
import SocioeconomicFields from './SocioeconomicFields'
import type { SocioeconomicFormPayload } from './types'
import type { SectionRow } from './SocioeconomicSection'
import {
  MARITAL_STATUS_LABELS,
  LIVING_ARRANGEMENT_LABELS,
  EMPLOYMENT_STATUS_LABELS,
  INCOME_LEVEL_LABELS,
  EDUCATION_LEVEL_LABELS,
  SMOKING_STATUS_LABELS,
  ALCOHOL_CONSUMPTION_LABELS,
  PHYSICAL_ACTIVITY_LABELS,
  TRANSPORTATION_ACCESS_LABELS,
  FOOD_SECURITY_LABELS,
} from './labels'

function lbl(map: Record<string, string>, val: any) {
  return val ? (map[val] ?? val) : null
}

function mapAttributesToForm(attrs?: SocioeconomicData | null): SocioeconomicFormPayload {
  if (!attrs) return {}
  return {
    marital_status: attrs.maritalStatus ?? null,
    number_of_dependents: attrs.numberOfDependents ?? null,
    living_arrangement: attrs.livingArrangement ?? null,
    employment_status: attrs.employmentStatus ?? null,
    occupation: attrs.occupation ?? null,
    income_level: attrs.incomeLevel ?? null,
    has_health_insurance: attrs.hasHealthInsurance ?? null,
    education_level: attrs.educationLevel ?? null,
    smoking_status: attrs.smokingStatus ?? null,
    alcohol_consumption: attrs.alcoholConsumption ?? null,
    physical_activity_level: attrs.physicalActivityLevel ?? null,
    has_family_support: attrs.hasFamilySupport ?? null,
    has_caregiver: attrs.hasCaregiver ?? null,
    transportation_access: attrs.transportationAccess ?? null,
    food_security_status: attrs.foodSecurityStatus ?? null,
    dietary_restrictions_cultural: attrs.dietaryRestrictionsCultural ?? null,
    additional_notes: attrs.additionalNotes ?? null,
  }
}

export default function SocioeconomicTab({ patient }: { patient: PatientResource }) {
  const { user } = useAuth()
  const canEdit = user?.role === 'admin' || user?.role === 'doktor'

  const attrs = patient.attributes as PatientResourceAttributes & {
    socioeconomicData?: { type: string; id: string; attributes: SocioeconomicData } | null
  }

  const data = attrs.socioeconomicData?.attributes ?? null

  const demographics: SectionRow[] = [
    { label: 'Marital status', value: lbl(MARITAL_STATUS_LABELS, data?.maritalStatus) },
    { label: 'Number of dependents', value: data?.numberOfDependents ?? null },
    { label: 'Living arrangement', value: lbl(LIVING_ARRANGEMENT_LABELS, data?.livingArrangement) },
  ]

  const economic: SectionRow[] = [
    { label: 'Employment status', value: lbl(EMPLOYMENT_STATUS_LABELS, data?.employmentStatus) },
    { label: 'Occupation', value: data?.occupation ?? null },
    { label: 'Income level', value: lbl(INCOME_LEVEL_LABELS, data?.incomeLevel) },
    { label: 'Has health insurance', value: data?.hasHealthInsurance ?? null, boolean: true },
  ]

  const lifestyle: SectionRow[] = [
    { label: 'Education level', value: lbl(EDUCATION_LEVEL_LABELS, data?.educationLevel) },
    { label: 'Smoking status', value: lbl(SMOKING_STATUS_LABELS, data?.smokingStatus) },
    { label: 'Alcohol consumption', value: lbl(ALCOHOL_CONSUMPTION_LABELS, data?.alcoholConsumption) },
    { label: 'Physical activity', value: lbl(PHYSICAL_ACTIVITY_LABELS, data?.physicalActivityLevel) },
  ]

  const support: SectionRow[] = [
    { label: 'Has family support', value: data?.hasFamilySupport ?? null, boolean: true },
    { label: 'Has caregiver', value: data?.hasCaregiver ?? null, boolean: true },
    { label: 'Transportation access', value: lbl(TRANSPORTATION_ACCESS_LABELS, data?.transportationAccess) },
  ]

  const food: SectionRow[] = [
    { label: 'Food security', value: lbl(FOOD_SECURITY_LABELS, data?.foodSecurityStatus) },
    { label: 'Dietary restrictions (cultural)', value: data?.dietaryRestrictionsCultural ?? null },
    { label: 'Additional notes', value: data?.additionalNotes ?? null },
  ]

  // Dialog state
  const [editOpen, setEditOpen] = React.useState(false)
  const [formData, setFormData] = React.useState<SocioeconomicFormPayload>(() => mapAttributesToForm(data))
  const [saving, setSaving] = React.useState(false)
  const [saveError, setSaveError] = React.useState<string | null>(null)

  const handleSave = async () => {
    setSaving(true)
    setSaveError(null)
    try {
      const res = await fetch(`/api/patients/${patient.id}`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ socioeconomic: formData }),
      })
      if (!res.ok) {
        const err = await res.json().catch(() => ({}))
        throw new Error(err?.message ?? `${res.status} ${res.statusText}`)
      }
      setEditOpen(false)
      window.dispatchEvent(new CustomEvent('patients:changed'))
    } catch (err: any) {
      setSaveError(err?.message ?? 'Save failed')
    } finally {
      setSaving(false)
    }
  }

  return (
    <>
      <Grid container spacing={2}>
        <Grid size={12}>
          <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
            <Typography variant='h6'>Socioeconomic Profile</Typography>
            {canEdit && (
              <Button
                variant='outlined'
                onClick={() => { setFormData(mapAttributesToForm(data)); setSaveError(null); setEditOpen(true) }}
              >
                Edit
              </Button>
            )}
          </Box>
        </Grid>

        <Grid size={{ xs: 12, md: 6 }}>
          <SocioeconomicSection title='Demographics & Social' rows={demographics} />
        </Grid>

        <Grid size={{ xs: 12, md: 6 }}>
          <SocioeconomicSection title='Economic' rows={economic} />
        </Grid>

        <Grid size={{ xs: 12, md: 6 }}>
          <SocioeconomicSection title='Lifestyle' rows={lifestyle} />
        </Grid>

        <Grid size={{ xs: 12, md: 6 }}>
          <SocioeconomicSection title='Support Systems' rows={support} />
        </Grid>

        <Grid size={12}>
          <SocioeconomicSection title='Food Security' rows={food} />
        </Grid>
      </Grid>

      <Dialog open={editOpen} onClose={() => { if (!saving) setEditOpen(false) }} maxWidth='md' fullWidth>
        <DialogTitle>Edit Socioeconomic Profile</DialogTitle>
        <DialogContent>
          {saveError && <Alert severity='error'>{saveError}</Alert>}
          <SocioeconomicFields value={formData} onChange={setFormData} />
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setEditOpen(false)} disabled={saving}>Cancel</Button>
          <Button onClick={handleSave} disabled={saving} variant='contained' startIcon={saving ? <CircularProgress size={16} /> : null}>
            Save
          </Button>
        </DialogActions>
      </Dialog>
    </>
  )
}
