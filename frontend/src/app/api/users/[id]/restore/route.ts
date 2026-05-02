import type { NextRequest } from 'next/server';

export async function POST(request: NextRequest, { params }: { params: { id: string } }) {
  const id = params.id;
  // Placeholder: call backend restore endpoint
  return new Response(null, { status: 204 });
}
