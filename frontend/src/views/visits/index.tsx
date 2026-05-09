'use client'

import React, { useEffect, useState } from 'react'
import Link from 'next/link'
import {
	Box,
	Button,
	Chip,
	CircularProgress,
	Alert,
	Table,
	TableBody,
	TableCell,
	TableContainer,
	TableHead,
	TableRow,
	Paper,
	Toolbar,
	Typography,
	Dialog,
	DialogTitle,
	DialogContent,
} from '@mui/material'

import VisitForm from './VisitForm'
import VisitEditForm from './VisitEditForm'
import type { VisitResource } from '@/api/generated/nutriBaseAPI.schemas'

interface ApiResponse {
	data: VisitResource[]
	meta?: {
		current_page: number
		total: number
	}
}

export default function VisitsView() {
	const [loading, setLoading] = useState(true)
	const [error, setError] = useState<string | null>(null)
	const [visits, setVisits] = useState<VisitResource[]>([])
	const [formOpen, setFormOpen] = useState(false)
	const [editOpen, setEditOpen] = useState(false)
	const [selectedVisit, setSelectedVisit] = useState<VisitResource | null>(null)

	const fetchVisits = async () => {
		try {
			setLoading(true)
			setError(null)
			const res = await fetch('/api/visits')
			if (!res.ok) {
				throw new Error(`HTTP ${res.status}`)
			}
			const json: ApiResponse = await res.json()
			setVisits(json.data || [])
		} catch (e) {
			setError(String(e))
		} finally {
			setLoading(false)
		}
	}

	useEffect(() => {
		fetchVisits()

		const handleRefresh = () => {
			fetchVisits()
		}

		window.addEventListener('visits:changed', handleRefresh)
		return () => {
			window.removeEventListener('visits:changed', handleRefresh)
		}
	}, [])

	const today = new Date().toISOString().split('T')[0]
	const upcoming = visits
		.filter(v => v.attributes.date >= today)
		.sort((a, b) => {
			const dateCompare = a.attributes.date.localeCompare(b.attributes.date)
			if (dateCompare !== 0) return dateCompare
			const aTime = a.attributes.time || '00:00'
			const bTime = b.attributes.time || '00:00'
			return aTime.localeCompare(bTime)
		})

	const past = visits
		.filter(v => v.attributes.date < today)
		.sort((a, b) => b.attributes.date.localeCompare(a.attributes.date))

	const handleAddVisit = () => {
		setFormOpen(true)
	}

	const handleEditVisit = (visit: VisitResource) => {
		setSelectedVisit(visit)
		setEditOpen(true)
	}

	const handleFormClose = () => {
		setFormOpen(false)
	}

	const handleEditClose = () => {
		setEditOpen(false)
		setSelectedVisit(null)
	}

	const handleFormSuccess = () => {
		setFormOpen(false)
		fetchVisits()
	}

	const handleEditSuccess = () => {
		setEditOpen(false)
		setSelectedVisit(null)
		fetchVisits()
	}

	const VisitsTable = ({ visits }: { visits: VisitResource[] }) => (
		<TableContainer component={Paper}>
			<Table>
				<TableHead>
					<TableRow sx={{ backgroundColor: '#f5f5f5' }}>
						<TableCell>Date</TableCell>
						<TableCell>Time</TableCell>
						<TableCell>Patient</TableCell>
						<TableCell>Doctor</TableCell>
						<TableCell>Notes</TableCell>
						<TableCell align='center'>Action</TableCell>
					</TableRow>
				</TableHead>
				<TableBody>
					{visits.length > 0 ? (
						visits.map(v => (
							<TableRow key={v.id}>
								<TableCell>{new Date(v.attributes.date + 'T00:00:00').toLocaleDateString()}</TableCell>
								<TableCell>{v.attributes.time}</TableCell>
								<TableCell>{v.attributes.patientName}</TableCell>
								<TableCell>{v.attributes.doctorName}</TableCell>
								<TableCell>{v.attributes.notes || '—'}</TableCell>
								<TableCell align='center'>
									<Box sx={{ display: 'flex', gap: 1, justifyContent: 'center' }}>
										<Button
											size='small'
											variant='outlined'
											component={Link}
											href={`/dashboard/visits/${v.id}?patient=${v.attributes.patientId}`}
										>
											View
										</Button>
										<Button
											size='small'
											variant='outlined'
											onClick={() => handleEditVisit(v)}
											disabled={!v.attributes.isEditable}
										>
											Edit
										</Button>
									</Box>
								</TableCell>
							</TableRow>
						))
					) : (
						<TableRow>
							<TableCell colSpan={6} align='center'>
								No visits
							</TableCell>
						</TableRow>
					)}
				</TableBody>
			</Table>
		</TableContainer>
	)

	return (
		<Box sx={{ p: 3 }}>
			{/* Toolbar */}
			<Toolbar disableGutters sx={{ mb: 3 }}>
				<Typography variant='h5' sx={{ flexGrow: 1 }}>
					Visits
				</Typography>
				<Button variant='contained' onClick={handleAddVisit} disabled={loading}>
					Add Visit
				</Button>
			</Toolbar>

			{/* Error state */}
			{error && <Alert severity='error' sx={{ mb: 3 }}>{error}</Alert>}

			{/* Loading state */}
			{loading && <CircularProgress />}

			{/* Visits content */}
			{!loading && !error && (
				<Box>
					{/* Upcoming visits section */}
					{upcoming.length > 0 && (
						<Box sx={{ mb: 4 }}>
							<Box sx={{ display: 'flex', alignItems: 'center', gap: 2, mb: 2 }}>
								<Chip label='Upcoming' color='primary' variant='filled' />
							</Box>
							<VisitsTable visits={upcoming} />
						</Box>
					)}

					{/* Past visits section */}
					{past.length > 0 && (
						<Box>
							<Typography variant='h6' sx={{ mb: 2 }}>
								Past Visits
							</Typography>
							<VisitsTable visits={past} />
						</Box>
					)}

					{/* No visits state */}
					{upcoming.length === 0 && past.length === 0 && (
						<Alert severity='info'>No visits found.</Alert>
					)}
				</Box>
			)}

			{/* Add Visit Dialog */}
			<Dialog open={formOpen} onClose={handleFormClose} maxWidth='sm' fullWidth>
				<DialogTitle>Create New Visit</DialogTitle>
				<DialogContent sx={{ pt: 2 }}>
					<VisitForm onSuccess={handleFormSuccess} onCancel={handleFormClose} />
				</DialogContent>
			</Dialog>

			{/* Edit Visit Dialog */}
			{selectedVisit && (
				<Dialog open={editOpen} onClose={handleEditClose} maxWidth='sm' fullWidth>
					<DialogTitle>Edit Visit</DialogTitle>
					<DialogContent sx={{ pt: 2 }}>
						<VisitEditForm
							visit={selectedVisit}
							patientId={selectedVisit.attributes.patientId || ''}
							onSuccess={handleEditSuccess}
							onCancel={handleEditClose}
						/>
					</DialogContent>
				</Dialog>
			)}
		</Box>
	)
}

