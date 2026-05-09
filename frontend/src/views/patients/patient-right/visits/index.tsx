'use client'

import { useEffect, useState } from 'react'
import Link from 'next/link'

import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import CircularProgress from '@mui/material/CircularProgress'
import Dialog from '@mui/material/Dialog'
import DialogContent from '@mui/material/DialogContent'
import DialogTitle from '@mui/material/DialogTitle'
import Stack from '@mui/material/Stack'
import Table from '@mui/material/Table'
import TableBody from '@mui/material/TableBody'
import TableCell from '@mui/material/TableCell'
import TableContainer from '@mui/material/TableContainer'
import TableHead from '@mui/material/TableHead'
import TableRow from '@mui/material/TableRow'
import Typography from '@mui/material/Typography'

import type { PatientResource, VisitResource } from '@/api/generated/nutriBaseAPI.schemas'
import VisitForm from '@/views/visits/VisitForm'
import VisitEditForm from '@/views/visits/VisitEditForm'

export default function VisitsTab({ patient }: { patient: PatientResource }) {
	const [visits, setVisits] = useState<VisitResource[]>([])
	const [loading, setLoading] = useState(true)
	const [error, setError] = useState<string | null>(null)
	const [addDialogOpen, setAddDialogOpen] = useState(false)
	const [editDialogOpen, setEditDialogOpen] = useState(false)
	const [editingVisit, setEditingVisit] = useState<VisitResource | null>(null)

	const fetchVisits = async () => {
		try {
			const res = await fetch(`/api/patients/${patient.id}/visits`)
			const json = await res.json()

			if (!res.ok) {
				setError(json?.message ?? 'Failed to load visits.')
				return
			}

			setVisits(json.data || [])
			setError(null)
		} catch {
			setError('Failed to load visits.')
		} finally {
			setLoading(false)
		}
	}

	useEffect(() => {
		fetchVisits()
	}, [patient.id])

	useEffect(() => {
		const handleVisitsChanged = () => {
			fetchVisits()
		}
		window.addEventListener('visits:changed', handleVisitsChanged)
		return () => window.removeEventListener('visits:changed', handleVisitsChanged)
	}, [patient.id])

	const handleAddSuccess = () => {
		setAddDialogOpen(false)
		fetchVisits()
	}

	const handleEditSuccess = () => {
		setEditDialogOpen(false)
		setEditingVisit(null)
		fetchVisits()
	}

	const handleEditClick = (visit: VisitResource) => {
		setEditingVisit(visit)
		setEditDialogOpen(true)
	}

	if (error) {
		return (
			<Card>
				<CardContent>
					<Alert severity='error'>{error}</Alert>
				</CardContent>
			</Card>
		)
	}

	if (loading) {
		return (
			<Box sx={{ display: 'flex', justifyContent: 'center', py: 8 }}>
				<CircularProgress />
			</Box>
		)
	}

	if (!visits || visits.length === 0) {
		return (
			<Card>
				<CardContent>
					<Stack spacing={2}>
						<Typography variant='body2' color='text.secondary' sx={{ textAlign: 'center', py: 4 }}>
							No visits recorded.
						</Typography>
						<Box sx={{ display: 'flex', justifyContent: 'center' }}>
							<Button variant='contained' onClick={() => setAddDialogOpen(true)}>
								Add Visit
							</Button>
						</Box>
					</Stack>
				</CardContent>
			</Card>
		)
	}

	return (
		<>
			<Card>
				<CardContent>
					<Box sx={{ mb: 2, display: 'flex', justifyContent: 'flex-end' }}>
						<Button variant='contained' onClick={() => setAddDialogOpen(true)}>
							Add Visit
						</Button>
					</Box>

					<TableContainer>
						<Table>
							<TableHead>
								<TableRow sx={{ backgroundColor: '#f5f5f5' }}>
									<TableCell>Date</TableCell>
									<TableCell>Time</TableCell>
									<TableCell>Doctor</TableCell>
									<TableCell>Notes</TableCell>
									<TableCell align='right'>Actions</TableCell>
								</TableRow>
							</TableHead>
							<TableBody>
								{visits.map(visit => (
									<TableRow key={visit.id}>
										<TableCell>{visit.attributes.date}</TableCell>
										<TableCell>{visit.attributes.time || '—'}</TableCell>
										<TableCell>{visit.attributes.doctorName || '—'}</TableCell>
										<TableCell>{visit.attributes.notes || '—'}</TableCell>
										<TableCell align='right'>
											<Box sx={{ display: 'flex', gap: 1, justifyContent: 'flex-end' }}>
												<Button
													size='small'
													variant='outlined'
													component={Link}
													href={`/dashboard/visits/${visit.id}?patient=${patient.id}`}
												>
													View
												</Button>
												<Button
													size='small'
													variant='outlined'
													onClick={() => handleEditClick(visit)}
													disabled={!visit.attributes.isEditable}
												>
													Edit
												</Button>
											</Box>
										</TableCell>
									</TableRow>
								))}
							</TableBody>
						</Table>
					</TableContainer>
				</CardContent>
			</Card>

			{/* Add Visit Dialog */}
			<Dialog open={addDialogOpen} onClose={() => setAddDialogOpen(false)} maxWidth='sm' fullWidth>
				<DialogTitle>Add Visit</DialogTitle>
				<DialogContent>
					<Box sx={{ pt: 2 }}>
						<VisitForm
							patientId={patient.id}
							onSuccess={handleAddSuccess}
							onCancel={() => setAddDialogOpen(false)}
						/>
					</Box>
				</DialogContent>
			</Dialog>

			{/* Edit Visit Dialog */}
			{editingVisit && (
				<Dialog open={editDialogOpen} onClose={() => setEditDialogOpen(false)} maxWidth='sm' fullWidth>
					<DialogTitle>Edit Visit</DialogTitle>
					<DialogContent>
						<Box sx={{ pt: 2 }}>
							<VisitEditForm
								visit={editingVisit}
								patientId={patient.id}
								onSuccess={handleEditSuccess}
								onCancel={() => setEditDialogOpen(false)}
							/>
						</Box>
					</DialogContent>
				</Dialog>
			)}
		</>
	)
}
