'use client'

import { useState } from 'react'
import type { SyntheticEvent } from 'react'

import Tab from '@mui/material/Tab'
import TabContext from '@mui/lab/TabContext'
import TabList from '@mui/lab/TabList'
import TabPanel from '@mui/lab/TabPanel'

import type { PatientResource } from '@/api/generated/nutriBaseAPI.schemas'
import DietPlanSection from '../diet-plans/DietPlanSection'
import MedicalTab from './medical'
import VisitsTab from './visits'
import VitalsHistoryTab from './vitals'
import SocioeconomicTab from '../socioeconomic/SocioeconomicTab'

interface Props {
	patient: PatientResource
}

export default function PatientRightTabs({ patient }: Props) {
	const [activeTab, setActiveTab] = useState('medical')

	const handleChange = (_: SyntheticEvent, value: string) => setActiveTab(value)

	return (
		<TabContext value={activeTab}>
			<TabList onChange={handleChange} variant='scrollable'>
				<Tab value='medical' label='Medical' />
				<Tab value='visits' label='Visits' />
				<Tab value='vitals' label='Vitals' />
				<Tab value='socioeconomic' label='Socioeconomic' />
				<Tab value='diet-plans' label='Diet Plans' />
			</TabList>
			<TabPanel value='medical' sx={{ px: 0, pt: 4 }}>
				<MedicalTab patient={patient} />
			</TabPanel>
			<TabPanel value='visits' sx={{ px: 0, pt: 4 }}>
				<VisitsTab patient={patient} />
			</TabPanel>
			<TabPanel value='vitals' sx={{ px: 0, pt: 4 }}>
				<VitalsHistoryTab patient={patient} />
			</TabPanel>
			<TabPanel value='socioeconomic' sx={{ px: 0, pt: 4 }}>
				<SocioeconomicTab patient={patient} />
			</TabPanel>
			<TabPanel value='diet-plans' sx={{ px: 0, pt: 4 }}>
				<DietPlanSection patientId={Number(patient.id)} />
			</TabPanel>
		</TabContext>
	)
}
