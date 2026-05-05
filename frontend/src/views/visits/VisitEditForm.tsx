'use client'

import { useState } from 'react'
import { toast } from 'react-toastify'

import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import CircularProgress from '@mui/material/CircularProgress'
import Stack from '@mui/material/Stack'
import TextField from '@mui/material/TextField'

import type { VisitResource } from '@/api/generated/nutriBaseAPI.schemas'

interface Props {
	visit: VisitResource
	patientId: string
	onSuccess?: () => void
	onCancel?: () => void
}

export default function VisitEditForm({ visit, patientId, onSuccess, onCancel }: Props) {
	const [datetimeValue, setDatetimeValue] = useState(
		`${visit.attributes.date}T${visit.attributes.time?.slice(0, 5) ?? '00:00'}`
	)
	const [notes, setNotes] = useState(visit.attributes.notes ?? '')

	const [errors, setErrors] = useState<Record<string, string[]>>({})
	const [formError, setFormError] = useState('')
	const [loading, setLoading] = useState(false)

	const fieldError = (field: string) => errors[field]?.[0]

	const submit = async (e: React.FormEvent) => {
		e.preventDefault()
		setLoading(true)
		setErrors({})
		setFormError('')

		try {
			// Split datetime-local format: "2025-01-15T14:30" -> ["2025-01-15", "14:30"]
			const [date, time] = datetimeValue.split('T')

			const payload = {
				date,
				time,
				notes,
			}

			const res = await fetch(`/api/patients/${patientId}/visits/${visit.id}`, {
				method: 'PATCH',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(payload),
			})

			const json = await res.json()

			if (res.status === 422) {
				setErrors(json.errors ?? {})
				return
			}
			if (!res.ok) {
				setFormError(json.message ?? `Server error ${res.status}`)
				return
			}

			window.dispatchEvent(new CustomEvent('visits:changed'))
			toast.success('Visit updated successfully.')
			onSuccess?.()
		} catch (error) {
			setFormError('Failed to update visit.')
		} finally {
			setLoading(false)
		}
	}

	return (
		<Box component='form' onSubmit={submit} noValidate>
			<Stack spacing={3} sx={{ pt: 1 }}>
				{formError && <Alert severity='error'>{formError}</Alert>}

				{/* DateTime field */}
				<TextField
					label='Date & Time'
					type='datetime-local'
					value={datetimeValue}
					onChange={e => setDatetimeValue(e.target.value)}
					error={!!fieldError('date') || !!fieldError('time')}
					helperText={fieldError('date') || fieldError('time')}
					fullWidth
					required
					InputLabelProps={{ shrink: true }}
				/>

				{/* Notes field */}
				<TextField
					label='Notes'
					value={notes}
					onChange={e => setNotes(e.target.value)}
					error={!!fieldError('notes')}
					helperText={fieldError('notes')}
					fullWidth
					multiline
					rows={3}
				/>

				{/* Action buttons */}
				<Box sx={{ display: 'flex', gap: 2, justifyContent: 'flex-end' }}>
					{onCancel && (
						<Button variant='outlined' onClick={onCancel} disabled={loading}>
							Cancel
						</Button>
					)}
					<Button
						type='submit'
						variant='contained'
						disabled={loading}
						startIcon={loading ? <CircularProgress size={16} /> : null}
					>
						Save Changes
					</Button>
				</Box>
			</Stack>
		</Box>
	)
}
