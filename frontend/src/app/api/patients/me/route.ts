import { customFetchMutator } from '@/api/auth.mutator'
import type { PatientsIndex200 } from '@/api/generated/nutriBaseAPI.schemas'

export async function GET() {
  const res = await customFetchMutator<{ data: PatientsIndex200; status: number }>(
    'http://localhost:8000/api/patients', { method: 'GET' }
  )
  
  const patient = (res.data?.data as any)?.[0] ?? null
  
  return Response.json({ patient }, { status: res.status })
}
