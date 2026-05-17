import type { NextRequest } from 'next/server'

import {
  patientsVisitsVitalsDestroy,
  patientsVisitsVitalsShow,
  patientsVisitsVitalsStore,
  patientsVisitsVitalsUpdate,
} from '@/api/generated/vital-sign/vital-sign'

type Params = { params: Promise<{ id: string; visitId: string }> }

export async function GET(_req: NextRequest, { params }: Params) {
  const { id, visitId } = await params
  const res = await patientsVisitsVitalsShow(Number(id), Number(visitId))
  return new Response(JSON.stringify(res.data), { status: res.status })
}

export async function POST(req: NextRequest, { params }: Params) {
  const { id, visitId } = await params
  const body: unknown = await req.json()
  const res = await patientsVisitsVitalsStore(Number(id), Number(visitId), {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(body),
  })
  return new Response(JSON.stringify(res.data), { status: res.status })
}

export async function PATCH(req: NextRequest, { params }: Params) {
  const { id, visitId } = await params
  const body: unknown = await req.json()
  const res = await patientsVisitsVitalsUpdate(Number(id), Number(visitId), {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(body),
  })
  return new Response(JSON.stringify(res.data), { status: res.status })
}

export async function DELETE(_req: NextRequest, { params }: Params) {
  const { id, visitId } = await params
  const res = await patientsVisitsVitalsDestroy(Number(id), Number(visitId))
  const status = res.status as number
  return new Response(status === 204 ? null : JSON.stringify(res.data), { status })
}
