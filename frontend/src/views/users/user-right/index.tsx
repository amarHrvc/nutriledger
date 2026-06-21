'use client'

import { useState } from 'react'
import type { SyntheticEvent } from 'react'

import Tab from '@mui/material/Tab'
import TabContext from '@mui/lab/TabContext'
import TabList from '@mui/lab/TabList'
import TabPanel from '@mui/lab/TabPanel'

import type { UserResource } from '@/api/generated/nutriBaseAPI.schemas'
import OverviewTab from './overview'
import PatientTab from './patient'
import UserForm from '../UserForm'

interface Props {
  user: UserResource
}

export default function UserRightTabs({ user }: Props) {
  const [activeTab, setActiveTab] = useState('overview')
  const isPatient = user.attributes.role === 'pacijent'

  const handleChange = (_: SyntheticEvent, value: string) => setActiveTab(value)

  return (
    <TabContext value={activeTab}>
      <TabList onChange={handleChange} variant='scrollable'>
        <Tab value='overview' label='Overview' />
        <Tab value='edit' label='Edit' />
        {isPatient && <Tab value='patient' label='Patient Record' />}
      </TabList>

      <TabPanel value='overview' sx={{ px: 0, pt: 4 }}>
        <OverviewTab user={user} />
      </TabPanel>

      <TabPanel value='edit' sx={{ px: 0, pt: 4 }}>
        <UserForm
          mode='edit'
          user={user}
          onSuccess={() => {
            window.dispatchEvent(new CustomEvent('users:changed'))
            setActiveTab('overview')
          }}
        />
      </TabPanel>

      {isPatient && (
        <TabPanel value='patient' sx={{ px: 0, pt: 4 }}>
          <PatientTab user={user} />
        </TabPanel>
      )}
    </TabContext>
  )
}
