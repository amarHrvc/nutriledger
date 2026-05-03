'use client'

import Grid from '@mui/material/Grid'

import type { UserResource } from '@/api/generated/nutriBaseAPI.schemas'
import UserLeftCard from './user-left'
import UserRightTabs from './user-right'

interface Props {
  user: UserResource
}

export default function UserDetail({ user }: Props) {
  return (
    <Grid container spacing={6}>
      <Grid size={{ xs: 12, md: 5, lg: 4 }}>
        <UserLeftCard user={user} />
      </Grid>
      <Grid size={{ xs: 12, md: 7, lg: 8 }}>
        <UserRightTabs user={user} />
      </Grid>
    </Grid>
  )
}
