'use client'

import { useState } from 'react'

import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Chip from '@mui/material/Chip'
import Snackbar from '@mui/material/Snackbar'
import Stack from '@mui/material/Stack'
import Table from '@mui/material/Table'
import TableBody from '@mui/material/TableBody'
import TableCell from '@mui/material/TableCell'
import TableContainer from '@mui/material/TableContainer'
import TableHead from '@mui/material/TableHead'
import TableRow from '@mui/material/TableRow'
import Tooltip from '@mui/material/Tooltip'
import Typography from '@mui/material/Typography'

import type { DietPlan } from './types'
import DietPlanDeliveryBadge from './DietPlanDeliveryBadge'
import DietPlanEditForm from './DietPlanEditForm'

interface Props {
  plan: DietPlan
  patientId: number
  onRegenerate: () => void
  onUpdate: (updated: DietPlan) => void
}

export default function DietPlanCard({ plan, patientId, onRegenerate, onUpdate }: Props) {
  const [isEditing, setIsEditing] = useState(false)
  const [sending, setSending] = useState(false)

  const [snackbar, setSnackbar] = useState<{ open: boolean; message: string; severity: 'success' | 'error' }>({
    open: false,
    message: '',
    severity: 'success',
  })

  const days = plan.days ?? []
  const goals = plan.nutritionalGoals
  const warnings = plan.warnings ?? []
  const isCompleted = plan.status === 'completed'

  const handleSend = async () => {
    if (plan.latestDelivery && !window.confirm('A delivery already exists. Resend the diet plan?')) {
      return
    }

    setSending(true)

    try {
      const res = await fetch(`/api/patients/${patientId}/diet-plans/${plan.id}/send`, { method: 'POST' })
      const json = await res.json()

      if (res.status === 202) {
        setSnackbar({ open: true, message: 'Diet plan queued for delivery.', severity: 'success' })

        // Propagate so DietPlanSection can refresh latestDelivery
        onUpdate({ ...plan, latestDelivery: json.data?.delivery ?? plan.latestDelivery })
      } else {
        setSnackbar({ open: true, message: json.message ?? 'Failed to send diet plan.', severity: 'error' })
      }
    } catch {
      setSnackbar({ open: true, message: 'Network error. Please try again.', severity: 'error' })
    } finally {
      setSending(false)
    }
  }

  if (isEditing) {
    return (
      <Card variant='outlined'>
        <CardContent>
          <DietPlanEditForm
            plan={plan}
            patientId={patientId}
            onSave={updated => {
              setIsEditing(false)
              onUpdate(updated)
            }}
            onCancel={() => setIsEditing(false)}
          />
        </CardContent>
      </Card>
    )
  }

  return (
    <>
      <Card variant='outlined'>
        <CardContent>
          {plan.rationale && (
            <Typography variant='body2' color='text.secondary' sx={{ fontStyle: 'italic', mb: 2 }}>
              {plan.rationale}
            </Typography>
          )}

          <Stack direction='row' spacing={3} alignItems='center' sx={{ mb: 2 }}>
            <Typography variant='h6' fontWeight={700}>
              {plan.dailyCalories} kcal/day
            </Typography>
            {goals && (
              <>
                <Typography variant='body2'>Protein: <strong>{goals.protein_g}g</strong></Typography>
                <Typography variant='body2'>Carbs: <strong>{goals.carbs_g}g</strong></Typography>
                <Typography variant='body2'>Fat: <strong>{goals.fat_g}g</strong></Typography>
              </>
            )}
            {plan.isEdited && (
              <Chip label='Edited' color='info' size='small' />
            )}
            <DietPlanDeliveryBadge latestDelivery={plan.latestDelivery} />
          </Stack>

          {warnings.length > 0 && (
            <Stack direction='row' spacing={1} flexWrap='wrap' sx={{ mb: 2 }}>
              {warnings.map((w, i) => (
                <Chip key={i} label={w} color='warning' size='small' />
              ))}
            </Stack>
          )}

          <TableContainer>
            <Table size='small'>
              <TableHead>
                <TableRow sx={{ backgroundColor: 'action.hover' }}>
                  <TableCell><strong>Day</strong></TableCell>
                  <TableCell><strong>Breakfast</strong></TableCell>
                  <TableCell><strong>Lunch</strong></TableCell>
                  <TableCell><strong>Dinner</strong></TableCell>
                  <TableCell><strong>Snack</strong></TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {days.map((day, i) => (
                  <TableRow key={i}>
                    <TableCell><strong>{day.day}</strong></TableCell>
                    <TableCell>{day.breakfast}</TableCell>
                    <TableCell>{day.lunch}</TableCell>
                    <TableCell>{day.dinner}</TableCell>
                    <TableCell>{day.snack}</TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </TableContainer>

          <Box sx={{ mt: 2, display: 'flex', gap: 1, flexWrap: 'wrap' }}>
            <Button variant='outlined' onClick={onRegenerate}>
              Regenerate
            </Button>
            <Button
              variant='outlined'
              onClick={() => setIsEditing(true)}
              disabled={!isCompleted}
            >
              Edit Plan
            </Button>
            <Button
              variant='contained'
              onClick={handleSend}
              disabled={!isCompleted || sending}
            >
              {sending ? 'Sending…' : 'Send to Patient'}
            </Button>
            <Tooltip title='Coming soon'>
              <span>
                <Button variant='outlined' disabled>
                  Export PDF
                </Button>
              </span>
            </Tooltip>
          </Box>
        </CardContent>
      </Card>

      <Snackbar
        open={snackbar.open}
        autoHideDuration={4000}
        onClose={() => setSnackbar(s => ({ ...s, open: false }))}
        anchorOrigin={{ vertical: 'bottom', horizontal: 'center' }}
      >
        <Alert severity={snackbar.severity} onClose={() => setSnackbar(s => ({ ...s, open: false }))}>
          {snackbar.message}
        </Alert>
      </Snackbar>
    </>
  )
}
