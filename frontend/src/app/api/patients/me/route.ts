import { patientsIndex } from '@/api/generated/patient/patient'

export async function GET() {
  const res = await patientsIndex({ paginate: 'false' })
  const patient = (res.data?.data as any)?.[0] ?? null
  return Response.json({ patient }, { status: res.status })
}
