import type { NextRequest } from 'next/server'
import { customFetchMutator } from '@/api/auth.mutator'

type Params = { params: Promise<{ id: string; planId: string }> }

export async function POST(
	_req: NextRequest,
	{ params }: Params
) {
	const { id, planId } = await params
	const res = await customFetchMutator<{ data: unknown; status: number }>(
		`http://localhost:8000/api/patients/${id}/diet-plans/${planId}/send`,
		{ method: 'POST' }
	)
	return new Response(JSON.stringify(res.data), { status: res.status })
}
