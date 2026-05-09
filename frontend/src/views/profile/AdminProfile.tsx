'use client'

import { useEffect, useState } from 'react'

import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import CircularProgress from '@mui/material/CircularProgress'

import Typography from '@mui/material/Typography'

import ProfileHeader from './shared/ProfileHeader'
import type { AuthUser } from '@/types/auth'

interface AdminProfileProps {
  user: AuthUser
}

interface DashboardStats {
  totalUsers: number
  totalPatients: number
}

function StatBlock({ label, value }: { label: string; value: number }) {
  return (
    <Card variant='outlined'>
      <CardContent>
        <Typography variant='h4' fontWeight='bold'>
          {value}
        </Typography>
        <Typography variant='body2' color='text.secondary'>
          {label}
        </Typography>
      </CardContent>
    </Card>
  )
}

export default function AdminProfile({ user }: AdminProfileProps) {
  const [stats, setStats] = useState<DashboardStats | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    fetch('/api/dashboard/stats')
      .then(res => {
        if (!res.ok) throw new Error(res.statusText)

        return res.json()
      })
      .then(data => {
        console.log(data)
        setStats(data.stats ?? null)
      })
      .catch(err => setError(err.message ?? 'Something went wrong'))
      .finally(() => setIsLoading(false))
  }, [])

  return (
    <Box className='flex flex-col gap-4'>
      <ProfileHeader name={user.name} email={user.email} role={user.role} />

      {isLoading && (
        <Box className='flex justify-center py-8'>
          <CircularProgress size={24} />
        </Box>
      )}

      {error && (
        <Typography color='error' variant='body2'>
          {error}
        </Typography>
      )}

      {!isLoading && !error && stats && (
        <Box className='grid gap-4 grid-cols-1 sm:grid-cols-2'>
          <StatBlock label='Total Users' value={stats.totalUsers} />
          <StatBlock label='Total Patients' value={stats.totalPatients} />
        </Box>
      )}
    </Box>
  )
}
