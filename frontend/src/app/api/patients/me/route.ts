import { cookies } from 'next/headers'
import { NextResponse } from 'next/server'
import { patientsIndex } from '@/api/generated/patient/patient'

export async function GET() {
  const cookieStore = await cookies()
  const token = cookieStore.get('auth_token')?.value?.split('|')[1]

  if (!token) {
    return NextResponse.json({ message: 'Unauthenticated' }, { status: 401 })
  }

  const result = await patientsIndex({
    headers: { Authorization: 'Bearer ' + token, Accept: 'application/json' },
  })

  if (result.status !== 200) {
    return NextResponse.json({ message: 'Failed to load patient data' }, { status: result.status })
  }

  const patient = (result.data.data as any)?.[0] ?? null

  return NextResponse.json({ patient })
}
