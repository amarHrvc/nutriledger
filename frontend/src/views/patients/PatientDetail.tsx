'use client'

import Box from '@mui/material/Box'

import type { PatientResource } from '@/api/generated/nutriBaseAPI.schemas'
import PatientDetailsCard from './patient-left/PatientDetailsCard'
import PatientRightTabs from './patient-right'

interface Props {
	patient: PatientResource
}

export default function PatientDetail({ patient }: Props) {
	return (
		<Box sx={{ display: 'flex', flexDirection: 'column', gap: 6 }}>
			<PatientDetailsCard patient={patient} />
			<PatientRightTabs patient={patient} />
		</Box>
	)
}
