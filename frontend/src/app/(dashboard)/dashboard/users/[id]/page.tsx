'use client'

import { useEffect, useState } from 'react'
import { useParams } from 'next/navigation'

import CircularProgress from '@mui/material/CircularProgress'
import Box from '@mui/material/Box'

import UserDetail from '@views/users/UserDetail'
import type { UserResource } from '@/api/generated/nutriBaseAPI.schemas'

export default function Page() {
  const { id } = useParams<{ id: string }>()
  const [user, setUser] = useState<UserResource | null>(null)
  const [refreshKey, setRefreshKey] = useState(0)

  useEffect(() => {
    fetch(`/api/users/${id}`)
      .then(res => res.json())
      .then(json => setUser(json.data?.user ?? null))
  }, [id, refreshKey])


  return (
    <Box sx={{ p: 3}}>
      {!user
        ? <CircularProgress />
        : <UserDetail user={user} onActionComplete={() => setRefreshKey(k => k + 1)} />
      }
    </Box>
  )
}
