import Avatar from '@mui/material/Avatar'
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Chip from '@mui/material/Chip'
import Typography from '@mui/material/Typography'

const ROLE_LABELS: Record<string, string> = {
  admin: 'Administrator',
  doktor: 'Doctor',
  pacijent: 'Patient'
}

const ROLE_COLORS: Record<string, 'primary' | 'secondary' | 'success'> = {
  admin: 'primary',
  doktor: 'secondary',
  pacijent: 'success'
}

interface ProfileHeaderProps {
  name: string
  email: string
  role: string
}

export default function ProfileHeader({ name, email, role }: ProfileHeaderProps) {
  const initials = name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)

  return (
    <Card>
      <CardContent className='flex items-center gap-4'>
        <Avatar sx={{ width: 72, height: 72, fontSize: 28, bgcolor: 'primary.main' }}>{initials}</Avatar>
        <Box className='flex flex-col gap-1'>
          <Typography variant='h5'>{name}</Typography>
          <Typography variant='body2' color='text.secondary'>
            {email}
          </Typography>
          <Chip
            label={ROLE_LABELS[role] ?? role}
            color={ROLE_COLORS[role] ?? 'default'}
            size='small'
            sx={{ width: 'fit-content', mt: 0.5 }}
          />
        </Box>
      </CardContent>
    </Card>
  )
}
