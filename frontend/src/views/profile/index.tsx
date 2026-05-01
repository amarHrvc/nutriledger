'use client'

import Box from '@mui/material/Box'
import Typography from '@mui/material/Typography'

import { useAuth } from '@/context/AuthContext'
import AdminProfile from '@views/profile/AdminProfile'
import DoctorProfile from '@views/profile/DoctorProfile'
import PatientProfile from '@views/profile/PatientProfile'

export default function ProfilePage() {
  const { user, isLoading } = useAuth()

  if (isLoading) {
    return (
      <Box className='flex justify-center items-center' sx={{ minHeight: 300 }}>
        //Progress here
      </Box>
    )
  }

  if (!user) {
    return <Typography color={'error'}>Unable to load profile.</Typography>
  }

  if (user.role === 'admin') return <AdminProfile user={user} />
  if (user.role === 'doktor') return <DoctorProfile user={user} />
  if (user.role === 'pacijent') return <PatientProfile user={user} />

  return <Typography color={'error'}>Unknown role: {user.role}</Typography>
}
