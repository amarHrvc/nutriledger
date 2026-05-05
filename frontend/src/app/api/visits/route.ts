import type { NextRequest } from 'next/server'
import { customFetchMutator } from '@/api/auth.mutator'
import type { VisitsGlobalIndex200 } from '@/api/generated/nutriBaseAPI.schemas'

export async function GET(_req: NextRequest) {
  const res = await customFetchMutator<{ data: VisitsGlobalIndex200; status: number }>(
    'http://localhost:8000/api/visits', { method: 'GET' }
  )
  return new Response(JSON.stringify(res.data), { status: res.status })
}
