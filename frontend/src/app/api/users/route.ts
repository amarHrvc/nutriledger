import type { NextRequest } from 'next/server';
import { usersIndex, usersStore } from '../../../api/generated/user/user';

export async function GET(request: NextRequest) {
  const { searchParams } = new URL(request.url);
  const page = Number(searchParams.get('page') ?? '1');
  const search = searchParams.get('search') ?? '';

  // Call Orval-generated client
  const res = await usersIndex();
  return new Response(JSON.stringify(res.data ?? { data: [], meta: { page } }), { status: 200 });
}

export async function POST(request: NextRequest) {
  // Forward create to backend via Orval client
  const body = await request.json();
  const res = await usersStore(body as any);
  return new Response(JSON.stringify(res.data ?? body), { status: res.status ?? 201 });
}
