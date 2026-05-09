import type { NextRequest } from 'next/server'

export async function GET(request: NextRequest, { params }: { params: Promise<{ id: string }> }) {
	const { id } = await params

	try {
		const res = await fetch(`${process.env.NEXT_PUBLIC_API_BASE_URL}/patients/${id}/visits`, {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json',
				Cookie: request.headers.get('cookie') || '',
			},
		})

		const data = await res.json()

		return new Response(JSON.stringify(data), { status: res.status ?? 200 })
	} catch (error) {
		return new Response(JSON.stringify({ message: 'Failed to fetch visits' }), { status: 500 })
	}
}
