import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Chip from '@mui/material/Chip'
import Typography from '@mui/material/Typography'
import LinearProgress from '@mui/material/LinearProgress'
import Button from '@mui/material/Button'
import Box from '@mui/material/Box'

export default function UserPlanCard() {
  return (
    <Card sx={{ border: 2, borderColor: 'primary.main' }}>
      <CardContent sx={{ display: 'flex', flexDirection: 'column', gap: 3 }}>
        <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
          <Chip label='Standard' size='small' color='primary' variant='outlined' />
          <Box sx={{ display: 'flex', alignItems: 'flex-start' }}>
            <Typography variant='h6' component='sup' color='primary.main' sx={{ mt: 0.5 }}>$</Typography>
            <Typography variant='h2' color='primary.main' sx={{ lineHeight: 1 }}>99</Typography>
            <Typography component='sub' color='text.secondary' sx={{ alignSelf: 'flex-end', mb: 0.5 }}>/month</Typography>
          </Box>
        </Box>

        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1 }}>
          {['10 Users', 'Up to 10 GB storage', 'Basic Support'].map(item => (
            <Box key={item} sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
              <Box sx={{ width: 8, height: 8, borderRadius: '50%', bgcolor: 'secondary.main', flexShrink: 0 }} />
              <Typography variant='body2'>{item}</Typography>
            </Box>
          ))}
        </Box>

        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 0.5 }}>
          <Box sx={{ display: 'flex', justifyContent: 'space-between' }}>
            <Typography fontWeight={500} color='text.primary'>Days</Typography>
            <Typography fontWeight={500} color='text.primary'>26 of 30 Days</Typography>
          </Box>
          <LinearProgress variant='determinate' value={65} />
          <Typography variant='body2'>4 days remaining</Typography>
        </Box>

        <Button variant='contained' fullWidth>Upgrade Plan</Button>
      </CardContent>
    </Card>
  )
}
