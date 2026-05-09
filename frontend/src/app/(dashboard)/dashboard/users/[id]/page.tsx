'use client'

import { useCallback, useEffect, useState } from 'react'
import { useParams } from 'next/navigation'

import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import CircularProgress from '@mui/material/CircularProgress'

import UserDetail from '@views/users/UserDetail'
import type { UserResource } from '@/api/generated/nutriBaseAPI.schemas'

export default function Page() {
  const { id } = useParams<{ id: string }>()
  const [user, setUser] = useState<UserResource | null>(null)
  const [error, setError] = useState<string | null>(null)

  const loadUser = useCallback(async () => {
    try {
      const res = await fetch(`/api/users/${id}`)
      const json = await res.json()

      if (!res.ok) {
        setError(json?.message ?? `Error ${res.status}`)
        return
      }

      setUser(json.data?.user ?? null)
      setError(null)
    } catch {
      setError('Failed to load user.')
    }
  }, [id])

  useEffect(() => {
    loadUser()
    window.addEventListener('users:changed', loadUser)

    return () => window.removeEventListener('users:changed', loadUser)
  }, [loadUser])

  if (error) {
    return (
      <Box sx={{ p: 3 }}>
        <Alert severity='error'>{error}</Alert>
      </Box>
    )
  }

  return <Box sx={{ p: 3 }}>{!user ? <CircularProgress /> : <UserDetail user={user} />}</Box>
}
