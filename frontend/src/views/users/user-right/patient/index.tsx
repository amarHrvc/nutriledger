'use client'

import { useEffect, useState } from 'react'
import Link from 'next/link'

import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import CircularProgress from '@mui/material/CircularProgress'
import Divider from '@mui/material/Divider'
import Typography from '@mui/material/Typography'

import type { UserResource, PatientResource } from '@/api/generated/nutriBaseAPI.schemas'

function InfoRow({ label, value }: { label: string; value: string | null | undefined }) {
  return (
    <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', py: 1 }}>
      <Typography variant='body2' color='text.secondary' sx={{ minWidth: 160 }}>
        {label}
      </Typography>
      <Typography variant='body2' color='text.primary' sx={{ textAlign: 'right' }}>
        {value ?? '—'}
      </Typography>
    </Box>
  )
}

export default function PatientTab({ user }: { user: UserResource }) {
  const patientId = user.relationships?.patient?.id
  const [patient, setPatient] = useState<PatientResource | null>(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    if (!patientId) {
      setLoading(false)
      return
    }

    fetch(`/api/patients/${patientId}`)
      .then(res => {
        if (!res.ok) throw new Error(res.statusText)
        return res.json()
      })
      .then(data => setPatient(data.data?.patient ?? null))
      .catch(err => setError(err.message ?? 'Failed to load patient record'))
      .finally(() => setLoading(false))
  }, [patientId])

  if (!patientId) {
    return (
      <Alert severity='info'>
        No patient record is linked to this user account.
      </Alert>
    )
  }

  if (loading) return <CircularProgress size={24} />
  if (error) return <Alert severity='error'>{error}</Alert>
  if (!patient) return <Alert severity='warning'>Patient record not found.</Alert>

  const a = patient.attributes

  return (
    <Box sx={{ display: 'flex', flexDirection: 'column', gap: 3 }}>
      <Card>
        <CardContent>
          <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 1 }}>
            <Typography variant='h6'>Personal Information</Typography>
            <Button
              component={Link}
              href={`/dashboard/patients/${patient.id}`}
              size='small'
              variant='outlined'
            >
              Open Patient Profile
            </Button>
          </Box>
          <Divider sx={{ mb: 1 }} />

          <InfoRow label='Full Name' value={a.fullName} />
          <Divider />
          <InfoRow label='Date of Birth' value={a.dateOfBirth} />
          <Divider />
          <InfoRow label='Gender' value={a.gender === 'M' ? 'Male' : a.gender === 'F' ? 'Female' : a.gender} />
          <Divider />
          <InfoRow label='Phone' value={a.phone} />
          <Divider />
          <InfoRow label='Address' value={a.address} />
          <Divider />
          <InfoRow label='City' value={a.city} />
          <Divider />
          <InfoRow label='Postal Code' value={a.postalCode} />
        </CardContent>
      </Card>

      <Card>
        <CardContent>
          <Typography variant='h6' sx={{ mb: 1 }}>Medical Information</Typography>
          <Divider sx={{ mb: 1 }} />
          <InfoRow label='Blood Type' value={a.bloodType} />
          <Divider />
          <InfoRow label='Allergies' value={a.allergies} />
          <Divider />
          <InfoRow label='Medical Notes' value={a.medicalNotes} />
        </CardContent>
      </Card>

      <Card>
        <CardContent>
          <Typography variant='h6' sx={{ mb: 1 }}>Emergency Contact</Typography>
          <Divider sx={{ mb: 1 }} />
          <InfoRow label='Name' value={a.emergencyContactName} />
          <Divider />
          <InfoRow label='Phone' value={a.emergencyContactPhone} />
        </CardContent>
      </Card>
    </Box>
  )
}
