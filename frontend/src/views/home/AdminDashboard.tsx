'use client'

import { useEffect, useState } from 'react'
import Link from 'next/link'

import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardActionArea from '@mui/material/CardActionArea'
import CardContent from '@mui/material/CardContent'
import CircularProgress from '@mui/material/CircularProgress'
import Divider from '@mui/material/Divider'
import Typography from '@mui/material/Typography'

import type { AuthUser } from '@/types/auth'

interface Stats {
  totalUsers: number
  totalPatients: number
}

function StatCard({ label, value, icon }: { label: string; value: number; icon: string }) {
  return (
    <Card variant='outlined'>
      <CardContent sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
        <Typography fontSize={32}>{icon}</Typography>
        <Box>
          <Typography variant='h4' fontWeight='bold' lineHeight={1}>
            {value}
          </Typography>
          <Typography variant='body2' color='text.secondary'>
            {label}
          </Typography>
        </Box>
      </CardContent>
    </Card>
  )
}

function QuickLink({ href, icon, label, description }: { href: string; icon: string; label: string; description: string }) {
  return (
    <Card variant='outlined'>
      <CardActionArea component={Link} href={href} sx={{ p: 2 }}>
        <Box sx={{ display: 'flex', alignItems: 'flex-start', gap: 2 }}>
          <Typography fontSize={28}>{icon}</Typography>
          <Box>
            <Typography variant='subtitle1' fontWeight={600}>
              {label}
            </Typography>
            <Typography variant='body2' color='text.secondary'>
              {description}
            </Typography>
          </Box>
        </Box>
      </CardActionArea>
    </Card>
  )
}

export default function AdminDashboard({ user }: { user: AuthUser }) {
  const [stats, setStats] = useState<Stats | null>(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    fetch('/api/dashboard/stats')
      .then(res => {
        if (!res.ok) throw new Error(res.statusText)
        return res.json()
      })
      .then(data => setStats(data.stats ?? null))
      .catch(err => setError(err.message ?? 'Failed to load stats'))
      .finally(() => setLoading(false))
  }, [])

  return (
    <Box className='flex flex-col gap-6'>
      <Box>
        <Typography variant='h4' fontWeight='bold'>
          Welcome back, {user.name}
        </Typography>
        <Typography variant='body2' color='text.secondary' mt={0.5}>
          System overview — Administrator
        </Typography>
      </Box>

      <Divider />

      <Box>
        <Typography variant='subtitle1' fontWeight={600} mb={2}>
          System Stats
        </Typography>
        {loading && <CircularProgress size={24} />}
        {error && <Alert severity='error'>{error}</Alert>}
        {!loading && !error && stats && (
          <Box className='grid grid-cols-1 sm:grid-cols-2 gap-4'>
            <StatCard label='Total Users' value={stats.totalUsers} icon='👥' />
            <StatCard label='Total Patients' value={stats.totalPatients} icon='🏥' />
          </Box>
        )}
      </Box>

      <Box>
        <Typography variant='subtitle1' fontWeight={600} mb={2}>
          Quick Actions
        </Typography>
        <Box className='grid grid-cols-1 sm:grid-cols-3 gap-4'>
          <QuickLink
            href='/dashboard/users'
            icon='👤'
            label='Manage Users'
            description='View, create, and manage system users'
          />
          <QuickLink
            href='/dashboard/patients'
            icon='🩺'
            label='Manage Patients'
            description='Browse patient records and profiles'
          />
          <QuickLink
            href='/dashboard/visits'
            icon='📋'
            label='All Visits'
            description='View all clinical visits across the system'
          />
        </Box>
      </Box>
    </Box>
  )
}
