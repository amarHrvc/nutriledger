import type { NextRequest } from 'next/server'
import { customFetchMutator } from '@/api/auth.mutator'

export async function PATCH(
	req: NextRequest,
	{ params }: { params: Promise<{ id: string; visitId: string }> }
) {
	const { id, visitId } = await params
	const body = await req.json()
	const res = await customFetchMutator<{ data: unknown; status: number }>(
		`http://localhost:8000/api/patients/${id}/visits/${visitId}`,
		{ method: 'PATCH', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) }
	)
	return new Response(JSON.stringify(res.data), { status: res.status })
}
