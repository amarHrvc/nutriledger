import type { NextRequest } from 'next/server'
import { customFetchMutator } from '@/api/auth.mutator'

export async function GET(
	_req: NextRequest,
	{ params }: { params: Promise<{ id: string }> }
) {
	const { id } = await params
	const res = await customFetchMutator<{ data: unknown; status: number }>(
		`http://localhost:8000/api/patients/${id}/diet-plans`, { method: 'GET' }
	)
	return new Response(JSON.stringify(res.data), { status: res.status })
}

export async function POST(
	_req: NextRequest,
	{ params }: { params: Promise<{ id: string }> }
) {
	const { id } = await params
	const res = await customFetchMutator<{ data: unknown; status: number }>(
		`http://localhost:8000/api/patients/${id}/diet-plans`, { method: 'POST' }
	)
	return new Response(JSON.stringify(res.data), { status: res.status })
}
