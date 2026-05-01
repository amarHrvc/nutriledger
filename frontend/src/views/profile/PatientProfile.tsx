import Box from '@mui/material/Box'
import Typography from '@mui/material/Typography'

import ProfileHeader from './shared/ProfileHeader'
import SectionCard from './shared/SectionCard'
import type { AuthUser } from '@/types/auth'

interface PatientProfileProps {
  user: AuthUser
}

export default function PatientProfile({ user }: PatientProfileProps) {
  return (
    <Box className='flex flex-col gap-4'>
      <ProfileHeader name={user.name} email={user.email} role={user.role} />

      <SectionCard title='Medical Information'>
        <Typography variant='body2' color='text.secondary'>
          Your medical record will appear here.
        </Typography>
      </SectionCard>

      <SectionCard title='Emergency Contact'>
        <Typography variant='body2' color='text.secondary'>
          Emergency contact details will appear here.
        </Typography>
      </SectionCard>

      <SectionCard title='Recent Visits'>
        <Typography variant='body2' color='text.secondary'>
          Your visit history will appear here.
        </Typography>
      </SectionCard>
    </Box>
  )
}
