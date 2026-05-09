import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import CardHeader from '@mui/material/CardHeader'
import IconButton from '@mui/material/IconButton'
import TextField from '@mui/material/TextField'
import Typography from '@mui/material/Typography'
import { IconEdit, IconUserPlus } from '@tabler/icons-react'

export default function TwoStepVerification() {
  return (
    <Card>
      <CardHeader
        title='Two-step verification'
        subheader='Keep your account secure with authentication step.'
      />
      <CardContent>
        <Typography variant='body2' fontWeight={500} color='text.primary' sx={{ mb: 1 }}>SMS</Typography>
        <Box sx={{ display: 'flex', alignItems: 'center', gap: 3, mb: 3 }}>
          <TextField placeholder='+1(968) 819-2547' fullWidth size='small' />
          <Box sx={{ display: 'flex', gap: 0.5 }}>
            <IconButton color='default' size='small'><IconEdit size={18} /></IconButton>
            <IconButton color='default' size='small'><IconUserPlus size={18} /></IconButton>
          </Box>
        </Box>
        <Typography variant='body2'>
          Two-factor authentication adds an additional layer of security to your account by requiring more than just a
          password to log in.{' '}
          <Typography component='span' variant='body2' color='primary.main' sx={{ cursor: 'pointer' }}>
            Learn more.
          </Typography>
        </Typography>
      </CardContent>
    </Card>
  )
}
