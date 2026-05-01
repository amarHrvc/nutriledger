import Box from '@mui/material/Box'
import Typography from '@mui/material/Typography'

import ProfileHeader from './shared/ProfileHeader'
import SectionCard from './shared/SectionCard'
import type { AuthUser } from '@/types/auth'

interface AdminProfileProps {
  user: AuthUser
}

export default function AdminProfile({ user }: AdminProfileProps) {
  return (
    <Box className='flex flex-col gap-4'>
      <ProfileHeader name={user.name} email={user.email} role={user.role} />

      <SectionCard title='System Overview'>
        <Typography variant='body2' color='text.secondary'>
          System statistics will appear here in a future update.
        </Typography>
      </SectionCard>
    </Box>
  )
}
