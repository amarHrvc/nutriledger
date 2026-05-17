import { patientsIndex } from '@/api/generated/patient/patient'
import type { PatientsIndex200 } from '@/api/generated/nutriBaseAPI.schemas'

export async function GET() {
  const res = await patientsIndex(undefined, { method: 'GET', credentials: 'include' })
  
  const patient = (res.data?.data as any)?.[0] ?? null
  
  return Response.json({ patient }, { status: res.status })
}
