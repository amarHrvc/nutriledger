'use client'

import { useCallback, useEffect, useState } from 'react'
import { useParams, useSearchParams } from 'next/navigation'

import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import CircularProgress from '@mui/material/CircularProgress'

import VisitDetail from '@views/visits/VisitDetail'
import type { VisitResource } from '@/api/generated/nutriBaseAPI.schemas'

export default function Page() {
	const { id } = useParams<{ id: string }>()
	const searchParams = useSearchParams()
	const patientId = searchParams.get('patient') ?? ''

	const [visit, setVisit] = useState<VisitResource | null>(null)
	const [error, setError] = useState<string | null>(null)

	const loadVisit = useCallback(async () => {
		if (!patientId) {
			setError('Missing patient context.')
			return
		}
		try {
			const res = await fetch(`/api/patients/${patientId}/visits/${id}`)
			const json = await res.json()
			if (!res.ok) {
				setError(json?.message ?? `Error ${res.status}`)
				return
			}
			setVisit(json.data?.visit ?? null)
			setError(null)
		} catch {
			setError('Failed to load visit.')
		}
	}, [id, patientId])

	useEffect(() => {
		loadVisit()
		window.addEventListener('visits:changed', loadVisit)
		return () => window.removeEventListener('visits:changed', loadVisit)
	}, [loadVisit])

	if (error) {
		return (
			<Box sx={{ p: 3 }}>
				<Alert severity='error'>{error}</Alert>
			</Box>
		)
	}

	return (
		<Box sx={{ p: 3 }}>
			{!visit ? <CircularProgress /> : <VisitDetail visit={visit} onUpdated={loadVisit} />}
		</Box>
	)
}
