import  { NextRequest, NextResponse } from 'next/server';
import { usersIndex, usersStore } from '@/api/generated/user/user';
import { cookies } from 'next/headers'

export async function GET(request: NextRequest) {
  const { searchParams } = new URL(request.url);
  const page = Number(searchParams.get('page') ?? '1');
  const search = searchParams.get('search') ?? ''

  const cookieStore = await cookies()
  const token = cookieStore.get('auth_token')?.value?.split('|')[1]

  if (!token) {
    return NextResponse.json({ message: 'Unauthenticated' }, { status: 401 })
  }

  // Call Orval-generated client
  const res = await usersIndex(
    { paginate: 'false' },
    { headers: { Authorization: 'Bearer ' + token, Accept: 'application/json' } }
  )

  console.log("| Users-route | ############", res.data)

  return new Response(JSON.stringify(res.data ?? { data: [], meta: { page } }), { status: 200 });
}

export async function POST(request: NextRequest) {
  // Forward create to backend via Orval client
  const body = await request.json();
  const res = await usersStore(body as any);

  return new Response(JSON.stringify(res.data ?? body), { status: res.status ?? 201 });
}
