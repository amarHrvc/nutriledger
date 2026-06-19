'use client'

import { useEffect, useState } from 'react'

import { usePathname, useRouter } from 'next/navigation'

import Box from '@mui/material/Box'
import CircularProgress from '@mui/material/CircularProgress'

import { useAuth } from '@/context/AuthContext'

interface PatientData {
  id: number
}

export default function PatientRedirectGuard({ children }: { children: React.ReactNode }) {
  const router = useRouter()
  const pathname = usePathname()
  const { user, isLoading } = useAuth()
  const [redirectSettled, setRedirectSettled] = useState(false)

  useEffect(() => {
    console.warn("user !!!!!!!!!", JSON.stringify(user))
    const handleRedirect = async () => {
      // Still loading auth state - show loading spinner
      if (isLoading) {
        return
      }

      // No user logged in - allow rendering (router will handle auth)
      if (!user) {
        setRedirectSettled(true)

        return
      }

      // Not a patient - allow rendering as normal
      if (user.role !== 'pacijent') {
        setRedirectSettled(true)

        return
      }

      // Patient is already on an allowed page - allow rendering
      const patientPagePattern = /^\/dashboard\/(patients\/\d+|visits\/\d+)/

      if (patientPagePattern.test(pathname)) {
        setRedirectSettled(true)

        return
      }

      // Patient is on a protected admin/doctor route - redirect to their patient page
      try {
        const res = await fetch('/api/patients/me')
        const json = await res.json()
        const patient = json.patient as PatientData | null

        if (patient?.id) {
          const targetPath = `/dashboard/patients/${patient.id}`

          router.push(targetPath)
          await new Promise(resolve => setTimeout(resolve, 100))
        }
      } catch {

        // Continue rendering if fetch fails
      } finally {
        setRedirectSettled(true)
      }
    }

    handleRedirect()
  }, [isLoading, user, pathname, router])

  if (!redirectSettled) {
    return (
      <Box sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '100vh' }}>
        <CircularProgress />
      </Box>
    )
  }

  return children
}
