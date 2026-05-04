'use client'

import Grid from '@mui/material/Grid'

import type { PatientResource } from '@/api/generated/nutriBaseAPI.schemas'
import PatientDetailsCard from './PatientDetailsCard'

interface Props {
	patient: PatientResource
}

export default function PatientLeftOverview({ patient }: Props) {
	return (
		<Grid container spacing={6}>
			<Grid size={{ xs: 12 }}>
				<PatientDetailsCard patient={patient} />
			</Grid>
		</Grid>
	)
}
