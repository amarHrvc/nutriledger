'use client'

import { useState } from 'react'
import { toast } from 'react-toastify'

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
import Divider from '@mui/material/Divider'

import type { PatientResource } from '@/api/generated/nutriBaseAPI.schemas'

interface Props {
	patient: PatientResource
	onSuccess?: () => void
	onCancel?: () => void
}

export default function PatientEditForm({ patient, onSuccess, onCancel }: Props) {
	const [firstName, setFirstName] = useState(patient.attributes?.firstName ?? '')
	const [lastName, setLastName] = useState(patient.attributes?.lastName ?? '')
	const [dateOfBirth, setDateOfBirth] = useState(patient.attributes?.dateOfBirth ?? '')
	const [gender, setGender] = useState(patient.attributes?.gender ?? '')
	const [phone, setPhone] = useState(patient.attributes?.phone ?? '')
	const [emergencyContactName, setEmergencyContactName] = useState(patient.attributes?.emergencyContactName ?? '')
	const [emergencyContactPhone, setEmergencyContactPhone] = useState(patient.attributes?.emergencyContactPhone ?? '')
	const [address, setAddress] = useState(patient.attributes?.address ?? '')
	const [city, setCity] = useState(patient.attributes?.city ?? '')
	const [postalCode, setPostalCode] = useState(patient.attributes?.postalCode ?? '')
	const [bloodType, setBloodType] = useState(patient.attributes?.bloodType ?? '')
	const [allergies, setAllergies] = useState(patient.attributes?.allergies ?? '')
	const [medicalNotes, setMedicalNotes] = useState(patient.attributes?.medicalNotes ?? '')

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
			const payload = {
				first_name: firstName,
				last_name: lastName,
				date_of_birth: dateOfBirth,
				gender,
				phone,
				emergency_contact_name: emergencyContactName,
				emergency_contact_phone: emergencyContactPhone,
				address,
				city,
				postal_code: postalCode,
				blood_type: bloodType,
				allergies,
				medical_notes: medicalNotes,
			}

			const res = await fetch(`/api/patients/${patient.id}`, {
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

			window.dispatchEvent(new CustomEvent('patients:changed'))
			toast.success('Patient updated successfully.')
			onSuccess?.()
		} catch (error) {
			setFormError('Failed to update patient.')
		} finally {
			setLoading(false)
		}
	}

	return (
		<Box component='form' onSubmit={submit} noValidate>
			<Stack spacing={3} sx={{ pt: 1 }}>
				{formError && <Alert severity='error'>{formError}</Alert>}

				{/* Patient Information Section */}
				<Box>
					<Divider sx={{ mb: 3 }} />
					<Stack spacing={2}>
						<TextField
							label='First Name'
							value={firstName}
							onChange={e => setFirstName(e.target.value)}
							error={!!fieldError('first_name')}
							helperText={fieldError('first_name')}
							fullWidth
							required
							inputProps={{ maxLength: 50 }}
						/>

						<TextField
							label='Last Name'
							value={lastName}
							onChange={e => setLastName(e.target.value)}
							error={!!fieldError('last_name')}
							helperText={fieldError('last_name')}
							fullWidth
							required
							inputProps={{ maxLength: 50 }}
						/>

						<TextField
							label='Date of Birth'
							type='date'
							value={dateOfBirth}
							onChange={e => setDateOfBirth(e.target.value)}
							error={!!fieldError('date_of_birth')}
							helperText={fieldError('date_of_birth')}
							fullWidth
							required
							InputLabelProps={{ shrink: true }}
						/>

						<FormControl fullWidth required error={!!fieldError('gender')}>
							<InputLabel>Gender</InputLabel>
							<Select value={gender} label='Gender' onChange={e => setGender(e.target.value)}>
								<MenuItem value='M'>Male</MenuItem>
								<MenuItem value='F'>Female</MenuItem>
							</Select>
						</FormControl>

						<TextField
							label='Phone'
							value={phone}
							onChange={e => setPhone(e.target.value)}
							error={!!fieldError('phone')}
							helperText={fieldError('phone')}
							fullWidth
							required
						/>

						<TextField
							label='Emergency Contact Name'
							value={emergencyContactName}
							onChange={e => setEmergencyContactName(e.target.value)}
							error={!!fieldError('emergency_contact_name')}
							helperText={fieldError('emergency_contact_name')}
							fullWidth
							required
						/>

						<TextField
							label='Emergency Contact Phone'
							value={emergencyContactPhone}
							onChange={e => setEmergencyContactPhone(e.target.value)}
							error={!!fieldError('emergency_contact_phone')}
							helperText={fieldError('emergency_contact_phone')}
							fullWidth
							required
						/>

						<TextField
							label='Address'
							value={address}
							onChange={e => setAddress(e.target.value)}
							error={!!fieldError('address')}
							helperText={fieldError('address')}
							fullWidth
						/>

						<TextField
							label='City'
							value={city}
							onChange={e => setCity(e.target.value)}
							error={!!fieldError('city')}
							helperText={fieldError('city')}
							fullWidth
						/>

						<TextField
							label='Postal Code'
							value={postalCode}
							onChange={e => setPostalCode(e.target.value)}
							error={!!fieldError('postal_code')}
							helperText={fieldError('postal_code')}
							fullWidth
						/>

						<FormControl fullWidth>
							<InputLabel>Blood Type</InputLabel>
							<Select value={bloodType} label='Blood Type' onChange={e => setBloodType(e.target.value)}>
								<MenuItem value=''>None</MenuItem>
								<MenuItem value='A+'>A+</MenuItem>
								<MenuItem value='A-'>A-</MenuItem>
								<MenuItem value='B+'>B+</MenuItem>
								<MenuItem value='B-'>B-</MenuItem>
								<MenuItem value='AB+'>AB+</MenuItem>
								<MenuItem value='AB-'>AB-</MenuItem>
								<MenuItem value='O+'>O+</MenuItem>
								<MenuItem value='O-'>O-</MenuItem>
							</Select>
						</FormControl>

						<TextField
							label='Allergies'
							value={allergies}
							onChange={e => setAllergies(e.target.value)}
							error={!!fieldError('allergies')}
							helperText={fieldError('allergies')}
							fullWidth
							multiline
							rows={3}
						/>

						<TextField
							label='Medical Notes'
							value={medicalNotes}
							onChange={e => setMedicalNotes(e.target.value)}
							error={!!fieldError('medical_notes')}
							helperText={fieldError('medical_notes')}
							fullWidth
							multiline
							rows={3}
							inputProps={{ maxLength: 10000 }}
						/>
					</Stack>
				</Box>

				<Box sx={{ display: 'flex', gap: 2, justifyContent: 'flex-end' }}>
					{onCancel && (
						<Button variant='outlined' onClick={onCancel} disabled={loading}>
							Cancel
						</Button>
					)}
					<Button type='submit' variant='contained' disabled={loading} startIcon={loading ? <CircularProgress size={16} /> : null}>
						Save Changes
					</Button>
				</Box>
			</Stack>
		</Box>
	)
}
