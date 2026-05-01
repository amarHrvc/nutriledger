import { cookies } from 'next/headers'
import { NextResponse } from 'next/server'
import { usersIndex } from '@/api/generated/user/user'
import { patientsIndex } from '@/api/generated/patient/patient'

export async function GET() {
  const cookieStore = await cookies()
  const token = cookieStore.get('auth_token')?.value?.split('|')[1]

  if (!token) {
    return NextResponse.json({ message: 'Unauthenticated' }, { status: 401 })
  }

  const authHeaders = { Authorization: 'Bearer ' + token, Accept: 'application/json' }

  const [usersResult, patientsResult] = await Promise.all([
    usersIndex({ headers: authHeaders }),
    patientsIndex({ headers: authHeaders }),
  ])

  if (usersResult.status !== 200 || patientsResult.status !== 200) {
    return NextResponse.json({ message: 'Failed to load stats' }, { status: 502 })
  }

  return NextResponse.json({
    stats: {
      totalUsers: (usersResult.data.meta as any)?.total ?? 0,
      totalPatients: (patientsResult.data.meta as any)?.total ?? 0,
    },
  })
}
