'use client'

import { useCallback, useEffect, useState } from 'react'
import { useParams } from 'next/navigation'

import CircularProgress from '@mui/material/CircularProgress'
import Box from '@mui/material/Box'

import UserDetail from '@views/users/UserDetail'
import type { UserResource } from '@/api/generated/nutriBaseAPI.schemas'

export default function Page() {
  const { id } = useParams<{ id: string }>()
  const [user, setUser] = useState<UserResource | null>(null)

  const loadUser = useCallback(() => {
    fetch(`/api/users/${id}`)
      .then(res => res.json())
      .then(json => setUser(json.data?.user ?? null))
  }, [id])

  useEffect(() => {
    loadUser()
    window.addEventListener('users:changed', loadUser)

    return () => window.removeEventListener('users:changed', loadUser)
  }, [loadUser])

  return (
    <Box sx={{ p: 3 }}>
      {!user ? <CircularProgress /> : <UserDetail user={user} />}
    </Box>
  )
}
