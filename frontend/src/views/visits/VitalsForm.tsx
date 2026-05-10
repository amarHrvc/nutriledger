'use client'

import { useState } from 'react'

import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import CircularProgress from '@mui/material/CircularProgress'
import Dialog from '@mui/material/Dialog'
import DialogActions from '@mui/material/DialogActions'
import DialogContent from '@mui/material/DialogContent'
import DialogTitle from '@mui/material/DialogTitle'
import TextField from '@mui/material/TextField'

import type { VitalSignResource } from './vitals.types'

interface Props {
  patientId: string
  visitId: string
  existing?: VitalSignResource
  onSuccess: () => void
  onClose: () => void
}

interface FieldErrors {
  systolic_bp?: string[]
  diastolic_bp?: string[]
  heart_rate?: string[]
  temperature?: string[]
  weight?: string[]
  height?: string[]
  vitals?: string[]
}

function toField(value: number | string | null | undefined): string {
  if (value === null || value === undefined) return ''
  return String(value)
}

export default function VitalsForm({ patientId, visitId, existing, onSuccess, onClose }: Props) {
  const isEdit = existing !== undefined

  const [systolicBp, setSystolicBp] = useState(() => toField(existing?.attributes.systolicBp))
  const [diastolicBp, setDiastolicBp] = useState(() => toField(existing?.attributes.diastolicBp))
  const [heartRate, setHeartRate] = useState(() => toField(existing?.attributes.heartRate))
  const [temperature, setTemperature] = useState(() => toField(existing?.attributes.temperature))
  const [weight, setWeight] = useState(() => toField(existing?.attributes.weight))
  const [height, setHeight] = useState(() => toField(existing?.attributes.height))

  const [errors, setErrors] = useState<FieldErrors>({})
  const [conflict, setConflict] = useState(false)
  const [formError, setFormError] = useState('')
  const [loading, setLoading] = useState(false)

  const bmiPreview =
    weight && height
      ? (parseFloat(weight) / Math.pow(parseFloat(height) / 100, 2)).toFixed(1)
      : null

  const fieldErr = (key: keyof FieldErrors): string | undefined => errors[key]?.[0]

  const submit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)
    setErrors({})
    setConflict(false)
    setFormError('')

    const payload: Record<string, number | null> = {
      systolic_bp: systolicBp !== '' ? parseFloat(systolicBp) : null,
      diastolic_bp: diastolicBp !== '' ? parseFloat(diastolicBp) : null,
      heart_rate: heartRate !== '' ? parseFloat(heartRate) : null,
      temperature: temperature !== '' ? parseFloat(temperature) : null,
      weight: weight !== '' ? parseFloat(weight) : null,
      height: height !== '' ? parseFloat(height) : null,
    }

    try {
      const res = await fetch(`/api/patients/${patientId}/visits/${visitId}/vitals`, {
        method: isEdit ? 'PATCH' : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      })

      const json = await res.json().catch(() => ({}))

      if (res.status === 409) {
        setConflict(true)
        return
      }

      if (res.status === 422) {
        setErrors(json.errors ?? {})
        return
      }

      if (!res.ok) {
        setFormError(json.message ?? `Server error ${res.status}`)
        return
      }

      onSuccess()
    } catch {
      setFormError(isEdit ? 'Failed to update vital signs.' : 'Failed to record vital signs.')
    } finally {
      setLoading(false)
    }
  }

  return (
    <Dialog open onClose={onClose} maxWidth='sm' fullWidth>
      <DialogTitle>{isEdit ? 'Edit vital signs' : 'Record vital signs'}</DialogTitle>
      <Box component='form' onSubmit={submit} noValidate>
        <DialogContent>
          {formError && <Alert severity='error' sx={{ mb: 2 }}>{formError}</Alert>}
          {conflict && (
            <Alert severity='warning' sx={{ mb: 2 }}>
              Vital signs already recorded for this visit — use Edit to update.
            </Alert>
          )}
          {fieldErr('vitals') && (
            <Alert severity='error' sx={{ mb: 2 }}>{fieldErr('vitals')}</Alert>
          )}

          <Box sx={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 2 }}>
            <TextField
              label='Systolic BP (mmHg)'
              type='number'
              value={systolicBp}
              onChange={e => setSystolicBp(e.target.value)}
              error={!!fieldErr('systolic_bp')}
              helperText={fieldErr('systolic_bp')}
              inputProps={{ min: 1, max: 350, step: 1 }}
            />
            <TextField
              label='Diastolic BP (mmHg)'
              type='number'
              value={diastolicBp}
              onChange={e => setDiastolicBp(e.target.value)}
              error={!!fieldErr('diastolic_bp')}
              helperText={fieldErr('diastolic_bp')}
              inputProps={{ min: 1, max: 250, step: 1 }}
            />
            <TextField
              label='Heart Rate (bpm)'
              type='number'
              value={heartRate}
              onChange={e => setHeartRate(e.target.value)}
              error={!!fieldErr('heart_rate')}
              helperText={fieldErr('heart_rate')}
              inputProps={{ min: 1, max: 350, step: 1 }}
            />
            <TextField
              label='Temperature (°C)'
              type='number'
              value={temperature}
              onChange={e => setTemperature(e.target.value)}
              error={!!fieldErr('temperature')}
              helperText={fieldErr('temperature')}
              inputProps={{ min: 30, max: 45, step: 0.1 }}
            />
            <TextField
              label='Weight (kg)'
              type='number'
              value={weight}
              onChange={e => setWeight(e.target.value)}
              error={!!fieldErr('weight')}
              helperText={fieldErr('weight')}
              inputProps={{ min: 1, max: 500, step: 0.01 }}
            />
            <TextField
              label='Height (cm)'
              type='number'
              value={height}
              onChange={e => setHeight(e.target.value)}
              error={!!fieldErr('height')}
              helperText={bmiPreview ? `BMI preview: ${bmiPreview}` : fieldErr('height')}
              inputProps={{ min: 50, max: 300, step: 0.1 }}
            />
          </Box>
        </DialogContent>

        <DialogActions sx={{ px: 3, pb: 2 }}>
          <Button variant='outlined' onClick={onClose} disabled={loading}>
            Cancel
          </Button>
          <Button
            type='submit'
            variant='contained'
            disabled={loading}
            startIcon={loading ? <CircularProgress size={16} /> : null}
          >
            {isEdit ? 'Save changes' : 'Record'}
          </Button>
        </DialogActions>
      </Box>
    </Dialog>
  )
}
