import type { NextRequest } from 'next/server'

import { patientsIndex, patientsRegister } from '@/api/generated/patient/patient'
import type { RegisterPatientRequest } from '@/api/generated/nutriBaseAPI.schemas'

export async function GET() {
  const res = await patientsIndex({paginate: 'false'})

  return new Response(JSON.stringify(res.data), { status: res.status })
}

// Creates the patient-role login account and the linked patient record together,
// in one backend transaction (POST /api/patients/register). Replaces the previous
// two-call orchestration (create user, then create patient) which silently failed
// for doctors — general account creation is admin-only, but this endpoint is scoped
// so it can only ever create a "pacijent" account.
export async function POST(request: NextRequest) {
  const body: RegisterPatientRequest = await request.json()

  const res = await patientsRegister(body)

  return new Response(JSON.stringify(res.data), { status: res.status })
}
