import { cookies } from 'next/headers'
import { NextResponse } from 'next/server'
import { userMe } from '@/api/generated/auth/auth'

export async function GET() {
  const cookieStore = await cookies()
  const token = cookieStore.get('auth_token')?.value?.split('|')[1]

  if (!token) {
    return NextResponse.json({ message: 'Unauthenticated' }, { status: 401 })
  }

  const userData = await userMe({
    headers: { Authorization: 'Bearer ' + token, Accept: 'application/json' },
  })

  if (userData.status !== 200) {
    return NextResponse.json({ message: 'Failed to load profile' }, { status: userData.status })
  }

  return NextResponse.json(userData.data.data)
}
