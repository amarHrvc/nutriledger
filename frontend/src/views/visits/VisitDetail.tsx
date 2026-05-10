'use client'

import { useCallback, useEffect, useState } from 'react'
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
import DialogActions from '@mui/material/DialogActions'
import DialogContent from '@mui/material/DialogContent'
import DialogContentText from '@mui/material/DialogContentText'
import DialogTitle from '@mui/material/DialogTitle'
import Divider from '@mui/material/Divider'
import MuiLink from '@mui/material/Link'
import Stack from '@mui/material/Stack'
import Typography from '@mui/material/Typography'

import VitalsCard from './VitalsCard'
import VitalsForm from './VitalsForm'
import VisitEditForm from './VisitEditForm'
import { useAuth } from '@/context/AuthContext'
import type { VisitResource } from '@/api/generated/nutriBaseAPI.schemas'
import type { VitalSignResource } from './vitals.types'

interface Props {
	visit: VisitResource
	onUpdated: () => void
}

export default function VisitDetail({ visit, onUpdated }: Props) {
	const { user } = useAuth()
	const router = useRouter()
	const [editOpen, setEditOpen] = useState(false)
	const [deleteLoading, setDeleteLoading] = useState(false)

	const [vitals, setVitals] = useState<VitalSignResource | null>(null)
	const [vitalsLoading, setVitalsLoading] = useState(true)
	const [vitalsFormOpen, setVitalsFormOpen] = useState(false)
	const [deleteVitalsOpen, setDeleteVitalsOpen] = useState(false)
	const [deleteVitalsLoading, setDeleteVitalsLoading] = useState(false)

	const patientId = visit.attributes.patientId ?? ''
	const doctorId = visit.relationships.doctor.data?.id ?? ''

	const canEdit =
		user?.role === 'admin' || (user?.role === 'doktor' && doctorId === String(user?.id))
	const canDelete = user?.role === 'admin'

	const fetchVitals = useCallback(async () => {
		setVitalsLoading(true)
		try {
			const res = await fetch(`/api/patients/${patientId}/visits/${visit.id}/vitals`)
			if (res.status === 404) {
				setVitals(null)
				return
			}
			if (!res.ok) return
			const json = await res.json()
			setVitals((json?.data ?? null) as VitalSignResource | null)
		} catch {
			// leave vitals as null on network error
		} finally {
			setVitalsLoading(false)
		}
	}, [patientId, visit.id])

	useEffect(() => {
		if (patientId) fetchVitals()
	}, [patientId, fetchVitals])

	const handleDeleteVisit = async () => {
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

	const confirmDeleteVitals = async () => {
		setDeleteVitalsLoading(true)
		try {
			const res = await fetch(`/api/patients/${patientId}/visits/${visit.id}/vitals`, {
				method: 'DELETE',
			})
			if (res.status === 204 || res.ok) {
				toast.success('Vital signs deleted.')
				setVitals(null)
				setDeleteVitalsOpen(false)
			} else {
				const json = await res.json().catch(() => ({}))
				toast.error(json.message ?? `Error ${res.status}`)
			}
		} catch {
			toast.error('Failed to delete vital signs.')
		} finally {
			setDeleteVitalsLoading(false)
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
		<Box>
			<Box sx={{ mb: 3 }}>
				<Button component={Link} href='/dashboard/visits' variant='text' size='small'>
					← Back to Visits
				</Button>
			</Box>

			<Card sx={{ mb: 3 }}>
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
									onClick={handleDeleteVisit}
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
							<Box>
								<Typography variant='caption' color='text.secondary' display='block'>
									Patient
								</Typography>
								<MuiLink component={Link} href={`/dashboard/patients/${patientId}`} variant='body1' underline='hover'>
									{visit.attributes.patientName || '—'}
								</MuiLink>
							</Box>
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

			{/* Vital Signs section */}
			{vitalsLoading ? (
				<Box sx={{ display: 'flex', justifyContent: 'center', py: 3 }}>
					<CircularProgress />
				</Box>
			) : vitals !== null ? (
				<VitalsCard
					vitals={vitals}
					patientId={patientId}
					visitId={visit.id}
					canEdit={canEdit}
					canDelete={canDelete}
					onEdit={() => setVitalsFormOpen(true)}
					onDelete={() => setDeleteVitalsOpen(true)}
				/>
			) : canEdit ? (
				<Box sx={{ textAlign: 'center', py: 3 }}>
					<Button variant='outlined' onClick={() => setVitalsFormOpen(true)}>
						Record vital signs
					</Button>
				</Box>
			) : null}

			{vitalsFormOpen && (
				<VitalsForm
					patientId={patientId}
					visitId={visit.id}
					existing={vitals ?? undefined}
					onSuccess={() => {
						setVitalsFormOpen(false)
						fetchVitals()
					}}
					onClose={() => setVitalsFormOpen(false)}
				/>
			)}

			{/* Delete vitals confirmation */}
			<Dialog open={deleteVitalsOpen} onClose={() => setDeleteVitalsOpen(false)} maxWidth='xs' fullWidth>
				<DialogTitle>Delete vital signs?</DialogTitle>
				<DialogContent>
					<DialogContentText>This action cannot be undone.</DialogContentText>
				</DialogContent>
				<DialogActions>
					<Button onClick={() => setDeleteVitalsOpen(false)} disabled={deleteVitalsLoading}>
						Cancel
					</Button>
					<Button
						variant='contained'
						color='error'
						onClick={confirmDeleteVitals}
						disabled={deleteVitalsLoading}
						startIcon={deleteVitalsLoading ? <CircularProgress size={16} /> : null}
					>
						Delete
					</Button>
				</DialogActions>
			</Dialog>

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
