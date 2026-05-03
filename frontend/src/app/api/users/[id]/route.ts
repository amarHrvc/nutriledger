import type { NextRequest } from 'next/server';
import { usersShow, usersUpdate, usersDestroy } from '@/api/generated/user/user';

export async function GET(request: NextRequest, { params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const res = await usersShow(id);
  return new Response(JSON.stringify(res.data ?? { data: { id } }), { status: res.status ?? 200 });
}

export async function PATCH(request: NextRequest, { params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const body = await request.json();
  const res = await usersUpdate(id, body as any);
  return new Response(JSON.stringify(res.data ?? { id, ...body }), { status: res.status ?? 200 });
}

export async function DELETE(request: NextRequest, { params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const res = await usersDestroy(id);
  return new Response(null, { status: res.status ?? 204 });
}
