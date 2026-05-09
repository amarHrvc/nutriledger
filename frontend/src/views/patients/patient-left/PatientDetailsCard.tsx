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
import Dialog from '@mui/material/Dialog'
import DialogTitle from '@mui/material/DialogTitle'
import DialogContent from '@mui/material/DialogContent'
import Divider from '@mui/material/Divider'
import Typography from '@mui/material/Typography'
import { IconUserCancel } from '@tabler/icons-react'

import type { PatientResource } from '@/api/generated/nutriBaseAPI.schemas'
import ConfirmDialog from '@views/users/shared/ConfirmDialog'
import PatientEditForm from '../PatientEditForm'

function initials(name: string): string {
	return name
		.split(' ')
		.slice(0, 2)
		.map(n => n[0])
		.join('')
		.toUpperCase()
}

interface Props {
	patient: PatientResource
}

export default function PatientDetailsCard({ patient }: Props) {
	const [confirmOpen, setConfirmOpen] = useState(false)
	const [editOpen, setEditOpen] = useState(false)
	const [loading, setLoading] = useState(false)
	const router = useRouter()

	const { fullName = '', dateOfBirth = '', gender = '', phone = '', createdAt = '' } = patient.attributes

	const openConfirm = () => {
		setConfirmOpen(true)
	}

	const onConfirm = async () => {
		setLoading(true)

		try {
			const res = await fetch(`/api/patients/${patient.id}`, { method: 'DELETE' })

			if (!res.ok) {
				const json = await res.json().catch(() => ({}))
				toast.error(json?.message ?? 'Failed to suspend patient.')
				return
			}

			toast.success('Patient suspended.')
			router.push('/dashboard/patients')
		} catch {
			toast.error('Failed to suspend patient.')
		} finally {
			setLoading(false)
			setConfirmOpen(false)
		}
	}

	return (
		<>
			<Card>
				<CardContent sx={{ display: 'flex', flexDirection: 'column', gap: 6, pt: 6 }}>
					{/* Avatar + name + gender chip */}
					<Box sx={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 4 }}>
						<Box sx={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 1 }}>
							<Avatar sx={{ width: 120, height: 120, fontSize: 42, bgcolor: 'primary.main' }}>
								{initials(fullName)}
							</Avatar>
							<Typography variant='h5'>{fullName}</Typography>
						</Box>
						{gender && <Chip label={gender} size='small' variant='outlined' />}
					</Box>

					{/* Stats row */}
					<Box sx={{ display: 'flex', justifyContent: 'space-around', flexWrap: 'wrap', gap: 4 }}>
						<Box sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
							<Box>
								<Typography variant='h5'>—</Typography>
								<Typography variant='body2' color='text.secondary'>
									Visits
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
							{dateOfBirth && (
								<Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 0.5 }}>
									<Typography fontWeight={500} color='text.primary'>
										Date of Birth:
									</Typography>
									<Typography>{dateOfBirth}</Typography>
								</Box>
							)}
							{phone && (
								<Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 0.5 }}>
									<Typography fontWeight={500} color='text.primary'>
										Phone:
									</Typography>
									<Typography>{phone}</Typography>
								</Box>
							)}
							{createdAt && (
								<Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 0.5 }}>
									<Typography fontWeight={500} color='text.primary'>
										Member since:
									</Typography>
									<Typography>{createdAt}</Typography>
								</Box>
							)}
						</Box>
					</Box>

					{/* Action buttons */}
					<Box sx={{ display: 'flex', gap: 2, justifyContent: 'center' }}>
						<Button variant='outlined' onClick={() => setEditOpen(true)} disabled={loading}>
							Edit
						</Button>
						<Button
							variant='tonal'
							color='error'
							onClick={openConfirm}
							startIcon={<IconUserCancel size={18} />}
							disabled={loading}
						>
							Suspend
						</Button>
					</Box>
				</CardContent>
			</Card>

			<ConfirmDialog
				open={confirmOpen}
				title='Suspend Patient'
				message='Are you sure you want to suspend this patient?'
				onConfirm={onConfirm}
				onCancel={() => {
					setConfirmOpen(false)
				}}
			/>

			<Dialog open={editOpen} onClose={() => setEditOpen(false)} fullWidth maxWidth='sm'>
				<DialogTitle>Edit Patient</DialogTitle>
				<DialogContent sx={{ pt: 2 }}>
					<PatientEditForm
						patient={patient}
						onSuccess={() => setEditOpen(false)}
						onCancel={() => setEditOpen(false)}
					/>
				</DialogContent>
			</Dialog>
		</>
	)
}
