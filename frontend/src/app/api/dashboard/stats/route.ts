import { NextResponse } from 'next/server'
import { usersIndex } from '@/api/generated/user/user'
import { patientsIndex } from '@/api/generated/patient/patient'

export async function GET() {

  const [usersResult, patientsResult] = await Promise.all([
    usersIndex(),
    patientsIndex(),
  ])

  if (usersResult.status !== 200 || patientsResult.status !== 200) {
    return NextResponse.json({ message: 'Failed to load stats' }, { status: 502 })
  }

  return NextResponse.json({
    stats: {
      totalUsers: ((usersResult.data as any)?.meta as any)?.total ?? 0,
      totalPatients: ((patientsResult.data as any)?.meta as any)?.total ?? 0,
    },
  })
}
