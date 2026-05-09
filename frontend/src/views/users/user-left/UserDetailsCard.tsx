'use client'

import { useState } from 'react'
import { toast } from 'react-toastify'
import { useRouter } from 'next/navigation'

import Avatar from '@mui/material/Avatar'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Chip from '@mui/material/Chip'
import Divider from '@mui/material/Divider'
import Typography from '@mui/material/Typography'
import { IconUserCancel, IconCalendarStats, IconStethoscope } from '@tabler/icons-react'

import type { UserResource } from '@/api/generated/nutriBaseAPI.schemas'
import ConfirmDialog from '../shared/ConfirmDialog'

type Action = 'deactivate' | 'restore' | 'force'

const ACTION_LABELS: Record<Action, string> = {
  deactivate: 'Deactivate',
  restore: 'Restore',
  force: 'Permanently Delete',
}

const ROLE_COLOR: Record<string, 'default' | 'primary' | 'secondary' | 'error' | 'warning' | 'info' | 'success'> = {
  admin: 'error',
  doktor: 'primary',
  pacijent: 'success',
}

function initials(name: string): string {
  return name
    .split(' ')
    .slice(0, 2)
    .map(n => n[0])
    .join('')
    .toUpperCase()
}

interface Props {
  user: UserResource
}

export default function UserDetailsCard({ user }: Props) {
  const [confirmOpen, setConfirmOpen] = useState(false)
  const [confirmAction, setConfirmAction] = useState<Action | null>(null)
  const [loading, setLoading] = useState(false)
  const router = useRouter()

  const { name = '', email = '', role = '', createdAt, deletedAt, isDeleted } = user.attributes

  const openConfirm = (action: Action) => {
    setConfirmAction(action)
    setConfirmOpen(true)
  }

  const onConfirm = async () => {
    if (!confirmAction) return
    setLoading(true)

    try {
      let res: Response

      if (confirmAction === 'deactivate') {
        res = await fetch(`/api/users/${user.id}`, { method: 'DELETE' })
      } else if (confirmAction === 'restore') {
        res = await fetch(`/api/users/${user.id}/restore`, { method: 'POST' })
      } else {
        res = await fetch(`/api/users/${user.id}/force`, { method: 'DELETE' })
      }

      if (!res.ok) {
        const json = await res.json().catch(() => ({}))
        toast.error(json?.message ?? `Failed to ${ACTION_LABELS[confirmAction].toLowerCase()} user.`)
        return
      }

      toast.success(`User ${ACTION_LABELS[confirmAction].toLowerCase()}d successfully.`)

      if (confirmAction === 'force') {
        router.push('/dashboard/users')
        return
      }

      window.dispatchEvent(new CustomEvent('users:changed'))
    } catch {
      toast.error(`Failed to ${ACTION_LABELS[confirmAction].toLowerCase()} user.`)
    } finally {
      setLoading(false)
      setConfirmOpen(false)
      setConfirmAction(null)
    }
  }

  return (
    <>
      <Card>
        <CardContent sx={{ display: 'flex', flexDirection: 'column', gap: 6, pt: 6 }}>
          {/* Avatar + name + role chip */}
          <Box sx={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 4 }}>
            <Box sx={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 1 }}>
              <Avatar sx={{ width: 120, height: 120, fontSize: 42, bgcolor: 'primary.main' }}>{initials(name)}</Avatar>
              <Typography variant='h5'>{name}</Typography>
            </Box>
            <Chip label={role} color={ROLE_COLOR[role] ?? 'default'} size='small' variant='outlined' />
          </Box>

          {/* Stats row */}
          <Box sx={{ display: 'flex', justifyContent: 'space-around', flexWrap: 'wrap', gap: 4 }}>
            <Box sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
              <Avatar variant='rounded' sx={{ bgcolor: 'white', color: 'primary.main', width: 44, height: 44 }}>
                <IconCalendarStats size={22} />
              </Avatar>
              <Box>
                <Typography variant='h5'>—</Typography>
                <Typography variant='body2' color='text.secondary'>
                  Visits
                </Typography>
              </Box>
            </Box>
            <Box sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
              <Avatar
                variant='rounded'
                sx={{ bgcolor: 'white', color: 'primary.main', borderColor: 'primary.main', width: 44, height: 44 }}
              >
                <IconStethoscope size={22} />
              </Avatar>
              <Box>
                <Typography variant='h5'>—</Typography>
                <Typography variant='body2' color='text.secondary'>
                  Patients
                </Typography>
              </Box>
            </Box>
          </Box>

          {/* Details */}
          <Box>
            <Typography variant='h5' sx={{ mb: 1 }}>
              Details
            </Typography>
            <Divider sx={{ mb: 2 }} />
            <Box sx={{ display: 'flex', flexDirection: 'column', gap: 1 }}>
              <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 0.5 }}>
                <Typography fontWeight={500} color='text.primary'>
                  Email:
                </Typography>
                <Typography>{email}</Typography>
              </Box>
              <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 0.5 }}>
                <Typography fontWeight={500} color='text.primary'>
                  Status:
                </Typography>
                <Chip
                  label={isDeleted ? 'Deactivated' : 'Active'}
                  color={isDeleted ? 'error' : 'success'}
                  size='small'
                />
              </Box>
              <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 0.5 }}>
                <Typography fontWeight={500} color='text.primary'>
                  Role:
                </Typography>
                <Typography color='text.primary'>{role}</Typography>
              </Box>
              <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 0.5 }}>
                <Typography fontWeight={500} color='text.primary'>
                  Member since:
                </Typography>
                <Typography color='text.primary'>{createdAt}</Typography>
              </Box>
              {deletedAt && (
                <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 0.5 }}>
                  <Typography fontWeight={500} color='text.primary'>
                    Deactivated:
                  </Typography>
                  <Typography color='text.primary'>{deletedAt}</Typography>
                </Box>
              )}
            </Box>
          </Box>

          {/* Action buttons */}
          <Box sx={{ display: 'flex', gap: 2, justifyContent: 'center' }}>
            {isDeleted ? (
              <>
                <Button variant='contained' color='success' onClick={() => openConfirm('restore')} disabled={loading}>
                  Restore
                </Button>
                <Button variant='outlined' color='error' onClick={() => openConfirm('force')} disabled={loading}>
                  Force Delete
                </Button>
              </>
            ) : (
              <Button
                variant='tonal'
                color='error'
                onClick={() => openConfirm('deactivate')}
                startIcon={<IconUserCancel size={18} />}
                disabled={loading}
              >
                Suspend
              </Button>
            )}
          </Box>
        </CardContent>
      </Card>

      <ConfirmDialog
        open={confirmOpen}
        title={`${confirmAction ? ACTION_LABELS[confirmAction] : ''} User`}
        message={`Are you sure you want to ${confirmAction ? ACTION_LABELS[confirmAction]?.toLowerCase() : ''} this user?`}
        onConfirm={onConfirm}
        onCancel={() => {
          setConfirmOpen(false)
          setConfirmAction(null)
        }}
      />
    </>
  )
}
