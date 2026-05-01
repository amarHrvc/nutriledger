import { cookies } from 'next/headers'
import { NextResponse } from 'next/server'

import { toAuthUser } from '@/types/auth'

const INTERNAL_API = process.env.INTERNAL_API_URL ?? 'http://localhost:8000'

export async function POST(request: Request) {
  const body = await request.json()

  let laravelRes: Response

  try {
    laravelRes = await fetch(INTERNAL_API + '/api/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify(body),
    })
  } catch {
    return NextResponse.json({ message: 'Service unavailable' }, { status: 503 })
  }

  const payload = await laravelRes.json()

  console.log('###########[payload.data]', payload.data)


  if (!laravelRes.ok) {
    return NextResponse.json(
      { message: payload.message ?? 'Authentication failed' },
      { status: laravelRes.status }
    )
  }

  const cookieStore = await cookies()


  cookieStore.set('auth_token', payload.data.token, {
    httpOnly: true,
    sameSite: 'lax',
    secure: process.env.NODE_ENV === 'production',
    path: '/',
    maxAge: 60 * 60 * 8,
  })

  return NextResponse.json({ user: toAuthUser(payload.data.user) }, { status: 200 })
}
