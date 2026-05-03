'use client'
import { useState } from 'react'
import { toast } from 'react-toastify'

import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import CardHeader from '@mui/material/CardHeader'
import Chip from '@mui/material/Chip'
import Divider from '@mui/material/Divider'
import Stack from '@mui/material/Stack'
import Typography from '@mui/material/Typography'

import type { UserResource } from '@/api/generated/nutriBaseAPI.schemas'
import ConfirmDialog from './shared/ConfirmDialog'
import { IconUserCancel } from '@tabler/icons-react'
import { useRouter } from 'next/navigation'

type Action = 'deactivate' | 'restore' | 'force'

const ACTION_LABELS: Record<Action, string> = {
  deactivate: 'Deactivate',
  restore: 'Restore',
  force: 'Permanently Delete'
}

export default function UserDetail({ user, onActionComplete }: { user: UserResource; onActionComplete?: () => void }) {
  const [confirmOpen, setConfirmOpen] = useState(false)
  const [confirmAction, setConfirmAction] = useState<Action | null>(null)
  const router = useRouter()

  const isDeleted = user.attributes.isDeleted

  const openConfirm = (action: Action) => {
    setConfirmAction(action)
    setConfirmOpen(true)
  }

  let isLoading = false

  const onConfirm = async () => {
    if (!confirmAction) return

    try {
      if (confirmAction === 'deactivate') {
        isLoading = true
        await fetch(`/api/users/${user.id}`, { method: 'DELETE' })
        isLoading = false
        router.refresh()
      } else if (confirmAction === 'restore') {
        isLoading = true
        await fetch(`/api/users/${user.id}/restore`, { method: 'POST' })
        isLoading = false
        router.refresh()
      } else if (confirmAction === 'force') {
        await fetch(`/api/users/${user.id}/force`, { method: 'DELETE' })
        router.back()
        isLoading = false
      }

      toast.success(`User ${ACTION_LABELS[confirmAction].toLowerCase()}d successfully.`)
      window.dispatchEvent(new CustomEvent('users:changed'))
      onActionComplete?.()
    } catch {
      toast.error(`Failed to ${ACTION_LABELS[confirmAction].toLowerCase()} user.`)
    } finally {
      setConfirmOpen(false)
      setConfirmAction(null)
    }
  }

  return (
    <>
      <Card>
        <CardHeader title='User Details' />
        <Divider />
        <CardContent>
          <Stack spacing={2}>
            <Box sx={{ display: 'flex', gap: 1, alignItems: 'center' }}>
              <Typography variant='body2' color='text.secondary' sx={{ minWidth: 100 }}>
                Name
              </Typography>
              <Typography variant='body1'>{user.attributes.name}</Typography>
            </Box>
            <Box sx={{ display: 'flex', gap: 1, alignItems: 'center' }}>
              <Typography variant='body2' color='text.secondary' sx={{ minWidth: 100 }}>
                Email
              </Typography>
              <Typography variant='body1'>{user.attributes.email}</Typography>
            </Box>
            <Box sx={{ display: 'flex', gap: 1, alignItems: 'center' }}>
              <Typography variant='body2' color='text.secondary' sx={{ minWidth: 100 }}>
                Role
              </Typography>
              <Chip label={user.attributes.role} size='small' variant='outlined' />
            </Box>
            <Box sx={{ display: 'flex', gap: 1, alignItems: 'center' }}>
              <Typography variant='body2' color='text.secondary' sx={{ minWidth: 100 }}>
                Status
              </Typography>
              <Chip label={isDeleted ? 'Deactivated' : 'Active'} color={isDeleted ? 'error' : 'success'} size='small' />
            </Box>
            <Box sx={{ display: 'flex', gap: 1, alignItems: 'center' }}>
              <Typography variant='body2' color='text.secondary' sx={{ minWidth: 100 }}>
                Created
              </Typography>
              <Typography variant='body1'>{user.attributes.createdAt}</Typography>
            </Box>
            {user.attributes.deletedAt && (
              <Box sx={{ display: 'flex', gap: 1, alignItems: 'center' }}>
                <Typography variant='body2' color='text.secondary' sx={{ minWidth: 100 }}>
                  Deactivated
                </Typography>
                <Typography variant='body1'>{user.attributes.deletedAt}</Typography>
              </Box>
            )}
          </Stack>
        </CardContent>
        <Divider />
        <Box sx={{ display: 'flex', gap: 2, p: 2 }}>
          {isDeleted ? (
            <>
              <Button variant='contained' color='success' onClick={() => openConfirm('restore')}>
                Restore
              </Button>
              <Button variant='outlined' color='error' onClick={() => openConfirm('force')}>
                Permanently Delete
              </Button>
            </>
          ) : (
            <Button
              variant='outlined'
              color='warning'
              onClick={() => openConfirm('deactivate')}
              startIcon={<IconUserCancel />}
            >
              Deactivate
            </Button>
          )}
        </Box>
      </Card>

      <ConfirmDialog
        open={confirmOpen}
        title={`${confirmAction ? ACTION_LABELS[confirmAction] : ''} User`}
        message={`Are you sure you want to ${confirmAction ? ACTION_LABELS[confirmAction].toLowerCase() : ''} this user?`}
        onConfirm={onConfirm}
        onCancel={() => {
          setConfirmOpen(false)
          setConfirmAction(null)
        }}
      />
    </>
  )
}
