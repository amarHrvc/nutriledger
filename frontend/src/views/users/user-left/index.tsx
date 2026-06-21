'use client'

import type { UserResource } from '@/api/generated/nutriBaseAPI.schemas'
import UserDetailsCard from './UserDetailsCard'

interface Props {
  user: UserResource
}

export default function UserLeftOverview({ user }: Props) {
  return <UserDetailsCard user={user} />
}
