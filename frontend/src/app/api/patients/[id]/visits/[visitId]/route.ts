import type { NextRequest } from 'next/server'

import { patientsVisitsDestroy, patientsVisitsShow, patientsVisitsUpdate } from '@/api/generated/visit/visit'
import type { UpdateVisitRequest } from '@/api/generated/nutriBaseAPI.schemas'

type Params = { params: Promise<{ id: string; visitId: string }> }

export async function GET(
	_req: NextRequest,
	{ params }: Params
) {
	const { id, visitId } = await params
	const res = await patientsVisitsShow(Number(id), Number(visitId))
	return new Response(JSON.stringify(res.data), { status: res.status })
}

export async function PATCH(
	req: NextRequest,
	{ params }: Params
) {
	const { id, visitId } = await params
	const body: UpdateVisitRequest = await req.json()
	const res = await patientsVisitsUpdate(Number(id), Number(visitId), body)
	return new Response(JSON.stringify(res.data), { status: res.status })
}

export async function DELETE(
	_req: NextRequest,
	{ params }: Params
) {
	const { id, visitId } = await params
	const res = await patientsVisitsDestroy(Number(id), Number(visitId))
	return new Response(res.status === 204 ? null : JSON.stringify(res.data), { status: res.status })
}
