import type { NextRequest } from 'next/server'
import { patientsDietPlansSend } from '@/api/generated/diet-plan/diet-plan'

type Params = { params: Promise<{ id: string; planId: string }> }

export async function POST(
	_req: NextRequest,
	{ params }: Params
) {
	const { id, planId } = await params
	const res = await patientsDietPlansSend(Number(id), Number(planId))
	return new Response(JSON.stringify(res.data), { status: res.status })
}
