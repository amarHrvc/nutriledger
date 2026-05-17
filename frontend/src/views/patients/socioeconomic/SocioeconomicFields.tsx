'use client'

import Grid from '@mui/material/Grid'
import Typography from '@mui/material/Typography'
import Divider from '@mui/material/Divider'
import FormControl from '@mui/material/FormControl'
import InputLabel from '@mui/material/InputLabel'
import Select from '@mui/material/Select'
import MenuItem from '@mui/material/MenuItem'
import TextField from '@mui/material/TextField'
import Switch from '@mui/material/Switch'
import FormControlLabel from '@mui/material/FormControlLabel'
import Box from '@mui/material/Box'

import type { SocioeconomicFormPayload } from './types'
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

interface Props {
  value: SocioeconomicFormPayload
  onChange: (data: SocioeconomicFormPayload) => void
  errors?: Record<string, string[]>
}

export default function SocioeconomicFields({ value, onChange, errors = {} }: Props) {
  const update = (key: keyof SocioeconomicFormPayload, val: unknown) => {
    onChange({ ...value, [key]: val })
  }

  const fieldError = (key: string) => errors[key]?.[0]

  return (
    <Box>
      <Grid container spacing={2}>
        <Grid size={12}>
          <Typography variant='subtitle2'>Household</Typography>
          <Divider sx={{ mb: 2 }} />
        </Grid>

        <Grid size={{ xs: 12, sm: 6 }}>
          <FormControl fullWidth>
            <InputLabel>Marital status</InputLabel>
            <Select
              value={value.marital_status ?? ''}
              label='Marital status'
              onChange={(e) => update('marital_status', (e.target.value as string) || null)}
            >
              <MenuItem value=''>— Not specified —</MenuItem>
              {Object.entries(MARITAL_STATUS_LABELS).map(([k, v]) => (
                <MenuItem key={k} value={k}>{v}</MenuItem>
              ))}
            </Select>
          </FormControl>
        </Grid>

        <Grid size={{ xs: 12, sm: 6 }}>
          <TextField
            label='Number of dependents'
            type='number'
            slotProps={{ htmlInput: { min: 0 } }}
            value={value.number_of_dependents ?? ''}
            onChange={(e) => update('number_of_dependents', e.target.value === '' ? null : parseInt(e.target.value, 10))}
            fullWidth
            error={!!fieldError('number_of_dependents')}
            helperText={fieldError('number_of_dependents')}
          />
        </Grid>

        <Grid size={{ xs: 12, sm: 6 }}>
          <FormControl fullWidth>
            <InputLabel>Living arrangement</InputLabel>
            <Select
              value={value.living_arrangement ?? ''}
              label='Living arrangement'
              onChange={(e) => update('living_arrangement', (e.target.value as string) || null)}
            >
              <MenuItem value=''>— Not specified —</MenuItem>
              {Object.entries(LIVING_ARRANGEMENT_LABELS).map(([k, v]) => (
                <MenuItem key={k} value={k}>{v}</MenuItem>
              ))}
            </Select>
          </FormControl>
        </Grid>

        <Grid size={12}>
          <Typography variant='subtitle2'>Work & Income</Typography>
          <Divider sx={{ mb: 2 }} />
        </Grid>

        <Grid size={{ xs: 12, sm: 6 }}>
          <FormControl fullWidth>
            <InputLabel>Employment status</InputLabel>
            <Select
              value={value.employment_status ?? ''}
              label='Employment status'
              onChange={(e) => update('employment_status', (e.target.value as string) || null)}
            >
              <MenuItem value=''>— Not specified —</MenuItem>
              {Object.entries(EMPLOYMENT_STATUS_LABELS).map(([k, v]) => (
                <MenuItem key={k} value={k}>{v}</MenuItem>
              ))}
            </Select>
          </FormControl>
        </Grid>

        <Grid size={{ xs: 12, sm: 6 }}>
          <TextField
            label='Occupation'
            value={value.occupation ?? ''}
            onChange={(e) => update('occupation', e.target.value || null)}
            fullWidth
          />
        </Grid>

        <Grid size={{ xs: 12, sm: 6 }}>
          <FormControl fullWidth>
            <InputLabel>Income level</InputLabel>
            <Select
              value={value.income_level ?? ''}
              label='Income level'
              onChange={(e) => update('income_level', (e.target.value as string) || null)}
            >
              <MenuItem value=''>— Not specified —</MenuItem>
              {Object.entries(INCOME_LEVEL_LABELS).map(([k, v]) => (
                <MenuItem key={k} value={k}>{v}</MenuItem>
              ))}
            </Select>
          </FormControl>
        </Grid>

        <Grid size={12}>
          <Typography variant='subtitle2'>Health & Lifestyle</Typography>
          <Divider sx={{ mb: 2 }} />
        </Grid>

        <Grid size={{ xs: 12, sm: 6 }}>
          <FormControlLabel
            control={
              <Switch
                checked={value.has_health_insurance === true}
                onChange={(e) => update('has_health_insurance', e.target.checked)}
              />
            }
            label='Has health insurance'
          />
        </Grid>

        <Grid size={{ xs: 12, sm: 6 }}>
          <FormControl fullWidth>
            <InputLabel>Education level</InputLabel>
            <Select
              value={value.education_level ?? ''}
              label='Education level'
              onChange={(e) => update('education_level', (e.target.value as string) || null)}
            >
              <MenuItem value=''>— Not specified —</MenuItem>
              {Object.entries(EDUCATION_LEVEL_LABELS).map(([k, v]) => (
                <MenuItem key={k} value={k}>{v}</MenuItem>
              ))}
            </Select>
          </FormControl>
        </Grid>

        <Grid size={{ xs: 12, sm: 6 }}>
          <FormControl fullWidth>
            <InputLabel>Smoking status</InputLabel>
            <Select
              value={value.smoking_status ?? ''}
              label='Smoking status'
              onChange={(e) => update('smoking_status', (e.target.value as string) || null)}
            >
              <MenuItem value=''>— Not specified —</MenuItem>
              {Object.entries(SMOKING_STATUS_LABELS).map(([k, v]) => (
                <MenuItem key={k} value={k}>{v}</MenuItem>
              ))}
            </Select>
          </FormControl>
        </Grid>

        <Grid size={{ xs: 12, sm: 6 }}>
          <FormControl fullWidth>
            <InputLabel>Alcohol consumption</InputLabel>
            <Select
              value={value.alcohol_consumption ?? ''}
              label='Alcohol consumption'
              onChange={(e) => update('alcohol_consumption', (e.target.value as string) || null)}
            >
              <MenuItem value=''>— Not specified —</MenuItem>
              {Object.entries(ALCOHOL_CONSUMPTION_LABELS).map(([k, v]) => (
                <MenuItem key={k} value={k}>{v}</MenuItem>
              ))}
            </Select>
          </FormControl>
        </Grid>

        <Grid size={{ xs: 12, sm: 6 }}>
          <FormControl fullWidth>
            <InputLabel>Physical activity</InputLabel>
            <Select
              value={value.physical_activity_level ?? ''}
              label='Physical activity'
              onChange={(e) => update('physical_activity_level', (e.target.value as string) || null)}
            >
              <MenuItem value=''>— Not specified —</MenuItem>
              {Object.entries(PHYSICAL_ACTIVITY_LABELS).map(([k, v]) => (
                <MenuItem key={k} value={k}>{v}</MenuItem>
              ))}
            </Select>
          </FormControl>
        </Grid>

        <Grid size={12}>
          <Typography variant='subtitle2'>Support & Access</Typography>
          <Divider sx={{ mb: 2 }} />
        </Grid>

        <Grid size={{ xs: 12, sm: 4 }}>
          <FormControl fullWidth>
            <InputLabel>Transportation</InputLabel>
            <Select
              value={value.transportation_access ?? ''}
              label='Transportation'
              onChange={(e) => update('transportation_access', (e.target.value as string) || null)}
            >
              <MenuItem value=''>— Not specified —</MenuItem>
              {Object.entries(TRANSPORTATION_ACCESS_LABELS).map(([k, v]) => (
                <MenuItem key={k} value={k}>{v}</MenuItem>
              ))}
            </Select>
          </FormControl>
        </Grid>

        <Grid size={{ xs: 12, sm: 4 }}>
          <FormControl fullWidth>
            <InputLabel>Food security</InputLabel>
            <Select
              value={value.food_security_status ?? ''}
              label='Food security'
              onChange={(e) => update('food_security_status', (e.target.value as string) || null)}
            >
              <MenuItem value=''>— Not specified —</MenuItem>
              {Object.entries(FOOD_SECURITY_LABELS).map(([k, v]) => (
                <MenuItem key={k} value={k}>{v}</MenuItem>
              ))}
            </Select>
          </FormControl>
        </Grid>

        <Grid size={{ xs: 12, sm: 4 }}>
          <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1 }}>
            <FormControlLabel
              control={
                <Switch
                  checked={value.has_family_support === true}
                  onChange={(e) => update('has_family_support', e.target.checked)}
                />
              }
              label='Has family support'
            />
            <FormControlLabel
              control={
                <Switch
                  checked={value.has_caregiver === true}
                  onChange={(e) => update('has_caregiver', e.target.checked)}
                />
              }
              label='Has caregiver'
            />
          </Box>
        </Grid>

        <Grid size={12}>
          <Typography variant='subtitle2'>Notes</Typography>
          <Divider sx={{ mb: 2 }} />
        </Grid>

        <Grid size={12}>
          <TextField
            label='Dietary restrictions / cultural notes'
            value={value.dietary_restrictions_cultural ?? ''}
            onChange={(e) => update('dietary_restrictions_cultural', e.target.value || null)}
            fullWidth
            multiline
            rows={3}
          />
        </Grid>

        <Grid size={12}>
          <TextField
            label='Additional notes'
            value={value.additional_notes ?? ''}
            onChange={(e) => update('additional_notes', e.target.value || null)}
            fullWidth
            multiline
            rows={3}
          />
        </Grid>
      </Grid>
    </Box>
  )
}
