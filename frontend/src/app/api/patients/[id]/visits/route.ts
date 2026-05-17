import type { NextRequest } from 'next/server'
import { customFetchMutator } from '@/api/auth.mutator'
import type { PatientsVisitsIndex200 } from '@/api/generated/nutriBaseAPI.schemas'

export async function GET(
	_req: NextRequest,
	{ params }: { params: Promise<{ id: string }> }
) {
	const { id } = await params
	const res = await customFetchMutator<{ data: PatientsVisitsIndex200; status: number }>(
		`http://localhost:8000/api/patients/${id}/visits`, { method: 'GET' }
	)
	return new Response(JSON.stringify(res.data), { status: res.status })
}

export async function POST(
	req: NextRequest,
	{ params }: { params: Promise<{ id: string }> }
) {
	const { id } = await params
	const body = await req.json()
	const res = await customFetchMutator<{ data: unknown; status: number }>(
		`http://localhost:8000/api/patients/${id}/visits`,
		{ method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) }
	)
	return new Response(JSON.stringify(res.data), { status: res.status })
}
