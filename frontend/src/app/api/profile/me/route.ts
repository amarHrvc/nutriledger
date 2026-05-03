import { NextResponse } from 'next/server'

import { userMe } from '@/api/generated/auth/auth'

export async function GET() {
  const userData = await userMe()

  if (userData.status !== 200) {
    return NextResponse.json({ message: 'Failed to load profile' }, { status: userData.status })
  }

  return NextResponse.json(userData.data)
}
