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
import Typography from '@mui/material/Typography'
import Stepper from '@mui/material/Stepper'
import Step from '@mui/material/Step'
import StepLabel from '@mui/material/StepLabel'

import StepperCustomDot from '@/components/stepper-dot'
import SocioeconomicFields from './socioeconomic/SocioeconomicFields'

interface Props {
	onSuccess?: () => void
	onCancel?: () => void
}

const steps = ['Account', 'Patient details', 'Socioeconomic']

export default function PatientForm({ onSuccess, onCancel }: Props) {
	const [activeStep, setActiveStep] = useState(0)

	const [name, setName] = useState('')
	const [email, setEmail] = useState('')
	const [password, setPassword] = useState('')
	const [passwordConfirm, setPasswordConfirm] = useState('')

	const [firstName, setFirstName] = useState('')
	const [lastName, setLastName] = useState('')
	const [dateOfBirth, setDateOfBirth] = useState('')
	const [gender, setGender] = useState('')
	const [phone, setPhone] = useState('')
	const [emergencyContactName, setEmergencyContactName] = useState('')
	const [emergencyContactPhone, setEmergencyContactPhone] = useState('')
	const [address, setAddress] = useState('')
	const [city, setCity] = useState('')
	const [postalCode, setPostalCode] = useState('')
	const [bloodType, setBloodType] = useState('')
	const [allergies, setAllergies] = useState('')
	const [medicalNotes, setMedicalNotes] = useState('')

	// Socioeconomic data collected optionally on the final step. Only sent if non-empty.
	const [socioData, setSocioData] = useState<Record<string, any>>({})

	const [errors, setErrors] = useState<Record<string, string[]>>({})
	const [formError, setFormError] = useState('')
	const [stepError, setStepError] = useState('')
	const [loading, setLoading] = useState(false)

	const fieldError = (field: string) => errors[field]?.[0]

	const socioErrors = Object.fromEntries(
		Object.entries(errors)
			.filter(([key]) => key.startsWith('socioeconomic.'))
			.map(([key, value]) => [key.replace('socioeconomic.', ''), value])
	)

	const validateStep = (step: number): boolean => {
		setStepError('')

		if (step === 0) {
			if (!name || !email || !password || !passwordConfirm) {
				setStepError('Please fill in all account fields before continuing.')
				
return false
			}

			if (password !== passwordConfirm) {
				setStepError('Password and confirmation do not match.')
				
return false
			}
		}

		if (step === 1) {
			if (!firstName || !lastName || !dateOfBirth || !gender || !phone || !emergencyContactName || !emergencyContactPhone) {
				setStepError('Please fill in all required patient fields before continuing.')
				
return false
			}
		}

		return true
	}

	const handleNext = () => {
		if (!validateStep(activeStep)) return
		setActiveStep(step => step + 1)
	}

	const handleBack = () => {
		setStepError('')
		setActiveStep(step => step - 1)
	}

	const submit = async () => {
		if (!validateStep(0) || !validateStep(1)) {
			return
		}

		setLoading(true)
		setErrors({})
		setFormError('')

		try {
			const payload: Record<string, any> = {
				name,
				email,
				password,
				password_confirmation: passwordConfirm,
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

			if (Object.keys(socioData).length > 0) {
				payload.socioeconomic = socioData
			}

			const res = await fetch('/api/patients', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(payload),
			})

			const json = await res.json()

			if (res.status === 422) {
				setErrors(json.errors ?? {})

				// Jump back to whichever step owns the first invalid field, so the doctor
				// isn't left staring at the socioeconomic step for an account/patient error.
				const invalidFields = Object.keys(json.errors ?? {})

				if (invalidFields.some(f => ['name', 'email', 'password', 'password_confirmation'].includes(f))) {
					setActiveStep(0)
				} else if (invalidFields.some(f => !f.startsWith('socioeconomic'))) {
					setActiveStep(1)
				}

				
return
			}

			if (!res.ok) {
				setFormError(json.message ?? `Server error ${res.status}`)
				
return
			}

			window.dispatchEvent(new CustomEvent('patients:changed'))
			toast.success('Patient created successfully.')
			onSuccess?.()
		} catch {
			setFormError('Failed to create patient.')
		} finally {
			setLoading(false)
		}
	}

	return (
		<Box>
			<Stepper activeStep={activeStep} sx={{ mb: 4 }}>
				{steps.map(label => (
					<Step key={label}>
						<StepLabel StepIconComponent={StepperCustomDot}>{label}</StepLabel>
					</Step>
				))}
			</Stepper>

			{formError && <Alert severity='error' sx={{ mb: 3 }}>{formError}</Alert>}
			{stepError && <Alert severity='warning' sx={{ mb: 3 }}>{stepError}</Alert>}

			{activeStep === 0 && (
				<Stack spacing={2}>
					<Typography variant='body2' color='text.secondary'>
						Creates the patient&apos;s login account. They will use this email and password to sign in.
					</Typography>

					<TextField
						label='Full Name'
						value={name}
						onChange={e => setName(e.target.value)}
						error={!!fieldError('name')}
						helperText={fieldError('name')}
						fullWidth
						required
					/>

					<TextField
						label='Email'
						type='email'
						value={email}
						onChange={e => setEmail(e.target.value)}
						error={!!fieldError('email')}
						helperText={fieldError('email')}
						fullWidth
						required
					/>

					<TextField
						label='Password'
						type='password'
						value={password}
						onChange={e => setPassword(e.target.value)}
						error={!!fieldError('password')}
						helperText={fieldError('password')}
						fullWidth
						required
					/>

					<TextField
						label='Confirm Password'
						type='password'
						value={passwordConfirm}
						onChange={e => setPasswordConfirm(e.target.value)}
						error={!!fieldError('password_confirmation')}
						helperText={fieldError('password_confirmation')}
						fullWidth
						required
					/>
				</Stack>
			)}

			{activeStep === 1 && (
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
			)}

			{activeStep === 2 && (
				<Box>
					<Typography variant='body2' color='text.secondary' sx={{ mb: 2 }}>
						Optional — can be filled in later from the patient&apos;s profile.
					</Typography>
					<SocioeconomicFields value={socioData} onChange={setSocioData} errors={socioErrors} />
				</Box>
			)}

			<Box sx={{ display: 'flex', gap: 2, justifyContent: 'space-between', mt: 4 }}>
				<Box>
					{activeStep === 0 && onCancel && (
						<Button variant='outlined' onClick={onCancel} disabled={loading}>
							Cancel
						</Button>
					)}
					{activeStep > 0 && (
						<Button variant='outlined' onClick={handleBack} disabled={loading}>
							Back
						</Button>
					)}
				</Box>

				<Box>
					{activeStep < steps.length - 1 && (
						<Button variant='contained' onClick={handleNext}>
							Next
						</Button>
					)}
					{activeStep === steps.length - 1 && (
						<Button
							variant='contained'
							onClick={submit}
							disabled={loading}
							startIcon={loading ? <CircularProgress size={16} /> : null}
						>
							Create Patient
						</Button>
					)}
				</Box>
			</Box>
		</Box>
	)
}
