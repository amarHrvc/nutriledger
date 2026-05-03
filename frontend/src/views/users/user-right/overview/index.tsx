import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Divider from '@mui/material/Divider'
import Typography from '@mui/material/Typography'

import type { UserResource } from '@/api/generated/nutriBaseAPI.schemas'

function InfoRow({ label, value }: { label: string; value: string }) {
  return (
    <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', py: 1 }}>
      <Typography variant='body2' color='text.secondary' sx={{ minWidth: 130 }}>
        {label}
      </Typography>
      <Typography variant='body2' color='text.primary' sx={{ textAlign: 'right' }}>
        {value}
      </Typography>
    </Box>
  )
}

export default function OverviewTab({ user }: { user: UserResource }) {
  const { name, email, role, createdAt, deletedAt } = user.attributes

  return (
    <Card>
      <CardContent>
        <Typography variant='h6' sx={{ mb: 2 }}>Account Information</Typography>
        <Divider sx={{ mb: 2 }} />

        <InfoRow label='Full Name' value={name ?? '—'} />
        <Divider />
        <InfoRow label='Email' value={email ?? '—'} />
        <Divider />
        <InfoRow label='Role' value={role ?? '—'} />
        <Divider />
        <InfoRow label='Member Since' value={createdAt ?? '—'} />
        {deletedAt && (
          <>
            <Divider />
            <InfoRow label='Deactivated At' value={deletedAt} />
          </>
        )}
      </CardContent>
    </Card>
  )
}
