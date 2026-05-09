'use client'

import { useCallback, useEffect, useState } from 'react'
import { useParams } from 'next/navigation'

import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import CircularProgress from '@mui/material/CircularProgress'

import PatientDetail from '@views/patients/PatientDetail'
import type { PatientResource } from '@/api/generated/nutriBaseAPI.schemas'

export default function Page() {
	const { id } = useParams<{ id: string }>()
	const [patient, setPatient] = useState<PatientResource | null>(null)
	const [error, setError] = useState<string | null>(null)

	const loadPatient = useCallback(async () => {
		try {
			const res = await fetch(`/api/patients/${id}`)
			const json = await res.json()

			if (!res.ok) {
				setError(json?.message ?? `Error ${res.status}`)
				return
			}

			setPatient(json.data?.patient ?? null)
			setError(null)
		} catch {
			setError('Failed to load patient.')
		}
	}, [id])

	useEffect(() => {
		loadPatient()
		window.addEventListener('patients:changed', loadPatient)

		return () => window.removeEventListener('patients:changed', loadPatient)
	}, [loadPatient])

	if (error) {
		return (
			<Box sx={{ p: 3 }}>
				<Alert severity='error'>{error}</Alert>
			</Box>
		)
	}

	return <Box sx={{ p: 3 }}>{!patient ? <CircularProgress /> : <PatientDetail patient={patient} />}</Box>
}
