'use client'

import Box from '@mui/material/Box'
import Chip from '@mui/material/Chip'
import Stack from '@mui/material/Stack'
import Tooltip from '@mui/material/Tooltip'
import Typography from '@mui/material/Typography'

import type { DietPlanSummary } from './types'

interface Props {
  plans: DietPlanSummary[]
  onSelect: (plan: DietPlanSummary) => void
}

const STATUS_COLOR: Record<string, 'success' | 'error' | 'warning'> = {
  completed: 'success',
  failed: 'error',
  pending: 'warning',
}

function formatDate(dateStr: string): string {
  return new Date(dateStr).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
}

export default function DietPlanHistory({ plans, onSelect }: Props) {
  if (plans.length === 0) return null

  return (
    <Box sx={{ mt: 3 }}>
      <Typography variant='subtitle2' color='text.secondary' sx={{ mb: 1 }}>
        Previous Generations
      </Typography>
      <Stack spacing={1}>
        {plans.map(plan => (
          <Box
            key={plan.id}
            onClick={() => plan.status === 'completed' && onSelect(plan)}
            sx={{
              display: 'flex',
              alignItems: 'center',
              gap: 2,
              px: 2,
              py: 1,
              border: '1px solid',
              borderColor: 'divider',
              borderRadius: 1,
              cursor: plan.status === 'completed' ? 'pointer' : 'default',
              '&:hover': plan.status === 'completed' ? { backgroundColor: 'action.hover' } : {},
            }}
          >
            <Typography variant='body2' sx={{ minWidth: 110 }}>
              {formatDate(plan.createdAt)}
            </Typography>
            <Typography variant='body2' color='text.secondary' sx={{ flexGrow: 1 }}>
              {plan.generatedBy?.name ?? '—'}
            </Typography>
            <Chip
              label={plan.status}
              color={STATUS_COLOR[plan.status] ?? 'default'}
              size='small'
            />
            {plan.status === 'failed' && plan.failureReason && (
              <Tooltip title={plan.failureReason}>
                <Typography variant='caption' color='error'>⚠ Failed</Typography>
              </Tooltip>
            )}
          </Box>
        ))}
      </Stack>
    </Box>
  )
}
