import type { NextRequest } from 'next/server';
import { usersRestore } from '../../../../api/generated/user/user';

export async function POST(request: NextRequest, { params }: { params: { id: string } }) {
  const id = params.id;
  const res = await usersRestore(id);
  return new Response(null, { status: res.status ?? 200 });
}
