'use client'

import { useState } from 'react'

import Alert from '@mui/material/Alert'
import AlertTitle from '@mui/material/AlertTitle'
import Button from '@mui/material/Button'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import CardHeader from '@mui/material/CardHeader'
import Grid from '@mui/material/Grid'
import IconButton from '@mui/material/IconButton'
import InputAdornment from '@mui/material/InputAdornment'
import TextField from '@mui/material/TextField'
import { IconEye, IconEyeOff } from '@tabler/icons-react'

export default function ChangePassword() {
  const [showPassword, setShowPassword] = useState(false)
  const [showConfirm, setShowConfirm] = useState(false)

  return (
    <Card>
      <CardHeader title='Change Password' />
      <CardContent sx={{ display: 'flex', flexDirection: 'column', gap: 3 }}>
        <Alert icon={false} severity='warning'>
          <AlertTitle>Ensure that these requirements are met</AlertTitle>
          Minimum 8 characters long, uppercase &amp; symbol
        </Alert>
        <Grid container spacing={4}>
          <Grid size={{ xs: 12, sm: 6 }}>
            <TextField
              fullWidth
              label='New Password'
              type={showPassword ? 'text' : 'password'}
              slotProps={{
                input: {
                  endAdornment: (
                    <InputAdornment position='end'>
                      <IconButton edge='end' onClick={() => setShowPassword(v => !v)} onMouseDown={e => e.preventDefault()}>
                        {showPassword ? <IconEyeOff size={20} /> : <IconEye size={20} />}
                      </IconButton>
                    </InputAdornment>
                  )
                }
              }}
            />
          </Grid>
          <Grid size={{ xs: 12, sm: 6 }}>
            <TextField
              fullWidth
              label='Confirm New Password'
              type={showConfirm ? 'text' : 'password'}
              slotProps={{
                input: {
                  endAdornment: (
                    <InputAdornment position='end'>
                      <IconButton edge='end' onClick={() => setShowConfirm(v => !v)} onMouseDown={e => e.preventDefault()}>
                        {showConfirm ? <IconEyeOff size={20} /> : <IconEye size={20} />}
                      </IconButton>
                    </InputAdornment>
                  )
                }
              }}
            />
          </Grid>
          <Grid size={{ xs: 12 }}>
            <Button variant='contained'>Change Password</Button>
          </Grid>
        </Grid>
      </CardContent>
    </Card>
  )
}
