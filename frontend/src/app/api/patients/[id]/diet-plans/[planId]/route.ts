import type { NextRequest } from 'next/server'
import {
	patientsDietPlansShow,
	patientsDietPlansUpdate,
} from '@/api/generated/diet-plan/diet-plan'

type Params = { params: Promise<{ id: string; planId: string }> }

export async function GET(
	_req: NextRequest,
	{ params }: Params
) {
	const { id, planId } = await params
	const res = await patientsDietPlansShow(Number(id), Number(planId))
	return new Response(JSON.stringify(res.data), { status: res.status })
}

export async function PATCH(
	req: NextRequest,
	{ params }: Params
) {
	const { id, planId } = await params
	const body = await req.json()
	const res = await patientsDietPlansUpdate(Number(id), Number(planId), body)
	return new Response(JSON.stringify(res.data), { status: res.status })
}
