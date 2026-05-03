'use client'

import { useState } from 'react'
import type { SyntheticEvent } from 'react'

import Tab from '@mui/material/Tab'
import TabContext from '@mui/lab/TabContext'
import TabList from '@mui/lab/TabList'
import TabPanel from '@mui/lab/TabPanel'

import type { UserResource } from '@/api/generated/nutriBaseAPI.schemas'
import OverviewTab from './overview'
import SecurityTab from './security'
import BillingPlansTab from './billing-plans'

interface Props {
  user: UserResource
}

export default function UserRightTabs({ user }: Props) {
  const [activeTab, setActiveTab] = useState('overview')

  const handleChange = (_: SyntheticEvent, value: string) => setActiveTab(value)

  return (
    <TabContext value={activeTab}>
      <TabList onChange={handleChange} variant='scrollable'>
        <Tab value='overview' label='Overview' />
        <Tab value='security' label='Security' />
        <Tab value='billing-plans' label='Billing &amp; Plans' />
      </TabList>
      <TabPanel value='overview' sx={{ px: 0, pt: 4 }}>
        <OverviewTab user={user} />
      </TabPanel>
      <TabPanel value='security' sx={{ px: 0, pt: 4 }}>
        <SecurityTab />
      </TabPanel>
      <TabPanel value='billing-plans' sx={{ px: 0, pt: 4 }}>
        <BillingPlansTab />
      </TabPanel>
    </TabContext>
  )
}
