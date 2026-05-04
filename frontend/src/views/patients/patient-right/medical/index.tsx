import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Divider from '@mui/material/Divider'
import Typography from '@mui/material/Typography'

import type { PatientResource } from '@/api/generated/nutriBaseAPI.schemas'

function InfoRow({ label, value }: { label: string; value: string | null | undefined }) {
	return (
		<Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', py: 1 }}>
			<Typography variant='body2' color='text.secondary' sx={{ minWidth: 130 }}>
				{label}
			</Typography>
			<Typography variant='body2' color='text.primary' sx={{ textAlign: 'right' }}>
				{value ?? '—'}
			</Typography>
		</Box>
	)
}

export default function MedicalTab({ patient }: { patient: PatientResource }) {
	const { bloodType = '', allergies = '', medicalNotes = '' } = patient.attributes

	return (
		<Card>
			<CardContent>
				<Typography variant='h6' sx={{ mb: 2 }}>
					Medical Information
				</Typography>
				<Divider sx={{ mb: 2 }} />

				<InfoRow label='Blood Type' value={bloodType} />
				<Divider />
				<InfoRow label='Allergies' value={allergies} />
				<Divider />
				<InfoRow label='Medical Notes' value={medicalNotes} />
			</CardContent>
		</Card>
	)
}
