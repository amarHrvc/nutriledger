'use client'

import Grid from '@mui/material/Grid'

import type { PatientResource } from '@/api/generated/nutriBaseAPI.schemas'
import PatientLeftOverview from './patient-left'
import PatientRightTabs from './patient-right'

interface Props {
	patient: PatientResource
}

export default function PatientDetail({ patient }: Props) {
	return (
		<Grid container spacing={6}>
			<Grid size={{ xs: 12, md: 5, lg: 4 }}>
				<PatientLeftOverview patient={patient} />
			</Grid>
			<Grid size={{ xs: 12, md: 7, lg: 8 }}>
				<PatientRightTabs patient={patient} />
			</Grid>
		</Grid>
	)
}
