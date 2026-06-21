'use client'

import { useEffect, useState } from 'react'
import Link from 'next/link'

import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Card from '@mui/material/Card'
import CardActionArea from '@mui/material/CardActionArea'
import CardContent from '@mui/material/CardContent'
import Chip from '@mui/material/Chip'
import CircularProgress from '@mui/material/CircularProgress'
import Divider from '@mui/material/Divider'
import Table from '@mui/material/Table'
import TableBody from '@mui/material/TableBody'
import TableCell from '@mui/material/TableCell'
import TableContainer from '@mui/material/TableContainer'
import TableHead from '@mui/material/TableHead'
import TableRow from '@mui/material/TableRow'
import Typography from '@mui/material/Typography'

import type { AuthUser } from '@/types/auth'
import type { PatientResource, VisitResource } from '@/api/generated/nutriBaseAPI.schemas'

function ProfileSnippet({ patient }: { patient: PatientResource }) {
  const attr = patient.attributes
  const fields: { label: string; value: string | null | undefined }[] = [
    { label: 'Date of Birth', value: attr.dateOfBirth },
    { label: 'Gender', value: attr.gender === 'M' ? 'Male' : attr.gender === 'F' ? 'Female' : attr.gender },
    { label: 'Blood Type', value: attr.bloodType },
    { label: 'Phone', value: attr.phone },
    { label: 'City', value: attr.city },
    { label: 'Allergies', value: attr.allergies },
  ]

  return (
    <Card variant='outlined'>
      <CardContent>
        <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 1 }}>
          <Typography variant='subtitle1' fontWeight={600}>
            My Profile
          </Typography>
          <Button component={Link} href='/dashboard/profile' size='small' variant='outlined'>
            View full profile
          </Button>
        </Box>
        <Divider sx={{ mb: 2 }} />
        <Box className='grid grid-cols-1 sm:grid-cols-2 gap-y-2 gap-x-6'>
          {fields.map(({ label, value }) => (
            <Box key={label}>
              <Typography variant='caption' color='text.secondary' display='block'>
                {label}
              </Typography>
              <Typography variant='body2'>
                {value ?? <span style={{ color: '#aaa' }}>Not set</span>}
              </Typography>
            </Box>
          ))}
        </Box>
      </CardContent>
    </Card>
  )
}

export default function PatientDashboard({ user }: { user: AuthUser }) {
  const [patient, setPatient] = useState<PatientResource | null>(null)
  const [visits, setVisits] = useState<VisitResource[]>([])
  const [loadingProfile, setLoadingProfile] = useState(true)
  const [loadingVisits, setLoadingVisits] = useState(true)
  const [profileError, setProfileError] = useState<string | null>(null)
  const [visitsError, setVisitsError] = useState<string | null>(null)

  useEffect(() => {
    fetch('/api/patients/me')
      .then(res => {
        if (!res.ok) throw new Error(res.statusText)
        return res.json()
      })
      .then(data => setPatient(data.patient ?? null))
      .catch(err => setProfileError(err.message ?? 'Failed to load profile'))
      .finally(() => setLoadingProfile(false))

    fetch('/api/visits')
      .then(res => {
        if (!res.ok) throw new Error(res.statusText)
        return res.json()
      })
      .then(data => setVisits(data.data ?? []))
      .catch(err => setVisitsError(err.message ?? 'Failed to load visits'))
      .finally(() => setLoadingVisits(false))
  }, [])

  const today = new Date().toISOString().split('T')[0]
  const upcoming = visits
    .filter(v => v.attributes.date >= today)
    .sort((a, b) => a.attributes.date.localeCompare(b.attributes.date))
    .slice(0, 5)

  return (
    <Box className='flex flex-col gap-6'>
      <Box>
        <Typography variant='h4' fontWeight='bold'>
          Hello, {user.name}
        </Typography>
        <Typography variant='body2' color='text.secondary' mt={0.5}>
          Your personal health dashboard
        </Typography>
      </Box>

      <Divider />

      {/* Profile summary */}
      <Box>
        {loadingProfile && <CircularProgress size={24} />}
        {profileError && <Alert severity='error'>{profileError}</Alert>}
        {!loadingProfile && !profileError && (
          patient ? (
            <ProfileSnippet patient={patient} />
          ) : (
            <Alert severity='warning'>
              Your patient profile has not been set up yet. Please contact your clinic.
            </Alert>
          )
        )}
      </Box>

      {/* Upcoming visits */}
      <Box>
        <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', mb: 2 }}>
          <Typography variant='subtitle1' fontWeight={600}>
            My Upcoming Visits
          </Typography>
          <Button component={Link} href='/dashboard/visits' size='small' variant='outlined'>
            View all
          </Button>
        </Box>

        {loadingVisits && <CircularProgress size={24} />}
        {visitsError && <Alert severity='error'>{visitsError}</Alert>}

        {!loadingVisits && !visitsError && (
          upcoming.length > 0 ? (
            <TableContainer component={Card} variant='outlined'>
              <Table size='small'>
                <TableHead>
                  <TableRow sx={{ backgroundColor: 'action.hover' }}>
                    <TableCell>Date</TableCell>
                    <TableCell>Time</TableCell>
                    <TableCell>Doctor</TableCell>
                    <TableCell>Status</TableCell>
                    <TableCell align='right'>Action</TableCell>
                  </TableRow>
                </TableHead>
                <TableBody>
                  {upcoming.map(v => (
                    <TableRow key={v.id} hover>
                      <TableCell>
                        {new Date(v.attributes.date + 'T00:00:00').toLocaleDateString()}
                      </TableCell>
                      <TableCell>{v.attributes.time ?? '—'}</TableCell>
                      <TableCell>{v.attributes.doctorName ?? '—'}</TableCell>
                      <TableCell>
                        <Chip
                          label={v.attributes.date === today ? 'Today' : 'Upcoming'}
                          color={v.attributes.date === today ? 'primary' : 'default'}
                          size='small'
                        />
                      </TableCell>
                      <TableCell align='right'>
                        <Button
                          size='small'
                          component={Link}
                          href={`/dashboard/visits/${v.id}?patient=${v.attributes.patientId}`}
                        >
                          View
                        </Button>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </TableContainer>
          ) : (
            <Alert severity='info'>No upcoming visits scheduled.</Alert>
          )
        )}
      </Box>

      {/* Quick link to profile */}
      <Card variant='outlined'>
        <CardActionArea component={Link} href='/dashboard/profile' sx={{ p: 2 }}>
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
            <Typography fontSize={28}>👤</Typography>
            <Box>
              <Typography variant='subtitle1' fontWeight={600}>My Full Profile</Typography>
              <Typography variant='body2' color='text.secondary'>
                View your complete medical and personal information
              </Typography>
            </Box>
          </Box>
        </CardActionArea>
      </Card>
    </Box>
  )
}
