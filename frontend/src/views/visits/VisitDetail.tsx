'use client'

import { useState } from 'react'
import Link from 'next/link'
import { useRouter } from 'next/navigation'
import { toast } from 'react-toastify'

import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import CardHeader from '@mui/material/CardHeader'
import CircularProgress from '@mui/material/CircularProgress'
import Dialog from '@mui/material/Dialog'
import DialogContent from '@mui/material/DialogContent'
import DialogTitle from '@mui/material/DialogTitle'
import Divider from '@mui/material/Divider'
import Stack from '@mui/material/Stack'
import Typography from '@mui/material/Typography'

import VisitEditForm from './VisitEditForm'
import { useAuth } from '@/context/AuthContext'
import type { VisitResource } from '@/api/generated/nutriBaseAPI.schemas'

interface Props {
	visit: VisitResource
	onUpdated: () => void
}

export default function VisitDetail({ visit, onUpdated }: Props) {
	const { user } = useAuth()
	const router = useRouter()
	const [editOpen, setEditOpen] = useState(false)
	const [deleteLoading, setDeleteLoading] = useState(false)

	const patientId = visit.attributes.patientId ?? ''

	const handleDelete = async () => {
		if (!confirm('Delete this visit? This cannot be undone.')) return
		setDeleteLoading(true)
		try {
			const res = await fetch(`/api/patients/${patientId}/visits/${visit.id}`, { method: 'DELETE' })
			if (!res.ok) {
				const json = await res.json().catch(() => ({}))
				toast.error(json.message ?? `Error ${res.status}`)
				return
			}
			toast.success('Visit deleted.')
			window.dispatchEvent(new CustomEvent('visits:changed'))
			router.push('/dashboard/visits')
		} catch {
			toast.error('Failed to delete visit.')
		} finally {
			setDeleteLoading(false)
		}
	}

	const field = (label: string, value: string | null | undefined) => (
		<Box>
			<Typography variant='caption' color='text.secondary' display='block'>
				{label}
			</Typography>
			<Typography variant='body1'>{value || '—'}</Typography>
		</Box>
	)

	return (
		<Box sx={{ maxWidth: 720, mx: 'auto' }}>
			<Box sx={{ mb: 3 }}>
				<Button component={Link} href='/dashboard/visits' variant='text' size='small'>
					← Back to Visits
				</Button>
			</Box>

			<Card>
				<CardHeader
					title='Visit Details'
					action={
						<Stack direction='row' spacing={1}>
							{visit.attributes.isEditable && (
								<Button variant='outlined' size='small' onClick={() => setEditOpen(true)}>
									Edit
								</Button>
							)}
							{user?.role === 'admin' && (
								<Button
									variant='outlined'
									color='error'
									size='small'
									onClick={handleDelete}
									disabled={deleteLoading}
								>
									{deleteLoading ? <CircularProgress size={16} /> : 'Delete'}
								</Button>
							)}
						</Stack>
					}
				/>
				<Divider />
				<CardContent>
					<Stack spacing={3}>
						<Stack direction={{ xs: 'column', sm: 'row' }} spacing={3}>
							{field('Date', new Date(visit.attributes.date + 'T00:00:00').toLocaleDateString())}
							{field('Time', visit.attributes.time?.slice(0, 5) ?? null)}
						</Stack>
						<Stack direction={{ xs: 'column', sm: 'row' }} spacing={3}>
							{field('Patient', visit.attributes.patientName)}
							{field('Doctor', visit.attributes.doctorName)}
						</Stack>
						<Box>
							<Typography variant='caption' color='text.secondary' display='block'>
								Notes
							</Typography>
							{visit.attributes.notes ? (
								<Typography variant='body1' sx={{ whiteSpace: 'pre-wrap' }}>
									{visit.attributes.notes}
								</Typography>
							) : (
								<Alert severity='info' sx={{ mt: 0.5 }}>
									No notes recorded for this visit.
								</Alert>
							)}
						</Box>
					</Stack>
				</CardContent>
			</Card>

			<Dialog open={editOpen} onClose={() => setEditOpen(false)} maxWidth='sm' fullWidth>
				<DialogTitle>Edit Visit</DialogTitle>
				<DialogContent sx={{ pt: 2 }}>
					<VisitEditForm
						visit={visit}
						patientId={patientId}
						onSuccess={() => {
							setEditOpen(false)
							onUpdated()
						}}
						onCancel={() => setEditOpen(false)}
					/>
				</DialogContent>
			</Dialog>
		</Box>
	)
}
