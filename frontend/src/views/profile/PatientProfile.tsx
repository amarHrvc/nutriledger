'use client'

import { useEffect, useState } from 'react'

import Box from '@mui/material/Box'
import CircularProgress from '@mui/material/CircularProgress'
import Typography from '@mui/material/Typography'

import ProfileHeader from './shared/ProfileHeader'
import SectionCard from './shared/SectionCard'
import InfoRow from './shared/InfoRow'
import type { AuthUser } from '@/types/auth'
import type { PatientResource } from '@/api/generated/nutriBaseAPI.schemas'

interface PatientProfileProps {
  user: AuthUser
}

export default function PatientProfile({ user }: PatientProfileProps) {
  const [patient, setPatient] = useState<PatientResource | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    fetch('/api/patients/me')
      .then(res => {
        if (!res.ok) throw new Error(res.statusText)

        return res.json()
      })
      .then(data => setPatient(data.patient ?? null))
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

      {!isLoading && !error && (
        <>
          <SectionCard title='Personal Information'>
            <InfoRow label='Full Name' value={patient?.attributes.fullName} />
            <InfoRow label='Date of Birth' value={patient?.attributes.dateOfBirth} />
            <InfoRow label='Gender' value={patient?.attributes.gender} />
            <InfoRow label='Phone' value={patient?.attributes.phone} />
            <InfoRow label='Address' value={patient?.attributes.address} />
            <InfoRow label='City' value={patient?.attributes.city} />
            <InfoRow label='Postal Code' value={patient?.attributes.postalCode} />
          </SectionCard>

          <SectionCard title='Medical Information'>
            <InfoRow label='Blood Type' value={patient?.attributes.bloodType} />
            <InfoRow label='Allergies' value={patient?.attributes.allergies} />
            <InfoRow label='Medical Notes' value={patient?.attributes.medicalNotes} />
          </SectionCard>

          <SectionCard title='Emergency Contact'>
            <InfoRow label='Name' value={patient?.attributes.emergencyContactName} />
            <InfoRow label='Phone' value={patient?.attributes.emergencyContactPhone} />
          </SectionCard>
        </>
      )}
    </Box>
  )
}
