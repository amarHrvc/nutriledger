'use client'

import Avatar from '@mui/material/Avatar'
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Chip from '@mui/material/Chip'
import CircularProgress from '@mui/material/CircularProgress'
import Typography from '@mui/material/Typography'

import { useAuth } from '@/context/AuthContext'

const ROLE_LABELS: Record<string, string> = {
  admin: 'Administrator',
  doktor: 'Doctor',
  pacijent: 'Patient',
}

const ROLE_COLORS: Record<string, 'primary' | 'secondary' | 'success'> = {
  admin: 'primary',
  doktor: 'secondary',
  pacijent: 'success',
}

export default function DashboardHome() {
  const { user, isLoading } = useAuth()

  if (isLoading) {
    return (
      <Box className='flex justify-center items-center' sx={{ minHeight: 200 }}>
        <CircularProgress />
      </Box>
    )
  }

  return (
    <Box className='flex flex-col gap-6'>
      <Typography variant='h4'>Welcome back, {user?.name ?? '...'}</Typography>

      <Card sx={{ maxWidth: 480 }}>
        <CardContent className='flex items-center gap-4'>
          <Avatar sx={{ width: 64, height: 64, fontSize: 28 }}>
            {user?.name?.charAt(0).toUpperCase() ?? '?'}
          </Avatar>
          <Box className='flex flex-col gap-1'>
            <Typography variant='h6'>{user?.name}</Typography>
            <Typography variant='body2' color='text.secondary'>
              {user?.email}
            </Typography>
            <Chip
              label={ROLE_LABELS[user?.role ?? ''] ?? user?.role}
              color={ROLE_COLORS[user?.role ?? ''] ?? 'default'}
              size='small'
              sx={{ width: 'fit-content', mt: 0.5 }}
            />
          </Box>
        </CardContent>
      </Card>

      <Typography variant='body2' color='text.secondary'>
        Use the sidebar to navigate to Patients, Visits, and other sections.
      </Typography>
    </Box>
  )
}
