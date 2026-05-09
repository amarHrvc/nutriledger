'use client'

import { useEffect, useState } from 'react'

import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import CircularProgress from '@mui/material/CircularProgress'
import List from '@mui/material/List'
import ListItem from '@mui/material/ListItem'
import ListItemText from '@mui/material/ListItemText'
import Typography from '@mui/material/Typography'
import Divider from '@mui/material/Divider'

import type { PatientResource } from '@/api/generated/nutriBaseAPI.schemas'

interface Visit {
	id: string
	date: string
	doctorName?: string
	notes?: string
}

export default function VisitsTab({ patient }: { patient: PatientResource }) {
	const [visits, setVisits] = useState<Visit[]>([])
	const [loading, setLoading] = useState(true)
	const [error, setError] = useState<string | null>(null)

	useEffect(() => {
		const fetchVisits = async () => {
			try {
				const res = await fetch(`/api/patients/${patient.id}/visits`)
				const json = await res.json()

				if (!res.ok) {
					setError(json?.message ?? 'Failed to load visits.')
					return
				}

				setVisits(json.data?.visits || [])
				setError(null)
			} catch {
				setError('Failed to load visits.')
			} finally {
				setLoading(false)
			}
		}

		fetchVisits()
	}, [patient.id])

	if (error) {
		return (
			<Card>
				<CardContent>
					<Alert severity='error'>{error}</Alert>
				</CardContent>
			</Card>
		)
	}

	if (loading) {
		return (
			<Box sx={{ display: 'flex', justifyContent: 'center', py: 8 }}>
				<CircularProgress />
			</Box>
		)
	}

	if (!visits || visits.length === 0) {
		return (
			<Card>
				<CardContent>
					<Typography variant='body2' color='text.secondary' sx={{ textAlign: 'center', py: 4 }}>
						No visits recorded.
					</Typography>
				</CardContent>
			</Card>
		)
	}

	return (
		<Card>
			<CardContent>
				<List>
					{visits.map((visit, index) => (
						<Box key={visit.id}>
							{index > 0 && <Divider />}
							<ListItem>
								<ListItemText
									primary={visit.date}
									secondary={`Doctor: ${visit.doctorName || 'N/A'} ${visit.notes ? `— ${visit.notes}` : ''}`}
								/>
							</ListItem>
						</Box>
					))}
				</List>
			</CardContent>
		</Card>
	)
}
