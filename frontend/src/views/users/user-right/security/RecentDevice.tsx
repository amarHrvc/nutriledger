import Card from '@mui/material/Card'
import CardHeader from '@mui/material/CardHeader'
import Table from '@mui/material/Table'
import TableBody from '@mui/material/TableBody'
import TableCell from '@mui/material/TableCell'
import TableContainer from '@mui/material/TableContainer'
import TableHead from '@mui/material/TableHead'
import TableRow from '@mui/material/TableRow'
import Typography from '@mui/material/Typography'
import Box from '@mui/material/Box'
import { IconBrandWindows, IconBrandAndroid, IconBrandApple, IconDeviceMobile } from '@tabler/icons-react'
import type { ReactElement } from 'react'

type DeviceRow = {
  device: string
  browser: string
  location: string
  recentActivity: string
  icon: ReactElement
}

const rows: DeviceRow[] = [
  { device: 'Dell XPS 15', browser: 'Chrome on Windows', location: 'United States', recentActivity: '10, Jan 2020 20:07', icon: <IconBrandWindows size={22} color='#0ea5e9' /> },
  { device: 'Google Pixel 3a', browser: 'Chrome on Android', location: 'Ghana', recentActivity: '11, Jan 2020 10:16', icon: <IconBrandAndroid size={22} color='#22c55e' /> },
  { device: 'Apple iMac', browser: 'Chrome on MacOS', location: 'Mayotte', recentActivity: '11, Jan 2020 12:10', icon: <IconBrandApple size={22} color='#94a3b8' /> },
  { device: 'Apple iPhone XR', browser: 'Chrome on iPhone', location: 'Mauritania', recentActivity: '12, Jan 2020 8:29', icon: <IconDeviceMobile size={22} color='#ef4444' /> },
]

export default function RecentDevice() {
  return (
    <Card>
      <CardHeader title='Recent Devices' />
      <TableContainer>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>Browser</TableCell>
              <TableCell>Device</TableCell>
              <TableCell>Location</TableCell>
              <TableCell>Recent Activity</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {rows.map((row, i) => (
              <TableRow key={i}>
                <TableCell>
                  <Box sx={{ display: 'flex', alignItems: 'center', gap: 1.5 }}>
                    {row.icon}
                    <Typography variant='body2' color='text.primary'>{row.browser}</Typography>
                  </Box>
                </TableCell>
                <TableCell><Typography variant='body2'>{row.device}</Typography></TableCell>
                <TableCell><Typography variant='body2'>{row.location}</Typography></TableCell>
                <TableCell><Typography variant='body2'>{row.recentActivity}</Typography></TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>
    </Card>
  )
}
