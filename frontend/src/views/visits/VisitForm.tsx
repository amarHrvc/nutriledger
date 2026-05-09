'use client'

import { useState, useEffect } from 'react'

import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import CircularProgress from '@mui/material/CircularProgress'
import FormControl from '@mui/material/FormControl'
import InputLabel from '@mui/material/InputLabel'
import MenuItem from '@mui/material/MenuItem'
import Select from '@mui/material/Select'
import Stack from '@mui/material/Stack'
import TextField from '@mui/material/TextField'

import { useAuth } from '@/context/AuthContext'

interface Patient {
	id: string
	attributes: {
		fullName: string
	}
}

interface Doctor {
	id: string
	attributes: {
		name: string
		role: string
	}
}

interface Props {
	patientId?: string
	onSuccess?: () => void
	onCancel?: () => void
}

export default function VisitForm({ patientId, onSuccess, onCancel }: Props) {
	const { user } = useAuth()
	const [datetimeValue, setDatetimeValue] = useState('')
	const [notes, setNotes] = useState('')
	const [selectedPatientId, setSelectedPatientId] = useState(patientId || '')
	const [selectedDoctorId, setSelectedDoctorId] = useState('')
	const [patients, setPatients] = useState<Patient[]>([])
	const [doctors, setDoctors] = useState<Doctor[]>([])
	const [patientsLoading, setPatientsLoading] = useState(true)
	const [doctorsLoading, setDoctorsLoading] = useState(true)

	const [errors, setErrors] = useState<Record<string, string[]>>({})
	const [formError, setFormError] = useState('')
	const [loading, setLoading] = useState(false)

	const fieldError = (field: string) => errors[field]?.[0]

	// Fetch patients on mount
	useEffect(() => {
		const fetchPatients = async () => {
			try {
				const res = await fetch('/api/patients')
				const json = await res.json()
				setPatients(json.data.patients || [])
			} catch (error) {
				setFormError('Failed to load patients.')
			} finally {
				setPatientsLoading(false)
			}
		}
		fetchPatients()
	}, [])

	// Fetch doctors when admin
	useEffect(() => {
		if (user?.role === 'admin') {
			const fetchDoctors = async () => {
				try {
					const res = await fetch('/api/users')
					const json = await res.json()
					const allUsers = json.data?.users || []
					const doctorUsers = allUsers.filter((u: Doctor) => u.attributes?.role === 'doktor')
					setDoctors(doctorUsers)
				} catch (error) {
					console.error('Failed to load doctors:', error)
				} finally {
					setDoctorsLoading(false)
				}
			}
			fetchDoctors()
		} else {
			setDoctorsLoading(false)
		}
	}, [user?.role])

	const submit = async (e: React.FormEvent) => {
		e.preventDefault()
		setLoading(true)
		setErrors({})
		setFormError('')

		const resolvedPatientId = patientId || selectedPatientId
		if (!resolvedPatientId) {
			setFormError('Please select a patient.')
			setLoading(false)
			return
		}

		try {
			// Split datetime-local format: "2025-01-15T14:30" -> ["2025-01-15", "14:30"]
			const [date, time] = datetimeValue.split('T')

			const payload: Record<string, any> = {
				date,
				time,
				notes,
			}

			// Include doctor_id only when admin and a doctor is selected
			if (user?.role === 'admin' && selectedDoctorId) {
				payload.doctor_id = parseInt(selectedDoctorId, 10)
			}

			const res = await fetch(`/api/patients/${resolvedPatientId}/visits`, {
				method: 'POST',
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
			onSuccess?.()
		} catch (error) {
			setFormError('Failed to create visit.')
		} finally {
			setLoading(false)
		}
	}

	return (
		<Box component='form' onSubmit={submit} noValidate>
			<Stack spacing={3} sx={{ pt: 1 }}>
				{formError && <Alert severity='error'>{formError}</Alert>}

				{/* Show patient select only if patientId prop is not provided */}
				{!patientId && (
					<FormControl fullWidth required error={!!fieldError('patient_id')} disabled={patientsLoading}>
						<InputLabel>Patient</InputLabel>
						<Select
							value={selectedPatientId}
							label='Patient'
							onChange={e => setSelectedPatientId(e.target.value)}
						>
							{patients.map(patient => (
								<MenuItem key={patient.id} value={patient.id}>
									{patient.attributes.fullName}
								</MenuItem>
							))}
						</Select>
					</FormControl>
				)}

				{/* Show doctor select only for admin */}
				{user?.role === 'admin' && (
					<FormControl fullWidth disabled={doctorsLoading}>
						<InputLabel>Doctor</InputLabel>
						<Select
							value={selectedDoctorId}
							label='Doctor'
							onChange={e => setSelectedDoctorId(e.target.value)}
						>
							<MenuItem value=''>No specific doctor</MenuItem>
							{doctors.map(doctor => (
								<MenuItem key={doctor.id} value={doctor.id}>
									{doctor.attributes?.name}
								</MenuItem>
							))}
						</Select>
					</FormControl>
				)}

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
						<Button variant='outlined' onClick={onCancel} disabled={loading || patientsLoading || doctorsLoading}>
							Cancel
						</Button>
					)}
					<Button type='submit' variant='contained' disabled={loading || patientsLoading || doctorsLoading} startIcon={loading ? <CircularProgress size={16} /> : null}>
						Create Visit
					</Button>
				</Box>
			</Stack>
		</Box>
	)
}
