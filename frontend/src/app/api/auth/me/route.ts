import { cookies } from 'next/headers'
import { NextResponse } from 'next/server'

import { toAuthUser } from '@/types/auth'

const INTERNAL_API = process.env.INTERNAL_API_URL ?? 'http://localhost:8000'

export async function GET() {
  const cookieStore = await cookies()
  const token = cookieStore.get('auth_token')?.value

  if (!token) {
    return NextResponse.json({ message: 'Unauthenticated' }, { status: 401 })
  }

  let laravelRes: Response

  try {
    laravelRes = await fetch(INTERNAL_API + '/api/user', {
      headers: { Authorization: 'Bearer ' + token, Accept: 'application/json' },
    })
  } catch {
    return NextResponse.json({ message: 'Service unavailable' }, { status: 503 })
  }

  if (!laravelRes.ok) {
    cookieStore.delete('auth_token')

    return NextResponse.json({ message: 'Unauthenticated' }, { status: 401 })
  }

  const payload = await laravelRes.json()

  return NextResponse.json({ user: toAuthUser(payload.data.user) }, { status: 200 })
}
