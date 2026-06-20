'use client'

import { useState } from 'react'

import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Divider from '@mui/material/Divider'
import Grid from '@mui/material/Grid'
import Stack from '@mui/material/Stack'
import TextField from '@mui/material/TextField'
import Typography from '@mui/material/Typography'

import type { DietDay, DietPlan } from './types'

interface Props {
  plan: DietPlan
  patientId: number
  onSave: (updated: DietPlan) => void
  onCancel: () => void
}

interface FormErrors {
  rationale?: string
  daily_calories?: string
  'nutritional_goals.protein_g'?: string
  'nutritional_goals.carbs_g'?: string
  'nutritional_goals.fat_g'?: string
  [key: string]: string | undefined
}

export default function DietPlanEditForm({ plan, patientId, onSave, onCancel }: Props) {
  const [rationale, setRationale] = useState(plan.rationale ?? '')
  const [dailyCalories, setDailyCalories] = useState(String(plan.dailyCalories ?? ''))
  const [proteinG, setProteinG] = useState(String(plan.nutritionalGoals?.protein_g ?? ''))
  const [carbsG, setCarbsG] = useState(String(plan.nutritionalGoals?.carbs_g ?? ''))
  const [fatG, setFatG] = useState(String(plan.nutritionalGoals?.fat_g ?? ''))

  const [days, setDays] = useState<DietDay[]>(
    plan.days ?? [
      { day: 'Monday', breakfast: '', lunch: '', dinner: '', snack: '' },
      { day: 'Tuesday', breakfast: '', lunch: '', dinner: '', snack: '' },
      { day: 'Wednesday', breakfast: '', lunch: '', dinner: '', snack: '' },
      { day: 'Thursday', breakfast: '', lunch: '', dinner: '', snack: '' },
      { day: 'Friday', breakfast: '', lunch: '', dinner: '', snack: '' },
      { day: 'Saturday', breakfast: '', lunch: '', dinner: '', snack: '' },
      { day: 'Sunday', breakfast: '', lunch: '', dinner: '', snack: '' },
    ]
  )

  const [warningsRaw, setWarningsRaw] = useState((plan.warnings ?? []).join('\n'))
  const [isDirty, setIsDirty] = useState(false)
  const [saving, setSaving] = useState(false)
  const [errors, setErrors] = useState<FormErrors>({})
  const [submitError, setSubmitError] = useState<string | null>(null)

  const markDirty = () => setIsDirty(true)

  const handleDayChange = (index: number, field: keyof DietDay, value: string) => {
    setDays(prev => prev.map((d, i) => i === index ? { ...d, [field]: value } : d))
    markDirty()
  }

  const handleCancel = () => {
    if (isDirty && !window.confirm('You have unsaved changes. Discard them?')) return
    onCancel()
  }

  const handleSubmit = async () => {
    setSaving(true)
    setErrors({})
    setSubmitError(null)

    const payload: Record<string, unknown> = {
      rationale: rationale || null,
      daily_calories: dailyCalories ? Number(dailyCalories) : null,
      nutritional_goals: {
        protein_g: proteinG ? Number(proteinG) : null,
        carbs_g: carbsG ? Number(carbsG) : null,
        fat_g: fatG ? Number(fatG) : null,
      },
      days,
      warnings: warningsRaw.trim()
        ? warningsRaw.split('\n').map(w => w.trim()).filter(Boolean)
        : [],
    }

    try {
      const res = await fetch(`/api/patients/${patientId}/diet-plans/${plan.id}`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      })

      const json = await res.json()

      if (res.status === 200) {
        onSave(json.data?.diet_plan as DietPlan)
      } else if (res.status === 422) {
        setErrors(json.errors ?? {})
        setSubmitError('Please fix the validation errors below.')
      } else {
        setSubmitError(json.message ?? 'Failed to save changes.')
      }
    } catch {
      setSubmitError('Network error. Please try again.')
    } finally {
      setSaving(false)
    }
  }

  return (
    <Box>
      <Typography variant='subtitle1' fontWeight={700} sx={{ mb: 2 }}>
        Edit Diet Plan
      </Typography>

      {submitError && (
        <Alert severity='error' sx={{ mb: 2 }}>{submitError}</Alert>
      )}

      <Stack spacing={2}>
        <TextField
          label='Rationale'
          multiline
          minRows={2}
          fullWidth
          value={rationale}
          onChange={e => { setRationale(e.target.value); markDirty() }}
          error={!!errors.rationale}
          helperText={errors.rationale}
        />

        <Grid container spacing={2}>
          <Grid size={{ xs: 12, sm: 3 }}>
            <TextField
              label='Daily Calories (kcal)'
              type='number'
              fullWidth
              value={dailyCalories}
              onChange={e => { setDailyCalories(e.target.value); markDirty() }}
              error={!!errors.daily_calories}
              helperText={errors.daily_calories}
            />
          </Grid>
          <Grid size={{ xs: 12, sm: 3 }}>
            <TextField
              label='Protein (g)'
              type='number'
              fullWidth
              value={proteinG}
              onChange={e => { setProteinG(e.target.value); markDirty() }}
              error={!!errors['nutritional_goals.protein_g']}
              helperText={errors['nutritional_goals.protein_g']}
            />
          </Grid>
          <Grid size={{ xs: 12, sm: 3 }}>
            <TextField
              label='Carbs (g)'
              type='number'
              fullWidth
              value={carbsG}
              onChange={e => { setCarbsG(e.target.value); markDirty() }}
              error={!!errors['nutritional_goals.carbs_g']}
              helperText={errors['nutritional_goals.carbs_g']}
            />
          </Grid>
          <Grid size={{ xs: 12, sm: 3 }}>
            <TextField
              label='Fat (g)'
              type='number'
              fullWidth
              value={fatG}
              onChange={e => { setFatG(e.target.value); markDirty() }}
              error={!!errors['nutritional_goals.fat_g']}
              helperText={errors['nutritional_goals.fat_g']}
            />
          </Grid>
        </Grid>

        <Divider />
        <Typography variant='subtitle2'>Meal Plan (7 days)</Typography>

        {days.map((day, i) => (
          <Grid container spacing={1} key={day.day} alignItems='center'>
            <Grid size={{ xs: 12, sm: 1 }}>
              <Typography variant='body2' fontWeight={600}>{day.day}</Typography>
            </Grid>
            {(['breakfast', 'lunch', 'dinner', 'snack'] as Array<keyof DietDay>).map(meal => (
              meal !== 'day' && (
                <Grid size={{ xs: 12, sm: 2.75 }} key={meal}>
                  <TextField
                    label={meal.charAt(0).toUpperCase() + meal.slice(1)}
                    size='small'
                    fullWidth
                    value={day[meal]}
                    onChange={e => handleDayChange(i, meal, e.target.value)}
                  />
                </Grid>
              )
            ))}
          </Grid>
        ))}

        <Divider />

        <TextField
          label='Warnings (one per line)'
          multiline
          minRows={2}
          fullWidth
          value={warningsRaw}
          onChange={e => { setWarningsRaw(e.target.value); markDirty() }}
          helperText='Each line becomes one warning chip'
        />

        <Box sx={{ display: 'flex', gap: 1, justifyContent: 'flex-end' }}>
          <Button variant='outlined' onClick={handleCancel} disabled={saving}>
            Cancel
          </Button>
          <Button variant='contained' onClick={handleSubmit} disabled={saving}>
            {saving ? 'Saving…' : 'Save Changes'}
          </Button>
        </Box>
      </Stack>
    </Box>
  )
}
