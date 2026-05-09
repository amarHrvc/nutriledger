import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Divider from '@mui/material/Divider'
import Typography from '@mui/material/Typography'

import type { PatientResource } from '@/api/generated/nutriBaseAPI.schemas'

function InfoRow({ label, value }: { label: string; value: string | null | undefined }) {
	return (
		<Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', py: 1 }}>
			<Typography variant='body2' color='text.secondary' sx={{ minWidth: 130 }}>
				{label}
			</Typography>
			<Typography variant='body2' color='text.primary' sx={{ textAlign: 'right' }}>
				{value ?? '—'}
			</Typography>
		</Box>
	)
}

export default function OverviewTab({ patient }: { patient: PatientResource }) {
	const {
		fullName = '',
		dateOfBirth = '',
		gender = '',
		phone = '',
		address = '',
		city = '',
		postalCode = '',
		emergencyContactName = '',
		emergencyContactPhone = '',
		createdAt = '',
	} = patient.attributes

	return (
		<Card>
			<CardContent>
				<Typography variant='h6' sx={{ mb: 2 }}>
					Personal Information
				</Typography>
				<Divider sx={{ mb: 2 }} />

				<InfoRow label='Full Name' value={fullName} />
				<Divider />
				<InfoRow label='Date of Birth' value={dateOfBirth} />
				<Divider />
				<InfoRow label='Gender' value={gender} />
				<Divider />
				<InfoRow label='Phone' value={phone} />

				{(address || city || postalCode) && (
					<>
						<Divider />
						<InfoRow label='Address' value={address} />
						<Divider />
						<InfoRow label='City' value={city} />
						<Divider />
						<InfoRow label='Postal Code' value={postalCode} />
					</>
				)}

				{(emergencyContactName || emergencyContactPhone) && (
					<>
						<Divider />
						<InfoRow label='Emergency Contact' value={emergencyContactName} />
						<Divider />
						<InfoRow label='Emergency Phone' value={emergencyContactPhone} />
					</>
				)}

				<Divider />
				<InfoRow label='Member Since' value={createdAt} />
			</CardContent>
		</Card>
	)
}
