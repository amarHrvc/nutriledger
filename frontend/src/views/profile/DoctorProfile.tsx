import { useEffect, useState } from 'react'

import Box from '@mui/material/Box'
import Typography from '@mui/material/Typography'

import ProfileHeader from './shared/ProfileHeader'
import SectionCard from './shared/SectionCard'
import type { AuthUser } from '@/types/auth'
import { UserResource } from '@/api/generated/nutriBaseAPI.schemas'
import InfoRow from '@views/profile/shared/InfoRow'
import CircularProgress from '@mui/material/CircularProgress'

interface DoctorProfileProps {
  user: AuthUser
}

export default function DoctorProfile({ user }: DoctorProfileProps) {
  const [doctor, setDoctor] = useState<UserResource | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);


  useEffect(() => {
    fetch('/api/profile/me')
      .then(res => {
        if (!res.ok) throw Error(res.statusText)

        return res.json()
      })
      .then(data => {
        console.log("|||||||| >>>>>>>>>>>>>>>>>", data)
        setDoctor(data.user ?? null)
      })
      .catch(err => setError(err.message ?? 'Somwthing went wrong'))
      .finally(() => setIsLoading(false))
  }, [])

  console.log('DoctorProfile[setDoctor] ::: ', doctor)

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

      {/*<pre style={{ fontSize: 11 }}>{JSON.stringify(doctor, null, 2)}</pre>*/}
      {!isLoading && !error && (
        <>
          <SectionCard title='Personal Information !!!!'>
            <InfoRow label='Email' value={doctor?.attributes.email} />
            <InfoRow label='Name' value={doctor?.attributes.name} />
            <InfoRow label='created At' value={doctor?.attributes.createdAt} />
          </SectionCard>
        </>
      )}
    </Box>
  )
}
