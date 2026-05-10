'use client'

import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import CardHeader from '@mui/material/CardHeader'
import Chip from '@mui/material/Chip'
import Divider from '@mui/material/Divider'
import Grid from '@mui/material/Grid'
import IconButton from '@mui/material/IconButton'
import Typography from '@mui/material/Typography'

import type { VitalSignResource } from './vitals.types'

interface Props {
  vitals: VitalSignResource
  patientId: string
  visitId: string
  canEdit: boolean
  canDelete: boolean
  onEdit: () => void
  onDelete: () => void
}

const bmiChipColor = (
  category: string | null
): 'default' | 'warning' | 'error' => {
  if (category === 'normal') return 'default'
  if (category === 'obese') return 'error'
  if (category === 'underweight' || category === 'overweight') return 'warning'
  return 'default'
}

const fmt = (value: string | number | null, unit: string): string =>
  value !== null && value !== undefined ? `${value} ${unit}` : '—'

export default function VitalsCard({
  vitals,
  canEdit,
  canDelete,
  onEdit,
  onDelete,
}: Props) {
  const a = vitals.attributes
  const prev = a.previousVisit

  const weightDeltaLabel = (delta: number | null): string | null => {
    if (delta === null) return null
    const sign = delta > 0 ? '+' : ''
    return `${sign}${delta.toFixed(1)} kg`
  }

  const measurements: { label: string; value: React.ReactNode }[] = [
    {
      label: 'Systolic BP',
      value: a.systolicBp !== null ? `${a.systolicBp} mmHg` : '—',
    },
    {
      label: 'Diastolic BP',
      value: a.diastolicBp !== null ? `${a.diastolicBp} mmHg` : '—',
    },
    {
      label: 'Heart Rate',
      value: a.heartRate !== null ? `${a.heartRate} bpm` : '—',
    },
    {
      label: 'Temperature',
      value: fmt(a.temperature, '°C'),
    },
    {
      label: 'Weight',
      value: (
        <Box>
          <Typography variant='body1'>
            {fmt(a.weight, 'kg')}
          </Typography>
          {prev && prev.weightDelta !== null && (
            <Typography
              variant='caption'
              color={prev.weightDelta < 0 ? 'success.main' : 'warning.main'}
            >
              {weightDeltaLabel(prev.weightDelta)} vs prev. visit
            </Typography>
          )}
        </Box>
      ),
    },
    {
      label: 'Height',
      value: fmt(a.height, 'cm'),
    },
    {
      label: 'BMI',
      value: a.bmi !== null ? (
        <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
          <Typography variant='body1'>{a.bmi}</Typography>
          {a.bmiCategory && (
            <Chip
              label={a.bmiCategory}
              size='small'
              color={bmiChipColor(a.bmiCategory)}
            />
          )}
        </Box>
      ) : '—',
    },
  ]

  return (
    <Card>
      <CardHeader
        title='Vital Signs'
        action={
          <Box sx={{ display: 'flex', gap: 0.5 }}>
            {canEdit && (
              <IconButton size='small' onClick={onEdit} aria-label='Edit vital signs'>
                <i className='tabler-edit' style={{ fontSize: '1.1rem' }} />
              </IconButton>
            )}
            {canDelete && (
              <IconButton size='small' onClick={onDelete} color='error' aria-label='Delete vital signs'>
                <i className='tabler-trash' style={{ fontSize: '1.1rem' }} />
              </IconButton>
            )}
          </Box>
        }
      />
      <Divider />
      <CardContent>
        {a.flags.length > 0 && (
          <Alert severity='warning' sx={{ mb: 2 }}>
            {a.flags.map((flag, i) => (
              <Box key={i} component='span' sx={{ display: 'block' }}>
                {flag.field}
                {flag.threshold ? `: ${flag.value} (normal: ${flag.threshold})` : `: ${flag.value} (${flag.category})`}
              </Box>
            ))}
          </Alert>
        )}

        <Grid container spacing={2}>
          {measurements.map(({ label, value }) => (
            <Grid key={label} size={{ xs: 6, sm: 4 }}>
              <Typography variant='caption' color='text.secondary' display='block'>
                {label}
              </Typography>
              {typeof value === 'string' ? (
                <Typography variant='body1'>{value}</Typography>
              ) : (
                value
              )}
            </Grid>
          ))}
        </Grid>

        {a.flags.length > 0 && (
          <Box sx={{ mt: 2, display: 'flex', flexWrap: 'wrap', gap: 1 }}>
            {a.flags.map((flag, i) => (
              <Chip
                key={i}
                label={flag.field}
                size='small'
                color='warning'
                variant='outlined'
              />
            ))}
          </Box>
        )}
      </CardContent>
    </Card>
  )
}
