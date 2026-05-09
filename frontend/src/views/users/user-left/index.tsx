'use client'

import Grid from '@mui/material/Grid'

import type { UserResource } from '@/api/generated/nutriBaseAPI.schemas'
import UserDetailsCard from './UserDetailsCard'
import UserPlanCard from './UserPlanCard'

interface Props {
  user: UserResource
}

export default function UserLeftOverview({ user }: Props) {
  return (
    <Grid container spacing={6}>
      <Grid size={{ xs: 12 }}>
        <UserDetailsCard user={user} />
      </Grid>
      <Grid size={{ xs: 12 }}>
        <UserPlanCard />
      </Grid>
    </Grid>
  )
}
