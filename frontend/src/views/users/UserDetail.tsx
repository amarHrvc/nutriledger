'use client'

import Grid from '@mui/material/Grid'

import type { UserResource } from '@/api/generated/nutriBaseAPI.schemas'
import UserLeftCard from './user-left'
// user-left/index.tsx now orchestrates UserDetailsCard + UserPlanCard
import UserRightTabs from './user-right'

interface Props {
  user: UserResource
  onActionComplete?: () => void
}

export default function UserDetail({ user, onActionComplete }: Props) {
  return (
    <Grid container spacing={6}>
      <Grid size={{ xs: 12, md: 5, lg: 4 }}>
        <UserLeftCard user={user} onActionComplete={onActionComplete} />
      </Grid>
      <Grid size={{ xs: 12, md: 7, lg: 8 }}>
        <UserRightTabs user={user} />
      </Grid>
    </Grid>
  )
}
