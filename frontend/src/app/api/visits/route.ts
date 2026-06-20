import { visitsIndex } from '@/api/generated/visit/visit'

export async function GET() {
  const res = await visitsIndex()
  return new Response(JSON.stringify(res.data), { status: res.status })
}
