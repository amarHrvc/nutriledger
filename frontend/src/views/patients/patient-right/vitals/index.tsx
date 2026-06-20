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
import type { VitalSignFlag, VitalSignResource } from '@/views/visits/vitals.types'

const FLAG_LABELS: Record<string, string> = {
	systolicBp: 'Systolic BP',
	diastolicBp: 'Diastolic BP',
	heartRate: 'Heart Rate',
	temperature: 'Temperature',
	bmi: 'BMI',
	weight: 'Weight',
	height: 'Height',
}

const MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']

function formatDate(dateStr: string | null): string {
	if (!dateStr) return '—'
	const [year, month, day] = dateStr.split('-').map(Number)

	return `${String(day).padStart(2, '0')} ${MONTHS[month - 1]} ${year}`
}

function BmiDeltaChip({ current, previous }: { current: string | null; previous: string | null }) {
	if (!current || !previous) return null
	const delta = parseFloat(current) - parseFloat(previous)

	if (Math.abs(delta) < 0.1) return <Chip label='→' size='small' sx={{ ml: 0.5 }} />
	if (delta < 0) return <Chip label={`↓ ${delta.toFixed(1)}`} size='small' color='success' variant='tonal' sx={{ ml: 0.5 }} />

	return <Chip label={`↑ +${delta.toFixed(1)}`} size='small' color='warning' variant='tonal' sx={{ ml: 0.5 }} />
}

function bmiCategoryColor(category: string | null): 'default' | 'warning' | 'error' {
	if (category === 'overweight' || category === 'underweight') return 'warning'
	if (category === 'obese') return 'error'

	return 'default'
}

function FlagsCell({ flags }: { flags: VitalSignFlag[] }) {
	if (flags.length === 0) {
		return <Typography variant='body2' color='text.disabled'>—</Typography>
	}

	return (
		<Box sx={{ display: 'flex', flexDirection: 'column', gap: 0.75 }}>
			{flags.map((flag, i) => {
				const label = FLAG_LABELS[flag.field] ?? flag.field
				const detail = flag.threshold
					? `${flag.value} (ok: ${flag.threshold})`
					: String(flag.value)

				return (
					<Chip
						key={i}
						label={`${label}: ${detail}`}
						size='small'
						color='warning'
						variant='tonal'
						sx={{ fontWeight: 500, justifyContent: 'flex-start', maxWidth: 240 }}
					/>
				)
			})}
		</Box>
	)
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
				<TableContainer sx={{ overflowX: 'auto' }}>
					<Table sx={{ minWidth: 820 }} size='small'>
						<TableHead>
							<TableRow sx={{ backgroundColor: 'action.hover' }}>
								<TableCell sx={{ whiteSpace: 'nowrap', fontWeight: 600 }}>Visit Date</TableCell>
								<TableCell sx={{ whiteSpace: 'nowrap', fontWeight: 600 }}>BP (mmHg)</TableCell>
								<TableCell sx={{ whiteSpace: 'nowrap', fontWeight: 600 }}>HR (bpm)</TableCell>
								<TableCell sx={{ whiteSpace: 'nowrap', fontWeight: 600 }}>Temp (°C)</TableCell>
								<TableCell sx={{ whiteSpace: 'nowrap', fontWeight: 600 }}>Weight (kg)</TableCell>
								<TableCell sx={{ whiteSpace: 'nowrap', fontWeight: 600 }}>Height (cm)</TableCell>
								<TableCell sx={{ whiteSpace: 'nowrap', fontWeight: 600 }}>BMI</TableCell>
								<TableCell sx={{ whiteSpace: 'nowrap', fontWeight: 600 }}>Category</TableCell>
								<TableCell sx={{ fontWeight: 600 }}>Flags</TableCell>
							</TableRow>
						</TableHead>
						<TableBody>
							{paginated.map((record, idx) => {
								const globalIdx = page * rowsPerPage + idx
								const nextRecord = records[globalIdx + 1] ?? null
								const a = record.attributes
								const hasFlags = a.flags.length > 0

								return (
									<TableRow
										key={record.id}
										hover
										sx={{ verticalAlign: hasFlags ? 'top' : 'middle' }}
									>
										<TableCell sx={{ whiteSpace: 'nowrap' }}>{formatDate(a.visitDate)}</TableCell>
										<TableCell sx={{ whiteSpace: 'nowrap' }}>
											{a.systolicBp && a.diastolicBp ? `${a.systolicBp}/${a.diastolicBp}` : '—'}
										</TableCell>
										<TableCell sx={{ whiteSpace: 'nowrap' }}>{a.heartRate ?? '—'}</TableCell>
										<TableCell sx={{ whiteSpace: 'nowrap' }}>{a.temperature ?? '—'}</TableCell>
										<TableCell sx={{ whiteSpace: 'nowrap' }}>{a.weight ?? '—'}</TableCell>
										<TableCell sx={{ whiteSpace: 'nowrap' }}>{a.height ?? '—'}</TableCell>
										<TableCell sx={{ whiteSpace: 'nowrap' }}>
											<Box sx={{ display: 'flex', alignItems: 'center' }}>
												{a.bmi ?? '—'}
												{nextRecord && (
													<BmiDeltaChip current={a.bmi} previous={nextRecord.attributes.bmi} />
												)}
											</Box>
										</TableCell>
										<TableCell sx={{ whiteSpace: 'nowrap' }}>
											{a.bmiCategory ? (
												<Chip
													label={a.bmiCategory}
													size='small'
													color={bmiCategoryColor(a.bmiCategory)}
													variant='tonal'
												/>
											) : '—'}
										</TableCell>
										<TableCell>
											<FlagsCell flags={a.flags} />
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
