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
import type { VisitResource } from '@/api/generated/nutriBaseAPI.schemas'

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

export default function DoctorDashboard({ user }: { user: AuthUser }) {
  const [visits, setVisits] = useState<VisitResource[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    fetch('/api/visits')
      .then(res => {
        if (!res.ok) throw new Error(res.statusText)
        return res.json()
      })
      .then(data => setVisits(data.data ?? []))
      .catch(err => setError(err.message ?? 'Failed to load visits'))
      .finally(() => setLoading(false))
  }, [])

  const today = new Date().toISOString().split('T')[0]
  const upcoming = visits
    .filter(v => v.attributes.date >= today)
    .sort((a, b) => a.attributes.date.localeCompare(b.attributes.date))
    .slice(0, 5)

  const todayVisits = visits.filter(v => v.attributes.date === today)

  return (
    <Box className='flex flex-col gap-6'>
      <Box>
        <Typography variant='h4' fontWeight='bold'>
          Welcome back, Dr. {user.name}
        </Typography>
        <Typography variant='body2' color='text.secondary' mt={0.5}>
          Here's your clinical overview for today
        </Typography>
      </Box>

      <Divider />

      {/* Stats row */}
      <Box className='grid grid-cols-1 sm:grid-cols-2 gap-4'>
        <Card variant='outlined'>
          <CardContent sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
            <Typography fontSize={32}>📅</Typography>
            <Box>
              <Typography variant='h4' fontWeight='bold' lineHeight={1}>
                {loading ? '—' : todayVisits.length}
              </Typography>
              <Typography variant='body2' color='text.secondary'>
                Today's visits
              </Typography>
            </Box>
          </CardContent>
        </Card>
        <Card variant='outlined'>
          <CardContent sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
            <Typography fontSize={32}>⏭️</Typography>
            <Box>
              <Typography variant='h4' fontWeight='bold' lineHeight={1}>
                {loading ? '—' : upcoming.length}
              </Typography>
              <Typography variant='body2' color='text.secondary'>
                Upcoming visits
              </Typography>
            </Box>
          </CardContent>
        </Card>
      </Box>

      {/* Upcoming visits table */}
      <Box>
        <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', mb: 2 }}>
          <Typography variant='subtitle1' fontWeight={600}>
            Upcoming Visits
          </Typography>
          <Button component={Link} href='/dashboard/visits' size='small' variant='outlined'>
            View all
          </Button>
        </Box>

        {loading && <CircularProgress size={24} />}
        {error && <Alert severity='error'>{error}</Alert>}

        {!loading && !error && (
          upcoming.length > 0 ? (
            <TableContainer component={Card} variant='outlined'>
              <Table size='small'>
                <TableHead>
                  <TableRow sx={{ backgroundColor: 'action.hover' }}>
                    <TableCell>Date</TableCell>
                    <TableCell>Time</TableCell>
                    <TableCell>Patient</TableCell>
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
                      <TableCell>{v.attributes.patientName ?? '—'}</TableCell>
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

      {/* Quick actions */}
      <Box>
        <Typography variant='subtitle1' fontWeight={600} mb={2}>
          Quick Actions
        </Typography>
        <Box className='grid grid-cols-1 sm:grid-cols-2 gap-4'>
          <QuickLink
            href='/dashboard/patients'
            icon='🩺'
            label='My Patients'
            description='Browse and manage patient records'
          />
          <QuickLink
            href='/dashboard/visits'
            icon='📋'
            label='All Visits'
            description='View visit history and manage appointments'
          />
        </Box>
      </Box>
    </Box>
  )
}
