'use client'

import { useEffect, useState } from 'react'

import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Chip from '@mui/material/Chip'
import CircularProgress from '@mui/material/CircularProgress'
import Table from '@mui/material/Table'
import TableBody from '@mui/material/TableBody'
import TableCell from '@mui/material/TableCell'
import TableContainer from '@mui/material/TableContainer'
import TableHead from '@mui/material/TableHead'
import TablePagination from '@mui/material/TablePagination'
import TableRow from '@mui/material/TableRow'
import Typography from '@mui/material/Typography'

import type { PatientResource } from '@/api/generated/nutriBaseAPI.schemas'
import type { VitalSignResource } from '@/views/visits/vitals.types'

function BmiDeltaChip({ current, previous }: { current: string | null; previous: string | null }) {
	if (!current || !previous) return null

	const delta = parseFloat(current) - parseFloat(previous)

	if (Math.abs(delta) < 0.1) {
		return <Chip label='→' size='small' sx={{ ml: 0.5 }} />
	}

	if (delta < 0) {
		return (
			<Chip
				label={`↓ ${delta.toFixed(1)}`}
				size='small'
				color='success'
				sx={{ ml: 0.5 }}
			/>
		)
	}

	return (
		<Chip
			label={`↑ +${delta.toFixed(1)}`}
			size='small'
			color='warning'
			sx={{ ml: 0.5 }}
		/>
	)
}

function bmiCategoryColor(category: string | null): 'default' | 'warning' | 'error' {
	if (category === 'overweight' || category === 'underweight') return 'warning'
	if (category === 'obese') return 'error'
	return 'default'
}

export default function VitalsHistoryTab({ patient }: { patient: PatientResource }) {
	const [records, setRecords] = useState<VitalSignResource[]>([])
	const [loading, setLoading] = useState(true)
	const [error, setError] = useState<string | null>(null)
	const [page, setPage] = useState(0)
	const [rowsPerPage, setRowsPerPage] = useState(10)

	useEffect(() => {
		const fetchHistory = async () => {
			try {
				const res = await fetch(`/api/patients/${patient.id}/vitals`)
				const json = await res.json()

				if (!res.ok) {
					setError(json?.message ?? 'Failed to load vitals history.')
					return
				}

				setRecords(json.data || [])
				setError(null)
			} catch {
				setError('Failed to load vitals history.')
			} finally {
				setLoading(false)
			}
		}

		fetchHistory()
	}, [patient.id])

	if (loading) {
		return (
			<Box sx={{ display: 'flex', justifyContent: 'center', py: 8 }}>
				<CircularProgress />
			</Box>
		)
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

	if (records.length === 0) {
		return (
			<Card>
				<CardContent>
					<Typography variant='body2' color='text.secondary' sx={{ textAlign: 'center', py: 4 }}>
						No vitals recorded for this patient yet. Record vitals from any visit detail page.
					</Typography>
				</CardContent>
			</Card>
		)
	}

	const paginated = records.slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage)

	return (
		<Card>
			<CardContent sx={{ p: 0 }}>
				<TableContainer>
					<Table size='small'>
						<TableHead>
							<TableRow sx={{ backgroundColor: 'action.hover' }}>
								<TableCell>Visit Date</TableCell>
								<TableCell>BP (mmHg)</TableCell>
								<TableCell>HR (bpm)</TableCell>
								<TableCell>Temp (°C)</TableCell>
								<TableCell>Weight (kg)</TableCell>
								<TableCell>Height (cm)</TableCell>
								<TableCell>BMI</TableCell>
								<TableCell>Category</TableCell>
								<TableCell>Flags</TableCell>
							</TableRow>
						</TableHead>
						<TableBody>
							{paginated.map((record, idx) => {
								const globalIdx = page * rowsPerPage + idx
								const nextRecord = records[globalIdx + 1] ?? null
								const attrs = record.attributes

								return (
									<TableRow key={record.id} hover>
										<TableCell>{attrs.visitDate ?? '—'}</TableCell>
										<TableCell>
											{attrs.systolicBp && attrs.diastolicBp
												? `${attrs.systolicBp}/${attrs.diastolicBp}`
												: '—'}
										</TableCell>
										<TableCell>{attrs.heartRate ?? '—'}</TableCell>
										<TableCell>{attrs.temperature ?? '—'}</TableCell>
										<TableCell>{attrs.weight ?? '—'}</TableCell>
										<TableCell>{attrs.height ?? '—'}</TableCell>
										<TableCell>
											<Box sx={{ display: 'flex', alignItems: 'center' }}>
												{attrs.bmi ?? '—'}
												{nextRecord && (
													<BmiDeltaChip
														current={attrs.bmi}
														previous={nextRecord.attributes.bmi}
													/>
												)}
											</Box>
										</TableCell>
										<TableCell>
											{attrs.bmiCategory ? (
												<Chip
													label={attrs.bmiCategory}
													size='small'
													color={bmiCategoryColor(attrs.bmiCategory)}
												/>
											) : (
												'—'
											)}
										</TableCell>
										<TableCell>
											{attrs.flags.length > 0 ? (
												<Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 0.5 }}>
													{attrs.flags.map((flag, i) => (
														<Chip key={i} label={flag.field} size='small' color='warning' variant='outlined' />
													))}
												</Box>
											) : (
												'—'
											)}
										</TableCell>
									</TableRow>
								)
							})}
						</TableBody>
					</Table>
				</TableContainer>
				<TablePagination
					component='div'
					count={records.length}
					page={page}
					rowsPerPage={rowsPerPage}
					rowsPerPageOptions={[5, 10, 25]}
					onPageChange={(_, newPage) => setPage(newPage)}
					onRowsPerPageChange={e => {
						setRowsPerPage(parseInt(e.target.value, 10))
						setPage(0)
					}}
				/>
			</CardContent>
		</Card>
	)
}
