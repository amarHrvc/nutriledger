import type { NextRequest } from 'next/server'
import { patientsVisitsIndex, patientsVisitsStore } from '@/api/generated/visit/visit'
import type { StoreVisitRequest } from '@/api/generated/nutriBaseAPI.schemas'

export async function GET(
	_req: NextRequest,
	{ params }: { params: Promise<{ id: string }> }
) {
	const { id } = await params
	const res = await patientsVisitsIndex(Number(id))
	return new Response(JSON.stringify(res.data), { status: res.status })
}

export async function POST(
	req: NextRequest,
	{ params }: { params: Promise<{ id: string }> }
) {
	const { id } = await params
	const body: StoreVisitRequest = await req.json()
	const res = await patientsVisitsStore(Number(id), body)
	return new Response(JSON.stringify(res.data), { status: res.status })
}
