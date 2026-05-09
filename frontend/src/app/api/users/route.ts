import { NextRequest } from 'next/server';
import { usersIndex, usersStore } from '@/api/generated/user/user';

export async function GET(request: NextRequest) {
  const res = await usersIndex({ paginate: 'false' });

  return new Response(JSON.stringify(res.data), { status: res.status });
}

export async function POST(request: NextRequest) {
  const body = await request.json();
  const res = await usersStore(body as any);

  return new Response(JSON.stringify(res.data), { status: res.status });
}
