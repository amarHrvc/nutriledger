import type { NextRequest } from 'next/server'

import { patientsIndex, patientsStore } from '@/api/generated/patient/patient'
import { usersStore } from '@/api/generated/user/user'

export async function GET() {
  const res = await patientsIndex({paginate: 'false'})

  console.log('[patientsINdex] ::: ', res)

  return new Response(JSON.stringify(res.data), { status: res.status })
}

export async function POST(request: NextRequest) {
  const body = await request.json()

  // Create linked user first
  const userPayload = {
    name: body.name,
    email: body.email,
    password: body.password,
    password_confirmation: body.password_confirmation,
    role: 'pacijent',
  }

  const userRes = await usersStore(userPayload as any)

  if (userRes.status !== 201) {
    return new Response(JSON.stringify(userRes.data), { status: userRes.status })
  }

  // Extract created user id (safe-path)
  const userId = (userRes.data as any)?.data?.user?.id

  if (!userId) {
    return new Response(JSON.stringify({ message: 'Failed to create linked user' }), { status: 500 })
  }

  // Build patient payload by removing account fields
  const { name, email, password, password_confirmation, ...patientFields } = body

  const patientPayload = { user_id: userId, ...patientFields }

  const patientRes = await patientsStore(patientPayload as any)

  return new Response(JSON.stringify(patientRes.data), { status: patientRes.status })
}
