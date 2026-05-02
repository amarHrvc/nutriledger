import type { NextRequest } from 'next/server';

export async function GET(request: NextRequest, { params }: { params: { id: string } }) {
  // Placeholder: return user by id
  const id = params.id;
  return new Response(JSON.stringify({ data: { id } }), { status: 200 });
}

export async function PATCH(request: NextRequest, { params }: { params: { id: string } }) {
  const id = params.id;
  const body = await request.json();
  return new Response(JSON.stringify({ data: { id, ...body } }), { status: 200 });
}

export async function DELETE(request: NextRequest, { params }: { params: { id: string } }) {
  // Soft-delete
  const id = params.id;
  return new Response(null, { status: 204 });
}
