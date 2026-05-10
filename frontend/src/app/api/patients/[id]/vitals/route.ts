import type { NextRequest } from 'next/server'

import { patientsVitalsHistory } from '@/api/generated/vital-sign/vital-sign'

type Params = { params: Promise<{ id: string }> }

export async function GET(_req: NextRequest, { params }: Params) {
  const { id } = await params
  const res = await patientsVitalsHistory(Number(id))
  return new Response(JSON.stringify(res.data), { status: res.status })
}
