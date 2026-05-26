import type { NextRequest } from 'next/server'
import { patientsDietPlansIndex, patientsDietPlansStore } from '@/api/generated/diet-plan/diet-plan'

export async function GET(
	_req: NextRequest,
	{ params }: { params: Promise<{ id: string }> }
) {
	const { id } = await params
	const res = await patientsDietPlansIndex(Number(id))
	return new Response(JSON.stringify(res.data), { status: res.status })
}

export async function POST(
	_req: NextRequest,
	{ params }: { params: Promise<{ id: string }> }
) {
	const { id } = await params
	const res = await patientsDietPlansStore(Number(id))
	return new Response(JSON.stringify(res.data), { status: res.status })
}
