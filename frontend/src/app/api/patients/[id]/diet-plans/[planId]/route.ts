import type { NextRequest } from 'next/server'
import { customFetchMutator } from '@/api/auth.mutator'

type Params = { params: Promise<{ id: string; planId: string }> }

export async function GET(
	_req: NextRequest,
	{ params }: Params
) {
	const { id, planId } = await params
	const res = await customFetchMutator<{ data: unknown; status: number }>(
		`http://localhost:8000/api/patients/${id}/diet-plans/${planId}`, { method: 'GET' }
	)
	return new Response(JSON.stringify(res.data), { status: res.status })
}

export async function PATCH(
	req: NextRequest,
	{ params }: Params
) {
	const { id, planId } = await params
	const body: unknown = await req.json()
	const res = await customFetchMutator<{ data: unknown; status: number }>(
		`http://localhost:8000/api/patients/${id}/diet-plans/${planId}`,
		{ method: 'PATCH', body: JSON.stringify(body), headers: { 'Content-Type': 'application/json' } }
	)
	return new Response(JSON.stringify(res.data), { status: res.status })
}
