import Box from '@mui/material/Box'
import Typography from '@mui/material/Typography'

import ProfileHeader from './shared/ProfileHeader'
import SectionCard from './shared/SectionCard'
import type { AuthUser } from '@/types/auth'

interface DoctorProfileProps {
  user: AuthUser
}

export default function DoctorProfile({ user }: DoctorProfileProps) {
  return (
    <Box className='flex flex-col gap-4'>
      <ProfileHeader name={user.name} email={user.email} role={user.role} />

      <SectionCard title='Clinical Activity'>
        <Typography variant='body2' color='text.secondary'>
          Your consultation history will appear here.
        </Typography>
      </SectionCard>
    </Box>
  )
}
