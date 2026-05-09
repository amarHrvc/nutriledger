import type { NextRequest } from 'next/server';
import { patientsShow, patientsUpdate, patientsDestroy } from '@/api/generated/patient/patient';

export async function GET(request: NextRequest, { params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const idNum = Number(id);
  const res = await patientsShow(idNum);
  return new Response(JSON.stringify(res.data ?? { data: { id } }), { status: res.status ?? 200 });
}

export async function PATCH(request: NextRequest, { params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const idNum = Number(id);
  const body = await request.json();
  const res = await patientsUpdate(idNum, body as any);
  return new Response(JSON.stringify(res.data ?? { id, ...body }), { status: res.status ?? 200 });
}

export async function DELETE(request: NextRequest, { params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const idNum = Number(id);
  const res = await patientsDestroy(idNum);
  return new Response(null, { status: res.status ?? 204 });
}
