'use client'

import { useCallback, useEffect, useRef, useState } from 'react'

import Alert from '@mui/material/Alert'
import Box from '@mui/material/Box'
import Button from '@mui/material/Button'
import CircularProgress from '@mui/material/CircularProgress'
import Typography from '@mui/material/Typography'

import { useAuth } from '@/context/AuthContext'

import type { DietPlan, DietPlanSummary } from './types'
import DietPlanCard from './DietPlanCard'
import DietPlanHistory from './DietPlanHistory'

interface Props {
  patientId: number
}

export default function DietPlanSection({ patientId }: Props) {
  const { user } = useAuth()
  const isPatient = user?.role === 'pacijent'
  const [plans, setPlans] = useState<DietPlanSummary[]>([])
  const [loading, setLoading] = useState(true)
  const [generating, setGenerating] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [selected, setSelected] = useState<DietPlanSummary | null>(null)
  const [fullPlan, setFullPlan] = useState<DietPlan | null>(null)
  const pollingRef = useRef<ReturnType<typeof setInterval> | null>(null)

  const fetchFullPlan = useCallback(async (planId: string): Promise<DietPlan | null> => {
    try {
      const res = await fetch(`/api/patients/${patientId}/diet-plans/${planId}`)
      const json = await res.json()

      if (!res.ok) return null

      return (json.data?.diet_plan ?? null) as DietPlan | null
    } catch {
      return null
    }
  }, [patientId])

  const fetchPlans = useCallback(async (): Promise<DietPlanSummary[]> => {
    try {
      const res = await fetch(`/api/patients/${patientId}/diet-plans`)
      const json = await res.json()

      if (!res.ok) return []

      const items: DietPlanSummary[] = json.data?.diet_plans ?? []

      setPlans(items)

      if (items[0]?.status === 'completed') {
        fetchFullPlan(items[0].id).then(plan => setFullPlan(plan))
      }

      return items
    } catch {
      return []
    }
  }, [patientId, fetchFullPlan])

  const stopPolling = useCallback(() => {
    if (pollingRef.current) {
      clearInterval(pollingRef.current)
      pollingRef.current = null
    }
  }, [])

  const startPolling = useCallback(() => {
    if (pollingRef.current) return
    pollingRef.current = setInterval(async () => {
      const latest = await fetchPlans()

      if (!latest[0] || latest[0].status !== 'pending') {
        stopPolling()
      }
    }, 3000)
  }, [fetchPlans, stopPolling])

  useEffect(() => {
    fetchPlans().then(items => {
      setLoading(false)
      if (items[0]?.status === 'pending') startPolling()
    })

    return stopPolling
  }, [patientId, fetchPlans, startPolling, stopPolling])

  const handleGenerate = async () => {
    setGenerating(true)
    setError(null)

    try {
      const res = await fetch(`/api/patients/${patientId}/diet-plans`, { method: 'POST' })

      if (res.status === 202) {
        await fetchPlans()
        startPolling()
      } else {
        setError('Could not start generation. Please try again.')
      }
    } catch {
      setError('Could not start generation. Please try again.')
    } finally {
      setGenerating(false)
    }
  }

  const handleUpdate = async (updated: DietPlan) => {
    // Update summaries list optimistically
    setPlans(prev => prev.map(p => p.id === updated.id ? { ...p, ...updated } : p))

    // Re-fetch full plan to get fresh latestDelivery and all fields
    const fresh = await fetchFullPlan(updated.id)

    if (fresh) {
      setFullPlan(fresh)
    }
  }

  if (loading) {
    return (
      <Box sx={{ display: 'flex', justifyContent: 'center', py: 6 }}>
        <CircularProgress />
      </Box>
    )
  }

  const latest = plans[0] ?? null
  const isPending = latest?.status === 'pending'
  const isFailed = latest?.status === 'failed'
  const displayPlan = fullPlan
  const history = selected ? plans : plans.slice(1)

  return (
    <Box>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
        <Typography variant='h6'>Diet Plans</Typography>
        {!isPatient && (
          <Button variant='contained' onClick={handleGenerate} disabled={generating || isPending}>
            {generating ? 'Starting…' : isPending ? 'Generating…' : 'Generate Diet Plan'}
          </Button>
        )}
      </Box>

      {error && <Alert severity='error' sx={{ mb: 2 }}>{error}</Alert>}

      {isPending && (
        <Alert severity='info' icon={<CircularProgress size={18} />} sx={{ mb: 2 }}>
          Generating your patient&apos;s diet plan… This takes about 10–30 seconds.
        </Alert>
      )}

      {isFailed && !fullPlan && (
        <Alert
          severity='error'
          sx={{ mb: 2 }}
          action={
            <Button color='inherit' size='small' onClick={handleGenerate}>
              Try Again
            </Button>
          }
        >
          Plan generation failed: {latest?.failureReason ?? 'Unknown error'}
        </Alert>
      )}

      {displayPlan && (
        <DietPlanCard
          plan={displayPlan}
          patientId={patientId}
          onRegenerate={() => { setSelected(null); setFullPlan(null); handleGenerate() }}
          onUpdate={handleUpdate}
        />
      )}

      {plans.length === 0 && !isPending && (
        <Typography variant='body2' color='text.secondary' sx={{ textAlign: 'center', py: 4 }}>
          No diet plans generated yet.
        </Typography>
      )}

      <DietPlanHistory
        plans={history}
        onSelect={plan => {
          if (selected?.id === plan.id) {
            setSelected(null)
            fetchFullPlan(latest?.id ?? plan.id).then(full => { if (full) setFullPlan(full) })
          } else {
            setSelected(plan)
            fetchFullPlan(plan.id).then(full => { if (full) setFullPlan(full) })
          }
        }}
      />
    </Box>
  )
}
