'use client'

import Chip from '@mui/material/Chip'
import Tooltip from '@mui/material/Tooltip'

import type { DeliveryRecord } from './types'

interface Props {
  latestDelivery: DeliveryRecord | null
}

function formatDate(dateStr: string): string {
  return new Date(dateStr).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
}

export default function DietPlanDeliveryBadge({ latestDelivery }: Props) {
  if (!latestDelivery) return null

  if (latestDelivery.status === 'sent') {
    return (
      <Chip
        label={`Sent ${formatDate(latestDelivery.createdAt)}`}
        color='success'
        size='small'
      />
    )
  }

  if (latestDelivery.status === 'pending') {
    return (
      <Chip
        label='Sending…'
        color='warning'
        size='small'
      />
    )
  }

  // failed
  return (
    <Tooltip title={latestDelivery.failureReason ?? 'Unknown error'}>
      <Chip
        label='Send failed'
        color='error'
        size='small'
      />
    </Tooltip>
  )
}
