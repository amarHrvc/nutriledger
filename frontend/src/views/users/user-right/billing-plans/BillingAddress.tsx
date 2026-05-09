import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import CardHeader from '@mui/material/CardHeader'
import Grid from '@mui/material/Grid'
import Typography from '@mui/material/Typography'
import { IconPlus } from '@tabler/icons-react'

const address = {
  firstName: 'John', lastName: 'Doe',
  email: 'johndoe@gmail.com',
  taxId: 'TAX-875623', vatNumber: 'SDF754K77',
  address1: '100 Water Plant Avenue,', address2: 'Building 1303 Wake Island',
  contact: '+1(609) 933-44-22', landmark: 'Near Water Plant',
  city: 'New York', state: 'Capholim', zipCode: '403114', country: 'US',
}

function Row({ label, value }: { label: string; value: string }) {
  return (
    <Box sx={{ display: 'flex', gap: 1, py: 0.5 }}>
      <Typography fontWeight={500} color='text.primary' sx={{ minWidth: 140 }}>{label}:</Typography>
      <Typography variant='body2'>{value}</Typography>
    </Box>
  )
}

export default function BillingAddress() {
  return (
    <Card>
      <CardHeader
        title='Billing Address'
        action={
          <Button variant='contained' size='small' startIcon={<IconPlus size={16} />}>
            Edit Address
          </Button>
        }
      />
      <CardContent>
        <Grid container spacing={2}>
          <Grid size={{ xs: 12, md: 6 }}>
            <Row label='Name' value={`${address.firstName} ${address.lastName}`} />
            <Row label='Billing Email' value={address.email} />
            <Row label='Tax ID' value={address.taxId} />
            <Row label='VAT Number' value={address.vatNumber} />
            <Row label='Billing Address' value={`${address.address1} ${address.address2}`} />
          </Grid>
          <Grid size={{ xs: 12, md: 6 }}>
            <Row label='Contact' value={address.contact} />
            <Row label='Landmark' value={address.landmark} />
            <Row label='City' value={address.city} />
            <Row label='Country' value={address.country} />
            <Row label='State' value={address.state} />
            <Row label='Zip Code' value={address.zipCode} />
          </Grid>
        </Grid>
      </CardContent>
    </Card>
  )
}
