'use client'

import Box from '@mui/material/Box'
import CircularProgress from '@mui/material/CircularProgress'

import { useAuth } from '@/context/AuthContext'
import AdminDashboard from './AdminDashboard'
import DoctorDashboard from './DoctorDashboard'
import PatientDashboard from './PatientDashboard'

export default function DashboardHome() {
  const { user, isLoading } = useAuth()

  if (isLoading) {
    return (
      <Box sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: 200 }}>
        <CircularProgress />
      </Box>
    )
  }

  if (!user) return null

  if (user.role === 'admin') return <AdminDashboard user={user} />
  if (user.role === 'doktor') return <DoctorDashboard user={user} />
  return <PatientDashboard user={user} />
}
