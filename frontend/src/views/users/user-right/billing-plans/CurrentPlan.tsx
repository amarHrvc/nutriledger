'use client'

import Alert from '@mui/material/Alert'
import AlertTitle from '@mui/material/AlertTitle'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import CardHeader from '@mui/material/CardHeader'
import Chip from '@mui/material/Chip'
import Grid from '@mui/material/Grid'
import LinearProgress from '@mui/material/LinearProgress'
import Typography from '@mui/material/Typography'

export default function CurrentPlan() {
  return (
    <Card>
      <CardHeader title='Current Plan' />
      <CardContent>
        <Grid container spacing={6}>
          <Grid size={{ xs: 12, md: 6 }} sx={{ display: 'flex', flexDirection: 'column', gap: 3 }}>
            <Box>
              <Typography fontWeight={500} color='text.primary'>Your Current Plan is Basic</Typography>
              <Typography variant='body2'>A simple start for everyone</Typography>
            </Box>
            <Box>
              <Typography fontWeight={500} color='text.primary'>Active until Dec 09, 2025</Typography>
              <Typography variant='body2'>We will send you a notification upon subscription expiration</Typography>
            </Box>
            <Box sx={{ display: 'flex', flexDirection: 'column', gap: 0.5 }}>
              <Box sx={{ display: 'flex', alignItems: 'center', gap: 1 }}>
                <Typography fontWeight={500} color='text.primary'>$99 Per Month</Typography>
                <Chip color='primary' label='Popular' size='small' variant='outlined' />
              </Box>
              <Typography variant='body2'>Standard plan for small to medium businesses</Typography>
            </Box>
          </Grid>
          <Grid size={{ xs: 12, md: 6 }}>
            <Alert icon={false} severity='warning' sx={{ mb: 3 }}>
              <AlertTitle>We need your attention!</AlertTitle>
              Your plan requires update
            </Alert>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 0.5 }}>
              <Typography fontWeight={500} color='text.primary'>Days</Typography>
              <Typography fontWeight={500} color='text.primary'>26 of 30 Days</Typography>
            </Box>
            <LinearProgress variant='determinate' value={80} sx={{ mb: 1, height: 10, borderRadius: 1 }} />
            <Typography variant='body2'>Your plan requires update</Typography>
          </Grid>
          <Grid size={{ xs: 12 }} sx={{ display: 'flex', gap: 2, flexWrap: 'wrap' }}>
            <Button variant='contained'>Upgrade Plan</Button>
            <Button variant='outlined' color='error'>Cancel Subscription</Button>
          </Grid>
        </Grid>
      </CardContent>
    </Card>
  )
}
