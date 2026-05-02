import type { NextRequest } from 'next/server';

export async function DELETE(request: NextRequest, { params }: { params: { id: string } }) {
  const id = params.id;
  // Placeholder: call backend force-delete endpoint
  return new Response(null, { status: 204 });
}
