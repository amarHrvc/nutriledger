'use client'

import dynamic from 'next/dynamic'
import { useEffect, useState } from 'react'
import { toast } from 'react-toastify'
import { useRouter } from 'next/navigation'

import Avatar from '@mui/material/Avatar'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Chip from '@mui/material/Chip'
import Dialog from '@mui/material/Dialog'
import DialogContent from '@mui/material/DialogContent'
import DialogTitle from '@mui/material/DialogTitle'
import Divider from '@mui/material/Divider'
import Grid from '@mui/material/Grid'
import Typography from '@mui/material/Typography'
import { useTheme } from '@mui/material/styles'
import { IconUserCancel } from '@tabler/icons-react'
import type { ApexOptions } from 'apexcharts'

import type { PatientResource } from '@/api/generated/nutriBaseAPI.schemas'
import type { VitalSignResource } from '@/views/visits/vitals.types'
import ConfirmDialog from '@views/users/shared/ConfirmDialog'
import PatientEditForm from '../PatientEditForm'

const AppReactApexCharts = dynamic(() => import('@/libs/styles/AppReactApexCharts'))

function initials(name: string): string {
	return name
		.split(' ')
		.slice(0, 2)
		.map(n => n[0])
		.join('')
		.toUpperCase()
}

function computeAge(dob: string): number | null {
	if (!dob) return null
	const [year, month, day] = dob.split('-').map(Number)
	const birth = new Date(year, month - 1, day)
	const today = new Date()
	let age = today.getFullYear() - birth.getFullYear()
	const m = today.getMonth() - birth.getMonth()

	if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--

	return age
}

const MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']

function formatDate(dateStr: string | null | undefined): string {
	if (!dateStr) return '—'
	const datePart = dateStr.split('T')[0]
	const [year, month, day] = datePart.split('-').map(Number)

	return `${String(day).padStart(2, '0')} ${MONTHS[month - 1]} ${year}`
}

function formatChartDate(dateStr: string): string {
	const [, month, day] = dateStr.split('-').map(Number)

	return `${String(day).padStart(2, '0')} ${MONTHS[month - 1]}`
}

function bmiCategoryColor(category: string): 'default' | 'warning' | 'error' {
	if (category === 'overweight' || category === 'underweight') return 'warning'
	if (category === 'obese') return 'error'

	return 'default'
}

const GENDER_LABELS: Record<string, string> = { M: 'Male', F: 'Female' }

function InfoCell({ label, value }: { label: string; value: string | null | undefined }) {
	return (
		<Grid size={{ xs: 12, sm: 6, md: 4 }}>
			<Typography variant='caption' color='text.secondary' display='block' sx={{ mb: 0.25 }}>
				{label}
			</Typography>
			<Typography variant='body2' fontWeight={500}>
				{value || '—'}
			</Typography>
		</Grid>
	)
}

interface Props {
	patient: PatientResource
}

export default function PatientDetailsCard({ patient }: Props) {
	const [confirmOpen, setConfirmOpen] = useState(false)
	const [editOpen, setEditOpen] = useState(false)
	const [loading, setLoading] = useState(false)
	const [latestBmiCategory, setLatestBmiCategory] = useState<string | null>(null)
	const [vitalsHistory, setVitalsHistory] = useState<VitalSignResource[]>([])
	const router = useRouter()
	const theme = useTheme()

	useEffect(() => {
		fetch(`/api/patients/${patient.id}/vitals`)
			.then(r => r.json())
			.then((json: { data?: VitalSignResource[] }) => {
				const records = json.data ?? []

				setLatestBmiCategory(records[0]?.attributes?.bmiCategory ?? null)
				setVitalsHistory(records)
			})
			.catch(() => null)
	}, [patient.id])

	const {
		fullName = '',
		dateOfBirth = '',
		gender = '',
		phone = '',
		city = '',
		bloodType = '',
		allergies = '',
		emergencyContactName = '',
		emergencyContactPhone = '',
		createdAt = '',
	} = patient.attributes

	const age = computeAge(dateOfBirth)

	// Build chart data — records come newest-first, reverse for chronological display
	const chartPoints = [...vitalsHistory]
		.reverse()
		.filter(r => r.attributes.visitDate && (r.attributes.weight !== null || r.attributes.bmi !== null))

	const hasChartData = chartPoints.length >= 2

	const divider = 'var(--mui-palette-divider)'
	const textDisabled = 'var(--mui-palette-text-disabled)'
	const textSecondary = 'var(--mui-palette-text-secondary)'

	const chartSeries = [
		{ name: 'Weight (kg)', type: 'area', data: chartPoints.map(r => parseFloat(r.attributes.weight ?? '0') || null) },
		{ name: 'BMI', type: 'line', data: chartPoints.map(r => parseFloat(r.attributes.bmi ?? '0') || null) },
	]

	const chartOptions: ApexOptions = {
		chart: { toolbar: { show: false }, parentHeightOffset: 0, zoom: { enabled: false }, animations: { enabled: false } },
		stroke: { curve: 'smooth', width: [2, 2] },
		fill: { type: ['gradient', 'solid'], gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 100] } },
		colors: [theme.palette.primary.main, '#FF9F43'],
		dataLabels: { enabled: false },
		legend: {
			position: 'top',
			horizontalAlign: 'left',
			labels: { colors: textSecondary },
			fontSize: '12px',
			markers: { offsetY: 1, offsetX: theme.direction === 'rtl' ? 7 : -4 },
			itemMargin: { horizontal: 8 },
		},
		grid: { borderColor: divider, strokeDashArray: 4, padding: { top: -10, right: 8 } },
		xaxis: {
			categories: chartPoints.map(r => formatChartDate(r.attributes.visitDate!)),
			axisBorder: { show: false },
			axisTicks: { color: divider },
			labels: { style: { colors: textDisabled, fontSize: '11px' } },
		},
		yaxis: [
			{
				labels: {
					formatter: (v: number) => `${v}`,
					style: { colors: textDisabled, fontSize: '11px' },
				},
				title: { text: 'kg', style: { color: textDisabled, fontSize: '11px', fontWeight: 400 } },
			},
			{
				opposite: true,
				min: 15,
				max: 45,
				tickAmount: 6,
				labels: {
					formatter: (v: number) => v.toFixed(1),
					style: { colors: textDisabled, fontSize: '11px' },
				},
				title: { text: 'BMI', style: { color: textDisabled, fontSize: '11px', fontWeight: 400 } },
			},
		],
		annotations: {
			yaxis: [
				{
					y: 18.5,
					y2: 24.9,
					yAxisIndex: 1,
					fillColor: '#28C76F',
					opacity: 0.08,
					label: {
						text: 'Normal BMI',
						position: 'right',
						offsetX: -8,
						style: { color: '#28C76F', fontSize: '10px', background: 'transparent', cssClass: '' },
					},
				},
			],
		},
		tooltip: { shared: true, intersect: false },
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
				<CardContent>
					<Box sx={{ display: 'flex', gap: 4, flexWrap: 'wrap', alignItems: 'flex-start' }}>

						{/* Left: avatar + name + chips + actions */}
						<Box sx={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 2, minWidth: 160 }}>
							<Avatar sx={{ width: 80, height: 80, fontSize: 28, bgcolor: 'primary.main' }}>
								{initials(fullName)}
							</Avatar>
							<Typography variant='h5' sx={{ textAlign: 'center' }}>
								{fullName}
							</Typography>
							<Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 1, justifyContent: 'center' }}>
								{gender && (
									<Chip
										label={GENDER_LABELS[gender] ?? gender}
										size='small'
										variant='tonal'
									/>
								)}
								{latestBmiCategory && latestBmiCategory !== 'normal' && (
									<Chip
										label={latestBmiCategory.charAt(0).toUpperCase() + latestBmiCategory.slice(1)}
										size='small'
										color={bmiCategoryColor(latestBmiCategory)}
										variant='tonal'
									/>
								)}
								{bloodType && (
									<Chip
										label={bloodType}
										size='small'
										variant='tonal'
										color='info'
									/>
								)}
							</Box>
							<Box sx={{ display: 'flex', gap: 1.5 }}>
								<Button
									size='small'
									variant='contained'
									onClick={() => setEditOpen(true)}
									disabled={loading}
								>
									Edit
								</Button>
								<Button
									size='small'
									variant='tonal'
									color='error'
									startIcon={<IconUserCancel size={16} />}
									onClick={() => setConfirmOpen(true)}
									disabled={loading}
								>
									Suspend
								</Button>
							</Box>
						</Box>

						<Divider orientation='vertical' flexItem sx={{ display: { xs: 'none', sm: 'block' } }} />

						{/* Right: info grid */}
						<Box sx={{ flex: 1, minWidth: 260 }}>
							<Grid container spacing={3}>
								<InfoCell
									label='Date of Birth'
									value={dateOfBirth ? `${formatDate(dateOfBirth)}${age !== null ? ` (${age}y)` : ''}` : null}
								/>
								<InfoCell label='Phone' value={phone} />
								<InfoCell label='City' value={city} />
								<InfoCell label='Allergies' value={allergies} />
								<InfoCell label='Emergency Contact' value={emergencyContactName} />
								<InfoCell label='Emergency Phone' value={emergencyContactPhone} />
								<InfoCell label='Member Since' value={formatDate(createdAt)} />
							</Grid>
						</Box>
					</Box>

					{/* Weight & BMI trend chart */}
					{hasChartData && (
						<>
							<Divider sx={{ my: 3 }} />
							<Typography variant='subtitle2' color='text.secondary' sx={{ mb: 0.5 }}>
								Weight & BMI Trend
							</Typography>
							<AppReactApexCharts
								type='line'
								width='100%'
								height={220}
								options={chartOptions}
								series={chartSeries}
							/>
						</>
					)}
				</CardContent>
			</Card>

			<ConfirmDialog
				open={confirmOpen}
				title='Suspend Patient'
				message='Are you sure you want to suspend this patient?'
				onConfirm={onConfirm}
				onCancel={() => setConfirmOpen(false)}
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
