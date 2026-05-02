import type { NextRequest } from 'next/server';

export async function GET(request: NextRequest) {
  // Placeholder BFF GET /api/users - will call Orval generated client
  const { searchParams } = new URL(request.url);
  const page = searchParams.get('page') ?? '1';
  const search = searchParams.get('search') ?? '';

  return new Response(JSON.stringify({ data: [], meta: { page } }), { status: 200 });
}

export async function POST(request: NextRequest) {
  // Placeholder BFF POST /api/users - forward create to backend via Orval client
  const body = await request.json();
  return new Response(JSON.stringify({ data: body }), { status: 201 });
}
