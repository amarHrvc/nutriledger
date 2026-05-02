import type { NextRequest } from 'next/server';
import { usersForceDelete } from '../../../../api/generated/user/user';

export async function DELETE(request: NextRequest, { params }: { params: { id: string } }) {
  const id = params.id;
  const res = await usersForceDelete(id);
  return new Response(null, { status: res.status ?? 204 });
}
