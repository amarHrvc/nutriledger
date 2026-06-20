'use client'

import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import CardHeader from '@mui/material/CardHeader'
import Chip from '@mui/material/Chip'
import Divider from '@mui/material/Divider'
import Grid from '@mui/material/Grid'
import Typography from '@mui/material/Typography'
import Box from '@mui/material/Box'


export type SectionRow = {
  label: string
  value: string | number | boolean | null | undefined
  boolean?: true
}

interface Props {
  title: string
  rows: SectionRow[]
}

export default function SocioeconomicSection({ title, rows }: Props) {
  return (
    <Card>
      <CardHeader title={title} />
      <Divider />
      <CardContent>
        <Grid container spacing={2}>
          {rows.map((row) => (
            <Grid key={row.label} size={{ xs: 12, sm: 6 }}>
              <Typography variant='caption' color='text.secondary' display='block'>
                {row.label}
              </Typography>

              {/* boolean override: show Yes/No Chip */}
              {row.boolean === true ? (
                <Box sx={{ mt: 0.5 }}>
                  <Chip
                    label={row.value === true ? 'Yes' : 'No'}
                    size='small'
                    color={(row.value === true ? 'success' : 'default') as any}
                  />
                </Box>
              ) : row.value === null || row.value === undefined || row.value === '' ? (
                <Typography variant='body1'>—</Typography>
              ) : (
                <Typography variant='body1'>
                  {String(row.value)}
                </Typography>
              )}
            </Grid>
          ))}
        </Grid>
      </CardContent>
    </Card>
  )
}
