import { patientsIndex } from '@/api/generated/patient/patient'

export async function GET() {
  const res = await patientsIndex({ paginate: 'false' })
  const patients = (res.data as any)?.data?.patients
  const patient = Array.isArray(patients) ? patients[0] : null

  return Response.json({ patient }, { status: res.status })
}
