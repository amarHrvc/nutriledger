import { cookies } from 'next/headers'
import { NextResponse } from 'next/server'

const INTERNAL_API = process.env.INTERNAL_API_URL ?? 'http://localhost:8000'

export async function POST() {
  const cookieStore = await cookies()
  const token = cookieStore.get('auth_token')?.value

  if (token) {
    try {
      await fetch(INTERNAL_API + '/api/logout', {
        method: 'POST',
        headers: { Authorization: 'Bearer ' + token, Accept: 'application/json' },
      })
    } catch {
      // best-effort: always clear cookie regardless of server response
    }
  }

  cookieStore.delete('auth_token')
  return new NextResponse(null, { status: 204 })
}
